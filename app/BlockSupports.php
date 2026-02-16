<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Block Supports - Manages WordPress block support configuration.
 *
 * Defines all available WordPress block supports and provides methods
 * for getting/setting per-block support overrides via plugin settings.
 */
class BlockSupports
{
    /**
     * Option name for storing block support settings.
     */
    const OPTION_NAME = 'starboard_block_supports';

    /**
     * All available WordPress block supports with their default values and descriptions.
     *
     * @return array
     */
    public static function getAvailableSupports(): array
    {
        return [
            // Alignment & Layout
            'align' => [
                'label' => 'Alignment',
                'description' => 'Block alignment options (left, center, right, wide, full)',
                'type' => 'select',
                'options' => [
                    'false' => 'Disabled',
                    'true' => 'All alignments',
                    'wide-full' => 'Wide and Full only',
                    'basic' => 'Left, Center, Right only',
                ],
                'category' => 'layout',
            ],
            'anchor' => [
                'label' => 'HTML Anchor',
                'description' => 'Adds an anchor/ID field to the block settings',
                'type' => 'boolean',
                'category' => 'layout',
            ],
            'className' => [
                'label' => 'Additional CSS Class',
                'description' => 'Allows adding custom CSS classes',
                'type' => 'boolean',
                'default' => true,
                'category' => 'layout',
            ],
            'customClassName' => [
                'label' => 'Custom Class Name Input',
                'description' => 'Shows custom class name input field',
                'type' => 'boolean',
                'default' => true,
                'category' => 'layout',
            ],

            // Color
            'color.background' => [
                'label' => 'Background Color',
                'description' => 'Background color picker',
                'type' => 'boolean',
                'category' => 'color',
            ],
            'color.text' => [
                'label' => 'Text Color',
                'description' => 'Text color picker',
                'type' => 'boolean',
                'category' => 'color',
            ],
            'color.gradients' => [
                'label' => 'Gradients',
                'description' => 'Gradient background support',
                'type' => 'boolean',
                'category' => 'color',
            ],
            'color.link' => [
                'label' => 'Link Color',
                'description' => 'Link color picker',
                'type' => 'boolean',
                'category' => 'color',
            ],
            'color.heading' => [
                'label' => 'Heading Color',
                'description' => 'Heading color picker',
                'type' => 'boolean',
                'category' => 'color',
            ],
            'color.button' => [
                'label' => 'Button Color',
                'description' => 'Button color picker',
                'type' => 'boolean',
                'category' => 'color',
            ],

            // Typography
            'typography.fontSize' => [
                'label' => 'Font Size',
                'description' => 'Font size control',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.lineHeight' => [
                'label' => 'Line Height',
                'description' => 'Line height control',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.fontFamily' => [
                'label' => 'Font Family',
                'description' => 'Font family selector',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.fontWeight' => [
                'label' => 'Font Weight',
                'description' => 'Font weight control',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.fontStyle' => [
                'label' => 'Font Style',
                'description' => 'Font style (normal, italic)',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.letterSpacing' => [
                'label' => 'Letter Spacing',
                'description' => 'Letter spacing control',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.textTransform' => [
                'label' => 'Text Transform',
                'description' => 'Text transform (uppercase, lowercase, capitalize)',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.textDecoration' => [
                'label' => 'Text Decoration',
                'description' => 'Text decoration (underline, strikethrough)',
                'type' => 'boolean',
                'category' => 'typography',
            ],
            'typography.textAlign' => [
                'label' => 'Text Align',
                'description' => 'Text alignment control',
                'type' => 'boolean',
                'category' => 'typography',
            ],

            // Spacing
            'spacing.margin' => [
                'label' => 'Margin',
                'description' => 'Margin controls',
                'type' => 'boolean',
                'category' => 'spacing',
            ],
            'spacing.padding' => [
                'label' => 'Padding',
                'description' => 'Padding controls',
                'type' => 'boolean',
                'category' => 'spacing',
            ],
            'spacing.blockGap' => [
                'label' => 'Block Gap',
                'description' => 'Gap between inner blocks',
                'type' => 'boolean',
                'category' => 'spacing',
            ],

            // Border
            'border.color' => [
                'label' => 'Border Color',
                'description' => 'Border color picker',
                'type' => 'boolean',
                'category' => 'border',
            ],
            'border.radius' => [
                'label' => 'Border Radius',
                'description' => 'Border radius control',
                'type' => 'boolean',
                'category' => 'border',
            ],
            'border.style' => [
                'label' => 'Border Style',
                'description' => 'Border style (solid, dashed, dotted)',
                'type' => 'boolean',
                'category' => 'border',
            ],
            'border.width' => [
                'label' => 'Border Width',
                'description' => 'Border width control',
                'type' => 'boolean',
                'category' => 'border',
            ],

            // Dimensions
            'dimensions.aspectRatio' => [
                'label' => 'Aspect Ratio',
                'description' => 'Aspect ratio control',
                'type' => 'boolean',
                'category' => 'dimensions',
            ],
            'dimensions.minHeight' => [
                'label' => 'Minimum Height',
                'description' => 'Minimum height control',
                'type' => 'boolean',
                'category' => 'dimensions',
            ],

            // Background
            'background.backgroundImage' => [
                'label' => 'Background Image',
                'description' => 'Background image support',
                'type' => 'boolean',
                'category' => 'background',
            ],
            'background.backgroundSize' => [
                'label' => 'Background Size',
                'description' => 'Background size control',
                'type' => 'boolean',
                'category' => 'background',
            ],

            // Position
            'position.sticky' => [
                'label' => 'Sticky Position',
                'description' => 'Allow sticky positioning',
                'type' => 'boolean',
                'category' => 'position',
            ],

            // Other
            'shadow' => [
                'label' => 'Box Shadow',
                'description' => 'Box shadow controls',
                'type' => 'boolean',
                'category' => 'effects',
            ],
            'filter.duotone' => [
                'label' => 'Duotone Filter',
                'description' => 'Duotone color filter',
                'type' => 'boolean',
                'category' => 'effects',
            ],
            'html' => [
                'label' => 'Edit as HTML',
                'description' => 'Allow editing block as HTML',
                'type' => 'boolean',
                'default' => true,
                'category' => 'advanced',
            ],
            'lock' => [
                'label' => 'Locking',
                'description' => 'Allow locking the block',
                'type' => 'boolean',
                'category' => 'advanced',
            ],
            'renaming' => [
                'label' => 'Renaming',
                'description' => 'Allow renaming the block',
                'type' => 'boolean',
                'category' => 'advanced',
            ],
            'multiple' => [
                'label' => 'Allow Multiple',
                'description' => 'Allow multiple instances of this block',
                'type' => 'boolean',
                'default' => true,
                'category' => 'advanced',
            ],
            'reusable' => [
                'label' => 'Reusable',
                'description' => 'Allow saving as reusable block',
                'type' => 'boolean',
                'default' => true,
                'category' => 'advanced',
            ],
            'inserter' => [
                'label' => 'Show in Inserter',
                'description' => 'Show block in the inserter',
                'type' => 'boolean',
                'default' => true,
                'category' => 'advanced',
            ],
        ];
    }

