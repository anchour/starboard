<?php
/**
 * Blocks tab content.
 *
 * @var array $blocks All available blocks
 * @var array $enabled Enabled block names
 * @var bool $canModifyTheme Whether theme can be modified
 * @var string $themeModifyDisabledReason Reason theme can't be modified
 */

defined('ABSPATH') || exit;

use Starboard\BlockLoader;
use Starboard\BlockUsage;
use Starboard\TextImageVariants;

$customVariants = TextImageVariants::getVariants();
$editSlug = isset($_GET['edit']) ? sanitize_title($_GET['edit']) : null;
$editVariant = $editSlug ? TextImageVariants::getVariant($editSlug) : null;
$isAdding = isset($_GET['add']);
$openBlock = isset($_GET['block']) ? sanitize_title($_GET['block']) : null;

// Get usage data
$refreshUsage = isset($_GET['refresh_usage']);
$usageData = BlockUsage::getUsage($refreshUsage);
$lastUpdated = $usageData['last_updated'] ?? null;
?>

<?php if (isset($_GET['saved'])) : ?>
	<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])) : ?>
	<div class="notice notice-success is-dismissible"><p>Variant deleted successfully.</p></div>
<?php endif; ?>

<?php if (isset($_GET['copied'])) : ?>
	<div class="notice notice-success is-dismissible">
		<p><strong>Block copied to theme!</strong> You can now edit it in your theme's <code>starboard/blocks/<?= esc_html($_GET['block'] ?? ''); ?>/</code> directory.</p>
	</div>
<?php endif; ?>

<?php if (isset($_GET['removed'])) : ?>
	<div class="notice notice-success is-dismissible"><p>Theme override removed. The plugin version will now be used.</p></div>
<?php endif; ?>

<?php if (isset($_GET['error'])) : ?>
	<div class="notice notice-error is-dismissible"><p><?= esc_html($_GET['error']); ?></p></div>
<?php endif; ?>

<?php if ($refreshUsage) : ?>
	<div class="notice notice-success is-dismissible"><p>Usage data refreshed.</p></div>
<?php endif; ?>

<?php if ($isAdding || $editVariant) : ?>
	<?php include __DIR__ . '/variant-form.php'; ?>
