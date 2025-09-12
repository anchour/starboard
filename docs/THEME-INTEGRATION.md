# Theme Integration Guide

## Overview

This guide explains how WordPress themes integrate with Starboard components using the `@starboard/styles` NPM package. Themes have complete control over styling through Tailwind configuration and CSS overrides.

## Installation Methods

### Method 1: NPM Package (Recommended)

```bash
# In your theme directory
npm init -y
npm install @starboard/styles
npm install -D tailwindcss@^3 postcss autoprefixer

# Initialize Tailwind
npx tailwindcss init -p
```

### Method 2: CDN (Quick Start)
```html
<!-- In theme header.php -->
<link rel="stylesheet" href="https://unpkg.com/@starboard/styles@latest/dist/starboard.min.css">
<script src="https://unpkg.com/@starboard/styles@latest/dist/starboard.js" defer></script>
```

### Method 3: Download & Host
```bash
# Download and extract to theme
wget https://github.com/starboard/styles/releases/download/v1.0.0/starboard-styles.zip
unzip starboard-styles.zip -d assets/starboard/
```

---

## Theme Setup Examples

### Basic Integration

#### 1. Theme Functions.php
```php
<?php
// functions.php

// Declare Starboard support
add_theme_support('starboard-styles');

// Enqueue compiled styles
function theme_enqueue_styles() {
    wp_enqueue_style(
        'starboard-styles',
        get_template_directory_uri() . '/dist/styles.css',
        [],
        '1.0.0'
    );
    
    wp_enqueue_script(
        'starboard-behaviors',
        get_template_directory_uri() . '/dist/scripts.js',
        [],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
```

#### 2. Basic Tailwind Config
```javascript
// tailwind.config.js
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.{js,css}',
    // Include Starboard styles in content scanning
    './node_modules/@starboard/styles/src/**/*.css'
  ],
  theme: {
    extend: {
      // Your theme colors (Starboard components will use these)
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          900: '#1e3a8a',
        },
        secondary: {
          500: '#6b7280',
          600: '#4b5563',
        }
      }
    }
  }
}
```

#### 3. Main CSS File
```css
/* src/styles/main.css */
@import 'tailwindcss/base';
@import 'tailwindcss/components';

/* Import Starboard components */
@import '@starboard/styles';

@import 'tailwindcss/utilities';

/* Theme-specific overrides */
@import './theme-overrides.css';
```

#### 4. Package.json Scripts
```json
{
  "scripts": {
    "build": "npm run build:css && npm run build:js",
    "build:css": "tailwindcss -i ./src/styles/main.css -o ./dist/styles.css --minify",
    "build:js": "esbuild src/scripts/main.js --bundle --outfile=dist/scripts.js --minify",
    "watch": "tailwindcss -i ./src/styles/main.css -o ./dist/styles.css --watch",
    "dev": "npm run watch"
  }
}
```

### Advanced Integration

#### 1. Custom Theme Configuration
```javascript
// tailwind.config.js
const starboardConfig = require('@starboard/styles/tailwind.config.js');

module.exports = {
  // Extend Starboard's config
  ...starboardConfig,
  content: [
    ...starboardConfig.content,
    './**/*.php',
    './src/**/*.{js,css}'
  ],
  theme: {
    ...starboardConfig.theme,
    extend: {
      ...starboardConfig.theme.extend,
      
      // Brand-specific customizations
      colors: {
        brand: {
          primary: '#0066cc',
          secondary: '#6c757d',
          accent: '#28a745',
        }
      },
      
      // Custom spacing for components
      spacing: {
        'section': '5rem',
        'section-lg': '8rem',
      },
      
      // Custom breakpoints if needed
      screens: {
        'xs': '475px',
        '3xl': '1600px',
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ]
}
```

#### 2. Selective Component Import
```css
/* src/styles/starboard.css - Import only needed components */

/* Base styles */
@import '@starboard/styles/src/base/variables.css';
@import '@starboard/styles/src/base/reset.css';

/* Only import components you use */
@import '@starboard/styles/src/components/columns.css';
@import '@starboard/styles/src/components/buttons.css';
@import '@starboard/styles/src/components/hero.css';
@import '@starboard/styles/src/components/cards.css';
/* Skip carousel, tables, etc. if not used */

/* Utilities */
@import '@starboard/styles/src/utilities/spacing.css';
```

