# Spacing System

## Overview

The Starboard spacing system provides consistent, predictable spacing across all components using Tailwind CSS spacing scale with custom extensions for sectional layouts. All spacing is controlled via data attributes and CSS custom properties for easy theme customization.

## Spacing Philosophy

### Design Principles
1. **Consistent Scale**: Based on Tailwind's 4px base unit
2. **Semantic Naming**: Sizes reflect usage, not pixel values
3. **Responsive Ready**: Automatic scaling across breakpoints
4. **Theme Controllable**: Easily overridden by themes
5. **Composable**: Vertical and horizontal spacing work together

### Base Scale
```css
/* Tailwind spacing scale (rem values) */
0    = 0px
1    = 0.25rem = 4px
2    = 0.5rem  = 8px
3    = 0.75rem = 12px
4    = 1rem    = 16px
6    = 1.5rem  = 24px
8    = 2rem    = 32px
12   = 3rem    = 48px
16   = 4rem    = 64px
24   = 6rem    = 96px
32   = 8rem    = 128px
```

---

## Component Spacing Types

### 1. Section Spacing (Top-level blocks)

All top-level Starboard components support section spacing:

```html
<section class="sb-hero" 
         data-spacing-top="lg"
         data-spacing-bottom="lg">
  <!-- Component content -->
</section>
```

#### Section Spacing Scale
| Value | Top/Bottom | Use Case |
|-------|------------|----------|
| `none` | 0 | No spacing, flush layouts |
| `xs` | 1rem (16px) | Minimal separation |
| `sm` | 2rem (32px) | Tight layouts |
| `default` | 4rem (64px) | Standard section spacing |
| `lg` | 6rem (96px) | Generous spacing |
| `xl` | 8rem (128px) | Hero sections, major breaks |
| `2xl` | 12rem (192px) | Maximum spacing |

#### CSS Implementation
```css
/* Base spacing variables */
:root {
  --sb-spacing-none: 0;
  --sb-spacing-xs: theme('spacing.4');   /* 1rem */
  --sb-spacing-sm: theme('spacing.8');   /* 2rem */
  --sb-spacing-default: theme('spacing.16'); /* 4rem */
  --sb-spacing-lg: theme('spacing.24');  /* 6rem */
  --sb-spacing-xl: theme('spacing.32');  /* 8rem */
  --sb-spacing-2xl: theme('spacing.48'); /* 12rem */
}

/* Data attribute mapping */
[data-spacing-top="none"] { padding-top: var(--sb-spacing-none); }
[data-spacing-top="xs"] { padding-top: var(--sb-spacing-xs); }
[data-spacing-top="sm"] { padding-top: var(--sb-spacing-sm); }
[data-spacing-top="default"] { padding-top: var(--sb-spacing-default); }
[data-spacing-top="lg"] { padding-top: var(--sb-spacing-lg); }
[data-spacing-top="xl"] { padding-top: var(--sb-spacing-xl); }
[data-spacing-top="2xl"] { padding-top: var(--sb-spacing-2xl); }

[data-spacing-bottom="none"] { padding-bottom: var(--sb-spacing-none); }
[data-spacing-bottom="xs"] { padding-bottom: var(--sb-spacing-xs); }
[data-spacing-bottom="sm"] { padding-bottom: var(--sb-spacing-sm); }
[data-spacing-bottom="default"] { padding-bottom: var(--sb-spacing-default); }
[data-spacing-bottom="lg"] { padding-bottom: var(--sb-spacing-lg); }
[data-spacing-bottom="xl"] { padding-bottom: var(--sb-spacing-xl); }
[data-spacing-bottom="2xl"] { padding-bottom: var(--sb-spacing-2xl); }

/* Combined spacing */
[data-spacing="none"] { 
  padding-top: var(--sb-spacing-none); 
  padding-bottom: var(--sb-spacing-none); 
}
[data-spacing="xs"] { 
  padding-top: var(--sb-spacing-xs); 
  padding-bottom: var(--sb-spacing-xs); 
}
[data-spacing="sm"] { 
  padding-top: var(--sb-spacing-sm); 
  padding-bottom: var(--sb-spacing-sm); 
}
[data-spacing="default"] { 
  padding-top: var(--sb-spacing-default); 
  padding-bottom: var(--sb-spacing-default); 
}
[data-spacing="lg"] { 
  padding-top: var(--sb-spacing-lg); 
  padding-bottom: var(--sb-spacing-lg); 
}
[data-spacing="xl"] { 
  padding-top: var(--sb-spacing-xl); 
  padding-bottom: var(--sb-spacing-xl); 
}
[data-spacing="2xl"] { 
  padding-top: var(--sb-spacing-2xl); 
  padding-bottom: var(--sb-spacing-2xl); 
}
```

