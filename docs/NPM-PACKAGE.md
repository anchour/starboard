# NPM Package Architecture (@starboard/styles)

## Overview

The `@starboard/styles` NPM package provides all styling for Starboard blocks using Tailwind CSS v3. Themes import this package and can customize via Tailwind configuration and CSS overrides.

## Package Structure

```
packages/styles/
├── src/
│   ├── index.css                 # Main entry point
│   ├── base/
│   │   ├── reset.css            # Minimal resets
│   │   └── variables.css        # CSS custom properties
│   ├── components/
│   │   ├── columns.css          # Column layouts
│   │   ├── buttons.css          # Button styles
│   │   ├── hero.css             # Hero sections
│   │   ├── cards.css            # Card components
│   │   ├── carousel.css         # Carousel styles
│   │   ├── tables.css           # Table styles
│   │   ├── testimonials.css    # Testimonial layouts
│   │   ├── rates.css            # Rate displays
│   │   ├── icons.css            # Icon styles
│   │   ├── text-media.css       # Text/media splits
│   │   └── photo-feature.css    # Photo features
│   └── utilities/
│       ├── spacing.css          # Spacing utilities
│       └── responsive.css       # Responsive helpers
├── dist/
│   ├── starboard.css            # Built CSS
│   └── starboard.min.css        # Minified CSS
├── js/
│   ├── behaviors/
│   │   ├── carousel.js          # Carousel functionality
│   │   ├── accordion.js         # Accordion behavior
│   │   └── table-sort.js        # Table sorting
│   └── index.js                 # JS entry point
├── tailwind.config.js           # Tailwind configuration
├── postcss.config.js            # PostCSS configuration
├── package.json                 # Package definition
└── README.md                    # Usage documentation
```

## Package.json

```json
{
  "name": "@starboard/styles",
  "version": "1.0.0",
  "description": "Tailwind CSS v3 styles for Starboard WordPress blocks",
  "main": "dist/starboard.css",
  "style": "dist/starboard.css",
  "scripts": {
    "build": "npm run build:css && npm run build:js",
    "build:css": "tailwindcss -i ./src/index.css -o ./dist/starboard.css",
    "build:css:min": "tailwindcss -i ./src/index.css -o ./dist/starboard.min.css --minify",
    "build:js": "esbuild js/index.js --bundle --outfile=dist/starboard.js --format=esm",
    "watch": "npm run watch:css",
    "watch:css": "tailwindcss -i ./src/index.css -o ./dist/starboard.css --watch",
    "dev": "npm run watch"
  },
  "files": [
    "dist",
    "src",
    "js",
    "tailwind.config.js",
    "postcss.config.js"
  ],
  "peerDependencies": {
    "tailwindcss": "^3.0.0"
  },
  "devDependencies": {
    "tailwindcss": "^3.4.0",
    "postcss": "^8.4.38",
    "postcss-import": "^16.0.0",
    "autoprefixer": "^10.4.19",
    "esbuild": "^0.19.0"
  },
  "keywords": [
    "wordpress",
    "blocks",
    "tailwind",
    "css",
    "starboard"
  ],
  "author": "Starboard Team",
  "license": "MIT",
  "repository": {
    "type": "git",
    "url": "https://github.com/your-org/starboard.git"
  }
}
```

