# Implementation Guide

## Overview

This guide provides a step-by-step implementation plan for building the Starboard WordPress-independent component system with Tailwind v3 styling via NPM package.

## Phase 1: Foundation Setup (Week 1)

### Day 1-2: Project Structure & Build System

#### 1. Repository Setup
```bash
# Create monorepo structure
mkdir -p packages/{wordpress-plugin,styles,examples}
mkdir -p docs

# Initialize workspaces
echo 'packages:
  - "packages/*"' > pnpm-workspace.yaml

# Root package.json
echo '{
  "name": "starboard",
  "private": true,
  "workspaces": ["packages/*"]
}' > package.json
```

#### 2. NPM Package Structure
```bash
cd packages/styles

# Package setup
npm init -y
npm install -D tailwindcss@^3.4.0 postcss autoprefixer esbuild

# Create directories
mkdir -p {src/{base,components,utilities},dist,js/behaviors}

# Tailwind config
npx tailwindcss init -p
```

#### 3. Basic Build System
```json
// packages/styles/package.json
{
  "name": "@starboard/styles",
  "version": "1.0.0-alpha.1",
  "scripts": {
    "build": "npm run build:css && npm run build:js",
    "build:css": "tailwindcss -i ./src/index.css -o ./dist/starboard.css",
    "build:css:min": "npm run build:css -- --minify -o ./dist/starboard.min.css",
    "build:js": "esbuild js/index.js --bundle --outfile=dist/starboard.js --format=esm",
    "watch": "tailwindcss -i ./src/index.css -o ./dist/starboard.css --watch",
    "dev": "npm run watch"
  }
}
```

### Day 3-4: WordPress Plugin Foundation

#### 1. Plugin Structure
```bash
cd packages/wordpress-plugin

# Create plugin structure
mkdir -p {blocks/{columns,button},includes,languages}

# Main plugin file
touch starboard.php
```

#### 2. Plugin Core Files
```php
<?php
// packages/wordpress-plugin/starboard.php
/**
 * Plugin Name: Starboard Blocks
 * Description: WordPress-independent block components
 * Version: 1.0.0-alpha.1
 */

namespace Starboard;

if (!defined('ABSPATH')) exit;

define('STARBOARD_VERSION', '1.0.0-alpha.1');
define('STARBOARD_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once STARBOARD_PLUGIN_DIR . 'includes/class-plugin-core.php';

add_action('plugins_loaded', function() {
    $plugin = new Plugin_Core();
    $plugin->init();
});
```

#### 3. Block Loader
```php
<?php
// packages/wordpress-plugin/includes/class-block-loader.php
namespace Starboard;

class Block_Loader {
    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
    }

    public function register_blocks() {
        $blocks = ['columns', 'button'];
        
        foreach ($blocks as $block) {
            register_block_type(
                STARBOARD_PLUGIN_DIR . 'blocks/' . $block
            );
        }
    }
}
```

### Day 5-7: First Components (Columns & Buttons)

#### 1. Columns Block
```json
// packages/wordpress-plugin/blocks/columns/block.json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "starboard/columns",
  "title": "Columns",
  "category": "starboard-layout",
  "attributes": {
    "columns": {"type": "number", "default": 2},
    "columnsTablet": {"type": "number", "default": 2},
    "columnsMobile": {"type": "number", "default": 1},
    "gap": {"type": "string", "default": "default"}
  }
}
```

#### 2. Columns Styles
```css
/* packages/styles/src/components/columns.css */
.sb-columns {
  @apply grid;
  gap: var(--sb-gap, theme('spacing.6'));
  grid-template-columns: repeat(var(--sb-cols-mobile, 1), 1fr);
  
  @screen sm {
    grid-template-columns: repeat(var(--sb-cols-tablet, 2), 1fr);
  }
  
  @screen lg {
    grid-template-columns: repeat(var(--sb-cols-desktop, 4), 1fr);
  }
}

[data-columns="1"] { --sb-cols-desktop: 1; }
[data-columns="2"] { --sb-cols-desktop: 2; }
[data-columns="3"] { --sb-cols-desktop: 3; }
[data-columns="4"] { --sb-cols-desktop: 4; }
```

---

## Phase 2: Core Components (Week 2)

### Day 8-10: Cards & Hero Components

#### 1. Card Component
```html
<!-- Target HTML structure -->
<article class="sb-card" data-layout="vertical" data-spacing="default">
  <div class="sb-card__media">
    <img src="..." alt="...">
  </div>
  <div class="sb-card__content">
    <h3 class="sb-card__title">Title</h3>
    <p class="sb-card__text">Description</p>
  </div>
</article>
```

