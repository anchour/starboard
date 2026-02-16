<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Configuration manager for Starboard.
 *
 * Loads default values from config/defaults.php and allows
 * overriding via filters.
 */
class Config
{
	/**
	 * Cached configuration values.
	 *
	 * @var array|null
	 */
	private static ?array $config = null;

	/**
	 * Load and cache configuration.
	 *
	 * @return array
	 */
	private static function load(): array
	{
		if (self::$config === null) {
			$defaultsPath = STARBOARD_PLUGIN_PATH . 'config/defaults.php';

			if (file_exists($defaultsPath)) {
				self::$config = require $defaultsPath;
			} else {
				self::$config = [];
			}
		}

		return self::$config;
	}

	/**
	 * Get a configuration value.
	 *
	 * @param string $key Dot-notation key (e.g., 'screens.md' or 'access.allowed_domain')
	 * @param mixed $default Default value if key not found
	 * @return mixed
	 */
	public static function get(string $key, $default = null)
	{
		$config = self::load();
		$keys = explode('.', $key);
		$value = $config;

		foreach ($keys as $k) {
			if (!is_array($value) || !array_key_exists($k, $value)) {
				return $default;
			}
			$value = $value[$k];
		}

		return $value;
	}

	/**
	 * Get screen breakpoints.
	 *
	 * @return array
	 */
	public static function getScreens(): array
	{
		$screens = self::get('screens', []);

		/**
		 * Filter screen breakpoints.
		 *
		 * @param array $screens Breakpoint configuration
		 */
		return apply_filters('starboard_screens', $screens);
	}

	/**
	 * Get spacing configuration.
	 *
	 * @return array
	 */
	public static function getSpacing(): array
	{
		$spacing = self::get('spacing', []);

		/**
		 * Filter spacing configuration.
		 *
		 * @param array $spacing Spacing values per breakpoint
		 */
		return apply_filters('starboard_spacing', $spacing);
	}

	/**
	 * Get the allowed access domain.
	 *
	 * @return string
	 */
	public static function getAccessDomain(): string
	{
		$domain = self::get('access.allowed_domain', 'anchour.com');

		/**
		 * Filter the allowed access domain.
		 *
		 * @param string $domain The domain to allow access for
		 */
		return apply_filters('starboard_access_domain', $domain);
	}

	/**
	 * Get theme override directory name.
	 *
	 * @return string
	 */
	public static function getThemeOverrideDirectory(): string
	{
		return self::get('theme_override.directory', 'starboard/blocks');
	}

	/**
	 * Clear cached configuration (useful for testing).
	 *
	 * @return void
	 */
	public static function clearCache(): void
	{
		self::$config = null;
	}
}
