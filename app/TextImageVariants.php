<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Text Image Variants - Manages custom variants for the text-image block.
 */
class TextImageVariants
{
    /**
     * Option name for storing custom variants.
     */
    const OPTION_NAME = 'starboard_text_image_variants';

    /**
     * Get all custom variants.
     *
     * @return array
     */
    public static function getVariants(): array
    {
        return get_option(self::OPTION_NAME, []);
    }

    /**
     * Get a single variant by slug.
     *
     * @param string $slug
     * @return array|null
     */
    public static function getVariant(string $slug): ?array
    {
        $variants = self::getVariants();
        return $variants[$slug] ?? null;
    }

    /**
     * Save a variant.
     *
     * @param string $slug
     * @param array $variant
     * @return bool
     */
    public static function saveVariant(string $slug, array $variant): bool
    {
        $variants = self::getVariants();
        $variants[$slug] = $variant;
        return update_option(self::OPTION_NAME, $variants);
    }

    /**
     * Delete a variant.
     *
     * @param string $slug
     * @return bool
     */
    public static function deleteVariant(string $slug): bool
    {
        $variants = self::getVariants();
        unset($variants[$slug]);
        return update_option(self::OPTION_NAME, $variants);
    }

    /**
     * Get variants formatted for JavaScript.
     * Returns both variations array and layouts object.
     *
     * @return array
     */
    public static function getVariantsForJs(): array
    {
        $variants = self::getVariants();
        $variations = [];
        $layouts = [];

        foreach ($variants as $slug => $variant) {
            $variantName = 'text-image__' . $slug;

            // Build variation for block picker
            $variations[] = [
                'name' => $variantName,
                'title' => $variant['title'] ?? $slug,
                'description' => $variant['description'] ?? '',
                'isCustom' => true,
            ];

            // Build layout for grid positioning (lg, xl breakpoints)
            $layouts[$variantName] = [];

            foreach (['lg', 'xl'] as $breakpoint) {
                if (!isset($variant[$breakpoint])) {
                    continue;
                }

                $layouts[$variantName][$breakpoint] = [
                    'copyLeft' => [
                        'copy' => [
                            'start' => (int) ($variant[$breakpoint]['copy']['start'] ?? 1),
                            'span' => (int) ($variant[$breakpoint]['copy']['span'] ?? 6),
                        ],
                        'img' => [
                            'start' => (int) ($variant[$breakpoint]['img']['start'] ?? 7),
                            'span' => (int) ($variant[$breakpoint]['img']['span'] ?? 6),
                        ],
                    ],
                    'copyRight' => [
                        'copy' => [
                            'start' => 13 - (int) ($variant[$breakpoint]['copy']['start'] ?? 1) - (int) ($variant[$breakpoint]['copy']['span'] ?? 6) + 1,
                            'span' => (int) ($variant[$breakpoint]['copy']['span'] ?? 6),
                        ],
                        'img' => [
                            'start' => 1,
                            'span' => (int) ($variant[$breakpoint]['img']['span'] ?? 6),
                        ],
                    ],
                ];
            }
        }

        return [
            'variations' => $variations,
            'layouts' => $layouts,
        ];
    }

    /**
     * Validate variant data.
     *
     * @param array $data
     * @return array Validated and sanitized variant data
     */
    public static function validateVariant(array $data): array
    {
        $validated = [
            'title' => sanitize_text_field($data['title'] ?? ''),
            'description' => sanitize_text_field($data['description'] ?? ''),
        ];

        // Validate each breakpoint (lg, xl)
        foreach (['lg', 'xl'] as $breakpoint) {
            $validated[$breakpoint] = [
                'copy' => [
                    'start' => self::clampGrid((int) ($data[$breakpoint . '_copy_start'] ?? 1)),
                    'span' => self::clampGrid((int) ($data[$breakpoint . '_copy_span'] ?? 6)),
                ],
                'img' => [
                    'start' => self::clampGrid((int) ($data[$breakpoint . '_img_start'] ?? 7)),
                    'span' => self::clampGrid((int) ($data[$breakpoint . '_img_span'] ?? 6)),
                ],
            ];
        }

        return $validated;
    }

    /**
     * Clamp a grid value to valid range (1-12).
     *
     * @param int $value
     * @return int
     */
    private static function clampGrid(int $value): int
    {
        return max(1, min(12, $value));
    }
}
