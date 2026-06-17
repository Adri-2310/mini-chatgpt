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
  <div class="min-h-screen bg-background text-foreground transition-colors duration-200">
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

        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto">
          Votre assistant culinaire personnel. Inspirations de recettes, conseils de cuisine,
          et suggestions de menus adaptées à vos goûts et régimes.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
          <Link
            :href="route('register')"
            class="bg-primary text-primary-foreground hover:opacity-90 font-bold py-3 px-8 rounded-lg transition transform hover:scale-105"
          >
            Commencer gratuitement
          </Link>
          <Link
            :href="route('about')"
            class="border-2 border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold py-3 px-8 rounded-lg transition"
          >
            En savoir plus
          </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-8 pt-16 border-t border-border">
          <div>
            <div class="text-3xl font-bold text-primary">1000+</div>
            <div class="text-muted-foreground">Recettes</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-primary">3</div>
            <div class="text-muted-foreground">IA Experts</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-primary">24/7</div>
            <div class="text-muted-foreground">Disponible</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-secondary py-20 border-y border-border transition-colors duration-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-16 text-foreground">
          Pourquoi SaveurIA?
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">📖</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Recettes Infinies</h3>
            <p class="text-muted-foreground">
              Découvrez des milliers de recettes adaptées à vos ingrédients.
              Des classiques aux innovations culinaires.
            </p>
          </div>

          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">👨‍🍳</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Conseils d'Expert</h3>
            <p class="text-muted-foreground">
              Techniques de cuisson, astuces de chef, et trucs pour réussir.
              Améliorez votre savoir-faire culinaire.
            </p>
          </div>

          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">⚡</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Inspirations Rapides</h3>
            <p class="text-muted-foreground">
              Idées de menus en temps réel basées sur vos préférences.
              Pas d'inspiration? SaveurIA vous aide!
            </p>
          </div>

          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">🥗</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Régimes & Allergies</h3>
            <p class="text-muted-foreground">
              Végétalien, sans gluten, sans lactose...
              SaveurIA respecte vos contraintes alimentaires.
            </p>
          </div>

          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">🛒</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Liste de Courses</h3>
            <p class="text-muted-foreground">
              Générez automatiquement votre liste d'ingrédients.
              Partagez, modifiez et cochez facilement.
            </p>
          </div>

          <div class="bg-card border border-border rounded-xl p-8 hover:border-primary transition">
            <div class="text-4xl mb-4">💾</div>
            <h3 class="text-2xl font-bold mb-4 text-foreground">Vos Recettes Sauvegardées</h3>
            <p class="text-muted-foreground">
              Gardez vos recettes préférées et historique.
              Accédez-y n'importe quand, n'importe où.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-primary py-20 transition-colors duration-200">
      <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold mb-8 text-primary-foreground">
          Prêt à cuisiner?
        </h2>
        <p class="text-xl mb-8 text-primary-foreground opacity-90">
          Rejoignez les passionnés de cuisine qui font confiance à SaveurIA
          pour leurs inspirations culinaires.
        </p>
        <Link
          :href="route('register')"
          class="inline-block bg-primary-foreground text-primary hover:opacity-90 font-bold py-4 px-12 rounded-lg transition"
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
