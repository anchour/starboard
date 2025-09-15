# @anchour/starboard

Starboard wireframe design system - a modular CSS framework for rapid prototyping and design systems.

## Installation

```bash
npm install @anchour/starboard
```

## Usage

### Import Full Styles (Recommended)

```css
/* Pre-compiled, works with any build system */
@import '@anchour/starboard/styles';
```

### Import Individual Components

```css
/* Import just multicolumn component */
@import '@anchour/starboard/styles/components/multicolumn';

/* Import specific layers only */
@import '@anchour/starboard/styles/multicolumn/wireframe'; /* Structure only */
@import '@anchour/starboard/styles/multicolumn/basic';     /* + Spacing/typography */
@import '@anchour/starboard/styles/multicolumn/theme';     /* + Visual styling */
```

### Use with Your Own Tailwind Config

```css
/* Import source files to process with your Tailwind config */
@import '@anchour/starboard/styles/source';
```

## Components

### Multicolumn

A responsive grid component with three layers:

- **Wireframe**: Grid structure and responsive behavior
- **Basic**: Spacing, typography hierarchy, and layout patterns  
- **Theme**: Colors, borders, shadows, and visual styling

#### HTML Structure

```html
<section class="multicolumn">
  <div class="multicolumn__container">
    <header class="multicolumn__header heading-2xl">
      Heading goes here
    </header>
    <div class="multicolumn__wrapper">
      <div class="multicolumn-item multicolumn-item__has-border">
        <!-- Icon/Image -->
        <div class="multicolumn-item__text">
          <h3 class="multicolumn-item__text-header">Small heading</h3>
          <p class="multicolumn-item__text-body">Description text...</p>
        </div>
        <div class="multicolumn-item__buttons">
          <a href="#" class="btn btn--primary">Primary</a>
          <a href="#" class="btn btn--secondary">Secondary</a>
        </div>
      </div>
      <!-- More items... -->
    </div>
  </div>
</section>
```

## Customization

Override CSS custom properties to customize the design:

```css
:root {
  /* Spacing */
  --sb-spacing-lg: 2rem;
  --sb-multicolumn-padding-y: 4rem;
  
  /* Colors */
  --sb-text-primary: #1a1a1a;
  
  /* Layout */
  --sb-container-max-width: 1200px;
}
```

## Build System

This package ships pre-compiled CSS by default. Source files with `@apply` directives are also included if you want to process them through your own Tailwind configuration.

## License

MIT