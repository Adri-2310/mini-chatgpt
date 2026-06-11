# 🎨 Système de Thème Tweakcn

Documentation complète du système de thème CSS personnalisé de Mini-ChatGPT.

---

## 📋 Vue d'ensemble

**Tweakcn** est un système de variables CSS personnalisées inspiré de **shadcn/ui**, intégré à TailwindCSS 4. Il fournit :

- 🎯 Palette de couleurs cohérente (clair + sombre)
- 🌓 Support natif du mode sombre (`.dark` class)
- 🎨 Tokens de design réutilisables (couleurs, polices, ombres)
- 📏 Système de mise à l'échelle (spacing, radius, shadows)
- ♿ Respect des standards d'accessibilité (contraste, focus states)

### Architecture

```
resources/css/
├── theme.css              # Variables CSS personnalisées (tweakcn)
├── app.css                # Point d'entrée (@tailwind directives)
└── (utilisé par tous les composants Vue)

tailwind.config.js        # Extension TailwindCSS pour utiliser les variables
```

---

## 🎯 Variables CSS

### Modèle de thème

Chaque variable est définie en **deux états** :
- `:root` - Mode clair (light)
- `.dark` - Mode sombre (dark)

```css
:root {
  --background: #e8ebed;    /* Light mode */
}

.dark {
  --background: #1c2433;    /* Dark mode */
}
```

### Catégories de variables

#### 1️⃣ Couleurs principales

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--background` | `#e8ebed` | `#1c2433` | Fond principal de l'app |
| `--foreground` | `#333333` | `#e5e5e5` | Texte principal |

**Utilisation:**
```vue
<div class="bg-background text-foreground">
  Contenu principal
</div>
```

#### 2️⃣ Composants (Cards, Popovers)

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--card` | `#ffffff` | `#2a3040` | Fond des cartes |
| `--card-foreground` | `#333333` | `#e5e5e5` | Texte sur cartes |
| `--popover` | `#ffffff` | `#262b38` | Popovers/dropdowns |
| `--popover-foreground` | `#333333` | `#e5e5e5` | Texte popover |

**Utilisation:**
```vue
<div class="bg-card text-card-foreground p-4 rounded-lg">
  Contenu de carte
</div>
```

#### 3️⃣ Actions (Primary, Secondary, Destructive)

**Primary** (Boutons CTA, liens importants)

| Variable | Light/Dark | Usage |
|----------|-----------|-------|
| `--primary` | `#e05d38` | Fond bouton primaire |
| `--primary-foreground` | `#ffffff` | Texte sur primaire |

```vue
<button class="bg-primary text-primary-foreground hover:opacity-90">
  Action Principale
</button>
```

**Secondary** (Boutons secondaires)

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--secondary` | `#f3f4f6` | `#2a303e` | Fond secondaire |
| `--secondary-foreground` | `#4b5563` | `#e5e5e5` | Texte secondaire |

**Destructive** (Suppression, danger)

| Variable | Light/Dark | Usage |
|----------|-----------|-------|
| `--destructive` | `#ef4444` | Fond bouton danger |
| `--destructive-foreground` | `#ffffff` | Texte danger |

```vue
<button class="bg-destructive text-destructive-foreground">
  Supprimer
</button>
```

#### 4️⃣ États et accents

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--muted` | `#f9fafb` | `#2a303e` | Éléments estompés |
| `--muted-foreground` | `#6b7280` | `#a3a3a3` | Texte estompé |
| `--accent` | `#d6e4f0` | `#2a3656` | Highlights |
| `--accent-foreground` | `#1e3a8a` | `#bfdbfe` | Texte accent |

```vue
<!-- Placeholder, info secondaire -->
<p class="text-muted-foreground text-sm">
  Information secondaire
</p>

<!-- Highlight, badges -->
<span class="bg-accent text-accent-foreground px-2 py-1 rounded">
  Badge
</span>
```

#### 5️⃣ Éléments de formulaire

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--input` | `#f4f5f7` | `#3d4354` | Fond input/textarea |
| `--border` | `#dcdfe2` | `#3d4354` | Bordures |
| `--ring` | `#e05d38` | `#e05d38` | Focus ring (outline) |

