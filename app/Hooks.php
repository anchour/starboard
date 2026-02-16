<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Handles admin form submissions and actions.
 */
final class Hooks
{
	private function __construct()
	{
		add_action('admin_post_starboard_settings', [$this, 'saveSettings']);
		add_action('admin_post_starboard_block_supports', [$this, 'saveBlockSupports']);
		add_action('admin_post_starboard_access_settings', [$this, 'saveAccessSettings']);
		add_action('admin_post_starboard_save_variant', [$this, 'saveVariant']);
		add_action('admin_post_starboard_delete_variant', [$this, 'deleteVariant']);
		add_action('admin_post_starboard_copy_to_theme', [$this, 'copyBlockToTheme']);
		add_action('admin_post_starboard_remove_from_theme', [$this, 'removeBlockFromTheme']);
		add_action('admin_post_starboard_export', [$this, 'exportSettings']);
		add_action('admin_post_starboard_import', [$this, 'importSettings']);
	}

	public static function init(): void
	{
		new self();
	}

	/**
	 * Verify user has permission to manage options.
	 * Dies with error if unauthorized.
	 *
	 * @param string $action The action being performed (for logging)
	 * @return void
	 */
	private function requireManageOptions(string $action): void
	{
		if (!current_user_can('manage_options')) {
			self::log("Unauthorized access attempt: {$action}", 'warning');
			wp_die('Unauthorized', 'Unauthorized', ['response' => 403]);
		}
	}

	/**
	 * Verify a POST nonce.
	 * Dies with error if invalid.
	 *
	 * @param string $action The nonce action name
	 * @return void
	 */
	private function verifyPostNonce(string $action): void
	{
		check_admin_referer($action);
	}

