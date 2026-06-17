<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\LlmModel;
use App\Models\Message;
use App\Services\ChatService;
use App\Traits\CalculateCosts;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    use CalculateCosts;

    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Crée un message utilisateur et génère une réponse IA (non-streaming)
     *
     * @param Request $request Contient 'content' et 'model'
     * @param Conversation $conversation La conversation cible
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Conversation $conversation)
    {
        try {
            $this->authorize('addMessage', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:5000',
            'model' => ['required', 'string', Rule::in(LlmModel::getEnabled()->pluck('model_id'))],
        ]);

        try {
            $model = $request->input('model');
            $llmModel = LlmModel::where('model_id', $model)->first();

            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->input('content'),
                'model' => $model,
                'llm_model_id' => $llmModel?->id,
            ]);

            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get(['id', 'role', 'content', 'tokens_used', 'created_at']);

            if ($messages->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

            $messageHistory = $messages->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])->toArray();
            $systemPrompt = $this->chatService->buildSystemPrompt();
            $maxTokens = $llmModel?->max_tokens ?? 1024;

            $aiResult = $this->chatService->askWithHistory(
                $request->input('model'),
                $messageHistory,
                $systemPrompt,
                $maxTokens
            );

            $tokensUsed = $aiResult['tokens'] ?? 0;
            $cost = $this->calculateCostByTokens($model, $tokensUsed);

            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResult['content'],
                'model' => $request->input('model'),
                'tokens_used' => $tokensUsed,
                'cost_usd' => $cost,
                'llm_model_id' => $llmModel?->id,
            ]);
            $messages->push($assistantMessage);

            $titleUpdated = false;
            if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                if ($messages->count() >= 4) {
                    $title = $this->generateConversationTitle($conversation, $request->input('model'));
                    $conversation->update(['title' => $title]);
                    $titleUpdated = true;
                }
            }

            $messages = $messages->map(fn($msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'content' => $msg->content,
                'tokens_used' => $msg->tokens_used,
                'created_at' => $msg->created_at,
            ]);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'title_updated' => $titleUpdated,
                'new_title' => $titleUpdated ? $conversation->title : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Message store failed', [
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Génère un titre intelligent et descriptif pour la conversation.
     *
     * Approche en deux étapes décrites dans un seul prompt :
     *   1. L'IA détermine le contexte (cuisine / technique / autre) et identifie
     *      LE sujet/plat principal de la conversation.
     *   2. Elle produit un titre descriptif adapté au domaine détecté :
     *        - Cuisine   : "Recette [Plat]" (ex. "Recette Boeuf Bourguignon")
     *        - Technique  : "[Domaine] [Sujet]" (ex. "Config Docker Laravel")
     *        - Autre      : "[Sujet Principal]"
     *
     * Utilise toujours un modèle économique (GPT-4o mini) avec max_tokens minimal
     * pour éviter les erreurs de dépassement de crédits OpenRouter.
     *
     * @param Conversation $conversation
     * @param string $model (ignoré — on utilise le modèle de titre fixe)
     * @return string Titre généré (2-5 mots, max 40 caractères)
     */
    private function generateConversationTitle(Conversation $conversation, string $model): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->limit(4)
            ->get(['role', 'content'])
            ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
            ->toArray();

        $prompt = $this->buildTitlePrompt($messages);

        // Modèle économique fixe pour la génération de titre, max_tokens très bas (un titre = ~10 tokens)
        $titleModel = 'openai/gpt-4o-mini';

        try {
            // Plus de marge de tokens : l'IA fait un mini-raisonnement interne avant le titre.
            $result = $this->chatService->ask($titleModel, $prompt, null, 30);
            $title = $this->normalizeTitle($result['content'] ?? '');

            // Si l'IA renvoie un titre vide, trop long, ou une copie quasi-littérale
            // du premier message, on bascule sur le fallback intelligent.
            if ($title === '' || mb_strlen($title) > 40 || $this->isVerbatimCopy($title, $messages[0]['content'] ?? '')) {
                return $this->generateFallbackTitle($messages);
            }

            return $title;
        } catch (\Exception $e) {
            return $this->generateFallbackTitle($messages);
        }
    }

    /**
     * Construit le prompt en deux étapes (détection de contexte + génération du titre).
     */
    private function buildTitlePrompt(array $messages): string
    {
        $prompt = <<<'EOT'
Tu génères un titre court et descriptif pour une conversation, en français.

ÉTAPE 1 — Analyse la conversation et détermine en silence :
  - Le CONTEXTE : "cuisine" (recette, ingrédient, technique culinaire, plat),
    "technique" (code, configuration, logiciel, bug, outil), ou "autre".
  - LE sujet/plat PRINCIPAL (un seul, le plus important de toute la discussion).
  - Les détails clés (ingrédient principal, technologie, domaine concerné).

ÉTAPE 2 — Produis UN SEUL titre selon le contexte détecté :
  - cuisine   : "Recette <Plat>" — ex : "Recette Boeuf Bourguignon",
                "Recette Poulet Rôti", "Recette Gâteau Chocolat".
  - technique : "<Domaine> <Sujet>" — ex : "Config Docker Laravel",
                "Bug Python Async", "Setup WordPress".
  - autre     : "<Sujet Principal>" — 2 à 4 mots descriptifs.

RÈGLES :
  - 2 à 5 mots, 40 caractères maximum.
  - Un seul plat/sujet principal, jamais une liste.
  - Décris le SUJET GLOBAL de la discussion, ne recopie pas la première question.
  - Réponds UNIQUEMENT avec le titre final : pas de guillemets, pas de
    ponctuation finale, pas d'explication, pas le mot "Titre".

Conversation :

EOT;

        foreach ($messages as $msg) {
            $role = $msg['role'] === 'user' ? 'Utilisateur' : 'Assistant';
            $content = trim(substr($msg['content'], 0, 200));
            $prompt .= "$role: $content\n";
        }

        return $prompt;
    }

    /**
     * Nettoie et met en forme un titre brut renvoyé par l'IA.
     */
    private function normalizeTitle(string $raw): string
    {
        $title = trim($raw);

        // L'IA préfixe parfois "Titre :" ou "Titre -" : on l'enlève.
        $title = preg_replace('/^titre\s*[:\-–]\s*/iu', '', $title);

        // Enlever guillemets, tirets, astérisques et espaces en début/fin.
        $title = preg_replace('/^[\s\'"«»\-\*]+|[\s\'"«»\-\*]+$/u', '', $title);

        // Enlever une éventuelle ponctuation finale.
        $title = preg_replace('/[.!?]+$/u', '', $title);

        // Capitalisation propre (chaque mot).
        return ucwords(mb_strtolower(trim($title)));
    }

    /**
     * Détecte si le titre est une copie quasi mot-pour-mot du début du premier message.
     * Ne déclenche le fallback que dans ce cas précis (et non sur de simples mots-clés partagés).
     */
    private function isVerbatimCopy(string $title, string $firstMessage): bool
    {
        $normalizedTitle = strtolower(trim($title));
        $firstStart = strtolower(trim(substr($firstMessage, 0, strlen($normalizedTitle) + 5)));

        if ($normalizedTitle === '' || strlen($normalizedTitle) < 8) {
            return false;
        }

        // Le titre commence-t-il littéralement comme le premier message ?
        // (ex: titre "comment faire un gateau" == début du message "comment faire un gateau au chocolat")
        return str_starts_with($firstStart, $normalizedTitle);
    }

    /**
     * Fallback intelligent quand l'IA échoue ou renvoie un titre inutilisable.
     *
     * Détecte d'abord le domaine dominant de la conversation (cuisine / technique),
     * puis construit un titre cohérent avec ce domaine :
     *   - cuisine   : "Recette <Plat/Ingrédient dominant>"
     *   - technique : "<Domaine détecté> <Mot-clé>" (ex. "Config Docker")
     *   - sinon     : les mots-clés dominants de la conversation.
     */
    private function generateFallbackTitle(array $messages): string
    {
        $assistantContent = implode(' ', array_map(
            fn($m) => $m['content'],
            array_filter($messages, fn($m) => $m['role'] === 'assistant')
        ));
        $userContent = implode(' ', array_map(
            fn($m) => $m['content'],
            array_filter($messages, fn($m) => $m['role'] === 'user')
        ));

        $allContent = mb_strtolower($assistantContent . ' ' . $userContent);

        // Mots-clés culinaires courants (ingrédients + plats + vocabulaire de cuisine).
        $culinaryKeywords = [
            'recette', 'sauce', 'cuisine', 'plat', 'cuisson', 'four', 'poele', 'mijoter',
            'gateau', 'tarte', 'pizza', 'pain', 'pate', 'pates', 'soupe', 'salade', 'dessert',
            'boeuf', 'poulet', 'porc', 'agneau', 'veau', 'canard', 'dinde', 'poisson', 'saumon',
            'crevette', 'oeuf', 'oeufs', 'fromage', 'chocolat', 'creme', 'beurre', 'farine',
            'tomate', 'oignon', 'ail', 'pomme', 'patate', 'carotte', 'champignon', 'riz',
            'bourguignon', 'ratatouille', 'quiche', 'crepe', 'gratin', 'risotto', 'curry',
            'marinade', 'epices', 'herbes', 'vegetalien', 'vegetarien', 'gluten',
        ];

        // Mots-clés techniques courants (souvent des noms propres, on garde la casse d'origine).
        $techKeywords = [
            'docker', 'laravel', 'php', 'python', 'javascript', 'vue', 'react', 'node',
            'mysql', 'postgres', 'sql', 'api', 'git', 'github', 'linux', 'wordpress',
            'nginx', 'apache', 'composer', 'npm', 'bug', 'erreur', 'config', 'configuration',
            'serveur', 'deploiement', 'css', 'html', 'json', 'async', 'cache', 'redis',
        ];

        // --- Détection cuisine ---
        $foundCulinary = array_values(array_filter(
            $culinaryKeywords,
            fn($kw) => str_contains($allContent, $kw)
        ));
        // --- Détection technique ---
        $foundTech = array_values(array_filter(
            $techKeywords,
            fn($kw) => str_contains($allContent, $kw)
        ));

        // Mots-clés dominants (par fréquence) hors stopwords, pour compléter.
        $dominant = $this->extractDominantKeywords($allContent);

        // Cuisine : on privilégie l'ingrédient/plat le plus fréquent et descriptif.
        if (count($foundCulinary) > 0) {
            // Plat/ingrédient = le mot culinaire le plus présent, sinon le 1er trouvé.
            $main = $this->mostFrequentAmong($allContent, array_filter(
                $foundCulinary,
                fn($w) => !in_array($w, ['recette', 'cuisine', 'plat', 'cuisson'])
            )) ?? ($foundCulinary[0] ?? null);

            if ($main) {
                // Ajouter un second mot-clé dominant pour préciser (ex. "Boeuf Bourguignon").
                $extra = collect($dominant)
                    ->reject(fn($w) => $w === $main || in_array($w, ['recette', 'cuisine', 'plat', 'cuisson']))
                    ->first();
                $subject = trim($main . ($extra ? " $extra" : ''));
                return $this->capTitle('Recette ' . ucwords($subject));
            }
            return 'Recette';
        }

        // Technique : "<Domaine> <précision>".
        if (count($foundTech) > 0) {
            $domain = $foundTech[0];
            $extra = collect($foundTech)->skip(1)->first()
                ?? collect($dominant)->reject(fn($w) => $w === $domain)->first();
            $subject = trim($domain . ($extra ? " $extra" : ''));
            return $this->capTitle(ucwords($subject));
        }

        // Autre : mots-clés dominants.
        $keywords = array_slice($dominant, 0, 3);
        if (count($keywords) >= 2) {
            return $this->capTitle(ucwords(implode(' ', $keywords)));
        }
        if (count($keywords) === 1) {
            return $this->capTitle(ucwords($keywords[0]));
        }

        return 'Conversation';
    }

    /**
     * Renvoie les mots-clés dominants (par fréquence) d'un texte, hors stopwords.
     *
     * @return array<int, string>
     */
    private function extractDominantKeywords(string $content): array
    {
        $stopwords = [
            'le', 'la', 'les', 'de', 'des', 'du', 'un', 'une', 'et', 'ou', 'pour', 'dans',
            'avec', 'sur', 'tu', 'que', 'quoi', 'est', 'sont', 'qui', 'pas', 'plus', 'vous',
            'nous', 'cette', 'votre', 'mais', 'donc', 'comme', 'tout', 'tous', 'bien',
            'peut', 'fait', 'faire', 'voici', 'comment', 'pourquoi', 'quel', 'quelle',
            'aussi', 'alors', 'cela', 'sans', 'ainsi', 'leur', 'elle', 'aux',
        ];

        $words = str_word_count(mb_strtolower($content), 1, 'àâäéèêëïîôöùûüç');
        $words = array_filter($words, fn($w) => mb_strlen($w) > 3 && !in_array($w, $stopwords));

        $frequency = array_count_values($words);
        arsort($frequency);

        return array_keys($frequency);
    }

    /**
     * Renvoie, parmi une liste de mots-clés, celui qui apparaît le plus dans le texte.
     */
    private function mostFrequentAmong(string $content, array $candidates): ?string
    {
        $best = null;
        $bestCount = 0;
        foreach ($candidates as $candidate) {
            $count = mb_substr_count($content, $candidate);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $candidate;
            }
        }
        return $best;
    }

    /**
     * Tronque proprement un titre à 40 caractères.
     */
    private function capTitle(string $title): string
    {
        $title = trim($title);
        return mb_strlen($title) <= 40 ? $title : trim(mb_substr($title, 0, 40));
    }

    /**
     * Prépare le contexte du message (historique + system prompt)
     *
     * @param Conversation $conversation
     * @return array ['messageHistory' => array, 'systemPrompt' => string]
     */
    private function prepareMessageContext(Conversation $conversation): array
    {
        $messageHistory = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
            ->toArray();

        return [
            'messageHistory' => $messageHistory,
            'systemPrompt' => $this->chatService->buildSystemPrompt(),
        ];
    }

    /**
     * Crée un message utilisateur et génère une réponse IA avec streaming SSE
     *
     * @param Request $request Contient 'content' et 'model'
     * @param Conversation $conversation La conversation cible
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamStore(Request $request, Conversation $conversation)
    {
        try {
            $this->authorize('addMessage', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:5000',
            'model' => ['required', 'string', Rule::in(LlmModel::getEnabled()->pluck('model_id'))],
        ]);

        try {
            $model = $request->input('model');
            $llmModel = LlmModel::where('model_id', $model)->first();

            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->input('content'),
                'model' => $model,
                'llm_model_id' => $llmModel?->id,
            ]);

            if ($conversation->messages()->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

            ['messageHistory' => $messageHistory, 'systemPrompt' => $systemPrompt] = $this->prepareMessageContext($conversation);
            $maxTokens = $llmModel?->max_tokens ?? 1024;

            return response()->stream(function () use ($conversation, $messageHistory, $systemPrompt, $model, $request, $llmModel, $maxTokens) {
                $fullResponse = '';

                // 1. Lecture du flux depuis le ChatService
                $stream = $this->chatService->streamWithHistory($model, $messageHistory, $systemPrompt, $maxTokens);

                foreach ($stream as $chunk) {
                    if (!empty($chunk)) {
                        $fullResponse .= $chunk;

                        // 2. Formatage SSE avec echo (et encodage JSON pour la sécurité des caractères)
                        echo "data: " . json_encode(['content' => $chunk]) . "\n\n";

                        // 3. Vidage du buffer pour forcer l'envoi immédiat au navigateur
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // 4. Sauvegarder le message avec les tokens du streaming
                // Note: tokens_used vient de getLastStreamTokens() qui capture total_tokens de l'API
                $tokensUsed = $this->chatService->getLastStreamTokens() ?? 0;
                $cost = $this->calculateCostByTokens($model, $tokensUsed);

                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'model' => $model,
                    'tokens_used' => $tokensUsed,
                    'cost_usd' => $cost,
                    'llm_model_id' => $llmModel?->id,
                ]);

                if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                    $messageCount = $conversation->messages()->count();
                    if ($messageCount >= 4) {
                        $title = $this->generateConversationTitle($conversation, $model);
                        $conversation->update(['title' => $title]);

                        // Notifier le frontend du nouveau titre via SSE
                        echo "data: " . json_encode(['type' => 'title_updated', 'title' => $title]) . "\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // 5. Envoyer l'utilisation des tokens au frontend avant de fermer
                if ($tokensUsed) {
                    echo "data: " . json_encode(['type' => 'usage', 'tokens' => $tokensUsed]) . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }

                // 6. Envoyer le signal de fin au frontend
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (\Exception $e) {
            Log::error('Message stream store failed', [
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}