## Tailwind Configuration

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './src/**/*.css',
  ],
  theme: {
    extend: {
      // Custom spacing for consistent component spacing
      spacing: {
        'section-sm': '2rem',
        'section': '4rem',
        'section-lg': '6rem',
        'section-xl': '8rem',
      },
      // Component-specific grid templates
      gridTemplateColumns: {
        'split': '1fr 1fr',
        'sidebar-left': '300px 1fr',
        'sidebar-right': '1fr 300px',
        'thirds': 'repeat(3, 1fr)',
        'quarters': 'repeat(4, 1fr)',
      },
      // Custom breakpoints if needed
      screens: {
        'xs': '475px',
        '2xl': '1536px',
      },
      // Animation utilities
      animation: {
        'carousel': 'slide 0.3s ease-in-out',
        'fade-in': 'fadeIn 0.3s ease-in',
      },
      keyframes: {
        slide: {
          '0%': { transform: 'translateX(100%)' },
          '100%': { transform: 'translateX(0)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        }
      }
    }
  },
  corePlugins: {
    // Disable preflight to avoid conflicts with WordPress
    preflight: false,
  },
  plugins: [
    // Optional: Add Tailwind plugins if needed
    // require('@tailwindcss/forms'),
    // require('@tailwindcss/typography'),
  ],
}
```

## Component Styles

### Main Entry (index.css)
```css
/* src/index.css */

/* Import Tailwind layers */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Import custom base styles */
@import './base/variables.css';
@import './base/reset.css';

/* Import component styles */
@import './components/columns.css';
@import './components/buttons.css';
@import './components/hero.css';
@import './components/cards.css';
@import './components/carousel.css';
@import './components/tables.css';
@import './components/testimonials.css';
@import './components/rates.css';
@import './components/icons.css';
@import './components/text-media.css';
@import './components/photo-feature.css';

/* Import utility styles */
@import './utilities/spacing.css';
@import './utilities/responsive.css';
```

### CSS Variables (base/variables.css)
```css
/* CSS Custom Properties for theming */
:root {
  /* Spacing scale */
  --sb-space-xs: theme('spacing.2');
  --sb-space-sm: theme('spacing.4');
  --sb-space-md: theme('spacing.6');
  --sb-space-lg: theme('spacing.8');
  --sb-space-xl: theme('spacing.12');
  --sb-space-2xl: theme('spacing.16');
  
  /* Component-specific variables */
  --sb-button-padding-x: theme('spacing.4');
  --sb-button-padding-y: theme('spacing.2');
  --sb-card-padding: theme('spacing.6');
  --sb-hero-min-height: theme('spacing.96');
  
  /* Transitions */
  --sb-transition: 0.2s ease-in-out;
  
  /* Breakpoints for JS */
  --sb-breakpoint-sm: theme('screens.sm');
  --sb-breakpoint-md: theme('screens.md');
  --sb-breakpoint-lg: theme('screens.lg');
  --sb-breakpoint-xl: theme('screens.xl');
}
```

### Columns Component (components/columns.css)
```css
/* Columns Layout Component */
.sb-columns {
  @apply grid;
  gap: var(--sb-gap, theme('spacing.6'));
  
  /* Default responsive behavior */
  grid-template-columns: repeat(var(--sb-cols-mobile, 1), 1fr);
  
  @screen sm {
    grid-template-columns: repeat(var(--sb-cols-tablet, 2), 1fr);
  }
  
  @screen lg {
    grid-template-columns: repeat(var(--sb-cols-desktop, 4), 1fr);
  }
}

/* Data attribute mapping */
[data-columns="1"] { --sb-cols-desktop: 1; }
[data-columns="2"] { --sb-cols-desktop: 2; }
[data-columns="3"] { --sb-cols-desktop: 3; }
[data-columns="4"] { --sb-cols-desktop: 4; }
[data-columns="5"] { --sb-cols-desktop: 5; }
[data-columns="6"] { --sb-cols-desktop: 6; }

[data-columns-tablet="1"] { --sb-cols-tablet: 1; }
[data-columns-tablet="2"] { --sb-cols-tablet: 2; }
[data-columns-tablet="3"] { --sb-cols-tablet: 3; }
[data-columns-tablet="4"] { --sb-cols-tablet: 4; }

[data-columns-mobile="1"] { --sb-cols-mobile: 1; }
[data-columns-mobile="2"] { --sb-cols-mobile: 2; }

/* Gap variants */
[data-gap="none"] { --sb-gap: 0; }
[data-gap="sm"] { --sb-gap: theme('spacing.2'); }
[data-gap="default"] { --sb-gap: theme('spacing.6'); }
[data-gap="lg"] { --sb-gap: theme('spacing.8'); }
[data-gap="xl"] { --sb-gap: theme('spacing.12'); }

/* Special layouts */
.sb-columns[data-layout="sidebar-left"] {
  @apply lg:grid-cols-[300px_1fr];
}

.sb-columns[data-layout="sidebar-right"] {
  @apply lg:grid-cols-[1fr_300px];
}

.sb-columns[data-layout="split"] {
  @apply lg:grid-cols-2;
}
```

### Buttons Component (components/buttons.css)
```css
/* Button Component */
.sb-button {
  @apply inline-flex items-center justify-center;
  @apply border border-transparent rounded-md;
  @apply font-medium transition-colors duration-200;
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2;
  
  /* Default size */
  @apply px-4 py-2 text-base;
  
  /* Default variant (primary) */
  @apply text-white bg-blue-600 hover:bg-blue-700;
  @apply focus:ring-blue-500;
}

/* Variants */
[data-variant="primary"] {
  @apply text-white bg-blue-600 hover:bg-blue-700;
  @apply focus:ring-blue-500;
}

[data-variant="secondary"] {
  @apply text-gray-700 bg-gray-100 hover:bg-gray-200;
  @apply focus:ring-gray-500;
}

[data-variant="outline"] {
  @apply text-gray-700 bg-transparent border-gray-300;
  @apply hover:bg-gray-50;
  @apply focus:ring-gray-500;
}

[data-variant="ghost"] {
  @apply text-gray-700 bg-transparent border-transparent;
  @apply hover:bg-gray-100;
  @apply focus:ring-gray-500;
}

[data-variant="link"] {
  @apply text-blue-600 bg-transparent border-transparent;
  @apply hover:text-blue-700 hover:underline;
  @apply p-0;
}

/* Sizes */
[data-size="sm"] {
  @apply px-3 py-1.5 text-sm;
}

[data-size="lg"] {
  @apply px-6 py-3 text-lg;
}

[data-size="xl"] {
  @apply px-8 py-4 text-xl;
}

/* Icons */
.sb-button[data-icon] {
  @apply gap-2;
}

.sb-button[data-icon-position="left"] svg {
  @apply -ml-1 w-5 h-5;
}

.sb-button[data-icon-position="right"] svg {
  @apply -mr-1 w-5 h-5;
}

/* Full width */
[data-full-width="true"] {
  @apply w-full;
}
```

### Hero Component (components/hero.css)
```css
/* Hero Component */
.sb-hero {
  @apply relative overflow-hidden;
  min-height: var(--sb-hero-height, theme('spacing.96'));
}

/* Height variants */
[data-height="short"] {
  --sb-hero-height: theme('spacing.64');
}

[data-height="tall"] {
  --sb-hero-height: 100vh;
}

[data-height="medium"] {
  --sb-hero-height: 75vh;
}

/* Background */
.sb-hero__background {
  @apply absolute inset-0;
  
  img, video {
    @apply w-full h-full object-cover;
  }
}

/* Overlay */
.sb-hero[data-overlay="light"]::before {
  content: '';
  @apply absolute inset-0 bg-white bg-opacity-75 z-10;
}

.sb-hero[data-overlay="dark"]::before {
  content: '';
  @apply absolute inset-0 bg-black bg-opacity-50 z-10;
}

/* Content */
.sb-hero__content {
  @apply relative z-20;
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
  @apply py-16 sm:py-24 lg:py-32;
  @apply flex flex-col;
}

/* Alignment */
[data-alignment="center"] .sb-hero__content {
  @apply items-center text-center;
}

[data-alignment="left"] .sb-hero__content {
  @apply items-start text-left;
}

[data-alignment="right"] .sb-hero__content {
  @apply items-end text-right;
}

/* Typography */
.sb-hero__title {
  @apply text-4xl sm:text-5xl lg:text-6xl font-bold;
  @apply text-gray-900;
}

.sb-hero[data-overlay="dark"] .sb-hero__title {
  @apply text-white;
}

.sb-hero__subtitle {
  @apply mt-4 text-xl text-gray-600;
  @apply max-w-3xl;
}

.sb-hero[data-overlay="dark"] .sb-hero__subtitle {
  @apply text-gray-200;
}

/* Actions */
.sb-hero__actions {
  @apply mt-8 flex flex-wrap gap-4;
}

[data-alignment="center"] .sb-hero__actions {
  @apply justify-center;
}
```

### Spacing Utilities (utilities/spacing.css)
```css
/* Spacing Utilities */
[data-spacing-top="none"] {
  padding-top: 0;
}

[data-spacing-top="sm"] {
  @apply pt-8;
}

[data-spacing-top="default"] {
  @apply pt-16;
}

[data-spacing-top="lg"] {
  @apply pt-24;
}

[data-spacing-top="xl"] {
  @apply pt-32;
}

[data-spacing-bottom="none"] {
  padding-bottom: 0;
}

[data-spacing-bottom="sm"] {
  @apply pb-8;
}

[data-spacing-bottom="default"] {
  @apply pb-16;
}

[data-spacing-bottom="lg"] {
  @apply pb-24;
}

[data-spacing-bottom="xl"] {
  @apply pb-32;
}

/* Combined spacing */
[data-spacing="none"] {
  @apply py-0;
}

[data-spacing="sm"] {
  @apply py-8;
}

[data-spacing="default"] {
  @apply py-16;
}

[data-spacing="lg"] {
  @apply py-24;
}

[data-spacing="xl"] {
  @apply py-32;
}
```

## JavaScript Behaviors

### Main Entry (js/index.js)
```javascript
// Auto-initialization for interactive components
import { Carousel } from './behaviors/carousel.js';
import { Accordion } from './behaviors/accordion.js';
import { TableSort } from './behaviors/table-sort.js';

// Export for manual initialization
export { Carousel, Accordion, TableSort };

// Auto-init on DOM ready
if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    // Initialize carousels
    document.querySelectorAll('[data-carousel]').forEach(el => {
      new Carousel(el);
    });
    
    // Initialize accordions
    document.querySelectorAll('[data-accordion]').forEach(el => {
      new Accordion(el);
    });
    
    // Initialize sortable tables
    document.querySelectorAll('[data-sortable]').forEach(el => {
      new TableSort(el);
    });
  });
}
```

### Carousel Behavior (js/behaviors/carousel.js)
```javascript
export class Carousel {
  constructor(element) {
    this.element = element;
    this.slides = element.querySelectorAll('[data-slide]');
    this.currentIndex = 0;
    this.autoplay = element.dataset.autoplay === 'true';
    this.duration = parseInt(element.dataset.duration) || 5000;
    
    this.init();
  }
  
