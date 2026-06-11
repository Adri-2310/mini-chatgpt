import { ref, computed } from 'vue';

const currentTheme = ref(localStorage.getItem('theme') ||
    (typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));

export function useTheme() {
    const isDark = computed(() => currentTheme.value === 'dark');
    const isLight = computed(() => currentTheme.value === 'light');

    const applyTheme = () => {
        const html = document.documentElement;

        if (isDark.value) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        localStorage.setItem('theme', currentTheme.value);
    };

    const toggleTheme = () => {
        currentTheme.value = isDark.value ? 'light' : 'dark';
        applyTheme();
    };

    const setTheme = (theme) => {
        if (theme === 'light' || theme === 'dark') {
            currentTheme.value = theme;
            applyTheme();
        }
    };

    return {
        currentTheme,
        isDark,
        isLight,
        toggleTheme,
        setTheme,
    };
}
