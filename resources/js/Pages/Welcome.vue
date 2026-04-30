<script setup>
import { onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Nav from '@/Components/Nav.vue';
import Footer from '@/Components/Footer.vue';

const page = usePage();

onMounted(() => {
    if (page.props.auth?.user) {
        router.visit(route('dashboard'));
    }
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white">
    <Nav />

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
      <div class="text-center space-y-8">
        <h1 class="text-5xl md:text-6xl font-bold leading-tight">
          Parlez avec
          <span class="block bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            GPT-4, Gemini et Claude
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
    <Footer />
  </div>
</template>
<style scoped>
html {
  scroll-behavior: smooth;
}
</style>