  init() {
    this.setupControls();
    this.setupIndicators();
    
    if (this.autoplay) {
      this.startAutoplay();
    }
    
    // Touch support
    this.setupTouch();
  }
  
  setupControls() {
    const prevBtn = this.element.querySelector('[data-carousel-prev]');
    const nextBtn = this.element.querySelector('[data-carousel-next]');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', () => this.prev());
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', () => this.next());
    }
  }
  
  setupIndicators() {
    const indicators = this.element.querySelector('[data-carousel-indicators]');
    if (!indicators) return;
    
    this.slides.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.setAttribute('data-slide-to', index);
      dot.addEventListener('click', () => this.goTo(index));
      indicators.appendChild(dot);
    });
    
    this.updateIndicators();
  }
  
  setupTouch() {
    let startX = 0;
    let endX = 0;
    
    this.element.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
    });
    
    this.element.addEventListener('touchend', (e) => {
      endX = e.changedTouches[0].clientX;
      const diff = startX - endX;
      
      if (Math.abs(diff) > 50) {
        if (diff > 0) {
          this.next();
        } else {
          this.prev();
        }
      }
    });
  }
  
  goTo(index) {
    this.currentIndex = index;
    this.updateSlides();
    this.updateIndicators();
    
    if (this.autoplay) {
      this.resetAutoplay();
    }
  }
  
  next() {
    this.goTo((this.currentIndex + 1) % this.slides.length);
  }
  
  prev() {
    this.goTo((this.currentIndex - 1 + this.slides.length) % this.slides.length);
  }
  
  updateSlides() {
    this.slides.forEach((slide, index) => {
      slide.style.display = index === this.currentIndex ? 'block' : 'none';
    });
  }
  
  updateIndicators() {
    const dots = this.element.querySelectorAll('[data-slide-to]');
    dots.forEach((dot, index) => {
      dot.classList.toggle('active', index === this.currentIndex);
    });
  }
  
  startAutoplay() {
    this.autoplayInterval = setInterval(() => this.next(), this.duration);
  }
  
  resetAutoplay() {
    clearInterval(this.autoplayInterval);
    this.startAutoplay();
  }
}
```

## Build Process

### Development Build
```bash
npm run build
# Outputs: dist/starboard.css, dist/starboard.js
```

### Production Build
```bash
npm run build:css:min
# Outputs: dist/starboard.min.css
```

### Watch Mode
```bash
npm run watch
# Watches src/ and rebuilds on change
```

## Publishing to NPM

```bash
# Update version
npm version patch|minor|major

# Build production files
npm run build
npm run build:css:min

# Publish to NPM
npm publish --access public

# Tag release
git tag v1.0.0
git push origin v1.0.0
```

## Version Strategy

### Version 1.x (Tailwind v3)
- 1.0.0 - Initial release
- 1.1.0 - Add new components
- 1.2.0 - Add component variants
- 1.x.x - Bug fixes and improvements

### Version 2.x (Tailwind v4)
- 2.0.0 - Upgrade to Tailwind v4
- Breaking changes in class names
- New Tailwind v4 features

## Testing

### Visual Regression Testing
```javascript
// Use Playwright for visual testing
import { test, expect } from '@playwright/test';

test('columns component renders correctly', async ({ page }) => {
  await page.goto('/test/columns.html');
  await expect(page).toHaveScreenshot('columns.png');
});
```

### Unit Testing
```javascript
// Test utility functions
import { describe, it, expect } from 'vitest';
import { Carousel } from '../js/behaviors/carousel';

describe('Carousel', () => {
  it('initializes with correct index', () => {
    const carousel = new Carousel(mockElement);
    expect(carousel.currentIndex).toBe(0);
  });
});
```