### 2. Gap Spacing (Between elements)

For layouts like columns, cards, and grids:

```html
<div class="sb-columns" data-gap="lg">
  <!-- Column content -->
</div>
```

#### Gap Spacing Scale
| Value | Gap Size | Use Case |
|-------|----------|----------|
| `none` | 0 | No gap, flush layouts |
| `xs` | 0.5rem (8px) | Minimal separation |
| `sm` | 1rem (16px) | Tight grids |
| `default` | 1.5rem (24px) | Standard grid spacing |
| `lg` | 2rem (32px) | Generous grid spacing |
| `xl` | 3rem (48px) | Wide separations |

#### CSS Implementation
```css
/* Gap variables */
:root {
  --sb-gap-none: 0;
  --sb-gap-xs: theme('spacing.2');    /* 0.5rem */
  --sb-gap-sm: theme('spacing.4');    /* 1rem */
  --sb-gap-default: theme('spacing.6'); /* 1.5rem */
  --sb-gap-lg: theme('spacing.8');    /* 2rem */
  --sb-gap-xl: theme('spacing.12');   /* 3rem */
}

/* Gap application */
.sb-columns { gap: var(--sb-current-gap, var(--sb-gap-default)); }
.sb-card-grid { gap: var(--sb-current-gap, var(--sb-gap-default)); }

/* Data attribute mapping */
[data-gap="none"] { --sb-current-gap: var(--sb-gap-none); }
[data-gap="xs"] { --sb-current-gap: var(--sb-gap-xs); }
[data-gap="sm"] { --sb-current-gap: var(--sb-gap-sm); }
[data-gap="default"] { --sb-current-gap: var(--sb-gap-default); }
[data-gap="lg"] { --sb-current-gap: var(--sb-gap-lg); }
[data-gap="xl"] { --sb-current-gap: var(--sb-gap-xl); }
```

### 3. Internal Spacing (Component padding)

For spacing inside components like cards, buttons:

```html
<article class="sb-card" data-padding="lg">
  <!-- Card content -->
</article>
```

#### Internal Spacing Scale
| Value | Padding | Use Case |
|-------|---------|----------|
| `none` | 0 | No internal padding |
| `xs` | 0.75rem (12px) | Compact components |
| `sm` | 1rem (16px) | Small components |
| `default` | 1.5rem (24px) | Standard padding |
| `lg` | 2rem (32px) | Generous padding |
| `xl` | 3rem (48px) | Large components |

---

## Responsive Spacing

### Breakpoint-Aware Spacing

Spacing automatically scales across breakpoints using Tailwind's responsive prefixes:

```css
/* Responsive section spacing */
[data-spacing="lg"] {
  /* Mobile: smaller spacing */
  padding-top: theme('spacing.12');    /* 3rem */
  padding-bottom: theme('spacing.12'); 
}

@screen sm {
  [data-spacing="lg"] {
    /* Tablet: medium spacing */
    padding-top: theme('spacing.20');    /* 5rem */
    padding-bottom: theme('spacing.20'); 
  }
}

@screen lg {
  [data-spacing="lg"] {
    /* Desktop: full spacing */
    padding-top: theme('spacing.24');    /* 6rem */
    padding-bottom: theme('spacing.24'); 
  }
}
```

### Responsive Data Attributes

Components can have different spacing at different breakpoints:

```html
<!-- Future enhancement -->
<section class="sb-hero"
         data-spacing-mobile="sm"
         data-spacing-tablet="default" 
         data-spacing-desktop="lg">
  <!-- Responsive spacing -->
</section>
```

