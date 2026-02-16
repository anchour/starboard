# Starboard

A WordPress plugin providing custom Gutenberg blocks with theme override support, block supports customization, and an intuitive admin interface.

## Features

- **Custom Blocks**: Text/Image, FAQ, and FAQ Item blocks with responsive controls
- **Theme Override System**: Copy blocks to your theme for customization without modifying the plugin
- **Block Supports Management**: Enable/disable block features (colors, spacing, typography) per-block
- **Custom Variants**: Create and manage layout variants for the Text/Image block
- **Access Control**: Optional restriction to specific email domains

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Node.js 16+ (for development)

## Installation

1. Upload the `starboard` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Starboard** in the admin menu to configure blocks

## Available Blocks

### Text/Image Block (`starboard/text-image`)
Two-column layout with image and content areas. Features:
- Responsive grid positioning
- Content side toggle (left/right)
- Mobile reorder option
- Vertical alignment controls
- Custom spacing per breakpoint

### FAQ Block (`starboard/faq`)
Accordion-style FAQ container with automatic Schema.org FAQPage markup generation.

### FAQ Item Block (`starboard/faq-item`)
Individual FAQ items with question and answer fields. Must be used inside an FAQ block.

## Theme Overrides

Blocks can be copied to your theme for customization:

1. Go to **Starboard > Blocks** in the admin
2. Click "Copy to Theme" on any block
3. Edit the block files in `{theme}/starboard/blocks/{block-name}/`

The plugin checks for theme overrides in this order:
1. Child theme: `{theme}/starboard/blocks/{block-name}/`
2. Parent theme: `{parent-theme}/starboard/blocks/{block-name}/`
3. Plugin dist: `{plugin}/dist/blocks/{block-name}/`
4. Plugin src: `{plugin}/src/blocks/{block-name}/` (development)

## Development

### Setup

```bash
npm install
```

### Commands

```bash
npm run build      # Production build
npm run start      # Development server with hot reload
npm run lint:js    # Run ESLint
npm run lint:css   # Run Stylelint
npm run test:unit  # Run unit tests
```

### Directory Structure

```
starboard/
├── app/                 # PHP classes (PSR-4: Starboard\)
│   ├── AccessControl.php
│   ├── BlockLoader.php
│   ├── BlockSupports.php
│   ├── GetBlocks.php
│   ├── Hooks.php
│   ├── Setup.php
│   ├── TextImageVariants.php
│   └── ThemeOverride.php
├── src/
│   ├── blocks/          # Block source files
│   ├── components/      # Shared React components
│   ├── utils/           # Utility functions
│   ├── shared/          # Global exports (@starboard/*)
│   └── views/           # Admin templates
├── dist/                # Compiled output (gitignored)
├── build/               # Build scripts
├── tests/               # PHPUnit tests
└── webpack.config.js
```

### Using Shared Utilities

When creating theme overrides, import shared utilities from the plugin:

```js
import { Breakpoint } from '@starboard/components';
import { screens, screenKeys } from '@starboard/utils';
```

These are provided by the `starboard-shared` script which is automatically loaded.

## Filters

### `starboard_screens`
Customize breakpoint values.

```php
add_filter('starboard_screens', function($screens) {
    return [
        'md' => '640px',
        'lg' => '1024px',
        'xl' => '1440px',
    ];
});
```

### `starboard_default_spacing`
Customize default spacing values per breakpoint.

```php
add_filter('starboard_default_spacing', function($spacing) {
    return [
        'Default' => ['paddingTop' => 20, 'paddingBottom' => 20],
        'md' => ['paddingTop' => 24, 'paddingBottom' => 24],
        // ...
    ];
});
```

## Documentation

See [CUSTOMIZATION.md](CUSTOMIZATION.md) for detailed customization instructions and [CLAUDE.md](CLAUDE.md) for development guidelines.

## License

GPL-2.0-or-later