    /**
     * Get support categories for organized display.
     *
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            'layout' => 'Layout & Structure',
            'color' => 'Color',
            'typography' => 'Typography',
            'spacing' => 'Spacing',
            'border' => 'Border',
            'dimensions' => 'Dimensions',
            'background' => 'Background',
            'position' => 'Position',
            'effects' => 'Effects',
            'advanced' => 'Advanced',
        ];
    }

    /**
     * Get saved support settings for a specific block.
     *
     * @param string $blockSlug The block slug (without namespace)
     * @return array|null The saved supports or null if not customized
     */
    public static function getBlockSupports(string $blockSlug): ?array
    {
        $allSupports = get_option(self::OPTION_NAME, []);
        return $allSupports[$blockSlug] ?? null;
    }

    /**
     * Get all saved support settings.
     *
     * @return array
     */
    public static function getAllBlockSupports(): array
    {
        return get_option(self::OPTION_NAME, []);
    }

    /**
     * Save support settings for a specific block.
     *
     * @param string $blockSlug The block slug (without namespace)
     * @param array $supports The supports configuration
     * @return bool
     */
    public static function saveBlockSupports(string $blockSlug, array $supports): bool
    {
        $allSupports = self::getAllBlockSupports();
        $allSupports[$blockSlug] = $supports;
        return update_option(self::OPTION_NAME, $allSupports);
    }

