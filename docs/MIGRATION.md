# Migration Strategy

## Overview

This document outlines the migration strategy from the current Starboard implementation to the new WordPress-independent architecture with NPM-based styling.

## Current State Analysis

### Existing Architecture Issues
- Styles embedded in WordPress plugin
- WordPress/Gutenberg dependency for presentation
- Breaking changes cascade through styling
- No version control for styles
- Difficult to customize without core modifications

### Migration Benefits
- Style independence from WordPress updates
- Versioned styling with semantic versioning
- Theme-controlled update timeline
- Reusable components outside WordPress
- Modern development workflow

---

## Migration Timeline

### Phase 1: Preparation (Week 1)

#### Day 1-2: Audit Current Components
```bash
# Document current components and their usage
find . -name "*.php" -exec grep -l "sb-" {} \;
find . -name "*.css" -exec grep -l "sb-" {} \;

# Create inventory
cat > migration-inventory.md << EOF
# Current Components Inventory
- FAQ Block (starboard/faq)
- FAQ Item Block (starboard/faq-item)
- Stats Block (packages/temp/stats)
- Stats Item Block (packages/temp/stats-item)

# Styling Locations
- Inline styles in save.js files
- CSS files in component directories
- Theme overrides (if any)
EOF
```

#### Day 3-4: Create Migration Plan
1. Identify high-priority components for first migration
2. Map current HTML structures to new semantic structure
3. Plan backward compatibility approach
4. Set up parallel development environment

#### Day 5-7: Set Up New Architecture
```bash
# Create new structure alongside existing
mkdir -p packages/{new-plugin,styles-v2}

# Initialize NPM package
cd packages/styles-v2
npm init -y
npm install -D tailwindcss@^3 postcss autoprefixer
```

### Phase 2: Core Component Migration (Week 2-3)

#### Priority Order
1. **Columns** (foundation for other components)
2. **Buttons** (used across many components)
3. **Cards** (high usage, visual impact)
4. **FAQ components** (existing and working)
5. **Hero** (new component)
6. **Tables, Carousels, etc.** (additional components)

#### Migration Process per Component

##### Step 1: Create New Component Structure
```bash
# Example: Migrating FAQ component
mkdir -p packages/new-plugin/blocks/faq
mkdir -p packages/styles-v2/src/components
```

##### Step 2: Extract Semantic HTML
```javascript
// Before (current save.js with styles)
const Save = (props) => {
  return (
    <div className="faqs-wrapper" style={{paddingTop: '2rem'}}>
      <InnerBlocks.Content />
    </div>
  );
};

// After (new save.js, semantic only)
const Save = (props) => {
  const { spacingTop, spacingBottom } = props.attributes;
  
  return (
    <div className="sb-faq" 
         data-spacing-top={spacingTop}
         data-spacing-bottom={spacingBottom}>
      <InnerBlocks.Content />
    </div>
  );
};
```

##### Step 3: Create Tailwind Styles
```css
/* packages/styles-v2/src/components/faq.css */
.sb-faq {
  @apply space-y-4;
}

[data-spacing-top="sm"] { @apply pt-8; }
[data-spacing-top="default"] { @apply pt-16; }
[data-spacing-top="lg"] { @apply pt-24; }

[data-spacing-bottom="sm"] { @apply pb-8; }
[data-spacing-bottom="default"] { @apply pb-16; }
[data-spacing-bottom="lg"] { @apply pb-24; }
```

##### Step 4: Update Block Controls
```javascript
// Add spacing controls to existing blocks
const Edit = (props) => {
  const { attributes, setAttributes } = props;
  
  return (
    <>
      <InspectorControls>
        <PanelBody title="Spacing">
          <SelectControl
            label="Top Spacing"
            value={attributes.spacingTop}
            options={[
              { label: 'None', value: 'none' },
              { label: 'Small', value: 'sm' },
              { label: 'Default', value: 'default' },
              { label: 'Large', value: 'lg' }
            ]}
            onChange={(value) => setAttributes({ spacingTop: value })}
          />
        </PanelBody>
      </InspectorControls>
      {/* Existing edit interface */}
    </>
  );
};
```

### Phase 3: Parallel Deployment (Week 4)

#### Dual Block System
```php
// Support both old and new blocks during transition
class Migration_Block_Loader {
    public function register_blocks() {
        // Register new blocks with different names
        register_block_type(STARBOARD_PLUGIN_DIR . 'blocks/faq-v2');
        register_block_type(STARBOARD_PLUGIN_DIR . 'blocks/columns-v2');
        
        // Keep old blocks for backward compatibility
        register_block_type(STARBOARD_PLUGIN_DIR . 'blocks-legacy/faq');
        register_block_type(STARBOARD_PLUGIN_DIR . 'blocks-legacy/columns');
    }
}
```

