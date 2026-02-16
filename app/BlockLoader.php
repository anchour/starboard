<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Block Loader - Handles block location with theme override support.
 *
 * Lookup order:
 * 1. {theme}/starboard/blocks/{block-name}/ (theme override)
 * 2. {parent-theme}/starboard/blocks/{block-name}/ (parent theme override)
 * 3. {plugin}/dist/blocks/{block-name}/ (compiled plugin)
 * 4. {plugin}/src/blocks/{block-name}/ (dev fallback)
 */
class BlockLoader
{
	/** @var string Constant for theme source type */
	private const SOURCE_THEME = 'theme';

	/** @var string Constant for parent theme source type */
	private const SOURCE_PARENT_THEME = 'parent_theme';

	/** @var string Constant for plugin dist source type */
	private const SOURCE_PLUGIN_DIST = 'plugin_dist';

	/** @var string Constant for plugin src source type */
	private const SOURCE_PLUGIN_SRC = 'plugin_src';

	/**
	 * Validate a block name to prevent path traversal.
	 *
	 * @param string $blockName The block name to validate
	 * @return bool True if valid, false otherwise
	 */
	private static function isValidBlockName(string $blockName): bool
	{
		// Only allow alphanumeric characters, hyphens, and underscores
		return (bool) preg_match('/^[a-z0-9_-]+$/i', $blockName);
	}

	/**
	 * Get the path to a block directory, checking theme first.
	 *
	 * @param string $blockName The block name without namespace (e.g., 'text-image')
	 * @return string|null The path to the block directory, or null if not found
	 */
	public static function getBlockPath(string $blockName): ?string
	{
		// Validate block name to prevent path traversal
		if (!self::isValidBlockName($blockName)) {
			return null;
		}

		$result = self::resolveBlockLocation($blockName);
		return $result['path'];
	}

	/**
	 * Check if a block has a theme override.
	 *
	 * @param string $blockName The block name without namespace
	 * @return bool
	 */
	public static function hasThemeOverride(string $blockName): bool
	{
		// Validate block name
		if (!self::isValidBlockName($blockName)) {
			return false;
		}

		$result = self::resolveBlockLocation($blockName);
		return in_array($result['source'], [self::SOURCE_THEME, self::SOURCE_PARENT_THEME], true);
	}

	/**
	 * Resolve the location of a block, returning both path and source type.
	 *
	 * @param string $blockName The block name without namespace
	 * @return array{path: string|null, source: string|null}
	 */
	private static function resolveBlockLocation(string $blockName): array
	{
		$stylesheetDir = get_stylesheet_directory();
		$templateDir = get_template_directory();

		// Check theme first (allows full block override)
		$themePath = $stylesheetDir . '/starboard/blocks/' . $blockName;
		if (file_exists($themePath . '/block.json')) {
			return ['path' => $themePath, 'source' => self::SOURCE_THEME];
		}

		// Check parent theme if using child theme
		if ($templateDir !== $stylesheetDir) {
			$parentThemePath = $templateDir . '/starboard/blocks/' . $blockName;
			if (file_exists($parentThemePath . '/block.json')) {
				return ['path' => $parentThemePath, 'source' => self::SOURCE_PARENT_THEME];
			}
		}

		// Fallback to plugin dist (compiled)
		$distPath = STARBOARD_DIST_PATH . $blockName;
		if (file_exists($distPath . '/block.json')) {
			return ['path' => $distPath, 'source' => self::SOURCE_PLUGIN_DIST];
		}

		// Fallback to plugin src (dev)
		$srcPath = STARBOARD_BLOCKS_PATH . $blockName;
		if (file_exists($srcPath . '/block.json')) {
			return ['path' => $srcPath, 'source' => self::SOURCE_PLUGIN_SRC];
		}

		return ['path' => null, 'source' => null];
	}

	/**
	 * Get the URL to a block's assets directory.
	 *
	 * @param string $blockName The block name without namespace
	 * @return string|null The URL to the block directory, or null if block not found
	 */
	public static function getBlockUrl(string $blockName): ?string
	{
		// Validate block name
		if (!self::isValidBlockName($blockName)) {
			return null;
		}

		$result = self::resolveBlockLocation($blockName);

		if ($result['path'] === null) {
			return null;
		}

		switch ($result['source']) {
			case self::SOURCE_THEME:
				return get_stylesheet_directory_uri() . '/starboard/blocks/' . $blockName;

			case self::SOURCE_PARENT_THEME:
				return get_template_directory_uri() . '/starboard/blocks/' . $blockName;

			case self::SOURCE_PLUGIN_DIST:
				return STARBOARD_PLUGIN_URL . 'dist/blocks/' . $blockName;

			case self::SOURCE_PLUGIN_SRC:
				return STARBOARD_PLUGIN_URL . 'src/blocks/' . $blockName;

			default:
				return null;
		}
	}

	/**
	 * Get all available block names from both plugin and theme.
	 *
	 * @return array Array of block names (without namespace)
	 */
	public static function getAllBlockNames(): array
	{
		$blocks = [];

		// Get plugin blocks (dist first, then src)
		$distBlocks = glob(STARBOARD_DIST_PATH . '*', GLOB_ONLYDIR);
		$srcBlocks = glob(STARBOARD_BLOCKS_PATH . '*', GLOB_ONLYDIR);

		// Handle glob failures
		$distBlocks = is_array($distBlocks) ? $distBlocks : [];
		$srcBlocks = is_array($srcBlocks) ? $srcBlocks : [];

		$pluginBlocks = !empty($distBlocks) ? $distBlocks : $srcBlocks;

		foreach ($pluginBlocks as $blockPath) {
			if (file_exists($blockPath . '/block.json')) {
				$blockName = basename($blockPath);
				if (self::isValidBlockName($blockName)) {
					$blocks[] = $blockName;
				}
			}
		}

		// Get theme blocks (may override or add new blocks)
		$themeBlocksPath = get_stylesheet_directory() . '/starboard/blocks/';
		if (is_dir($themeBlocksPath)) {
			$themeBlocks = glob($themeBlocksPath . '*', GLOB_ONLYDIR);
			$themeBlocks = is_array($themeBlocks) ? $themeBlocks : [];

			foreach ($themeBlocks as $blockPath) {
				if (file_exists($blockPath . '/block.json')) {
					$blockName = basename($blockPath);
					if (self::isValidBlockName($blockName) && !in_array($blockName, $blocks, true)) {
						$blocks[] = $blockName;
					}
				}
			}
		}

		return $blocks;
	}
}