```vue
<input 
  class="bg-input border border-border text-foreground rounded focus:ring-2 focus:ring-ring"
/>
```

#### 6️⃣ Sidebar (optionnel)

Pour les layouts avec sidebar :

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--sidebar` | `#dddfe2` | `#2a303f` | Fond sidebar |
| `--sidebar-foreground` | `#333333` | `#e5e5e5` | Texte sidebar |
| `--sidebar-primary` | `#e05d38` | `#e05d38` | Primary sidebar |
| `--sidebar-accent` | `#d6e4f0` | `#2a3656` | Accent sidebar |
| `--sidebar-border` | `#e5e7eb` | `#3d4354` | Bordures sidebar |

```vue
<aside class="bg-sidebar text-sidebar-foreground border-r border-sidebar-border">
  <!-- Contenu sidebar -->
</aside>
```

#### 7️⃣ Graphiques (Charts)

Pour les données visualisées :

| Variable | Light | Dark | Usage |
|----------|-------|------|-------|
| `--chart-1` | `#86a7c8` | `#86a7c8` | Couleur dataset 1 |
| `--chart-2` | `#eea591` | `#e6a08f` | Couleur dataset 2 |
| `--chart-3` | `#5a7ca6` | `#5a7ca6` | Couleur dataset 3 |
| `--chart-4` | `#466494` | `#466494` | Couleur dataset 4 |
| `--chart-5` | `#334c82` | `#334c82` | Couleur dataset 5 |

```vue
<canvas :style="{ '--chart-1': 'var(--chart-1)', ... }">
  Graphique
</canvas>
```

#### 8️⃣ Typographie

| Variable | Valeur | Usage |
|----------|--------|-------|
| `--font-sans` | `Inter, sans-serif` | Police par défaut |
| `--font-serif` | `Source Serif 4, serif` | Corps de texte |
| `--font-mono` | `JetBrains Mono, monospace` | Code |

```vue
<p class="font-sans">Texte standard</p>
<p class="font-serif">Corps de texte</p>
<code class="font-mono">snippet()</code>
```

#### 9️⃣ Géométrie

| Variable | Valeur | Usage |
|----------|--------|-------|
| `--radius` | `0.75rem` | Rayon de bordure (rounded) |

```vue
<div class="rounded-[var(--radius)]">
  Contenu
</div>
```

#### 🔟 Ombres

```css
--shadow-2xs: 0px 1px 3px 0px hsl(0 0% 0% / 0.05);
--shadow-xs: 0px 1px 3px 0px hsl(0 0% 0% / 0.05);
--shadow-sm: 0px 1px 3px 0px hsl(0 0% 0% / 0.10), 0px 1px 2px -1px hsl(0 0% 0% / 0.10);
--shadow: 0px 1px 3px 0px hsl(0 0% 0% / 0.10), 0px 1px 2px -1px hsl(0 0% 0% / 0.10);
--shadow-md: 0px 1px 3px 0px hsl(0 0% 0% / 0.10), 0px 2px 4px -1px hsl(0 0% 0% / 0.10);
```

**Utilisation:**
```vue
<div class="shadow">Ombre standard</div>
<div class="shadow-md">Ombre moyenne</div>
<div class="shadow-lg">Ombre grande</div>
```

---

## 🎨 Utilisation

### Via Tailwind Classes (recommandé)

TailwindCSS génère automatiquement les classes à partir des variables :

```vue
<!-- Fond et texte -->
<div class="bg-background text-foreground">
  Contenu
</div>

<!-- Cartes -->
<div class="bg-card text-card-foreground p-4 rounded-lg shadow">
  Contenu carte
</div>

<!-- Boutons -->
<button class="bg-primary text-primary-foreground px-4 py-2 rounded hover:opacity-90">
  Action
</button>

<!-- Inputs -->
<input class="bg-input border border-border text-foreground rounded" />

<!-- Destructif -->
<button class="bg-destructive text-destructive-foreground">
  Supprimer
</button>

<!-- Muted/Accents -->
<p class="text-muted-foreground">Texte secondaire</p>
<span class="bg-accent text-accent-foreground">Badge</span>
```