#### 3. JavaScript Selective Loading
```javascript
// src/scripts/starboard.js
import { Carousel } from '@starboard/styles/js/behaviors/carousel';
import { Accordion } from '@starboard/styles/js/behaviors/accordion';

// Auto-initialize only components in use
document.addEventListener('DOMContentLoaded', () => {
  // Only initialize carousels if they exist
  const carousels = document.querySelectorAll('[data-carousel]');
  if (carousels.length > 0) {
    carousels.forEach(el => new Carousel(el));
  }
  
  // FAQ accordions
  const faqs = document.querySelectorAll('[data-accordion]');
  if (faqs.length > 0) {
    faqs.forEach(el => new Accordion(el));
  }
});
```

---

## Customization Patterns

### Level 1: CSS Custom Properties (Easiest)

```css
/* Theme customizes via CSS variables */
:root {
  /* Spacing overrides */
  --sb-space-sm: 1rem;
  --sb-space-md: 2rem;
  --sb-space-lg: 3rem;
  
  /* Component-specific variables */
  --sb-button-padding-x: 1.5rem;
  --sb-button-padding-y: 0.75rem;
  --sb-hero-min-height: 80vh;
  --sb-card-padding: 2rem;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  :root {
    --sb-text-color: #f8fafc;
    --sb-bg-color: #1a202c;
  }
}
```

### Level 2: Tailwind Theme Extensions

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        // Starboard components will use these automatically
        primary: '#0066cc',
        secondary: '#6c757d',
      },
      
      spacing: {
        // Custom spacing that components can use
        'component': '2.5rem',
        'section': '4rem',
      },
      
      borderRadius: {
        // Custom border radius
        'component': '0.5rem',
      }
    }
  }
}
```

### Level 3: Component Class Overrides

```css
/* Theme overrides specific component styles */

/* Button customizations */
.sb-button {
  /* Override default button styles */
  @apply font-semibold tracking-wide;
}

.sb-button[data-variant="primary"] {
  /* Custom primary button */
  @apply bg-gradient-to-r from-blue-500 to-purple-600;
  @apply hover:from-blue-600 hover:to-purple-700;
}

/* Hero customizations */
.sb-hero {
  /* Add custom background patterns */
  background-image: linear-gradient(45deg, transparent 25%, rgba(255,255,255,.1) 25%);
}

/* Card hover effects */
.sb-card {
  @apply transition-all duration-300;
  
  &:hover {
    @apply transform -translate-y-1 shadow-xl;
  }
}
```

### Level 4: New Component Variants

```css
/* Create new variants using Starboard patterns */

/* Custom card variant */
.sb-card--featured {
  @apply relative border-2 border-yellow-400;
  
  &::before {
    content: 'Featured';
    @apply absolute -top-2 left-4 bg-yellow-400 text-black px-2 py-1 text-xs font-bold rounded;
  }
}

/* Custom button variant */
.sb-button--cta {
  @apply relative overflow-hidden;
  @apply bg-gradient-to-r from-orange-500 to-red-500;
  @apply text-white font-bold py-4 px-8 text-lg;
  @apply transform transition-all duration-200;
  @apply hover:scale-105 hover:shadow-2xl;
}
```

---

## Theme Implementation Examples

### Minimal Theme

```php
<?php
// functions.php - Minimal setup
add_theme_support('starboard-styles');

wp_enqueue_style(
    'starboard',
    'https://unpkg.com/@starboard/styles@1/dist/starboard.min.css'
);
wp_enqueue_script(
    'starboard',
    'https://unpkg.com/@starboard/styles@1/dist/starboard.js',
    [], null, true
);
```

### Business Theme

#### Directory Structure
```
theme/
├── src/
│   ├── styles/
│   │   ├── main.css
│   │   ├── components/
│   │   └── utilities/
│   └── scripts/
│       ├── main.js
│       └── components/
├── dist/           # Built files
├── node_modules/
├── package.json
├── tailwind.config.js
└── functions.php
```

#### Tailwind Configuration
```javascript
// tailwind.config.js
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.{js,css}',
    './node_modules/@starboard/styles/src/**/*.css'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
        },
        gray: {
          50: '#f8fafc',
          900: '#0f172a',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        display: ['Poppins', 'sans-serif'],
      }
    }
  }
}
```

#### Custom Styles
```css
/* src/styles/main.css */
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import '@starboard/styles';
@import 'tailwindcss/utilities';

/* Business theme customizations */
.sb-hero {
  @apply relative;
  
  /* Custom overlay pattern */
  &::after {
    content: '';
    @apply absolute inset-0 opacity-10;
    background-image: url('data:image/svg+xml,...'); /* Pattern */
  }
}

.sb-card {
  @apply border border-gray-100;
  @apply hover:border-primary-200 hover:shadow-lg;
  @apply transition-all duration-300;
}