#### Style Loading Strategy
```php
// Detect which system to use
function starboard_load_styles() {
    if (current_theme_supports('starboard-v2-styles')) {
        // Theme handles new styles via NPM
        return;
    }
    
    if (starboard_has_v2_blocks()) {
        // Load fallback styles for new blocks
        wp_enqueue_style('starboard-v2-fallback', plugin_dir_url(__FILE__) . 'fallback.css');
    }
    
    if (starboard_has_legacy_blocks()) {
        // Load old styles for legacy blocks
        wp_enqueue_style('starboard-legacy', plugin_dir_url(__FILE__) . 'legacy.css');
    }
}
```

### Phase 4: Theme Migration (Week 5-6)

#### Migration Helper Script
```bash
#!/bin/bash
# migrate-theme.sh

echo "Starting Starboard theme migration..."

# Check if theme is ready for migration
if [ ! -f "package.json" ]; then
    echo "Initializing package.json..."
    npm init -y
fi

# Install new styles package
echo "Installing @starboard/styles..."
npm install @starboard/styles
npm install -D tailwindcss postcss autoprefixer

# Create Tailwind config
if [ ! -f "tailwind.config.js" ]; then
    npx tailwindcss init -p
fi

# Create basic CSS structure
mkdir -p src/styles
cat > src/styles/main.css << EOF
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import '@starboard/styles';
@import 'tailwindcss/utilities';
EOF

# Update functions.php
echo "Add this to your functions.php:"
echo "add_theme_support('starboard-v2-styles');"
```

#### Theme Migration Checklist
- [ ] Install Node.js and NPM
- [ ] Add `@starboard/styles` dependency
- [ ] Configure Tailwind CSS
- [ ] Update build process
- [ ] Add theme support declaration
- [ ] Test new components
- [ ] Replace old block usage
- [ ] Remove old style overrides

### Phase 5: Content Migration (Week 7-8)

#### Block Migration Script
```php
<?php
// Migration utility to update existing content

class Block_Content_Migrator {
    private $migrations = [
        'starboard/faq' => 'starboard/faq-v2',
        'starboard/faq-item' => 'starboard/faq-item-v2',
    ];
    
    public function migrate_post_content($post_id) {
        $content = get_post_field('post_content', $post_id);
        $updated_content = $content;
        
        foreach ($this->migrations as $old_block => $new_block) {
            $updated_content = preg_replace(
                '/<!-- wp:' . preg_quote($old_block, '/') . '/',
                '<!-- wp:' . $new_block,
                $updated_content
            );
        }
        
        if ($content !== $updated_content) {
            wp_update_post([
                'ID' => $post_id,
                'post_content' => $updated_content
            ]);
            
            return true; // Content was updated
        }
        
        return false; // No changes needed
    }
    
    public function migrate_all_content() {
        $posts = get_posts([
            'post_type' => 'any',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_starboard_migration_status',
                    'compare' => 'NOT EXISTS'
                ]
            ]
        ]);
        
        foreach ($posts as $post) {
            if ($this->migrate_post_content($post->ID)) {
                update_post_meta($post->ID, '_starboard_migration_status', 'migrated');
                update_post_meta($post->ID, '_starboard_migration_date', current_time('mysql'));
            }
        }
    }
}

// CLI command for migration
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('starboard migrate-content', function() {
        $migrator = new Block_Content_Migrator();
        $migrator->migrate_all_content();
        WP_CLI::success('Content migration completed.');
    });
}
```

#### Content Backup Strategy
```bash
# Before migration, backup database
wp db export backup-pre-starboard-migration.sql

# After migration, verify content
wp post list --post_type=page --meta_key=_starboard_migration_status --meta_value=migrated
```

---

## Backward Compatibility Strategy

### Version Detection
```php
class Starboard_Version_Manager {
    public function get_block_version($post_content) {
        if (strpos($post_content, 'starboard/faq-v2') !== false) {
            return '2.0';
        } elseif (strpos($post_content, 'starboard/faq') !== false) {
            return '1.0';
        }
        return null;
    }
    
    public function load_appropriate_styles($version) {
        switch ($version) {
            case '2.0':
                // New system - theme handles styles
                if (!current_theme_supports('starboard-v2-styles')) {
                    wp_enqueue_style('starboard-v2-fallback');
                }
                break;
                
            case '1.0':
                // Legacy system
                wp_enqueue_style('starboard-legacy');
                break;
        }
    }
}
```

### Gradual Migration Support
```php
// Allow both systems to coexist
add_action('wp_enqueue_scripts', function() {
    global $post;
    
    if (!$post) return;
    
    $version_manager = new Starboard_Version_Manager();
    $version = $version_manager->get_block_version($post->post_content);
    $version_manager->load_appropriate_styles($version);
});
```

