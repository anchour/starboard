<?php

namespace Starboard;

defined('ABSPATH') || exit;

class Setup
{
	/**
	 * Get screen breakpoints from config.
	 *
	 * @return array
	 */
	public static function getScreens(): array
	{
		return Config::getScreens();
	}

	/**
	 * Get spacing values from config.
	 *
	 * @return array
	 */
	public static function getSpacing(): array
	{
		return Config::getSpacing();
	}

	protected function __construct()
	{
		add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
		add_action('admin_enqueue_scripts', [$this, 'enqueueAdminStyles']);
		add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);
		add_action('admin_menu', [$this, 'setupAdminMenu']);

		// Register starboard-shared early so it's available as a dependency
		add_action('init', [$this, 'registerSharedScript'], 5);
		add_action('init', [$this, 'registerBlocks'], 10);

		// Filter block metadata to apply support overrides before registration
		add_filter('block_type_metadata_settings', [$this, 'filterBlockSupports'], 10, 2);

		// Filter theme.json to apply support overrides (needed because appearanceTools overrides block supports)
		add_filter('wp_theme_json_data_theme', [$this, 'filterThemeJson'], 10, 1);

		// Add starboard-shared dependency to all Starboard block editor scripts
		add_filter('block_type_metadata_settings', [$this, 'addSharedDependency'], 10, 2);
	}

	/**
	 * Registers the admin menu for the plugin.
	 *
	 * @return void
	 */
	public function setupAdminMenu(): void
	{
		add_menu_page('Starboard', 'Starboard', 'manage_options', 'starboard', [$this, 'renderAdminPage'], 'dashicons-star-filled');
	}

	/**
	 * Renders the admin page for the plugin.
	 *
	 * @return void
	 */
	public function renderAdminPage(): void
	{
		if (!AccessControl::currentUserHasAccess()) {
			$this->renderAccessDenied();
			return;
		}

		require STARBOARD_PLUGIN_PATH . 'src/views/settings.php';
	}

	/**
	 * Renders the access denied message.
	 *
	 * @return void
	 */
	private function renderAccessDenied(): void
	{
		$currentUser = wp_get_current_user();
		?>
		<div class="wrap">
			<h1>Starboard Settings</h1>
			<div style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #d63638; padding: 24px; max-width: 600px; margin-top: 20px;">
				<h2 style="margin-top: 0; color: #d63638;">Access Restricted</h2>
				<p>Starboard settings are restricted to users with <strong>@anchour.com</strong> email addresses.</p>
				<p>Your email: <code><?= esc_html($currentUser->user_email); ?></code></p>
				<p style="margin-bottom: 0;">Please contact an Anchour team member if you need access to these settings.</p>
			</div>
		</div>
		<?php
	}

	public static function init(): void
	{
		new self();
	}

	/**
	 * Register the shared utilities script early so it's available as a dependency.
	 *
	 * @return void
	 */
	public function registerSharedScript(): void
	{
		$wpDependencies = ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'];

		wp_register_script(
			'starboard-shared',
			STARBOARD_PLUGIN_URL . 'dist/starboard-shared.js',
			$wpDependencies,
			STARBOARD_VERSION,
			true
		);
	}

	/**
	 * Enqueues shared assets for the block editor.
	 *
	 * Block scripts are registered automatically by register_block_type_from_metadata().
	 * This method only needs to enqueue the shared utilities script.
	 *
	 * @return void
	 */
	public function enqueueAssets(): void
	{
		/** @var \WP_Screen|null $screen */
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;

		// Prevent enqueueing on the block-based Widgets screen or Customizer
		if ($screen && in_array($screen->base, ['widgets', 'customize'], true)) {
			return;
		}

		$enabled = GetBlocks::enabled();
		if (empty($enabled)) {
			return;
		}

		// Enqueue shared utilities (exposes window.starboard globals)
		// Block scripts depend on this via addSharedDependency filter
		wp_enqueue_script('starboard-shared');
	}

	/**
	 * Enqueue admin styles for the Starboard settings page.
	 *
	 * @param string $hook The current admin page hook
	 * @return void
	 */
	public function enqueueAdminStyles(string $hook): void
	{
		// Only load on the Starboard settings page
		if ($hook !== 'toplevel_page_starboard') {
			return;
		}

		wp_enqueue_style(
			'starboard-admin',
			STARBOARD_PLUGIN_URL . 'src/views/admin.css',
			[],
			filemtime(STARBOARD_PLUGIN_PATH . 'src/views/admin.css')
		);
	}

	/**
	 * Enqueue block editor assets and localize config.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorAssets(): void
	{
		$config = $this->getConfig();

		// Add block support overrides to config
		$config['blockSupports'] = $this->getBlockSupportsConfig();

		wp_localize_script('wp-blocks', 'starboardConfig', $config);

		// Add inline script to apply support overrides via JS filter
		$this->enqueueBlockSupportsFilter();
	}

	/**
	 * Get all block support overrides for JS.
	 *
	 * @return array
	 */
	private function getBlockSupportsConfig(): array
	{
		$allSupports = BlockSupports::getAllBlockSupports();
		$config = [];

		foreach ($allSupports as $slug => $supports) {
			$builtSupports = BlockSupports::buildSupportsArray($supports);
			if (!empty($builtSupports)) {
				$config['starboard/' . $slug] = $builtSupports;
			}
		}

		return $config;
	}

	/**
	 * Enqueue inline script to apply block support overrides in JS.
	 *
	 * @return void
	 */
	private function enqueueBlockSupportsFilter(): void
	{
		$script = "
			(function() {
				if (typeof wp === 'undefined' || !wp.hooks) return;

				var supportsConfig = (window.starboardConfig && window.starboardConfig.blockSupports) || {};

				wp.hooks.addFilter(
					'blocks.registerBlockType',
					'starboard/block-supports',
					function(settings, name) {
						if (!supportsConfig[name]) {
							return settings;
						}

						var overrides = supportsConfig[name];
						var newSupports = JSON.parse(JSON.stringify(settings.supports || {}));

						for (var key in overrides) {
							if (typeof overrides[key] === 'object' && overrides[key] !== null && !Array.isArray(overrides[key])) {
								if (!newSupports[key]) {
									newSupports[key] = {};
								}
								for (var subKey in overrides[key]) {
									if (overrides[key][subKey] === false) {
										delete newSupports[key][subKey];
									} else {
										newSupports[key][subKey] = overrides[key][subKey];
									}
								}
								if (Object.keys(newSupports[key]).length === 0) {
									delete newSupports[key];
								}
							} else if (overrides[key] === false) {
								delete newSupports[key];
							} else {
								newSupports[key] = overrides[key];
							}
						}

						return Object.assign({}, settings, { supports: newSupports });
					}
				);
			})();
		";

		wp_add_inline_script('wp-blocks', $script, 'after');
	}

	/**
	 * Get the Starboard configuration with filters applied.
	 *
	 * @return array
	 */
	public function getConfig(): array
	{
		// Get screens and spacing from config (filters applied in Config class)
		$screens = Config::getScreens();
		$spacing = Config::getSpacing();

		// Get custom text-image variants
		$customVariants = TextImageVariants::getVariantsForJs();

		return [
			'screens' => $screens,
			'defaultSpacing' => $spacing,
			'customVariants' => $customVariants,
		];
	}

	/**
	 * Loops through the blocks and registers them with theme override support.
	 *
	 * Uses BlockLoader to check for theme overrides before falling back to plugin blocks.
	 * Also registers nested sub-blocks found within block directories.
	 * Also auto-registers child blocks (blocks with parent property) when their parent is enabled.
	 * Support overrides are applied via the block_type_metadata_settings filter.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function registerBlocks(): void
	{
		$enabled = GetBlocks::enabled();

		if (empty($enabled)) {
			return;
		}

		$registered = [];

		foreach ($enabled as $blockName) {
			// Strip namespace to get block slug
			$slug = str_replace('starboard/', '', $blockName);

			// Use BlockLoader to get the path (checks theme first)
			$blockPath = BlockLoader::getBlockPath($slug);

			if (!$blockPath) {
				continue;
			}

			$blockJson = $blockPath . '/block.json';

			if (!file_exists($blockJson)) {
				throw new \Exception('block.json file not found for block: ' . $blockName);
			}

			// Register the main block (supports are filtered via block_type_metadata_settings)
			register_block_type_from_metadata($blockJson);
			$registered[] = $blockName;

			// Register nested sub-blocks (e.g., text-image/column)
			$subBlocks = glob($blockPath . '/*/block.json');
			foreach ($subBlocks as $subBlockJson) {
				register_block_type_from_metadata($subBlockJson);
			}
		}

		// Auto-register child blocks whose parent is enabled
		$this->registerChildBlocks($registered);
	}

	/**
	 * Register child blocks (blocks with parent property) when their parent is enabled.
	 *
	 * @param array $registeredBlocks Array of already registered block names
	 * @return void
	 */
	private function registerChildBlocks(array $registeredBlocks): void
	{
		$allBlocks = BlockLoader::getAllBlockNames();

		foreach ($allBlocks as $blockName) {
			$fullName = 'starboard/' . $blockName;

			// Skip if already registered
			if (in_array($fullName, $registeredBlocks, true)) {
				continue;
			}

			$blockPath = BlockLoader::getBlockPath($blockName);
			if (!$blockPath) {
				continue;
			}

			$blockJsonPath = $blockPath . '/block.json';
			if (!file_exists($blockJsonPath) || !is_readable($blockJsonPath)) {
				continue;
			}

			$content = file_get_contents($blockJsonPath);
			if ($content === false) {
				continue;
			}

			$blockData = json_decode($content, true);
			if (json_last_error() !== JSON_ERROR_NONE || !is_array($blockData) || empty($blockData['parent'])) {
				continue;
			}

			// Check if any parent is in the registered blocks
			$parents = (array) $blockData['parent'];
			foreach ($parents as $parent) {
				if (in_array($parent, $registeredBlocks, true)) {
					register_block_type_from_metadata($blockJsonPath);
					break;
				}
			}
		}
	}

	/**
	 * Filter theme.json data to apply block support overrides.
	 *
	 * This is needed because theme.json's appearanceTools setting
	 * overrides individual block supports. We add our blocks to the
	 * theme.json blocks section to properly disable features.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme.json data object.
	 * @return WP_Theme_JSON_Data Modified theme.json data.
	 */
	public function filterThemeJson($theme_json)
	{
		$allSupports = BlockSupports::getAllBlockSupports();

		if (empty($allSupports)) {
			return $theme_json;
		}

		$data = $theme_json->get_data();
		$blocksSettings = $data['settings']['blocks'] ?? [];

		foreach ($allSupports as $slug => $supports) {
			$blockName = 'starboard/' . $slug;

			if (!isset($blocksSettings[$blockName])) {
				$blocksSettings[$blockName] = [];
			}

			foreach ($supports as $key => $value) {
				// Handle dot notation (e.g., 'color.background')
				if (strpos($key, '.') !== false) {
					$parts = explode('.', $key);
					$parent = $parts[0];
					$child = $parts[1];

					if (!isset($blocksSettings[$blockName][$parent])) {
						$blocksSettings[$blockName][$parent] = [];
					}

					// In theme.json, false disables the feature
					$blocksSettings[$blockName][$parent][$child] = $value;
				} else {
					$blocksSettings[$blockName][$key] = $value;
				}
			}
		}

		$new_data = [
			'version' => $data['version'] ?? 2,
			'settings' => [
				'blocks' => $blocksSettings
			]
		];

		return $theme_json->update_with($new_data);
	}

	/**
	 * Filter block metadata settings to apply support overrides.
	 *
	 * This filter runs before the block is registered, affecting both
	 * PHP and JavaScript block registration.
	 *
	 * @param array $settings Array of block settings.
	 * @param array $metadata Block metadata from block.json.
	 * @return array Modified settings.
	 */
	public function filterBlockSupports(array $settings, array $metadata): array
	{
		// Only process Starboard blocks
		$blockName = $metadata['name'] ?? '';
		if (strpos($blockName, 'starboard/') !== 0) {
			return $settings;
		}

		// Get the block slug
		$slug = str_replace('starboard/', '', $blockName);

		// Get any saved support overrides
		$savedSupports = BlockSupports::getBlockSupports($slug);

		if (!$savedSupports) {
			return $settings;
		}

		// Get original supports from settings
		$originalSupports = $settings['supports'] ?? [];

		// Apply overrides - delete keys set to false, add keys set to true
		foreach ($savedSupports as $key => $value) {
			// Handle dot notation (e.g., 'color.background')
			if (strpos($key, '.') !== false) {
				$parts = explode('.', $key);
				$parent = $parts[0];
				$child = $parts[1];

				if ($value === false) {
					// Delete the nested key
					if (isset($originalSupports[$parent][$child])) {
						unset($originalSupports[$parent][$child]);
					}
					// If parent is now empty, remove it too
					if (isset($originalSupports[$parent]) && empty($originalSupports[$parent])) {
						unset($originalSupports[$parent]);
					}
				} else {
					// Set the nested key
					if (!isset($originalSupports[$parent])) {
						$originalSupports[$parent] = [];
					}
					$originalSupports[$parent][$child] = $value;
				}
			} else {
				// Top-level key
				if ($value === false) {
					unset($originalSupports[$key]);
				} else {
					$originalSupports[$key] = $value;
				}
			}
		}

		$settings['supports'] = $originalSupports;

		return $settings;
	}

	/**
	 * Add starboard-shared as a dependency for all Starboard block editor scripts.
	 *
	 * This ensures the @starboard/* externals are available before block scripts load.
	 * Modifies the script registration after WordPress sets it up from block.json.
	 *
	 * @param array $settings Block settings from metadata.
	 * @param array $metadata Block metadata from block.json.
	 * @return array Modified settings.
	 */
	public function addSharedDependency(array $settings, array $metadata): array
	{
		static $registeredHandles = [];

		$blockName = $metadata['name'] ?? '';
		if (strpos($blockName, 'starboard/') !== 0) {
			return $settings;
		}

		// Collect editor script handles to add dependency later
		if (!empty($settings['editor_script_handles'])) {
			foreach ($settings['editor_script_handles'] as $handle) {
				// Skip if we've already registered an action for this handle
				if (isset($registeredHandles[$handle])) {
					continue;
				}
				$registeredHandles[$handle] = true;

				// Use a late-running action to modify script deps after registration
				add_action('admin_enqueue_scripts', function () use ($handle) {
					global $wp_scripts;
					if (isset($wp_scripts->registered[$handle])) {
						if (!in_array('starboard-shared', $wp_scripts->registered[$handle]->deps, true)) {
							$wp_scripts->registered[$handle]->deps[] = 'starboard-shared';
						}
					}
				}, 0);
			}
		}

		return $settings;
	}
}