/* Custom button styles for business theme */
.sb-button[data-variant="primary"] {
  @apply bg-primary-600 hover:bg-primary-700;
  @apply shadow-lg hover:shadow-xl;
}
```

### E-commerce Theme

```css
/* Product-focused customizations */
.sb-card--product {
  @apply group cursor-pointer;
  
  .sb-card__media {
    @apply overflow-hidden;
    
    img {
      @apply transition-transform duration-300;
      @apply group-hover:scale-105;
    }
  }
  
  .sb-card__content {
    @apply relative;
    
    .sb-button {
      @apply opacity-0 transform translate-y-4;
      @apply group-hover:opacity-100 group-hover:translate-y-0;
      @apply transition-all duration-300;
    }
  }
}

/* Price display */
.sb-rates--pricing {
  .sb-rates__value {
    @apply text-3xl font-bold text-green-600;
  }
  
  .sb-rates__unit {
    @apply text-lg text-gray-500;
  }
}
```

---

## Build Process Integration

### Webpack Integration
```javascript
// webpack.config.js
const path = require('path');

module.exports = {
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          'style-loader',
          'css-loader',
          'postcss-loader'
        ],
      },
    ],
  },
  resolve: {
    alias: {
      '@starboard/styles': path.resolve(__dirname, 'node_modules/@starboard/styles/src')
    }
  }
};
```

### Gulp Integration
```javascript
// gulpfile.js
const gulp = require('gulp');
const postcss = require('gulp-postcss');
const tailwindcss = require('tailwindcss');

gulp.task('css', () => {
  return gulp.src('src/styles/main.css')
    .pipe(postcss([
      require('postcss-import'),
      tailwindcss,
      require('autoprefixer'),
    ]))
    .pipe(gulp.dest('dist/'));
});
```

### Laravel Mix Integration
```javascript
// webpack.mix.js
const mix = require('laravel-mix');

mix.postCss('src/styles/main.css', 'dist/', [
  require('postcss-import'),
  require('tailwindcss'),
  require('autoprefixer'),
]);

mix.js('src/scripts/main.js', 'dist/');
```

---

## Performance Optimization

### CSS Optimization

#### PurgeCSS Integration
```javascript
// tailwind.config.js
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.{js,css}',
    // Include Starboard templates for purging
    './node_modules/@starboard/styles/src/**/*.css'
  ],
  // Tailwind will purge unused styles automatically
}
```

#### Critical CSS
```bash
# Extract critical CSS for above-the-fold content
npm install critical --save-dev

# In build process
critical src/index.html --css dist/styles.css --target dist/critical.css --width 1300 --height 900
```

### JavaScript Optimization

#### Tree Shaking
```javascript
// Only import needed behaviors
import { Carousel } from '@starboard/styles/js/behaviors/carousel';
// Don't import the whole library
// import StarboardBehaviors from '@starboard/styles/js'; ❌
```

#### Lazy Loading
```javascript
// Lazy load interactive components
const loadCarousel = async () => {
  const { Carousel } = await import('@starboard/styles/js/behaviors/carousel');
  return Carousel;
};

// Initialize only when needed
if (document.querySelector('[data-carousel]')) {
  loadCarousel().then(Carousel => {
    document.querySelectorAll('[data-carousel]').forEach(el => {
      new Carousel(el);
    });
  });
}
```

---

## Troubleshooting

### Common Issues

#### 1. Styles Not Loading
```php
// Check if theme declared support
if (!current_theme_supports('starboard-styles')) {
    // Styles won't load from plugin
}
```

#### 2. Tailwind Conflicts
```css
/* Scope Starboard styles if needed */
.starboard-components {
  @import '@starboard/styles';
}
```

#### 3. Build Issues
```bash
# Clear cache and rebuild
rm -rf node_modules/.cache
npm run build
```

#### 4. JavaScript Not Working
```html
<!-- Check script loading -->
<script>
console.log('Starboard loaded:', window.StarboardBehaviors);
</script>
```

### Debug Mode

```php
// functions.php - Enable debug
define('STARBOARD_DEBUG', true);

// Shows component data attributes in source
add_filter('starboard_debug_attributes', '__return_true');
```

### Version Compatibility

```json
// package.json - Lock to specific versions
{
  "dependencies": {
    "@starboard/styles": "~1.0.0"
  }
}
```

---

## Migration Between Versions

### From v1.0 to v1.1
```bash
# Update package
npm install @starboard/styles@^1.1.0

# Check for new components
npm run build

# Test in development
npm run dev
```

### Breaking Changes (v2.0)
```bash
# Major version changes
npm install @starboard/styles@^2.0.0

# Update Tailwind if needed
npm install tailwindcss@^4.0.0

# Review migration guide
# Update custom overrides
```

This integration guide provides themes with multiple approaches to customize Starboard components while maintaining the benefits of the NPM package architecture.