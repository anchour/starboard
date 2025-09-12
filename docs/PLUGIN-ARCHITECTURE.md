# WordPress Plugin Architecture

## Overview

The Starboard WordPress plugin is responsible **ONLY** for:
- Registering blocks with WordPress
- Providing block editor interfaces
- Outputting semantic HTML with data attributes
- **NO CSS or styling decisions**

## Plugin Structure

```
packages/wordpress-plugin/
├── blocks/
│   ├── columns/
│   │   ├── block.json
│   │   ├── index.js
│   │   ├── edit.js
│   │   └── save.js
│   ├── button/
│   ├── hero/
│   ├── cards/
│   └── [other-blocks]/
├── includes/
│   ├── class-block-loader.php
│   ├── class-data-attributes.php
│   └── class-plugin-core.php
├── languages/
├── starboard.php              # Main plugin file
└── package.json              # Build dependencies only
```

## Block Registration Pattern

### block.json
```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "starboard/columns",
  "title": "Columns",
  "category": "starboard-layout",
  "icon": "columns",
  "description": "Responsive column layout",
  "supports": {
    "html": false,
    "anchor": true,
    "customClassName": true
  },
  "attributes": {
    "columns": {
      "type": "number",
      "default": 2
    },
    "columnsMobile": {
      "type": "number",
      "default": 1
    },
    "columnsTablet": {
      "type": "number",
      "default": 2
    },
    "columnsDesktop": {
      "type": "number",
      "default": 4
    },
    "gap": {
      "type": "string",
      "default": "default"
    },
    "spacingTop": {
      "type": "string",
      "default": "default"
    },
    "spacingBottom": {
      "type": "string",
      "default": "default"
    }
  }
}
```

### Edit Component (edit.js)
```javascript
import { InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';

const Edit = ({ attributes, setAttributes }) => {
  const {
    columns,
    columnsMobile,
    columnsTablet,
    columnsDesktop,
    gap,
    spacingTop,
    spacingBottom
  } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title="Layout Settings">
          <RangeControl
            label="Desktop Columns"
            value={columnsDesktop}
            onChange={(value) => setAttributes({ columnsDesktop: value })}
            min={1}
            max={6}
          />
          <RangeControl
            label="Tablet Columns"
            value={columnsTablet}
            onChange={(value) => setAttributes({ columnsTablet: value })}
            min={1}
            max={4}
          />
          <RangeControl
            label="Mobile Columns"
            value={columnsMobile}
            onChange={(value) => setAttributes({ columnsMobile: value })}
            min={1}
            max={2}
          />
        </PanelBody>
        
        <PanelBody title="Spacing">
          <SelectControl
            label="Gap"
            value={gap}
            options={[
              { label: 'None', value: 'none' },
              { label: 'Small', value: 'sm' },
              { label: 'Default', value: 'default' },
              { label: 'Large', value: 'lg' },
              { label: 'Extra Large', value: 'xl' }
            ]}
            onChange={(value) => setAttributes({ gap: value })}
          />
          <SelectControl
            label="Top Spacing"
            value={spacingTop}
            options={[
              { label: 'None', value: 'none' },
              { label: 'Small', value: 'sm' },
              { label: 'Default', value: 'default' },
              { label: 'Large', value: 'lg' },
              { label: 'Extra Large', value: 'xl' }
            ]}
            onChange={(value) => setAttributes({ spacingTop: value })}
          />
        </PanelBody>
      </InspectorControls>
      
      <div className="sb-columns-editor">
        <InnerBlocks
          allowedBlocks={true}
          template={[
            ['core/column'],
            ['core/column']
          ]}
        />
      </div>
    </>
  );
};

export default Edit;
```

