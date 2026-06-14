<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Trop de requêtes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-background text-foreground transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            <!-- Logo -->
            <div class="mb-8">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 hover:opacity-80 transition">
                    <span class="text-4xl">🌶️</span>
                    <span class="text-2xl font-bold">SaveurIA</span>
                </a>
            </div>

            <!-- Erreur -->
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-destructive mb-4">429</h1>
                <h2 class="text-2xl font-semibold text-foreground mb-4">Trop de requêtes</h2>
                <p class="text-muted-foreground mb-6">
                    Tu as envoyé trop de requêtes. Veuillez attendre un moment avant de réessayer.
                </p>
            </div>

            <!-- Liens -->
            <div class="space-y-3">
                <a href="{{ route('welcome') }}" class="block px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition font-medium">
                    Retour à l'accueil
                </a>
            </div>

            <!-- Illustration -->
            <div class="mt-12 text-6xl opacity-20">
                🚀
            </div>
        </div>
    </div>
</body>
</html>