### Via CSS Variables (raw CSS)

Pour des cas spécifiques :

```vue
<style scoped>
.custom-card {
  background-color: var(--card);
  color: var(--card-foreground);
  border-color: var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}
</style>
```

### Combinaisons utiles

**Page principale (background + text)**
```vue
<div class="bg-background text-foreground min-h-screen">
  <!-- Contenu -->
</div>
```

**Carte avec titre et description**
```vue
<div class="bg-card text-card-foreground p-6 rounded-lg shadow">
  <h2 class="text-lg font-bold mb-2">Titre</h2>
  <p class="text-muted-foreground text-sm">Description</p>
  <button class="bg-primary text-primary-foreground mt-4 px-4 py-2 rounded">
    Action
  </button>
</div>
```

**Formulaire**
```vue
<form class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-foreground mb-1">
      Label
    </label>
    <input 
      class="w-full bg-input border border-border text-foreground px-3 py-2 rounded focus:ring-2 focus:ring-ring focus:outline-none"
    />
  </div>
</form>
```

---

## 🌓 Mode sombre

### Activation

Le mode sombre est activé en ajoutant la classe `.dark` à l'élément `<html>` :

```javascript
// Activer le mode sombre
document.documentElement.classList.add('dark');

// Désactiver le mode sombre
document.documentElement.classList.remove('dark');

// Basculer
document.documentElement.classList.toggle('dark');
```

### Persistance (localStorage)

```javascript
// Au démarrage
const isDark = localStorage.getItem('theme') === 'dark';
if (isDark) {
  document.documentElement.classList.add('dark');
}

// Au changement
const toggleDark = () => {
  document.documentElement.classList.toggle('dark');
  const isDark = document.documentElement.classList.contains('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
};
```

### Composant Vue (exemple)

```vue
<template>
  <button 
    @click="toggleDark"
    class="p-2 rounded hover:bg-secondary"
    :aria-label="isDark ? 'Mode clair' : 'Mode sombre'"
  >
    <span v-if="isDark">☀️</span>
    <span v-else>🌙</span>
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(false);

onMounted(() => {
  isDark.value = localStorage.getItem('theme') === 'dark';
  updateTheme();
});

const toggleDark = () => {
  isDark.value = !isDark.value;
  updateTheme();
};

const updateTheme = () => {
  document.documentElement.classList.toggle('dark', isDark.value);
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};
</script>
```

---

## 🎨 Personnalisation

### Modifier les couleurs existantes

#### 1. Éditer `resources/css/theme.css`

```css
:root {
  --primary: #ff6b35;              /* Changé de #e05d38 */
  --primary-foreground: #ffffff;
  --secondary: #f3f4f6;
  /* ... */
}

.dark {
  --primary: #ff6b35;              /* Même couleur en dark */
  --primary-foreground: #ffffff;
  /* ... */
}
```

#### 2. Les changements s'appliquent instantanément

Tous les composants utilisant `bg-primary`, `text-primary`, etc. se mettent à jour automatiquement.

### Ajouter une nouvelle variable

#### 1. Ajouter dans `theme.css`

```css
:root {
  --new-color: #abc123;
  --new-color-foreground: #ffffff;
}

.dark {
  --new-color: #def456;
  --new-color-foreground: #000000;
}
```

#### 2. Enregistrer dans `tailwind.config.js`

```javascript
// tailwind.config.js
export default {
  theme: {
    extend: {
      colors: {
        'new-color': 'var(--new-color)',
        'new-color-foreground': 'var(--new-color-foreground)',
      },
    },
  },
};
```

#### 3. Utiliser dans les composants

```vue
<div class="bg-new-color text-new-color-foreground">
  Nouveau style
</div>
```

### Créer une palette alternative

Pour créer une palette complètement nouvelle (ex: thème "holiday") :

