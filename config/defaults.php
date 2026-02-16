<?php
/**
 * Starboard default configuration values.
 *
 * These values can be overridden via filters or by defining constants
 * before the plugin loads.
 *
 * @package Starboard
 */

defined('ABSPATH') || exit;

return [
	/**
	 * Screen breakpoints (matching Tailwind defaults).
	 *
	 * Used for responsive spacing calculations and media queries.
	 * Values should match your theme's Tailwind configuration.
	 *
	 * Filter: starboard_screens
	 */
	'screens' => [
		'md' => '768px',
		'lg' => '1024px',
		'xl' => '1280px',
	],

	/**
	 * Default spacing values per breakpoint.
	 *
	 * Used for block padding defaults. Values are in pixels.
	 *
	 * Filter: starboard_spacing
	 */
	'spacing' => [
		'Default' => ['paddingTop' => 16, 'paddingBottom' => 16],
		'md'      => ['paddingTop' => 18, 'paddingBottom' => 18],
		'lg'      => ['paddingTop' => 20, 'paddingBottom' => 20],
		'xl'      => ['paddingTop' => 24, 'paddingBottom' => 24],
	],

	/**
	 * Access control configuration.
	 *
	 * Domain used for restricting access to Starboard settings.
	 *
	 * Filter: starboard_access_domain
	 */
	'access' => [
		'allowed_domain' => 'anchour.com',
	],

	/**
	 * Block registration configuration.
	 */
	'blocks' => [
		// Blocks that should always be registered (even if not explicitly enabled)
		'always_enabled' => [],

		// Blocks that are disabled by default
		'disabled_by_default' => [],
	],

	/**
	 * Theme override configuration.
	 */
	'theme_override' => [
		// Directory name in theme for block overrides
		'directory' => 'starboard/blocks',
	],
];