<?php else : ?>

	<div class="starboard-note">
		Enable or disable blocks and configure their settings. Blocks with <span class="starboard-badge starboard-badge-override">Theme Override</span> are managed at the theme level and cannot be disabled here.
	</div>

	<div style="margin-bottom: 16px; display: flex; gap: 8px; align-items: center;">
		<button type="button" id="enable-all-blocks" class="button">Enable All</button>
		<button type="button" id="disable-all-blocks" class="button">Disable All</button>
		<span style="margin-left: auto; color: #646970; font-size: 12px;">
			Usage data:
			<?php if ($lastUpdated) : ?>
				<?= esc_html(human_time_diff(strtotime($lastUpdated), current_time('timestamp'))); ?> ago
			<?php else : ?>
				never scanned
			<?php endif; ?>
			<a href="?page=starboard&tab=blocks&refresh_usage=1" style="margin-left: 4px;">Refresh</a>
		</span>
	</div>

	<form name="starboard-settings" method="post" action="<?= esc_url(admin_url('admin-post.php')); ?>">
		<?php wp_nonce_field('starboard-settings'); ?>
		<input type="hidden" name="action" value="starboard_settings">
		<input type="hidden" name="blocks_submitted" value="1">

		<?php foreach ($blocks as $block) : ?>
			<?php
			$name = explode('/', $block->name)[1];
			$slug = sanitize_title($name);
			$hasOverride = BlockLoader::hasThemeOverride($name);
			$isEnabled = in_array($block->name, $enabled) || $hasOverride;
			$isOpen = $openBlock === $slug;
			$usageCount = $usageData['blocks'][$block->name]['count'] ?? 0;
			$usagePosts = $usageData['blocks'][$block->name]['posts'] ?? [];
			$isInUse = $usageCount > 0;

			$panelClasses = 'starboard-block-panel';
			if ($isOpen) $panelClasses .= ' is-open';
			if (!$isEnabled) $panelClasses .= ' is-disabled';
			?>

			<div class="<?= $panelClasses; ?>" data-block="<?= $slug; ?>" data-usage="<?= $usageCount; ?>">
				<div class="starboard-block-header" onclick="this.parentElement.classList.toggle('is-open')">
					<div class="starboard-block-header-left">
						<input type="checkbox"
							   id="block-<?= $slug; ?>"
							   name="blocks[]"
							   value="<?= esc_attr($block->name); ?>"
							   <?= $isEnabled ? 'checked' : ''; ?>
							   <?= $hasOverride ? 'disabled' : ''; ?>
							   onclick="event.stopPropagation();"
							   onchange="handleBlockToggle(this, <?= $usageCount; ?>)"
						>
						<?php if ($hasOverride) : ?>
							<input type="hidden" name="blocks[]" value="<?= esc_attr($block->name); ?>">
						<?php endif; ?>
						<h3>
							<?= esc_html($block->title); ?>
							<?php if ($hasOverride) : ?>
								<span class="starboard-badge starboard-badge-override">Theme Override</span>
							<?php endif; ?>
							<?php if ($slug === 'text-image' && !empty($customVariants) && !$hasOverride) : ?>
								<span class="starboard-badge starboard-badge-custom"><?= count($customVariants); ?> custom variant<?= count($customVariants) !== 1 ? 's' : ''; ?></span>
							<?php endif; ?>
						</h3>
					</div>
					<div style="display: flex; align-items: center; gap: 12px;">
						<?php if ($isInUse) : ?>
							<span class="starboard-usage-badge" title="Used in <?= $usageCount; ?> place<?= $usageCount !== 1 ? 's' : ''; ?>">
								<?= $usageCount; ?> use<?= $usageCount !== 1 ? 's' : ''; ?>
							</span>
						<?php endif; ?>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
				</div>

				<div class="starboard-block-content">
					<p class="starboard-block-description"><?= esc_html($block->description); ?></p>

					<?php if ($isInUse) : ?>
						<?php
						// Group by post type
						$byType = [];
						foreach ($usagePosts as $post) {
							$type = $post['type'];
							if (!isset($byType[$type])) {
								$byType[$type] = 0;
							}
							$byType[$type]++;
						}
						?>
						<div class="starboard-usage-info">
							<strong>In use:</strong>
							<?php
							$searchParam = urlencode('<!-- wp:' . $block->name);
							$links = [];
							foreach ($byType as $type => $count) {
								$typeObj = get_post_type_object($type);
								$label = $typeObj ? ($count === 1 ? $typeObj->labels->singular_name : $typeObj->labels->name) : $type;
								$url = admin_url('edit.php?post_type=' . $type . '&s=' . $searchParam);
								$links[] = '<a href="' . esc_url($url) . '" target="_blank">' . $count . ' ' . esc_html(strtolower($label)) . '</a>';
							}
							echo implode(', ', $links);
							?>
						</div>
					<?php endif; ?>

					<?php if ($hasOverride) : ?>
						<p style="color: #646970; font-style: italic;">
							This block has a theme override. Settings are managed in your theme's <code>starboard/blocks/<?= $slug; ?>/</code> directory.
						</p>
						<p style="margin-top: 12px;">
							<?php if ($canModifyTheme) : ?>
								<a href="<?= esc_url(wp_nonce_url(admin_url('admin-post.php?action=starboard_remove_from_theme&block_slug=' . urlencode($slug)), 'starboard-remove-from-theme')); ?>"
								   class="button"
								   onclick="return confirm('Remove this theme override? The plugin version will be used instead.');">
									Remove Theme Override
								</a>
							<?php else : ?>
								<button type="button" class="button" disabled style="opacity: 0.6;">
									Remove Theme Override
								</button>
								<span style="margin-left: 8px; color: #d63638; font-size: 12px;">
									<?= esc_html($themeModifyDisabledReason); ?>
								</span>
							<?php endif; ?>
						</p>
					<?php elseif ($slug === 'text-image') : ?>
						<div class="starboard-settings-section">
							<h4>Custom Variants</h4>
							<p class="description">Add custom layout variants that appear alongside the built-in options (Narrow Content, Wide Image, Balanced).</p>

							<p style="margin-top: 12px;">
								<a href="?page=starboard&tab=blocks&block=text-image&add=1" class="button button-primary">Add New Variant</a>
							</p>

							<?php if (!empty($customVariants)) : ?>
								<div class="starboard-variants-list">
									<?php foreach ($customVariants as $variantSlug => $variant) : ?>
										<?php
										$variantUsageCount = $usageData['variants'][$variantSlug]['count'] ?? 0;
										$variantInUse = $variantUsageCount > 0;
										?>
										<div class="starboard-variant-item">
											<div class="starboard-variant-info">
												<h4>
													<?= esc_html($variant['title']); ?>
													<code style="font-weight: normal; margin-left: 8px; font-size: 11px;">text-image__<?= esc_html($variantSlug); ?></code>
													<?php if ($variantInUse) : ?>
														<span class="starboard-usage-badge starboard-usage-badge-small"><?= $variantUsageCount; ?> use<?= $variantUsageCount !== 1 ? 's' : ''; ?></span>
													<?php endif; ?>
												</h4>
												<?php if (!empty($variant['description'])) : ?>
													<p><?= esc_html($variant['description']); ?></p>
												<?php endif; ?>
											</div>
											<div class="starboard-variant-actions">
												<a href="?page=starboard&tab=blocks&block=text-image&edit=<?= esc_attr($variantSlug); ?>" class="button button-small">Edit</a>
												<?php if ($variantInUse) : ?>
													<button type="button" class="button button-small" disabled title="Cannot delete: variant is in use" style="opacity: 0.6;">Delete</button>
												<?php else : ?>
													<a href="<?= esc_url(wp_nonce_url(admin_url('admin-post.php?action=starboard_delete_variant&variant_slug=' . urlencode($variantSlug)), 'starboard-delete-variant')); ?>"
													   class="button button-small"
													   onclick="return confirm('Are you sure you want to delete this variant?');">Delete</a>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<p style="color: #646970; font-style: italic;">No additional settings available for this block.</p>
					<?php endif; ?>

					<?php if (!$hasOverride) : ?>
						<div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0;">
							<?php if ($canModifyTheme) : ?>
								<a href="<?= esc_url(wp_nonce_url(admin_url('admin-post.php?action=starboard_copy_to_theme&block_slug=' . urlencode($slug)), 'starboard-copy-to-theme')); ?>"
								   class="button button-secondary">
									Copy to Theme
								</a>
								<span style="margin-left: 8px; color: #646970; font-size: 12px;">
									Copy this block to your theme for customization
								</span>
							<?php else : ?>
								<button type="button" class="button button-secondary" disabled style="opacity: 0.6;">
									Copy to Theme
								</button>
								<span style="margin-left: 8px; color: #d63638; font-size: 12px;">
									<?= esc_html($themeModifyDisabledReason); ?>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<p style="margin-top: 20px;">
			<button type="submit" class="button button-primary">Save Changes</button>
		</p>
	</form>

	<script>
		function handleBlockToggle(checkbox, usageCount) {
			const panel = checkbox.closest('.starboard-block-panel');
			const isDisabling = !checkbox.checked;

			if (isDisabling && usageCount > 0) {
				const confirmed = confirm(
					'Warning: This block is currently used in ' + usageCount + ' place' + (usageCount !== 1 ? 's' : '') + '.\n\n' +
					'Disabling it will prevent it from being added to new posts, but existing uses will continue to work.\n\n' +
					'Are you sure you want to disable this block?'
				);

				if (!confirmed) {
					checkbox.checked = true;
					return;
				}
			}

			panel.classList.toggle('is-disabled', !checkbox.checked);
		}

		document.getElementById('enable-all-blocks').addEventListener('click', function() {
			document.querySelectorAll('.starboard-block-panel input[type="checkbox"][name="blocks[]"]:not(:disabled)').forEach(function(cb) {
				cb.checked = true;
				cb.closest('.starboard-block-panel').classList.remove('is-disabled');
			});
		});

		document.getElementById('disable-all-blocks').addEventListener('click', function() {
			const panels = document.querySelectorAll('.starboard-block-panel[data-usage]');
			let totalUsage = 0;

			panels.forEach(function(panel) {
				totalUsage += parseInt(panel.dataset.usage || 0, 10);
			});

			if (totalUsage > 0) {
				const confirmed = confirm(
					'Warning: Some blocks are currently in use across your site.\n\n' +
					'Disabling them will prevent them from being added to new posts, but existing uses will continue to work.\n\n' +
					'Are you sure you want to disable all blocks?'
				);

				if (!confirmed) {
					return;
				}
			}

			document.querySelectorAll('.starboard-block-panel input[type="checkbox"][name="blocks[]"]:not(:disabled)').forEach(function(cb) {
				cb.checked = false;
				cb.closest('.starboard-block-panel').classList.add('is-disabled');
			});
		});
	</script>

<?php endif; ?>