```css
/* À la fin de theme.css */
.holiday {
  --background: #fff8f0;
  --foreground: #1a1a1a;
  --primary: #c41e3a;
  --primary-foreground: #ffffff;
  /* ... autres variables ... */
}
```

Activation :
```javascript
document.documentElement.classList.add('holiday');
```

---

## 📐 Configuration TailwindCSS

### `tailwind.config.js` (extrait)

```javascript
export default {
  darkMode: 'class',  // Utilise .dark class pour dark mode
  
  theme: {
    extend: {
      colors: {
        background: 'var(--background)',
        foreground: 'var(--foreground)',
        card: 'var(--card)',
        'card-foreground': 'var(--card-foreground)',
        primary: 'var(--primary)',
        'primary-foreground': 'var(--primary-foreground)',
        secondary: 'var(--secondary)',
        'secondary-foreground': 'var(--secondary-foreground)',
        muted: 'var(--muted)',
        'muted-foreground': 'var(--muted-foreground)',
        accent: 'var(--accent)',
        'accent-foreground': 'var(--accent-foreground)',
        destructive: 'var(--destructive)',
        'destructive-foreground': 'var(--destructive-foreground)',
        border: 'var(--border)',
        input: 'var(--input)',
        ring: 'var(--ring)',
      },
    },
  },
};
```

---

## ✅ Bonnes pratiques

### À faire ✅

- ✅ Utiliser les classes Tailwind (`bg-card`, `text-foreground`) pour la cohérence
- ✅ Personnaliser via `theme.css` pour un changement global
- ✅ Tester l'app en mode clair **et** sombre
- ✅ Utiliser l'opacité pour les variations : `bg-primary/80`, `bg-primary/50`
- ✅ Ajouter des transitions pour le changement de thème
- ✅ Respecter le contraste des couleurs (WCAG AA)
- ✅ Documenter les nouvelles variables

### À éviter ❌

- ❌ Utiliser des couleurs en dur (`bg-[#ff0000]`) — utiliser les variables
- ❌ Ajouter des couleurs directement dans `tailwind.config.js` hors du système tweakcn
- ❌ Oublier le mode sombre lors du développement
- ❌ Créer des variables CSS sans correspondance dans Tailwind
- ❌ Changer les couleurs primaires sans testing complet

---

## 🔍 Troubleshooting

### Les couleurs ne changent pas

1. **Vérifier le fichier `app.css`** :
   ```css
   @import "./theme.css";  /* Doit être en premier */
   @tailwind base;
   @tailwind components;
   @tailwind utilities;
   ```

2. **Rebuild Tailwind** :
   ```bash
   npm run dev
   # ou
   npm run build
   ```

3. **Vider le cache** :
   ```bash
   rm -rf node_modules/.vite
   npm run dev
   ```

### Mode sombre ne fonctionne pas

1. **Vérifier `tailwind.config.js`** :
   ```javascript
   darkMode: 'class',  // Doit être 'class', pas 'media'
   ```

2. **Vérifier la classe `.dark` sur `<html>`** :
   ```javascript
   console.log(document.documentElement.classList);
   // Doit contenir 'dark'
   ```

### Contraste insuffisant

Utiliser un outil de contraste :
- https://www.tpgi.com/color-contrast-checker/
- https://webaim.org/resources/contrastchecker/

Minimum requis : **WCAG AA** (4.5:1 pour du texte)

---

## 📚 Ressources

- **Fichier principal** : `resources/css/theme.css`
- **Configuration** : `tailwind.config.js`
- **README racine** : Voir section "🎨 Système de Thème Tweakcn"
- **TailwindCSS Docs** : https://tailwindcss.com/docs/customization/colors
- **shadcn/ui** (inspiration) : https://ui.shadcn.com/

---

## 🤝 Contribution

Avant de modifier le système de thème :

1. Créer une branche feature : `git checkout -b feat/theme-update`
2. Modifier `theme.css` et tester en mode clair/sombre
3. Mettre à jour `tailwind.config.js` si nouvelles variables
4. Documenter les changements dans ce fichier
5. Créer une PR avec description des changements

---

**Dernière mise à jour:** Juin 2026
