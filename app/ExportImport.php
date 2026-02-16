<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Handles export and import of Starboard settings.
 */
class ExportImport
{
	/**
	 * Export all Starboard settings as JSON.
	 *
	 * @return array{success: bool, data?: string, message?: string}
	 */
	public static function export(): array
	{
		$data = [
			'version' => STARBOARD_VERSION,
			'exported_at' => gmdate('c'),
			'settings' => [
				'enabled_blocks' => get_option('starboard_blocks', []),
				'block_supports' => get_option('starboard_block_supports', []),
				'text_image_variants' => get_option('starboard_text_image_variants', []),
				'restrict_access' => get_option('starboard_restrict_access', false),
			],
		];

		$json = wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		if ($json === false) {
			return [
				'success' => false,
				'message' => 'Failed to encode settings as JSON.',
			];
		}

		return [
			'success' => true,
			'data' => $json,
		];
	}

	/**
	 * Import Starboard settings from JSON.
	 *
	 * @param string $json The JSON data to import
	 * @return array{success: bool, message: string, imported?: array}
	 */
	public static function import(string $json): array
	{
		$data = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return [
				'success' => false,
				'message' => 'Invalid JSON: ' . json_last_error_msg(),
			];
		}

		if (!is_array($data)) {
			return [
				'success' => false,
				'message' => 'Invalid data format: expected object.',
			];
		}

		// Validate structure
		if (!isset($data['settings']) || !is_array($data['settings'])) {
			return [
				'success' => false,
				'message' => 'Invalid data format: missing settings.',
			];
		}

		$settings = $data['settings'];
		$imported = [];

		// Import enabled blocks
		if (isset($settings['enabled_blocks'])) {
			$blocks = self::validateBlocksArray($settings['enabled_blocks']);
			update_option('starboard_blocks', $blocks);
			$imported[] = 'enabled_blocks';
		}

		// Import block supports
		if (isset($settings['block_supports']) && is_array($settings['block_supports'])) {
			$supports = self::validateBlockSupports($settings['block_supports']);
			update_option('starboard_block_supports', $supports);
			$imported[] = 'block_supports';
		}

		// Import text-image variants
		if (isset($settings['text_image_variants']) && is_array($settings['text_image_variants'])) {
			$variants = self::validateVariants($settings['text_image_variants']);
			update_option('starboard_text_image_variants', $variants);
			$imported[] = 'text_image_variants';
		}

		// Import access restriction (optional, don't import by default for safety)
		// Uncomment if you want to import access settings:
		// if (isset($settings['restrict_access'])) {
		//     update_option('starboard_restrict_access', (bool) $settings['restrict_access']);
		//     $imported[] = 'restrict_access';
		// }

		if (empty($imported)) {
			return [
				'success' => false,
				'message' => 'No valid settings found to import.',
			];
		}

		return [
			'success' => true,
			'message' => 'Settings imported successfully.',
			'imported' => $imported,
		];
	}

	/**
	 * Validate and sanitize blocks array.
	 *
	 * @param mixed $blocks
	 * @return array
	 */
	private static function validateBlocksArray($blocks): array
	{
		if (!is_array($blocks)) {
			return [];
		}

		return array_filter(
			array_map('sanitize_text_field', $blocks),
			function ($block) {
				return preg_match('/^[a-z0-9-]+\/[a-z0-9-]+$/', $block);
			}
		);
	}

	/**
	 * Validate and sanitize block supports.
	 *
	 * @param array $supports
	 * @return array
	 */
	private static function validateBlockSupports(array $supports): array
	{
		$validated = [];

		foreach ($supports as $blockSlug => $blockSupports) {
			$slug = sanitize_title($blockSlug);

			if (!ThemeOverride::isValidSlug($slug) || !is_array($blockSupports)) {
				continue;
			}

			$validatedSupports = [];
			foreach ($blockSupports as $key => $value) {
				$sanitizedKey = sanitize_text_field($key);
				// Values should be boolean or string
				if (is_bool($value)) {
					$validatedSupports[$sanitizedKey] = $value;
				} elseif (is_string($value)) {
					$validatedSupports[$sanitizedKey] = sanitize_text_field($value);
				}
			}

			if (!empty($validatedSupports)) {
				$validated[$slug] = $validatedSupports;
			}
		}

		return $validated;
	}

	/**
	 * Validate and sanitize text-image variants.
	 *
	 * @param array $variants
	 * @return array
	 */
	private static function validateVariants(array $variants): array
	{
		$validated = [];

		foreach ($variants as $slug => $variant) {
			$sanitizedSlug = sanitize_title($slug);

			if (empty($sanitizedSlug) || !is_array($variant)) {
				continue;
			}

			// Use TextImageVariants validator if available
			$validated[$sanitizedSlug] = TextImageVariants::validateVariant($variant);
		}

		return $validated;
	}

	/**
	 * Get export filename.
	 *
	 * @return string
	 */
	public static function getExportFilename(): string
	{
		$siteName = sanitize_title(get_bloginfo('name'));
		$date = gmdate('Y-m-d');

		return "starboard-settings-{$siteName}-{$date}.json";
	}
}