    /**
     * Clear support settings for a specific block (use block.json defaults).
     *
     * @param string $blockSlug The block slug (without namespace)
     * @return bool
     */
    public static function clearBlockSupports(string $blockSlug): bool
    {
        $allSupports = self::getAllBlockSupports();
        unset($allSupports[$blockSlug]);
        return update_option(self::OPTION_NAME, $allSupports);
    }

    /**
     * Convert a dot-notation key to nested array structure.
     * e.g., 'color.background' => ['color' => ['background' => $value]]
     *
     * @param string $key The dot-notation key
     * @param mixed $value The value to set
     * @return array
     */
    public static function expandDotNotation(string $key, $value): array
    {
        $parts = explode('.', $key);
        $result = [];
        $current = &$result;

        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $current[$part] = $value;
            } else {
                $current[$part] = [];
                $current = &$current[$part];
            }
        }

        return $result;
    }

    /**
     * Merge flat support settings into a nested supports array.
     *
     * Includes both true (enable) and false (disable) values.
     * Empty/null values are skipped (use block.json defaults).
     *
     * @param array $flatSupports Flat array with dot-notation keys
     * @return array Nested supports array ready for block registration
     */
    public static function buildSupportsArray(array $flatSupports): array
    {
        $supports = [];

        foreach ($flatSupports as $key => $value) {
            // Skip empty/null values - these use block.json defaults
            if ($value === '' || $value === null) {
                continue;
            }

            // Handle the 'align' special case
            if ($key === 'align') {
                switch ($value) {
                    case 'false':
                        $supports['align'] = false;
                        break;
                    case 'true':
                        $supports['align'] = true;
                        break;
                    case 'wide-full':
                        $supports['align'] = ['wide', 'full'];
                        break;
                    case 'basic':
                        $supports['align'] = ['left', 'center', 'right'];
                        break;
                    default:
                        // Keep existing or skip
                        break;
                }
                continue;
            }

            // Handle dot notation (e.g., 'color.background')
            if (strpos($key, '.') !== false) {
                $expanded = self::expandDotNotation($key, $value);
                $supports = self::mergeSupports($supports, $expanded);
            } else {
                $supports[$key] = $value;
            }
        }

        return $supports;
    }

    /**
     * Deep merge supports arrays, with $b taking precedence.
     *
     * Unlike array_merge_recursive, this properly overwrites values
     * instead of creating arrays.
     *
     * @param array $a Base array
     * @param array $b Array to merge (takes precedence)
     * @return array Merged array
     */
    private static function mergeSupports(array $a, array $b): array
    {
        foreach ($b as $key => $value) {
            if (is_array($value) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = self::mergeSupports($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }
        return $a;
    }

    /**
     * Get the current supports from a block's block.json as a flat array.
     *
     * @param string $blockSlug The block slug
     * @return array Flat array with dot-notation keys
     */
    public static function getBlockJsonSupports(string $blockSlug): array
    {
        $blockPath = BlockLoader::getBlockPath($blockSlug);
        if (!$blockPath) {
            return [];
        }

        $blockJsonPath = $blockPath . '/block.json';
        if (!file_exists($blockJsonPath)) {
            return [];
        }

        $blockJson = json_decode(file_get_contents($blockJsonPath), true);
        $supports = $blockJson['supports'] ?? [];

        return self::flattenSupports($supports);
    }

    /**
     * Flatten nested supports array to dot-notation keys.
     *
     * @param array $supports The nested supports array
     * @param string $prefix Current key prefix
     * @return array Flat array with dot-notation keys
     */
    public static function flattenSupports(array $supports, string $prefix = ''): array
    {
        $flat = [];

        foreach ($supports as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value) && !self::isSequentialArray($value)) {
                // Recursively flatten nested objects
                $flat = array_merge($flat, self::flattenSupports($value, $fullKey));
            } else {
                $flat[$fullKey] = $value;
            }
        }

        return $flat;
    }

    /**
     * Check if an array is sequential (indexed) vs associative.
     *
     * @param array $arr
     * @return bool
     */
    private static function isSequentialArray(array $arr): bool
    {
        if (empty($arr)) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
