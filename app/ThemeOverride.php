<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Handles copying blocks to the theme for customization.
 */
class ThemeOverride
{
	/**
	 * Validate a block slug to prevent path traversal.
	 *
	 * @param string $slug The block slug to validate
	 * @return bool True if valid, false otherwise
	 */
	public static function isValidSlug(string $slug): bool
	{
		// Only allow alphanumeric characters, hyphens, and underscores
		return (bool) preg_match('/^[a-z0-9_-]+$/i', $slug);
	}

	/**
	 * Get the theme's starboard blocks directory path.
	 *
	 * @return string
	 */
	public static function getThemeBlocksPath(): string
	{
		return get_stylesheet_directory() . '/starboard/blocks/';
	}

	/**
	 * Check if theme overrides can be created/modified.
	 *
	 * Returns false if:
	 * - On Pantheon in Git mode
	 * - Theme directory is not writable
	 * - DISALLOW_FILE_MODS is enabled
	 *
	 * @return bool
	 */
	public static function canModifyTheme(): bool
	{
		// Check WordPress constant that disables file modifications
		if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
			return false;
		}

		// Check if on Pantheon
		if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
			// On Pantheon, check connection mode
			// In Git mode, files are read-only
			// PANTHEON_BINDING is only set in SFTP mode
			if (!isset($_SERVER['PANTHEON_BINDING'])) {
				return false;
			}
		}

		// Check if theme directory is writable
		$themeDir = get_stylesheet_directory();
		if (!is_writable($themeDir)) {
			return false;
		}

		return true;
	}

	/**
	 * Get the reason why theme modifications are disabled.
	 *
	 * @return string|null Reason string, or null if modifications are allowed
	 */
	public static function getDisabledReason(): ?string
	{
		if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
			return 'File modifications are disabled (DISALLOW_FILE_MODS).';
		}

		if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
			if (!isset($_SERVER['PANTHEON_BINDING'])) {
				return 'Pantheon is in Git mode. Switch to SFTP mode to copy blocks to theme.';
			}
		}

		$themeDir = get_stylesheet_directory();
		if (!is_writable($themeDir)) {
			return 'Theme directory is not writable.';
		}

		return null;
	}

	/**
	 * Copy a block from the plugin to the theme.
	 *
	 * @param string $blockSlug The block slug (e.g., 'text-image')
	 * @return array{success: bool, message: string}
	 */
	public static function copyBlockToTheme(string $blockSlug): array
	{
		// Validate slug to prevent path traversal
		if (!self::isValidSlug($blockSlug)) {
			self::log('Invalid block slug attempted: ' . $blockSlug, 'error');
			return [
				'success' => false,
				'message' => 'Invalid block slug.',
			];
		}

		// Get source path (compiled dist)
		$sourcePath = STARBOARD_DIST_PATH . $blockSlug;

		if (!file_exists($sourcePath . '/block.json')) {
			return [
				'success' => false,
				'message' => 'Block not found in plugin dist. Please run a build first.',
			];
		}

		// Get destination path
		$destPath = self::getThemeBlocksPath() . $blockSlug;

		// Create theme blocks directory if needed
		if (!file_exists(self::getThemeBlocksPath())) {
			if (!wp_mkdir_p(self::getThemeBlocksPath())) {
				self::log('Failed to create theme blocks directory', 'error');
				return [
					'success' => false,
					'message' => 'Could not create starboard/blocks directory in theme.',
				];
			}
		}

		// Remove existing override if present
		if (file_exists($destPath)) {
			if (!self::removeDirectory($destPath)) {
				self::log('Failed to remove existing override: ' . $destPath, 'error');
				return [
					'success' => false,
					'message' => 'Failed to remove existing theme override.',
				];
			}
		}

		// Copy the block directory
		if (!self::copyDirectory($sourcePath, $destPath)) {
			self::log('Failed to copy block from ' . $sourcePath . ' to ' . $destPath, 'error');
			return [
				'success' => false,
				'message' => 'Failed to copy block files.',
			];
		}

		// Fix asset.php files to include starboard-shared dependency
		$assetResult = self::fixAssetDependencies($destPath);
		if (!$assetResult['success']) {
			self::log('Failed to fix asset dependencies: ' . $assetResult['message'], 'warning');
		}

		// Add @starboard externals to theme's build config
		$externalsResult = self::addExternalsToThemeBuild();
		if (!$externalsResult) {
			self::log('Could not add externals to theme build config', 'warning');
		}

		self::log('Block copied to theme: ' . $blockSlug, 'info');

		return [
			'success' => true,
			'message' => 'Block copied to theme successfully. You can now edit it in your theme\'s starboard/blocks/' . $blockSlug . '/ directory.',
		];
	}

	/**
	 * Remove a block override from the theme.
	 *
	 * @param string $blockSlug The block slug
	 * @return array{success: bool, message: string}
	 */
	public static function removeBlockFromTheme(string $blockSlug): array
	{
		// Validate slug to prevent path traversal
		if (!self::isValidSlug($blockSlug)) {
			self::log('Invalid block slug for removal: ' . $blockSlug, 'error');
			return [
				'success' => false,
				'message' => 'Invalid block slug.',
			];
		}

		$destPath = self::getThemeBlocksPath() . $blockSlug;

		if (!file_exists($destPath)) {
			return [
				'success' => false,
				'message' => 'Block override not found in theme.',
			];
		}

		if (!self::removeDirectory($destPath)) {
			self::log('Failed to remove block override: ' . $destPath, 'error');
			return [
				'success' => false,
				'message' => 'Failed to remove block files.',
			];
		}

		self::log('Block override removed: ' . $blockSlug, 'info');

		return [
			'success' => true,
			'message' => 'Block override removed. The plugin version will now be used.',
		];
	}

	/**
	 * Fix asset.php files to include starboard-shared as a dependency.
	 * Uses proper PHP array parsing instead of regex.
	 *
	 * @param string $blockPath Path to the block directory
	 * @return array{success: bool, message: string}
	 */
	private static function fixAssetDependencies(string $blockPath): array
	{
		$assetFiles = glob($blockPath . '/*.asset.php');
		if ($assetFiles === false) {
			return ['success' => false, 'message' => 'Failed to scan for asset files'];
		}

		$errors = [];

		foreach ($assetFiles as $assetFile) {
			$result = self::fixSingleAssetFile($assetFile);
			if (!$result['success']) {
				$errors[] = basename($assetFile) . ': ' . $result['message'];
			}
		}

		// Also fix nested sub-block asset files
		$subDirs = glob($blockPath . '/*', GLOB_ONLYDIR);
		if ($subDirs !== false) {
			foreach ($subDirs as $subDir) {
				$subResult = self::fixAssetDependencies($subDir);
				if (!$subResult['success']) {
					$errors[] = $subResult['message'];
				}
			}
		}

		if (!empty($errors)) {
			return ['success' => false, 'message' => implode('; ', $errors)];
		}

		return ['success' => true, 'message' => 'Asset dependencies fixed'];
	}

	/**
	 * Fix a single asset.php file using proper PHP array parsing.
	 *
	 * @param string $assetFile Path to the asset.php file
	 * @return array{success: bool, message: string}
	 */
	private static function fixSingleAssetFile(string $assetFile): array
	{
		if (!is_readable($assetFile)) {
			return ['success' => false, 'message' => 'File not readable'];
		}

		// Include the asset file to get the array
		$assetData = include $assetFile;

		if (!is_array($assetData)) {
			return ['success' => false, 'message' => 'Invalid asset file format'];
		}

		// Check if starboard-shared is already present
		$dependencies = $assetData['dependencies'] ?? [];
		if (in_array('starboard-shared', $dependencies, true)) {
			return ['success' => true, 'message' => 'Already has dependency'];
		}

		// Add starboard-shared to dependencies
		$dependencies[] = 'starboard-shared';
		$assetData['dependencies'] = $dependencies;

		// Generate the new PHP content
		$content = '<?php return ' . var_export($assetData, true) . ';' . "\n";

		// Write back to file
		$result = file_put_contents($assetFile, $content);
		if ($result === false) {
			return ['success' => false, 'message' => 'Failed to write file'];
		}

		return ['success' => true, 'message' => 'Dependency added'];
	}

	/**
	 * Add @starboard externals to the theme's build configuration.
	 *
	 * Supports bud.config.mjs (Sage/Bud.js themes).
	 *
	 * @return bool Whether externals were added
	 */
	private static function addExternalsToThemeBuild(): bool
	{
		$themeDir = get_stylesheet_directory();
		$budConfig = $themeDir . '/bud.config.mjs';

		// Check for bud.config.mjs (Sage/Bud.js)
		if (file_exists($budConfig)) {
			return self::addExternalsToBudConfig($budConfig);
		}

		// Could add support for webpack.config.js here in the future
		return false;
	}

	/**
	 * Add @starboard externals to a bud.config.mjs file.
	 *
	 * @param string $configPath Path to bud.config.mjs
	 * @return bool
	 */
	private static function addExternalsToBudConfig(string $configPath): bool
	{
		$content = file_get_contents($configPath);
		if ($content === false) {
			self::log('Failed to read bud.config.mjs', 'error');
			return false;
		}

		// Check if externals are already present
		if (strpos($content, '@starboard/components') !== false) {
			return true; // Already configured
		}

		$externalsBlock = "
    /**
     * Starboard shared utilities externals
     * These are provided by the Starboard plugin's starboard-shared.js
     */
    .externals({
      '@starboard/components': ['starboard', 'components'],
      '@starboard/utils': ['starboard', 'utils'],
      '@starboard/config': ['starboard', 'config'],
    })
";

		// Find the app configuration chain and insert externals before .entry()
		$patterns = [
			'/(\s+app\s*)\n(\s*\.entry\()/s',
			'/(\s+app)(\s*\.entry\()/s',
		];

		$replaced = false;
		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $content)) {
				$content = preg_replace(
					$pattern,
					'$1' . $externalsBlock . "\n\$2",
					$content,
					1
				);
				$replaced = true;
				break;
			}
		}

		if (!$replaced && strpos($content, '.entry(') !== false) {
			$content = preg_replace(
				'/(\s*)(\.entry\()/s',
				$externalsBlock . "\n$1$2",
				$content,
				1
			);
			$replaced = true;
		}

		if ($replaced) {
			$result = file_put_contents($configPath, $content);
			if ($result === false) {
				self::log('Failed to write bud.config.mjs', 'error');
				return false;
			}
			return true;
		}

		return false;
	}

	/**
	 * Recursively copy a directory.
	 *
	 * @param string $source Source directory
	 * @param string $dest Destination directory
	 * @return bool
	 */
	private static function copyDirectory(string $source, string $dest): bool
	{
		if (!is_dir($source)) {
			self::log('Source is not a directory: ' . $source, 'error');
			return false;
		}

		if (!is_readable($source)) {
			self::log('Source directory not readable: ' . $source, 'error');
			return false;
		}

		if (!wp_mkdir_p($dest)) {
			self::log('Failed to create destination directory: ' . $dest, 'error');
			return false;
		}

		$dir = opendir($source);
		if ($dir === false) {
			self::log('Failed to open source directory: ' . $source, 'error');
			return false;
		}

		$success = true;

		while (($file = readdir($dir)) !== false) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$srcPath = $source . '/' . $file;
			$destPath = $dest . '/' . $file;

			if (is_dir($srcPath)) {
				if (!self::copyDirectory($srcPath, $destPath)) {
					$success = false;
					break;
				}
			} else {
				if (!is_readable($srcPath)) {
					self::log('Source file not readable: ' . $srcPath, 'error');
					$success = false;
					break;
				}
				if (!copy($srcPath, $destPath)) {
					self::log('Failed to copy file: ' . $srcPath . ' to ' . $destPath, 'error');
					$success = false;
					break;
				}
			}
		}

		closedir($dir);
		return $success;
	}

	/**
	 * Recursively remove a directory.
	 *
	 * @param string $dir Directory path
	 * @return bool
	 */
	private static function removeDirectory(string $dir): bool
	{
		if (!is_dir($dir)) {
			return false;
		}

		$files = scandir($dir);
		if ($files === false) {
			self::log('Failed to scan directory for removal: ' . $dir, 'error');
			return false;
		}

		$files = array_diff($files, ['.', '..']);

		foreach ($files as $file) {
			$path = $dir . '/' . $file;
			if (is_dir($path)) {
				if (!self::removeDirectory($path)) {
					return false;
				}
			} else {
				if (!is_writable($path)) {
					self::log('File not writable for deletion: ' . $path, 'error');
					return false;
				}
				if (!unlink($path)) {
					self::log('Failed to delete file: ' . $path, 'error');
					return false;
				}
			}
		}

		if (!rmdir($dir)) {
			self::log('Failed to remove directory: ' . $dir, 'error');
			return false;
		}

		return true;
	}

	/**
	 * Log a message for debugging.
	 *
	 * @param string $message The message to log
	 * @param string $level Log level: 'info', 'warning', 'error'
	 */
	private static function log(string $message, string $level = 'info'): void
	{
		if (!defined('WP_DEBUG') || !WP_DEBUG) {
			return;
		}

		$prefix = '[Starboard ThemeOverride]';
		$levelTag = strtoupper($level);
		error_log("{$prefix} [{$levelTag}] {$message}");
	}
}