```css
/* packages/styles/src/components/cards.css */
.sb-card {
  @apply bg-white rounded-lg shadow-sm overflow-hidden;
}

.sb-card__media img {
  @apply w-full h-auto object-cover;
}

.sb-card__content {
  @apply p-6;
}

[data-layout="horizontal"] {
  @apply flex;
  
  .sb-card__media {
    @apply flex-shrink-0 w-48;
  }
}
```

#### 2. WordPress Block Registration
```javascript
// Block edit component pattern
const Edit = ({ attributes, setAttributes }) => {
  return (
    <>
      <InspectorControls>
        {/* Block controls */}
      </InspectorControls>
      <div className="sb-card-editor">
        {/* Editor preview */}
      </div>
    </>
  );
};
```

### Day 11-14: Text-Media & Photo Feature

#### 1. Implementation Pattern
1. Create WordPress block (PHP/JS)
2. Define HTML structure
3. Add Tailwind styles
4. Test in example theme
5. Document usage

#### 2. Testing Framework
```bash
# Create example theme
mkdir -p packages/examples/theme-basic
cd packages/examples/theme-basic

# Install styles package
npm init -y
npm install ../../styles

# Basic theme files
touch {index.php,functions.php,style.css}
```

---

## Phase 3: Interactive Components (Week 3)

### Day 15-17: Carousel Component

#### 1. HTML Structure Design
```html
<div class="sb-carousel" data-carousel>
  <div class="sb-carousel__track">
    <div class="sb-carousel__slide">...</div>
  </div>
  <button class="sb-carousel__prev">Prev</button>
  <button class="sb-carousel__next">Next</button>
</div>
```

#### 2. JavaScript Implementation
```javascript
// packages/styles/js/behaviors/carousel.js
export class Carousel {
  constructor(element) {
    this.element = element;
    this.init();
  }
  
  init() {
    // Carousel logic
  }
}
```

#### 3. CSS with Tailwind
```css
.sb-carousel {
  @apply relative overflow-hidden;
}

.sb-carousel__track {
  @apply flex transition-transform duration-300;
}

.sb-carousel__slide {
  @apply flex-shrink-0 w-full;
}
```

### Day 18-21: Tables & Testimonials

#### 1. Progressive Enhancement Pattern
```css
/* Base styles work without JavaScript */
.sb-table {
  @apply w-full border-collapse;
}

/* Enhanced with JavaScript */
.sb-table[data-sortable] th {
  @apply cursor-pointer hover:bg-gray-50;
}
```

#### 2. Responsive Design
```css
/* Mobile-first responsive tables */
@media (max-width: 768px) {
  .sb-table[data-responsive="stack"] {
    @apply block;
    
    thead { @apply hidden; }
    
    td {
      @apply block text-right;
      
      &::before {
        content: attr(data-label) ": ";
        @apply float-left font-medium;
      }
    }
  }
}
```

---

## Phase 4: Specialized Components (Week 4)

### Day 22-24: Rates & Icons Components

#### 1. Rates Component Structure
```html
<div class="sb-rates" data-layout="cards">
  <div class="sb-rates__item" data-trend="up">
    <h4 class="sb-rates__title">30-Year Fixed</h4>
    <div class="sb-rates__value">
      <span class="sb-rates__number">6.95</span>
      <span class="sb-rates__unit">%</span>
    </div>
    <div class="sb-rates__change">+0.15</div>
  </div>
</div>
```

#### 2. SVG Icon System
```bash
# Icon processing workflow
mkdir -p packages/styles/icons/src
# Add SVG files to src/

# Build sprite
npm run build:icons
# Generates dist/icons.svg sprite
```

### Day 25-28: Polish & Testing

#### 1. Accessibility Audit
- ARIA labels
- Keyboard navigation
- Focus indicators
- Screen reader testing

#### 2. Performance Testing
- Bundle size analysis
- CSS purging
- JavaScript tree-shaking

---

## Phase 5: WordPress Integration (Week 5)

### Day 29-31: Block Controls & Data Flow

#### 1. Advanced Block Controls
```javascript
// Complex control patterns
import { ColorPalette, RangeControl } from '@wordpress/components';

const Edit = ({ attributes, setAttributes }) => {
  return (
    <InspectorControls>
      <PanelBody title="Spacing">
        <RangeControl
          label="Top Spacing"
          value={attributes.spacingTop}
          onChange={(value) => setAttributes({ spacingTop: value })}
          min={0}
          max={8}
        />
      </PanelBody>
      
      <PanelBody title="Colors">
        <ColorPalette
          colors={theme.colors}
          value={attributes.backgroundColor}
          onChange={(color) => setAttributes({ backgroundColor: color })}
        />
      </PanelBody>
    </InspectorControls>
  );
};
```

