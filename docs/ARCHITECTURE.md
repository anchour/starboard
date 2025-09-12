# Starboard Architecture Overview

## Project Vision

Create a WordPress block plugin with **completely independent components** that are immune to WordPress/Gutenberg breaking changes. Styling is delivered separately via NPM package, allowing themes full control over when and how they update.

## The Problem We're Solving

WordPress and Gutenberg frequently introduce breaking changes that disrupt custom blocks. By separating structure (plugin) from presentation (NPM package), we create a stable, maintainable system where:

- Plugin updates don't force style changes
- Themes control their own styling destiny
- Components can be reused outside WordPress
- Breaking changes are isolated and manageable

## Three-Layer Architecture

```
┌─────────────────────────────────────────────┐
│         WordPress Plugin Layer               │
│                                              │
│  • Block registration                        │
│  • Semantic HTML output                      │
│  • Data attributes for styling hooks         │
│  • Block editor interface                    │
│  • ZERO CSS                                  │
└──────────────────┬──────────────────────────┘
                   │
                   │ HTML + Data Attributes
                   │
                   ▼
┌─────────────────────────────────────────────┐
│         NPM Package Layer                    │
│         @starboard/styles                    │
│                                              │
│  • Tailwind v3 component styles              │
│  • CSS using @apply and theme()              │
│  • Minimal JavaScript behaviors              │
│  • Build tools and configuration             │
└──────────────────┬──────────────────────────┘
                   │
                   │ Import via package.json
                   │
                   ▼
┌─────────────────────────────────────────────┐
│         Theme Implementation Layer           │
│                                              │
│  • Imports @starboard/styles                 │
│  • Configures Tailwind theme                 │
│  • Customizes via CSS overrides              │
│  • Controls update timing                    │
└─────────────────────────────────────────────┘
```

## Key Principles

### 1. **Complete Separation of Concerns**
- Plugin handles WordPress integration only
- Styles live in versioned NPM package
- Themes own their styling implementation

### 2. **Semantic HTML First**
- Plugin outputs clean, semantic HTML
- No inline styles or style decisions
- Data attributes provide styling hooks

### 3. **Progressive Enhancement**
- Components work without JavaScript
- CSS handles 90% of functionality
- JavaScript only for essential interactivity

### 4. **Tailwind v3 Foundation**
- Consistent design system via Tailwind
- `theme()` function for configuration
- Utility-first with component classes

### 5. **Version Independence**
- Plugin version != Style version
- Themes lock to specific style versions
- Gradual migration paths available

## Benefits

### For Developers
- **Predictable Updates**: Know exactly what changes between versions
- **Local Control**: Test updates in development before production
- **Modern Workflow**: NPM-based development familiar to JS developers
- **Reusability**: Styles work outside WordPress

### For Sites
- **Stability**: WordPress updates don't break styling
- **Performance**: Optimized CSS, tree-shaking, PurgeCSS
- **Consistency**: Shared design system across projects
- **Flexibility**: Multiple style packages possible

### For Maintenance
- **Clear Boundaries**: Know where issues originate
- **Independent Testing**: Test structure and styles separately
- **Version Control**: Git-friendly, reviewable changes
- **Documentation**: Self-documenting component structure

## Component Library

### Foundation Components
1. **Columns** - CSS Grid layout system
2. **Buttons** - Action elements
3. **Spacing** - Consistent spacing system

### Content Components
4. **Hero** - Page headers and CTAs
5. **Cards** - Content containers
6. **Text-Media** - Content with images
7. **Photo Feature** - Featured images

### Interactive Components
8. **Carousels** - Image/content sliders
9. **Tables** - Data display
10. **Testimonials** - Social proof
11. **Card Carousels** - Combining cards + carousel

### Specialized Components
12. **Rates** - Financial data display
13. **Icons** - SVG icon system

## Technology Stack

### WordPress Plugin
- PHP 7.4+
- WordPress 5.9+
- Block API v3
- No external dependencies

### NPM Package
- Tailwind CSS v3
- PostCSS 8
- Autoprefixer
- Modern build tools

### Theme Requirements
- Node.js 16+
- NPM or Yarn
- Tailwind-compatible build process

## Versioning Strategy

### Semantic Versioning
```
MAJOR.MINOR.PATCH

1.0.0 - Initial release with Tailwind v3
1.1.0 - New component added
1.2.0 - New features added
2.0.0 - Tailwind v4 upgrade (breaking)
```

### Compatibility Matrix
| Plugin Version | NPM Package Version | Tailwind Version |
|---------------|--------------------|--------------------|
| 1.x           | 1.x                | v3                 |
| 1.x           | 2.x                | v4 (future)        |

## Project Structure

```
starboard/
├── packages/
│   ├── wordpress-plugin/     # WordPress plugin
│   │   ├── blocks/           # Block definitions
│   │   ├── includes/         # PHP classes
│   │   └── starboard.php     # Main plugin file
│   │
│   ├── styles/               # NPM package source
│   │   ├── src/              # Component styles
│   │   ├── dist/             # Built CSS
│   │   └── package.json      # NPM configuration
│   │
│   └── examples/             # Usage examples
│       ├── theme-basic/      # Basic integration
│       └── theme-advanced/   # Advanced customization
│
└── docs/                     # Documentation
    ├── ARCHITECTURE.md       # This file
    ├── PLUGIN-ARCHITECTURE.md
    ├── NPM-PACKAGE.md
    └── ...
```

## Success Metrics

- **Zero styling in WordPress plugin**
- **100% of styles in NPM package**
- **Components work without JavaScript**
- **Themes can override any style**
- **Clean semantic HTML output**
- **No WordPress dependencies in styles**

## Next Steps

1. Review [PLUGIN-ARCHITECTURE.md](./PLUGIN-ARCHITECTURE.md) for WordPress implementation
2. Review [NPM-PACKAGE.md](./NPM-PACKAGE.md) for style package details
3. Review [COMPONENTS.md](./COMPONENTS.md) for component specifications
4. Review [IMPLEMENTATION-GUIDE.md](./IMPLEMENTATION-GUIDE.md) for build timeline