### Migration Safety Checks
```php
// Prevent breaking changes during migration
function starboard_migration_safety_check() {
    if (!current_theme_supports('starboard-v2-styles')) {
        // Check if v2 blocks are used without theme support
        $v2_usage = new WP_Query([
            'post_type' => 'any',
            's' => 'starboard/faq-v2',
            'posts_per_page' => 1
        ]);
        
        if ($v2_usage->have_posts()) {
            add_action('admin_notices', function() {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <strong>Starboard Migration Warning:</strong> 
                        New blocks detected but theme doesn't support new styles. 
                        <a href="/wp-admin/themes.php">Update your theme</a> or 
                        <a href="/wp-admin/admin.php?page=starboard-migration">complete migration</a>.
                    </p>
                </div>
                <?php
            });
        }
    }
}
add_action('admin_init', 'starboard_migration_safety_check');
```

---

## Rollback Strategy

### Emergency Rollback
```php
// Emergency rollback to old system
function starboard_emergency_rollback() {
    // Disable new blocks
    remove_action('init', ['Starboard\\Block_Loader_V2', 'register_blocks']);
    
    // Force legacy styles
    add_action('wp_enqueue_scripts', function() {
        wp_dequeue_style('starboard-v2-styles');
        wp_enqueue_style('starboard-legacy-forced', 
            plugin_dir_url(__FILE__) . 'legacy/styles.css');
    }, 100);
}

// Activate rollback via constant
if (defined('STARBOARD_EMERGENCY_ROLLBACK') && STARBOARD_EMERGENCY_ROLLBACK) {
    starboard_emergency_rollback();
}
```

### Block Content Rollback
```bash
# Restore from backup if needed
wp db import backup-pre-starboard-migration.sql

# Or run reverse migration
wp starboard rollback-content
```

---

## Testing Strategy

### Pre-Migration Testing
```bash
# Test current system
npm run test:legacy

# Test theme compatibility
npm run test:theme-compat

# Performance baseline
npm run test:performance -- --baseline
```

### Migration Testing
```bash
# Test new components
npm run test:components

# Test migration script
npm run test:migration

# Integration testing
npm run test:integration
```

### Post-Migration Testing
```bash
# Verify all blocks render correctly
npm run test:visual-regression

# Performance comparison
npm run test:performance -- --compare

# Accessibility testing
npm run test:a11y
```

### Testing Checklist

#### Functional Testing
- [ ] All existing blocks render correctly
- [ ] New blocks work as expected
- [ ] Block editor functions properly
- [ ] Styles load correctly
- [ ] JavaScript behaviors work
- [ ] Mobile responsiveness maintained

#### Performance Testing
- [ ] Page load times comparable or better
- [ ] CSS file sizes optimized
- [ ] JavaScript execution time acceptable
- [ ] Core Web Vitals maintained

#### Compatibility Testing
- [ ] Works with major themes
- [ ] Compatible with popular plugins
- [ ] Functions across supported browsers
- [ ] Accessible to screen readers

---

## Communication Plan

### Stakeholder Communication

#### For Developers
```markdown
# Developer Migration Notice

## What's Changing
- Styles moving from plugin to NPM package
- New build process required for themes
- Improved customization capabilities

## Timeline
- **Week 1-2**: New architecture available for testing
- **Week 3-4**: Migration tools ready
- **Week 5-6**: Begin theme updates
- **Week 7-8**: Content migration
- **Week 9+**: Legacy support (6 months)

## Action Required
1. Update theme build process
2. Install @starboard/styles package
3. Test components in development
4. Schedule content migration

## Support
- Migration documentation: [link]
- Support forum: [link]
- Video tutorials: [link]
```

#### For Site Owners
```markdown
# Site Owner Migration Guide

## What This Means for You
- More stable components (fewer breaking changes)
- Better performance
- More customization options
- Modern, maintainable codebase

## What You Need to Do
1. Work with your developer to update theme
2. Test your site during migration period
3. Schedule content migration at convenient time
4. Review site after migration complete

## Support Available
- Priority support during migration
- Rollback option if issues arise
- Documentation and tutorials
```

### Migration Timeline Communication
```
Phase 1 (Week 1): ✅ Preparation Complete
├─ Current system audited
├─ New architecture ready
└─ Migration plan finalized

Phase 2 (Week 2-3): 🔄 Core Migration In Progress
├─ Components being migrated
├─ Testing in parallel environment
└─ Documentation being updated

Phase 3 (Week 4): ⏳ Parallel Deployment Ready
├─ Both systems available
├─ Migration tools ready
└─ Theme updates can begin

Phase 4 (Week 5-6): 🚀 Theme Migration Period
├─ Themes updating to new system
├─ Support available for migration
└─ Testing and validation

Phase 5 (Week 7-8): 📝 Content Migration
├─ Existing content updated
├─ Final testing and validation
└─ Legacy system cleanup

Phase 6 (Week 9+): ✨ New System Active
├─ All components on new architecture
├─ Legacy support (limited time)
└─ Future development on new system
```

This migration strategy ensures a smooth transition from the current tightly-coupled system to the new WordPress-independent architecture while minimizing disruption to existing sites.