	/**
	 * Verify a GET nonce (from wp_nonce_url).
	 * Dies with error if invalid.
	 *
	 * @param string $action The nonce action name
	 * @return void
	 */
	private function verifyGetNonce(string $action): void
	{
		if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', $action)) {
			self::log("Invalid nonce for action: {$action}", 'warning');
			wp_die('Invalid security token', 'Security Error', ['response' => 403]);
		}
	}

	/**
	 * Sanitize an array of block names.
	 *
	 * @param array $blocks Raw block names array
	 * @return array Sanitized block names
	 */
	private function sanitizeBlocksArray(array $blocks): array
	{
		return array_filter(
			array_map('sanitize_text_field', $blocks),
			function ($block) {
				// Only allow valid block name format: namespace/block-name
				return preg_match('/^[a-z0-9-]+\/[a-z0-9-]+$/', $block);
			}
		);
	}

	/**
	 * Save block enable/disable settings.
	 *
	 * @return void
	 */
	public function saveSettings(): void
	{
		$this->requireManageOptions('saveSettings');
		$this->verifyPostNonce('starboard-settings');

		if (isset($_POST['blocks_submitted'])) {
			$rawBlocks = $_POST['blocks'] ?? [];
			$blocks = is_array($rawBlocks) ? $this->sanitizeBlocksArray($rawBlocks) : [];
			update_option('starboard_blocks', $blocks);
			self::log('Block settings saved: ' . count($blocks) . ' blocks enabled', 'info');
		}

		wp_redirect(admin_url('admin.php?page=starboard&saved=1'));
		exit;
	}

	/**
	 * Save block support settings.
	 *
	 * @return void
	 */
	public function saveBlockSupports(): void
	{
		$this->requireManageOptions('saveBlockSupports');
		$this->verifyPostNonce('starboard-block-supports');

		$blockSlug = sanitize_text_field($_POST['block_slug'] ?? '');
		$reset = isset($_POST['reset']);

		if (empty($blockSlug) || !ThemeOverride::isValidSlug($blockSlug)) {
			self::log('Invalid block slug in saveBlockSupports: ' . $blockSlug, 'warning');
			wp_die('Invalid block', 'Invalid Request', ['response' => 400]);
		}

		if ($reset) {
			BlockSupports::clearBlockSupports($blockSlug);
			self::log("Block supports reset: {$blockSlug}", 'info');
		} else {
			$supports = $_POST['supports'] ?? [];
			$filteredSupports = [];

			if (is_array($supports)) {
				foreach ($supports as $key => $value) {
					$sanitizedKey = sanitize_text_field($key);

					// Skip empty values (Default)
					if ($value === '') {
						continue;
					}

					// Convert string '0' and '1' to boolean
					if ($value === '1') {
						$filteredSupports[$sanitizedKey] = true;
					} elseif ($value === '0') {
						$filteredSupports[$sanitizedKey] = false;
					} else {
						$filteredSupports[$sanitizedKey] = sanitize_text_field($value);
					}
				}
			}

			if (!empty($filteredSupports)) {
				BlockSupports::saveBlockSupports($blockSlug, $filteredSupports);
				self::log("Block supports saved: {$blockSlug}", 'info');
			} else {
				BlockSupports::clearBlockSupports($blockSlug);
				self::log("Block supports cleared (all defaults): {$blockSlug}", 'info');
			}
		}

		wp_redirect(admin_url('admin.php?page=starboard&tab=supports'));
		exit;
	}

	/**
	 * Save access control settings.
	 *
	 * @return void
	 */
	public function saveAccessSettings(): void
	{
		$this->requireManageOptions('saveAccessSettings');
		$this->verifyPostNonce('starboard-access-settings');

		$currentUser = wp_get_current_user();
		$isAnchourUser = AccessControl::isAnchourEmail($currentUser->user_email ?? '');
		$restrictionEnabled = AccessControl::isRestrictionEnabled();

		if ($restrictionEnabled && !$isAnchourUser) {
			self::log('Non-Anchour user attempted to modify access settings: ' . $currentUser->user_email, 'warning');
			wp_die('You cannot modify access settings because you are not an @anchour.com user.');
		}

		$restrictAccess = isset($_POST['restrict_access']) && $_POST['restrict_access'] === '1';
		AccessControl::setRestriction($restrictAccess);
		self::log('Access restriction ' . ($restrictAccess ? 'enabled' : 'disabled') . ' by ' . $currentUser->user_email, 'info');

		wp_redirect(admin_url('admin.php?page=starboard&tab=settings'));
		exit;
	}

	/**
	 * Save a text-image variant.
	 *
	 * @return void
	 */
	public function saveVariant(): void
	{
		$this->requireManageOptions('saveVariant');
		$this->verifyPostNonce('starboard-save-variant');

		$slug = sanitize_title($_POST['variant_slug'] ?? '');

		if (empty($slug)) {
			self::log('Empty variant slug in saveVariant', 'warning');
			wp_die('Invalid variant slug', 'Invalid Request', ['response' => 400]);
		}

		$validated = TextImageVariants::validateVariant($_POST);
		TextImageVariants::saveVariant($slug, $validated);
		self::log("Variant saved: {$slug}", 'info');

		wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&block=text-image&saved=1'));
		exit;
	}

	/**
	 * Delete a text-image variant.
	 *
	 * @return void
	 */
	public function deleteVariant(): void
	{
		$this->requireManageOptions('deleteVariant');
		$this->verifyGetNonce('starboard-delete-variant');

		$slug = sanitize_title($_GET['variant_slug'] ?? '');

		if (empty($slug)) {
			self::log('Empty variant slug in deleteVariant', 'warning');
			wp_die('Invalid variant slug', 'Invalid Request', ['response' => 400]);
		}

		TextImageVariants::deleteVariant($slug);
		self::log("Variant deleted: {$slug}", 'info');

		wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&block=text-image&deleted=1'));
		exit;
	}

	/**
	 * Copy a block to the theme for customization.
	 *
	 * @return void
	 */
	public function copyBlockToTheme(): void
	{
		$this->requireManageOptions('copyBlockToTheme');
		$this->verifyGetNonce('starboard-copy-to-theme');

		$slug = sanitize_title($_GET['block_slug'] ?? '');

		if (empty($slug) || !ThemeOverride::isValidSlug($slug)) {
			self::log('Invalid block slug in copyBlockToTheme: ' . $slug, 'warning');
			wp_die('Invalid block slug', 'Invalid Request', ['response' => 400]);
		}

		$result = ThemeOverride::copyBlockToTheme($slug);

		if ($result['success']) {
			wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&copied=1&block=' . $slug));
		} else {
			wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&error=' . urlencode($result['message'])));
		}
		exit;
	}

	/**
	 * Remove a block override from the theme.
	 *
	 * @return void
	 */
	public function removeBlockFromTheme(): void
	{
		$this->requireManageOptions('removeBlockFromTheme');
		$this->verifyGetNonce('starboard-remove-from-theme');

		$slug = sanitize_title($_GET['block_slug'] ?? '');

		if (empty($slug) || !ThemeOverride::isValidSlug($slug)) {
			self::log('Invalid block slug in removeBlockFromTheme: ' . $slug, 'warning');
			wp_die('Invalid block slug', 'Invalid Request', ['response' => 400]);
		}

		$result = ThemeOverride::removeBlockFromTheme($slug);

		if ($result['success']) {
			wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&removed=1'));
		} else {
			wp_redirect(admin_url('admin.php?page=starboard&tab=blocks&error=' . urlencode($result['message'])));
		}
		exit;
	}

	/**
	 * Export all Starboard settings as JSON download.
	 *
	 * @return void
	 */
	public function exportSettings(): void
	{
		$this->requireManageOptions('exportSettings');
		$this->verifyGetNonce('starboard-export');

		$result = ExportImport::export();

		if (!$result['success']) {
			self::log('Export failed: ' . ($result['message'] ?? 'Unknown error'), 'error');
			wp_die($result['message'] ?? 'Export failed', 'Export Error', ['response' => 500]);
		}

		$filename = ExportImport::getExportFilename();

		// Send as JSON file download
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . strlen($result['data']));
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		echo $result['data'];

		self::log('Settings exported successfully', 'info');
		exit;
	}

	/**
	 * Import Starboard settings from uploaded JSON file.
	 *
	 * @return void
	 */
	public function importSettings(): void
	{
		$this->requireManageOptions('importSettings');
		$this->verifyPostNonce('starboard-import');

		// Check for uploaded file
		if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
			$errorMessage = $this->getUploadErrorMessage($_FILES['import_file']['error'] ?? UPLOAD_ERR_NO_FILE);
			wp_redirect(admin_url('admin.php?page=starboard&tab=settings&import_error=' . urlencode($errorMessage)));
			exit;
		}

		$file = $_FILES['import_file'];

		// Validate file type
		$mimeType = mime_content_type($file['tmp_name']);
		if (!in_array($mimeType, ['application/json', 'text/plain', 'text/json'], true)) {
			wp_redirect(admin_url('admin.php?page=starboard&tab=settings&import_error=' . urlencode('Invalid file type. Please upload a JSON file.')));
			exit;
		}

		// Read file contents
		$json = file_get_contents($file['tmp_name']);
		if ($json === false) {
			wp_redirect(admin_url('admin.php?page=starboard&tab=settings&import_error=' . urlencode('Failed to read uploaded file.')));
			exit;
		}

		// Import settings
		$result = ExportImport::import($json);

		if ($result['success']) {
			self::log('Settings imported: ' . implode(', ', $result['imported'] ?? []), 'info');
			wp_redirect(admin_url('admin.php?page=starboard&tab=settings&imported=1'));
		} else {
			self::log('Import failed: ' . $result['message'], 'warning');
			wp_redirect(admin_url('admin.php?page=starboard&tab=settings&import_error=' . urlencode($result['message'])));
		}
		exit;
	}

	/**
	 * Get human-readable upload error message.
	 *
	 * @param int $errorCode
	 * @return string
	 */
	private function getUploadErrorMessage(int $errorCode): string
	{
		$messages = [
			UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit.',
			UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit.',
			UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
			UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
			UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary folder.',
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
			UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
		];

		return $messages[$errorCode] ?? 'Unknown upload error.';
	}

	/**
	 * Log a message for debugging.
	 *
	 * @param string $message The message to log
	 * @param string $level Log level: 'info', 'warning', 'error'
	 * @return void
	 */
	private static function log(string $message, string $level = 'info'): void
	{
		if (!defined('WP_DEBUG') || !WP_DEBUG) {
			return;
		}

		$prefix = '[Starboard Hooks]';
		$levelTag = strtoupper($level);
		error_log("{$prefix} [{$levelTag}] {$message}");
	}
}
