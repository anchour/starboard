# Starboard Plugin Customization

This document covers how to customize and extend Starboard blocks in your theme.

## Table of Contents

- [PHP Filters](#php-filters)
- [Theme Overrides](#theme-overrides)
- [Available Blocks](#available-blocks)
- [Shared Components](#shared-components)

---

## PHP Filters

Starboard provides several PHP filters to customize block behavior without modifying plugin code.

### `starboard_screens`

Customize the responsive breakpoints used by Starboard blocks.

```php
add_filter('starboard_screens', function ($screens) {
    return [
        'md' => '768px',   // Tablet
        'lg' => '1024px',  // Laptop
        'xl' => '1280px',  // Desktop
    ];
});
```

**Default value:**
```php
[
    'md' => '768px',
    'lg' => '1024px',
    'xl' => '1280px',
]
```

**Use case:** Match your theme's Tailwind or CSS breakpoints if they differ from defaults.

---

### `starboard_default_spacing`

Customize the default padding values shown in spacing controls.

```php
add_filter('starboard_default_spacing', function ($spacing) {
    return [
        'Default' => ['paddingTop' => 16, 'paddingBottom' => 16],
        'md'      => ['paddingTop' => 18, 'paddingBottom' => 18],
        'lg'      => ['paddingTop' => 20, 'paddingBottom' => 20],
        'xl'      => ['paddingTop' => 24, 'paddingBottom' => 24],
    ];
});
```

**Note:** Values are multiplied by `0.25rem`. So `16` = `4rem` (64px at default font size).

**Default value:**
```php
[
    'Default' => ['paddingTop' => 16, 'paddingBottom' => 16],  // 4rem
    'md'      => ['paddingTop' => 18, 'paddingBottom' => 18],  // 4.5rem
    'lg'      => ['paddingTop' => 20, 'paddingBottom' => 20],  // 5rem
    'xl'      => ['paddingTop' => 24, 'paddingBottom' => 24],  // 6rem
]
```

---

## Block Supports (Settings UI)

Starboard allows you to customize WordPress block supports for each block directly from the settings page, without modifying any block files.

### Accessing Block Supports

1. Go to **WP Admin → Starboard**
2. Click the **Block Supports** tab
3. Expand any block to see available support options
4. Change settings and click **Save Supports**

**Note:** Blocks with theme overrides are not shown in this list. If you have a theme override for a block, edit the `block.json` file in your theme's `starboard/blocks/` directory instead.

### Available Support Categories

#### Layout & Structure
- **Alignment** - Block alignment options (left, center, right, wide, full)
- **HTML Anchor** - Adds an anchor/ID field to block settings
- **Additional CSS Class** - Allows adding custom CSS classes
- **Custom Class Name Input** - Shows custom class name input field

#### Color
- **Background Color** - Background color picker
- **Text Color** - Text color picker
- **Gradients** - Gradient background support
- **Link Color** - Link color picker
- **Heading Color** - Heading color picker
- **Button Color** - Button color picker

#### Typography
- **Font Size** - Font size control
- **Line Height** - Line height control
- **Font Family** - Font family selector
- **Font Weight** - Font weight control
- **Font Style** - Font style (normal, italic)
- **Letter Spacing** - Letter spacing control
- **Text Transform** - Text transform (uppercase, lowercase, capitalize)
- **Text Decoration** - Text decoration (underline, strikethrough)
- **Text Align** - Text alignment control

#### Spacing
- **Margin** - Margin controls
- **Padding** - Padding controls
- **Block Gap** - Gap between inner blocks

#### Border
- **Border Color** - Border color picker
- **Border Radius** - Border radius control
- **Border Style** - Border style (solid, dashed, dotted)
- **Border Width** - Border width control

#### Dimensions
- **Aspect Ratio** - Aspect ratio control
- **Minimum Height** - Minimum height control

#### Background
- **Background Image** - Background image support
- **Background Size** - Background size control

#### Position
- **Sticky Position** - Allow sticky positioning

#### Effects
- **Box Shadow** - Box shadow controls
- **Duotone Filter** - Duotone color filter

#### Advanced
- **Edit as HTML** - Allow editing block as HTML
- **Locking** - Allow locking the block
- **Renaming** - Allow renaming the block
- **Allow Multiple** - Allow multiple instances of this block
- **Reusable** - Allow saving as reusable block
- **Show in Inserter** - Show block in the inserter

### How It Works

- Select **Default** to use the block's built-in setting from `block.json`
- Select **On** to enable a feature
- Select **Off** to disable a feature
- Click **Reset to Defaults** to clear all customizations for a block
- Blocks with customizations show a **Customized** badge

### Theme.json Compatibility

If your theme uses `appearanceTools: true` in `theme.json`, this globally enables many features (background colors, borders, spacing, etc.) for all blocks. This would normally override individual block supports.

Starboard handles this by injecting per-block settings into theme.json via the `wp_theme_json_data_theme` filter. When you set a support to "Off" in Starboard, it adds the block to theme.json's `settings.blocks` section:

```json
{
  "settings": {
    "blocks": {
      "starboard/text-image": {
        "color": {
          "background": false
        }
      }
    }
  }
}
```

This properly overrides the global `appearanceTools` setting for that specific block.

### Programmatic Override

You can also filter block supports via PHP:

```php
add_filter('register_block_type_args', function ($args, $block_type) {
    if ($block_type === 'starboard/text-image') {
        $args['supports']['typography']['fontSize'] = true;
        $args['supports']['spacing']['padding'] = true;
    }
    return $args;
}, 10, 2);
```

---

## Theme Overrides

Starboard supports full block overrides at the theme level. When a theme provides its own version of a block, it takes precedence over the plugin version.

### Directory Structure

Place your block override in your theme at:

```
your-theme/
└── starboard/
    └── blocks/
        └── {block-name}/
            ├── block.json
            ├── index.js
            ├── edit.js
            ├── save.js
            └── ... (any additional files)
```

### Example: Overriding Text/Image Block

1. Create the directory structure in your theme:
   ```
   your-theme/starboard/blocks/text-image/
   ```

2. Copy the block files from the plugin:
   ```bash
   cp -r wp-content/plugins/starboard/dist/blocks/text-image/* \
         wp-content/themes/your-theme/starboard/blocks/text-image/
   ```

3. Modify the files as needed. The theme version will be used instead of the plugin version.

### Override Detection

The Starboard settings page shows a "Has Theme Override" badge next to blocks that have theme-level overrides.

### Lookup Order

Starboard checks for blocks in this order:

1. `{theme}/starboard/blocks/{block-name}/block.json` → Theme version (priority)
2. `{plugin}/dist/blocks/{block-name}/block.json` → Plugin compiled version
3. `{plugin}/src/blocks/{block-name}/block.json` → Plugin source (development)

---

## Available Blocks

### Text/Image Split (`starboard/text-image`)

A two-column layout block for pairing text content with an image.

**Features:**
- Three layout variations: Narrow Content (4/5), Wide Image (4/7), Balanced (6/5)
- Content side toggle (left or right)
- Mobile order control (image top or bottom)
- Responsive spacing controls per breakpoint
- Background color support

**Attributes:**

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `copySide` | string | `split-copy-left` | Which side the text appears on (`split-copy-left` or `split-copy-right`) |
| `mobileReverse` | string | `image-top-mobile` | Mobile stacking order (`image-top-mobile` or `image-btm-mobile`) |
| `verticalAlignment` | string | `center` | Vertical alignment (`top`, `center`, `bottom`) |
| `selectedVariationName` | string | `""` | The selected layout variation class name |
| `spacing` | object | See below | Responsive padding settings |

**Spacing object structure:**
```json
{
  "Default": { "active": false, "settings": { "paddingTop": 16, "paddingBottom": 16 } },
  "md": { "active": false, "settings": { "paddingTop": 18, "paddingBottom": 18 } },
  "lg": { "active": false, "settings": { "paddingTop": 20, "paddingBottom": 20 } },
  "xl": { "active": false, "settings": { "paddingTop": 24, "paddingBottom": 24 } }
}
```

---

### FAQ (`starboard/faq`)

An accordion-style FAQ block with Schema.org markup.

**Features:**
- Collapsible question/answer pairs
- Automatic FAQPage schema generation
- Responsive spacing controls

---

### FAQ Item (`starboard/faq-item`)

Individual FAQ item for use within the FAQ block.

**Features:**
- Question and answer fields
- Expandable/collapsible behavior

---

## Shared Components

Starboard provides shared components and utilities that can be used across blocks.

### Breakpoint Component

Location: `src/components/breakpoint.js`

A UI component for displaying breakpoint-specific controls with toggle functionality.

```js
import Breakpoint, { createBreakpointFromScreen } from '../../components/breakpoint.js';

const breakpoint = createBreakpointFromScreen('md', '768px', true, {}, { paddingTop: 16 });

<Breakpoint
  breakpoint={breakpoint}
  onToggleChange={() => { /* toggle handler */ }}
>
  {/* Child controls */}
</Breakpoint>
```

### Screen Utilities

Location: `src/utils/screens.js`

Utilities for working with responsive breakpoints.

```js
import {
  screens,              // { md: '768px', lg: '1024px', xl: '1280px' }
  screenKeys,           // ['Default', 'md', 'lg', 'xl']
  breakpointLabels,     // { Default: 'Mobile', md: '768px', ... }
  defaultSpacingValues, // Default padding values per breakpoint
  getSpacingDescription // Helper: (16) => '4rem (64px)'
} from '../../utils/screens.js';
```

---

## CSS Custom Properties

Starboard blocks output CSS custom properties for styling flexibility.

### Spacing Variables

```css
/* Applied to blocks with spacing enabled */
--padding-top: 4rem;
--padding-bottom: 4rem;
--md-padding-top: 4.5rem;
--md-padding-bottom: 4.5rem;
--lg-padding-top: 5rem;
--lg-padding-bottom: 5rem;
--xl-padding-top: 6rem;
--xl-padding-bottom: 6rem;
```

### Grid Variables (Text/Image Block)

```css
/* Copy column positioning */
--lg-copy-start: 1;
--lg-copy-span: 6;
--xl-copy-start: 1;
--xl-copy-span: 6;

/* Image column positioning */
--lg-img-start: 7;
--lg-img-span: 6;
--xl-img-start: 7;
--xl-img-span: 6;
```

---

## Development

### Building the Plugin

```bash
cd wp-content/plugins/starboard
npm install
npm run build
```

### File Structure

```
starboard/
├── app/                    # PHP classes
│   ├── Setup.php           # Main setup, filters, asset loading
│   ├── GetBlocks.php       # Block discovery
│   ├── BlockLoader.php     # Theme override logic
│   ├── BlockSupports.php   # Block supports configuration
│   └── Hooks.php           # Admin form handlers
├── src/
│   ├── components/         # Shared React components
│   │   └── breakpoint.js   # Breakpoint toggle component
│   ├── utils/              # Shared utilities
│   │   └── screens.js      # Screen/breakpoint utilities
│   └── blocks/             # Block source files
│       ├── faq/
│       ├── faq-item/
│       └── text-image/
│           ├── block.json
│           ├── index.js
│           ├── edit.js
│           ├── save.js
│           ├── utils.js
│           ├── variations.js
│           ├── breakpoints.js
│           ├── frontend.css
│           ├── editor.css
│           └── column/     # Nested sub-block
├── dist/                   # Compiled output
└── src/views/              # Admin templates
```