---

## WordPress Block Controls

### Block Editor Interface

```javascript
// Spacing controls in block editor
import { SelectControl, PanelBody } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

const SpacingControls = ({ attributes, setAttributes }) => {
  const spacingOptions = [
    { label: 'None', value: 'none' },
    { label: 'Extra Small', value: 'xs' },
    { label: 'Small', value: 'sm' },
    { label: 'Default', value: 'default' },
    { label: 'Large', value: 'lg' },
    { label: 'Extra Large', value: 'xl' },
    { label: 'Double Extra Large', value: '2xl' },
  ];

  return (
    <InspectorControls>
      <PanelBody title="Spacing" initialOpen={true}>
        <SelectControl
          label="Top Spacing"
          value={attributes.spacingTop}
          options={spacingOptions}
          onChange={(value) => setAttributes({ spacingTop: value })}
          help="Space above this component"
        />
        
        <SelectControl
          label="Bottom Spacing"
          value={attributes.spacingBottom}
          options={spacingOptions}
          onChange={(value) => setAttributes({ spacingBottom: value })}
          help="Space below this component"
        />
      </PanelBody>
    </InspectorControls>
  );
};
```

### Block Attributes

```json
{
  "attributes": {
    "spacingTop": {
      "type": "string",
      "default": "default"
    },
    "spacingBottom": {
      "type": "string", 
      "default": "default"
    },
    "gap": {
      "type": "string",
      "default": "default"
    },
    "padding": {
      "type": "string",
      "default": "default"
    }
  }
}
```

---

## Theme Customization

### CSS Custom Properties

Themes can override spacing by redefining CSS custom properties:

```css
/* Theme customization */
:root {
  /* Override section spacing */
  --sb-spacing-sm: 1.5rem;      /* Instead of 2rem */
  --sb-spacing-default: 3rem;   /* Instead of 4rem */
  --sb-spacing-lg: 5rem;        /* Instead of 6rem */
  
  /* Override gap spacing */
  --sb-gap-default: 2rem;       /* Instead of 1.5rem */
  --sb-gap-lg: 3rem;           /* Instead of 2rem */
}

/* Brand-specific spacing */
.brand-theme {
  --sb-spacing-default: 4.5rem; /* Custom brand spacing */
}
```

### Tailwind Theme Extensions

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      spacing: {
        // Custom spacing values that components can use
        'section-sm': '2.5rem',
        'section': '5rem',
        'section-lg': '7.5rem',
        'section-xl': '10rem',
      }
    }
  }
}
```

### Component-Specific Overrides

```css
/* Customize spacing for specific components */
.sb-hero {
  --sb-spacing-default: theme('spacing.20'); /* 5rem instead of 4rem */
}

.sb-card {
  --sb-padding-default: theme('spacing.8'); /* 2rem instead of 1.5rem */
}

.sb-columns {
  --sb-gap-default: theme('spacing.8'); /* 2rem instead of 1.5rem */
}
```

---

## Spacing Utilities

### Margin Utilities

Additional margin utilities for fine-tuning:

```css
/* Margin utilities following same scale */
.sb-mt-none { margin-top: 0; }
.sb-mt-xs { margin-top: var(--sb-spacing-xs); }
.sb-mt-sm { margin-top: var(--sb-spacing-sm); }
.sb-mt-default { margin-top: var(--sb-spacing-default); }
.sb-mt-lg { margin-top: var(--sb-spacing-lg); }
.sb-mt-xl { margin-top: var(--sb-spacing-xl); }
.sb-mt-2xl { margin-top: var(--sb-spacing-2xl); }

/* Apply to all directions */
.sb-mb-*, .sb-ml-*, .sb-mr-* { /* Same pattern */ }
```

### Negative Spacing

For overlapping layouts:

```css
/* Negative margin utilities */
.sb--mt-sm { margin-top: calc(var(--sb-spacing-sm) * -1); }
.sb--mt-default { margin-top: calc(var(--sb-spacing-default) * -1); }
.sb--mt-lg { margin-top: calc(var(--sb-spacing-lg) * -1); }
```

Usage:
```html
<section class="sb-hero">...</section>
<section class="sb-content sb--mt-lg">
  <!-- Overlaps hero by large amount -->
