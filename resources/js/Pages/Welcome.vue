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
          Cuisinez mieux avec
          <span class="block bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
            SaveurIA
          </span>
        </h1>

        <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto">
          Votre assistant culinaire personnel. Inspirations de recettes, conseils de cuisine,
          et suggestions de menus adaptées à vos goûts et régimes.
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
            <div class="text-3xl font-bold text-orange-400">1000+</div>
            <div class="text-slate-400">Recettes</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-orange-400">3</div>
            <div class="text-slate-400">IA Experts</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-orange-400">24/7</div>
            <div class="text-slate-400">Disponible</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-slate-800/50 py-20 border-y border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-16">
          Pourquoi SaveurIA?
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-orange-400 transition">
            <div class="text-4xl mb-4">📖</div>
            <h3 class="text-2xl font-bold mb-4">Recettes Infinies</h3>
            <p class="text-slate-400">
              Découvrez des milliers de recettes adaptées à vos ingrédients.
              Des classiques aux innovations culinaires.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-red-400 transition">
            <div class="text-4xl mb-4">👨‍🍳</div>
            <h3 class="text-2xl font-bold mb-4">Conseils d'Expert</h3>
            <p class="text-slate-400">
              Techniques de cuisson, astuces de chef, et trucs pour réussir.
              Améliorez votre savoir-faire culinaire.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-yellow-400 transition">
            <div class="text-4xl mb-4">⚡</div>
            <h3 class="text-2xl font-bold mb-4">Inspirations Rapides</h3>
            <p class="text-slate-400">
              Idées de menus en temps réel basées sur vos préférences.
              Pas d'inspiration? SaveurIA vous aide!
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-green-400 transition">
            <div class="text-4xl mb-4">🥗</div>
            <h3 class="text-2xl font-bold mb-4">Régimes & Allergies</h3>
            <p class="text-slate-400">
              Végétalien, sans gluten, sans lactose...
              SaveurIA respecte vos contraintes alimentaires.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-blue-400 transition">
            <div class="text-4xl mb-4">🛒</div>
            <h3 class="text-2xl font-bold mb-4">Liste de Courses</h3>
            <p class="text-slate-400">
              Générez automatiquement votre liste d'ingrédients.
              Partagez, modifiez et cochez facilement.
            </p>
          </div>

          <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8 hover:border-pink-400 transition">
            <div class="text-4xl mb-4">💾</div>
            <h3 class="text-2xl font-bold mb-4">Vos Recettes Sauvegardées</h3>
            <p class="text-slate-400">
              Gardez vos recettes préférées et historique.
              Accédez-y n'importe quand, n'importe où.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-orange-600 to-red-600 py-20">
      <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold mb-8">
          Prêt à cuisiner?
        </h2>
        <p class="text-xl mb-8 text-orange-50">
          Rejoignez les passionnés de cuisine qui font confiance à SaveurIA
          pour leurs inspirations culinaires.
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
