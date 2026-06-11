export default {
    install(app) {
        if (typeof window !== 'undefined') {
            // Initialiser le thème au démarrage
            const initTheme = () => {
                const saved = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = saved || (prefersDark ? 'dark' : 'light');

                const html = document.documentElement;
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }

                localStorage.setItem('theme', theme);
                return theme;
            };

            // Initialiser immédiatement
            const currentTheme = initTheme();

            // Créer le composable avec le thème initial
            const themeState = {
                theme: currentTheme,
            };

            const toggleTheme = () => {
                const html = document.documentElement;
                const newTheme = themeState.theme === 'dark' ? 'light' : 'dark';

                if (newTheme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }

                localStorage.setItem('theme', newTheme);
                themeState.theme = newTheme;
            };

            const themeComposable = {
                theme: themeState.theme,
                isDark: themeState.theme === 'dark',
                toggleTheme,
            };

            app.config.globalProperties.$theme = themeComposable;
            app.provide('theme', themeComposable);
        }
    },
};