</section>
```

---

## Spacing Patterns

### Common Layout Patterns

#### 1. Standard Page Layout
```html
<main>
  <section class="sb-hero" data-spacing-top="none" data-spacing-bottom="xl">
    <!-- Page hero -->
  </section>
  
  <section class="sb-text-media" data-spacing="lg">
    <!-- Main content -->
  </section>
  
  <section class="sb-columns" data-spacing="lg" data-gap="lg">
    <!-- Feature grid -->
  </section>
  
  <section class="sb-hero" data-spacing="xl" data-spacing-bottom="none">
    <!-- CTA section -->
  </section>
</main>
```

#### 2. Landing Page Layout
```html
<main>
  <!-- Large hero, no top spacing -->
  <section class="sb-hero" data-spacing-top="none" data-spacing-bottom="2xl">
  
  <!-- Features with generous spacing -->  
  <section class="sb-columns" data-spacing="xl" data-gap="lg">
  
  <!-- Testimonials with large spacing -->
  <section class="sb-testimonials" data-spacing="xl">
  
  <!-- CTA with maximum spacing -->
  <section class="sb-hero" data-spacing="2xl">
</main>
```

#### 3. Compact Layout
```html
<main>
  <!-- Tighter spacing throughout -->
  <section class="sb-hero" data-spacing="sm">
  <section class="sb-cards" data-spacing="sm" data-gap="sm">
  <section class="sb-text-media" data-spacing="sm">
</main>
```

### Best Practices

#### 1. Consistent Rhythm
```html
<!-- Good: Consistent spacing creates rhythm -->
<section data-spacing="lg">...</section>
<section data-spacing="lg">...</section>
<section data-spacing="lg">...</section>

<!-- Avoid: Random spacing breaks rhythm -->
<section data-spacing="xs">...</section>
<section data-spacing="2xl">...</section>
<section data-spacing="sm">...</section>
```

#### 2. Hierarchical Spacing
```html
<!-- Good: Larger spacing for major sections -->
<section class="sb-hero" data-spacing="2xl">     <!-- Major -->
<section class="sb-features" data-spacing="xl">  <!-- Important -->
<section class="sb-details" data-spacing="lg">   <!-- Standard -->
<section class="sb-footer" data-spacing="sm">    <!-- Minor -->
```

#### 3. Component Gap Relationships
```html
<!-- Good: Gap smaller than section spacing -->
<section data-spacing="lg" data-gap="default">
<!-- lg spacing (6rem) > default gap (1.5rem) -->

<!-- Avoid: Gap larger than section spacing -->
<section data-spacing="sm" data-gap="xl">
<!-- sm spacing (2rem) < xl gap (3rem) - creates imbalance -->
```

---

## Accessibility Considerations

### Focus Spacing
Ensure adequate spacing around interactive elements:

```css
.sb-button {
  /* Minimum touch target size */
  min-height: 44px;
  min-width: 44px;
  
  /* Adequate spacing for focus rings */
  margin: theme('spacing.1');
}
```

### Reading Spacing
Maintain readable spacing for text content:

```css
.sb-text-content {
  /* Comfortable line height */
  line-height: 1.6;
  
  /* Paragraph spacing */
  p + p {
    margin-top: theme('spacing.4'); /* 1rem */
  }
}
```

---

## Performance Considerations

### CSS Custom Properties vs Classes
```css
/* More performant: CSS custom properties */
[data-spacing="lg"] {
  padding-top: var(--sb-spacing-lg);
  padding-bottom: var(--sb-spacing-lg);
}

/* Less performant: Individual classes */
.sb-spacing-top-lg { padding-top: 6rem; }
.sb-spacing-bottom-lg { padding-bottom: 6rem; }
/* Would need many more classes for combinations */
```

### Purge-Safe Spacing
All spacing utilities are purge-safe since they use data attributes:

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.{js,css}',
    // Starboard data attributes are always included
  ]
}
```

This comprehensive spacing system provides consistent, customizable, and maintainable spacing across all Starboard components while remaining flexible enough for any design system.