#### 2. Data Attributes Helper
```php
// Streamline data attribute generation
class Data_Attributes {
  public static function render($attributes, $mapping) {
    $output = '';
    foreach ($mapping as $attr => $data_key) {
      if (isset($attributes[$attr])) {
        $output .= sprintf(' data-%s="%s"', 
          $data_key, 
          esc_attr($attributes[$attr])
        );
      }
    }
    return $output;
  }
}
```

### Day 32-35: Documentation & Examples

#### 1. Component Documentation
- Usage examples for each component
- Block editor screenshots
- Frontend preview images
- Code snippets

#### 2. Example Implementations
```bash
# Create multiple example themes
mkdir -p packages/examples/{theme-minimal,theme-advanced}

# Theme with basic integration
cd packages/examples/theme-minimal
npm install @starboard/styles

# Theme with advanced customization
cd packages/examples/theme-advanced
npm install @starboard/styles
```

---

## Development Workflow

### Daily Development Process

#### 1. Component Development
```bash
# Start development
cd packages/styles
npm run watch

# In another terminal
cd packages/wordpress-plugin
npm run dev

# Test in example theme
cd packages/examples/theme-basic
npm run dev
```

#### 2. Testing Workflow
```bash
# Lint styles
npm run lint:css

# Test JavaScript
npm test

# Build production
npm run build
```

### Git Workflow

#### 1. Branch Strategy
```bash
# Feature branches
git checkout -b feature/hero-component
git checkout -b feature/carousel-component

# Development branch
git checkout -b develop

# Main branch for releases
git checkout main
```

#### 2. Commit Conventions
```
feat: add hero component with overlay options
fix: carousel navigation on mobile
docs: update component usage examples
style: format CSS according to guidelines
```

### Quality Assurance

#### 1. Code Review Checklist
- [ ] Semantic HTML structure
- [ ] Accessible markup
- [ ] Responsive design
- [ ] Data attributes consistent
- [ ] CSS follows Tailwind patterns
- [ ] JavaScript is progressive enhancement
- [ ] Documentation updated

#### 2. Testing Matrix
| Component | HTML | CSS | JS | A11y | Mobile |
|-----------|------|-----|----|----- |---------|
| Columns   | ✓    | ✓   | -  | ✓    | ✓       |
| Button    | ✓    | ✓   | -  | ✓    | ✓       |
| Carousel  | ✓    | ✓   | ✓  | ✓    | ✓       |

### Release Process

#### 1. Version Planning
```bash
# Alpha releases during development
1.0.0-alpha.1  # Phase 1 complete
1.0.0-alpha.2  # Phase 2 complete
1.0.0-beta.1   # Phase 4 complete
1.0.0          # Production ready
```

#### 2. Release Checklist
- [ ] All tests passing
- [ ] Documentation complete
- [ ] Example themes working
- [ ] Performance benchmarks met
- [ ] Accessibility tested
- [ ] Browser testing complete

#### 3. Publishing
```bash
# Build production files
npm run build

# Test package locally
npm pack
npm install starboard-styles-1.0.0.tgz

# Publish to NPM
npm publish --access public

# Tag release
git tag v1.0.0
git push origin v1.0.0
```

## Success Metrics

### Technical Metrics
- Bundle size: < 50kb CSS, < 10kb JS
- Build time: < 30 seconds
- Test coverage: > 90%
- Accessibility: WCAG 2.1 AA compliant

### Usage Metrics
- Plugin produces 0 CSS
- NPM package handles 100% of styling
- Components work without JavaScript
- Themes can override any style
- Clean semantic HTML output

### Performance Metrics
- Lighthouse scores > 90
- Core Web Vitals green
- Mobile performance optimized
- CSS can be tree-shaken

## Risk Mitigation

### Potential Issues & Solutions

#### 1. Tailwind Version Conflicts
- **Problem**: Theme uses different Tailwind version
- **Solution**: Scoped builds, peer dependencies

#### 2. WordPress Updates Breaking Blocks
- **Problem**: Block API changes
- **Solution**: Semantic versioning, deprecation warnings

#### 3. CSS Specificity Issues
- **Problem**: Theme styles conflicting
- **Solution**: Consistent naming, CSS custom properties

#### 4. JavaScript Compatibility
- **Problem**: Browser support issues
- **Solution**: Progressive enhancement, polyfills

## Support & Maintenance

### Documentation Strategy
- Comprehensive README files
- Live examples on documentation site
- Video tutorials for complex components
- Migration guides for updates

### Community Support
- GitHub issues for bug reports
- Discussion forum for questions
- Contributing guidelines
- Code of conduct

### Long-term Maintenance
- Regular dependency updates
- Security patches
- Performance improvements
- New component additions

This implementation guide provides a clear roadmap from project setup to production release, ensuring a systematic approach to building the WordPress-independent component system.