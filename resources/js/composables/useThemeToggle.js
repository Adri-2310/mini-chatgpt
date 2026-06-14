import { ref, computed, onMounted, inject } from 'vue';

export function useThemeToggle() {
  const theme = inject('theme');
  const forceUpdate = ref(0);

  const isDark = computed(() => {
    forceUpdate.value;
    if (typeof document !== 'undefined') {
      return document.documentElement.classList.contains('dark');
    }
    return false;
  });

  const toggleTheme = () => {
    if (theme?.toggleTheme) {
      theme.toggleTheme();
      forceUpdate.value++;
    }
  };

  onMounted(() => {
    const observer = new MutationObserver(() => {
      forceUpdate.value++;
    });

    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class'],
    });

    return () => observer.disconnect();
  });

  return {
    isDark,
    toggleTheme,
  };
}