### Save Component (save.js)
```javascript
import { InnerBlocks } from '@wordpress/block-editor';

const Save = ({ attributes }) => {
  const {
    columns,
    columnsMobile,
    columnsTablet,
    columnsDesktop,
    gap,
    spacingTop,
    spacingBottom
  } = attributes;

  // Build data attributes for styling hooks
  const dataAttributes = {
    'data-columns': columnsDesktop,
    'data-columns-tablet': columnsTablet,
    'data-columns-mobile': columnsMobile,
    'data-gap': gap,
    'data-spacing-top': spacingTop,
    'data-spacing-bottom': spacingBottom
  };

  return (
    <div className="sb-columns" {...dataAttributes}>
      <InnerBlocks.Content />
    </div>
  );
};

export default Save;
```

## HTML Output Examples

### Columns Block
```html
<div class="sb-columns" 
     data-columns="4" 
     data-columns-tablet="2" 
     data-columns-mobile="1"
     data-gap="default"
     data-spacing-top="lg"
     data-spacing-bottom="lg">
  <div class="wp-block-column">Content...</div>
  <div class="wp-block-column">Content...</div>
  <div class="wp-block-column">Content...</div>
  <div class="wp-block-column">Content...</div>
</div>
```

### Button Block
```html
<a class="sb-button" 
   href="/learn-more"
   data-variant="primary"
   data-size="lg"
   data-icon="arrow-right"
   data-icon-position="right">
  Learn More
</a>
```

### Hero Block
```html
<section class="sb-hero"
         data-height="tall"
         data-alignment="center"
         data-overlay="dark"
         data-spacing-top="none"
         data-spacing-bottom="xl">
  <div class="sb-hero__background">
    <img src="background.jpg" alt="" />
  </div>
  <div class="sb-hero__content">
    <h1 class="sb-hero__title">Welcome to Our Site</h1>
    <p class="sb-hero__subtitle">Building better experiences</p>
    <div class="sb-hero__actions">
      <a class="sb-button" data-variant="primary" href="#">Get Started</a>
    </div>
  </div>
</section>
```

### Card Block
```html
<article class="sb-card"
         data-layout="vertical"
         data-spacing="default">
  <div class="sb-card__media">
    <img src="image.jpg" alt="Card image" />
  </div>
  <div class="sb-card__content">
    <h3 class="sb-card__title">Card Title</h3>
    <p class="sb-card__text">Card description text goes here.</p>
    <a class="sb-button" data-variant="link" href="#">Read More</a>
  </div>
</article>
```

## Data Attributes System

### Naming Convention
```
data-[property]="value"
data-[property]-[breakpoint]="value"
```

### Common Data Attributes

#### Layout
- `data-columns` - Number of columns
- `data-layout` - Layout variant (vertical, horizontal, etc.)
- `data-alignment` - Content alignment
- `data-direction` - Flex/grid direction

#### Spacing
- `data-spacing-top` - Top spacing
- `data-spacing-bottom` - Bottom spacing
- `data-spacing` - All-around spacing
- `data-gap` - Gap between elements

#### Responsive
- `data-[property]-mobile` - Mobile value
- `data-[property]-tablet` - Tablet value
- `data-[property]-desktop` - Desktop value

#### Variants
- `data-variant` - Component variant
- `data-size` - Size variant
- `data-theme` - Theme variant

## PHP Architecture

### Main Plugin File
```php
<?php
/**
 * Plugin Name: Starboard Blocks
 * Description: WordPress-independent block components
 * Version: 1.0.0
 */

namespace Starboard;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('STARBOARD_VERSION', '1.0.0');
define('STARBOARD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STARBOARD_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load core classes
require_once STARBOARD_PLUGIN_DIR . 'includes/class-plugin-core.php';

// Initialize plugin
add_action('plugins_loaded', function() {
    $plugin = new Plugin_Core();
    $plugin->init();
});
```

### Block Loader Class
```php
<?php
namespace Starboard;

class Block_Loader {
    private $blocks = [
        'columns',
        'button',
        'hero',
        'card',
        'carousel',
        'table',
        'testimonial',
        'rates',
        'icon',
        'text-media',
        'photo-feature'
    ];

    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
    }

    public function register_blocks() {
        foreach ($this->blocks as $block) {
            register_block_type(
                STARBOARD_PLUGIN_DIR . 'blocks/' . $block
            );
        }
    }
}
```

