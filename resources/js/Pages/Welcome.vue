<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            🤖 Mini-ChatGPT
          </div>
          <div class="flex items-center space-x-4">
            <Link :href="route('login')" class="text-slate-300 hover:text-white transition">
              Connexion
            </Link>
            <Link :href="route('register')" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition">
              S'inscrire
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
      <div class="text-center space-y-8">
        <h1 class="text-5xl md:text-6xl font-bold leading-tight">
          Discutez avec les meilleures
          <span class="block bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            IA du marché
          </span>
        </h1>

        <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto">
          Accédez à GPT-4o, Gemini et Claude dans une seule interface.
          Personnalisez vos instructions et conversez en temps réel.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
          <Link
            :href="route('register')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105"
          >
            Commencer gratuitement
          </Link>
          <a
            href="#features"
            class="border-2 border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-slate-900 font-bold py-3 px-8 rounded-lg transition"
          >
            En savoir plus
          </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-8 pt-16 border-t border-slate-700">
          <div>
            <div class="text-3xl font-bold text-blue-400">3</div>
            <div class="text-slate-400">Modèles IA</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-blue-400">∞</div>
            <div class="text-slate-400">Conversations</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-blue-400">⚡</div>
            <div class="text-slate-400">Temps réel</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-slate-800/50 py-20 border-y border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-16">
          Pourquoi Mini-ChatGPT?
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-blue-400 transition">
            <div class="text-4xl mb-4">💬</div>
            <h3 class="text-2xl font-bold mb-4">Conversations illimitées</h3>
            <p class="text-slate-400">
              Créez autant de conversations que vous le souhaitez.
              L'historique est sauvegardé automatiquement.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-purple-400 transition">
            <div class="text-4xl mb-4">🔄</div>
            <h3 class="text-2xl font-bold mb-4">Plusieurs modèles</h3>
            <p class="text-slate-400">
              Basculez entre GPT-4o, Gemini et Claude.
              Comparez les réponses pour chaque tâche.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-green-400 transition">
            <div class="text-4xl mb-4">⚡</div>
            <h3 class="text-2xl font-bold mb-4">Streaming temps réel</h3>
            <p class="text-slate-400">
              Voyez les réponses s'afficher en direct.
              Pas d'attente, réponses instantanées.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-yellow-400 transition">
            <div class="text-4xl mb-4">⚙️</div>
            <h3 class="text-2xl font-bold mb-4">Instructions personnalisées</h3>
            <p class="text-slate-400">
              Configurez le comportement de l'IA.
              Définissez le style et le domaine d'expertise.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-red-400 transition">
            <div class="text-4xl mb-4">🎨</div>
            <h3 class="text-2xl font-bold mb-4">Interface moderne</h3>
            <p class="text-slate-400">
              Design épuré avec Vue.js 3 et TailwindCSS.
              Responsive sur tous les appareils.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-indigo-400 transition">
            <div class="text-4xl mb-4">🔒</div>
            <h3 class="text-2xl font-bold mb-4">Sécurisé</h3>
            <p class="text-slate-400">
              Vos données sont privées et sécurisées.
              Authentification robuste.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 py-20">
      <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold mb-8">
          Prêt à commencer?
        </h2>
        <p class="text-xl mb-8 text-blue-50">
          Rejoignez des utilisateurs qui discutent
          avec les meilleures IA du marché.
        </p>
        <Link
          :href="route('register')"
          class="inline-block bg-white text-blue-600 hover:bg-blue-50 font-bold py-4 px-12 rounded-lg transition"
        >
          Créer un compte gratuitement
        </Link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-700 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
          <div>
            <h3 class="font-bold text-lg mb-2">Mini-ChatGPT</h3>
            <p class="text-slate-400 text-sm">
              La plateforme tout-en-un pour discuter
              avec les meilleures IA.
            </p>
          </div>
          <div class="text-right text-slate-400 text-sm">
            © 2026 Mini-ChatGPT | Examen SGBD
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
html {
  scroll-behavior: smooth;
}
</style>
