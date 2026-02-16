<?php

namespace Starboard;

defined('ABSPATH') || exit;

class GetBlocks
{
	/**
	 * Returns an array of blocks provided by the plugin and theme overrides.
	 *
	 * Uses BlockLoader to discover all available blocks from both plugin
	 * and theme directories (theme takes precedence).
	 *
	 * @return array
	 */
	public static function run(): array
	{
		$blockNames = BlockLoader::getAllBlockNames();
		$blockData = [];

		foreach ($blockNames as $blockName) {
			$blockPath = BlockLoader::getBlockPath($blockName);
			$blockJsonPath = $blockPath . '/block.json';

			if (!$blockPath || !file_exists($blockJsonPath) || !is_readable($blockJsonPath)) {
				continue;
			}

			$content = file_get_contents($blockJsonPath);
			if ($content === false) {
				self::logError("Failed to read block.json for: {$blockName}");
				continue;
			}

			$decoded = json_decode($content);
			if (json_last_error() !== JSON_ERROR_NONE) {
				self::logError("Invalid JSON in block.json for {$blockName}: " . json_last_error_msg());
				continue;
			}

			$blockData[] = $decoded;
		}

		return $blockData;
	}

	/**
	 * Log an error message if WP_DEBUG is enabled.
	 *
	 * @param string $message
	 */
	private static function logError(string $message): void
	{
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('[Starboard GetBlocks] ' . $message);
		}
	}

	/**
	 * Returns an array of enabled blocks.
	 *
	 * @return array
	 */
	public static function enabled(): array
	{
		// Use a unique default to detect if option exists
		$notSet = '___NOT_SET___';
		$saved = get_option('starboard_blocks', $notSet);

		// If option exists (even if empty array), use it
		if ($saved !== $notSet) {
			return is_array($saved) ? $saved : [];
		}

		// Default: all blocks enabled
		return array_map(function ($block) {
			return $block->name;
		}, self::run());
	}
}
