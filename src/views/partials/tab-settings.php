<?php
/**
 * Settings tab content.
 *
 * @var bool $restrictAccess Whether access restriction is enabled
 * @var string $currentUserEmail Current user's email
 * @var bool $isAnchourUser Whether current user is an Anchour user
 */
?>

<?php if (isset($_GET['imported'])) : ?>
	<div class="notice notice-success is-dismissible"><p>Settings imported successfully.</p></div>
<?php endif; ?>

<?php if (isset($_GET['import_error'])) : ?>
	<div class="notice notice-error is-dismissible"><p><?= esc_html($_GET['import_error']); ?></p></div>
<?php endif; ?>

<div class="starboard-note">
	<strong>Access Control</strong><br>
	Restrict Starboard settings access to users with @anchour.com email addresses.
	<?php if ($restrictAccess && !$isAnchourUser) : ?>
		<br><br><strong>Note:</strong> You cannot modify these settings because access restriction is enabled and your email (<?= esc_html($currentUserEmail); ?>) is not an @anchour.com address.
	<?php endif; ?>
</div>

<form method="post" action="<?= esc_url(admin_url('admin-post.php')); ?>" class="starboard-settings-box">
	<?php wp_nonce_field('starboard-access-settings'); ?>
	<input type="hidden" name="action" value="starboard_access_settings">

	<h3>Email Restriction</h3>

	<p>
		<label style="display: flex; align-items: center; gap: 8px;">
			<input type="checkbox"
				   name="restrict_access"
				   value="1"
				   <?= checked($restrictAccess, true, false); ?>
				   <?= ($restrictAccess && !$isAnchourUser) ? 'disabled' : ''; ?>
			>
			<span>Only allow @anchour.com users to access Starboard settings</span>
		</label>
	</p>

	<p style="color: #646970; font-size: 13px;">
		When enabled, only WordPress users with an @anchour.com email address will be able to view and modify Starboard settings. Other administrators will see an access denied message.
	</p>

	<?php if ($isAnchourUser || !$restrictAccess) : ?>
		<p style="margin-bottom: 0;">
			<button type="submit" class="button button-primary">Save Access Settings</button>
		</p>
	<?php endif; ?>
</form>

<div class="starboard-user-info">
	<strong>Current User:</strong> <?= esc_html($currentUserEmail); ?><br>
	<strong>Status:</strong>
	<?php if ($isAnchourUser) : ?>
		<span style="color: #00a32a;">✓ Anchour user</span>
	<?php else : ?>
		<span style="color: #d63638;">✗ Not an Anchour user</span>
	<?php endif; ?>
</div>

<!-- Export/Import Settings -->
<div class="starboard-export-import">
	<h2>Export / Import Settings</h2>

	<div class="starboard-export-import-grid">
		<!-- Export -->
		<div class="starboard-export-box">
			<h3>Export</h3>
			<p style="color: #646970; font-size: 13px;">
				Download all Starboard settings as a JSON file. This includes enabled blocks, block supports, and text-image variants.
			</p>
			<p style="margin-bottom: 0;">
				<a href="<?= esc_url(wp_nonce_url(admin_url('admin-post.php?action=starboard_export'), 'starboard-export')); ?>"
				   class="button button-secondary">
					<span class="dashicons dashicons-download" style="vertical-align: middle; margin-right: 4px;"></span>
					Download Settings
				</a>
			</p>
		</div>

		<!-- Import -->
		<div class="starboard-import-box">
			<h3>Import</h3>
			<p style="color: #646970; font-size: 13px;">
				Upload a previously exported JSON file to restore Starboard settings.
			</p>
			<form method="post" action="<?= esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field('starboard-import'); ?>
				<input type="hidden" name="action" value="starboard_import">
				<p>
					<input type="file" name="import_file" accept=".json,application/json" required>
				</p>
				<p style="margin-bottom: 0;">
					<button type="submit" class="button button-secondary" onclick="return confirm('This will overwrite your current settings. Continue?');">
						<span class="dashicons dashicons-upload" style="vertical-align: middle; margin-right: 4px;"></span>
						Import Settings
					</button>
				</p>
			</form>
		</div>
	</div>

	<p style="margin-top: 12px; color: #646970; font-size: 12px;">
		<strong>Note:</strong> Import will merge with existing settings. Access restriction settings are not included in exports for security reasons.
	</p>
</div>