### Data Attributes Helper
```php
<?php
namespace Starboard;

class Data_Attributes {
    /**
     * Convert block attributes to data attributes
     */
    public static function build($attributes, $mapping = []) {
        $data_attrs = [];
        
        foreach ($mapping as $attr_key => $data_key) {
            if (isset($attributes[$attr_key])) {
                $data_attrs['data-' . $data_key] = esc_attr($attributes[$attr_key]);
            }
        }
        
        return $data_attrs;
    }
    
    /**
     * Output data attributes as string
     */
    public static function render($attributes, $mapping = []) {
        $data_attrs = self::build($attributes, $mapping);
        $output = '';
        
        foreach ($data_attrs as $key => $value) {
            $output .= sprintf(' %s="%s"', $key, $value);
        }
        
        return $output;
    }
}
```

## Block Categories

```php
add_filter('block_categories_all', function($categories) {
    return array_merge(
        [
            [
                'slug' => 'starboard-layout',
                'title' => 'Starboard Layout',
                'icon' => 'layout'
            ],
            [
                'slug' => 'starboard-content',
                'title' => 'Starboard Content',
                'icon' => 'media-document'
            ],
            [
                'slug' => 'starboard-interactive',
                'title' => 'Starboard Interactive',
                'icon' => 'slides'
            ]
        ],
        $categories
    );
});
```

## Plugin Settings

### No Style Loading
```php
// The plugin does NOT enqueue any styles
// This is intentional - styles come from NPM package

add_action('init', function() {
    // Check if theme has declared style support
    if (current_theme_supports('starboard-styles')) {
        // Theme is handling styles, do nothing
        return;
    }
    
    // Optional: Admin notice if no styles detected
    add_action('admin_notices', function() {
        ?>
        <div class="notice notice-info">
            <p>Starboard Blocks: No styles detected. Install @starboard/styles in your theme.</p>
        </div>
        <?php
    });
});
```

## Block Editor Styles

For the block editor preview, minimal structural styles only:

```css
/* editor-styles.css - Structural only, no design */
.sb-columns-editor {
  display: grid;
  gap: 20px;
}

.sb-hero-editor {
  position: relative;
  min-height: 400px;
}

/* Visual indicators for editor only */
.sb-block-placeholder {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
}
```

## Testing Considerations

### Unit Tests
```php
class Test_Data_Attributes extends WP_UnitTestCase {
    public function test_build_data_attributes() {
        $attributes = [
            'columns' => 4,
            'gap' => 'large'
        ];
        
        $mapping = [
            'columns' => 'columns',
            'gap' => 'gap'
        ];
        
        $result = Data_Attributes::build($attributes, $mapping);
        
        $this->assertEquals('4', $result['data-columns']);
        $this->assertEquals('large', $result['data-gap']);
    }
}
```

### E2E Tests
```javascript
// Test that blocks output correct HTML structure
test('Columns block outputs correct data attributes', async ({ page }) => {
  await page.goto('/wp-admin/post-new.php');
  
  // Add columns block
  await page.click('[aria-label="Add block"]');
  await page.fill('[placeholder="Search"]', 'columns');
  await page.click('text=Columns');
  
  // Configure block
  await page.click('[aria-label="Settings"]');
  await page.fill('[aria-label="Desktop Columns"]', '4');
  
  // Save and check output
  await page.click('[aria-label="Publish"]');
  
  const content = await page.content();
  expect(content).toContain('data-columns="4"');
});
```

## Performance Considerations

1. **No CSS Loading**: Zero impact on page load
2. **Minimal JavaScript**: Only block editor code
3. **Semantic HTML**: Clean, efficient output
4. **No Dependencies**: No external libraries
5. **Lazy Loading**: Blocks load on demand in editor

## Security Considerations

1. **Escaping**: All attributes properly escaped
2. **Sanitization**: Input sanitized on save
3. **No Direct File Access**: All files check for ABSPATH
4. **Nonce Verification**: AJAX calls verified
5. **Capability Checks**: User permissions verified