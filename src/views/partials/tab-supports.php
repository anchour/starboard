<?php
/**
 * Block Supports tab content.
 *
 * @var array $blocks All available blocks
 * @var array $allSupports Available support options
 * @var array $categories Support categories
 * @var array $savedSupports Saved support configurations
 */

defined('ABSPATH') || exit;

use Starboard\BlockLoader;
use Starboard\BlockSupports;
?>

<div class="starboard-note">
	<strong>Note:</strong> Block supports control which editing features are available for each block.
	<strong>On</strong> enables a feature, <strong>Off</strong> disables it, <strong>Default</strong> uses the block's built-in setting.
</div>

<?php foreach ($blocks as $block) : ?>
	<?php
	$blockSlug = explode('/', $block->name)[1];
	$hasOverride = BlockLoader::hasThemeOverride($blockSlug);
	$hasSavedSupports = isset($savedSupports[$blockSlug]);
	$blockJsonSupports = BlockSupports::getBlockJsonSupports($blockSlug);
	$currentSupports = $savedSupports[$blockSlug] ?? [];
	?>

	<?php if ($hasOverride) : ?>
		<div class="starboard-supports-block">
			<div class="starboard-supports-header" onclick="this.parentElement.classList.toggle('is-open')">
				<h3>
					<?= esc_html($block->title); ?>
					<span class="starboard-badge starboard-badge-override">Theme Override</span>
				</h3>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class="starboard-supports-content">
				<p style="color: #646970; font-style: italic; margin: 0;">
					This block has a theme override. Edit the <code>block.json</code> file in your theme's <code>starboard/blocks/<?= esc_html($blockSlug); ?>/</code> directory to customize supports.
				</p>
			</div>
		</div>
	<?php else : ?>
		<form method="post" action="<?= esc_url(admin_url('admin-post.php')); ?>" class="starboard-supports-block">
			<?php wp_nonce_field('starboard-block-supports'); ?>
			<input type="hidden" name="action" value="starboard_block_supports">
			<input type="hidden" name="block_slug" value="<?= esc_attr($blockSlug); ?>">

			<div class="starboard-supports-header" onclick="this.parentElement.classList.toggle('is-open')">
				<h3>
					<?= esc_html($block->title); ?>
					<?php if ($hasSavedSupports) : ?>
						<span class="starboard-badge starboard-badge-default">Customized</span>
					<?php endif; ?>
				</h3>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</div>

			<div class="starboard-supports-content">
			<?php foreach ($categories as $catKey => $catLabel) : ?>
				<?php
				$catSupports = array_filter($allSupports, function($support) use ($catKey) {
					return $support['category'] === $catKey;
				});

				if (empty($catSupports)) continue;
				?>

				<div class="starboard-category" data-category="<?= esc_attr($catKey); ?>">
					<div class="starboard-category-header">
						<h4><?= esc_html($catLabel); ?></h4>
						<div class="starboard-category-actions">
							<button type="button" class="starboard-cat-all-on">All On</button>
							<button type="button" class="starboard-cat-all-off">All Off</button>
							<button type="button" class="starboard-cat-reset">Reset</button>
						</div>
					</div>

					<?php foreach ($catSupports as $supportKey => $supportDef) : ?>
						<?php
						$blockDefault = $blockJsonSupports[$supportKey] ?? null;
						$savedValue = $currentSupports[$supportKey] ?? null;
						$currentValue = $savedValue ?? '';
						$hasBlockDefault = $blockDefault !== null;
						?>

						<div class="starboard-support-row">
							<div class="starboard-support-label">
								<?= esc_html($supportDef['label']); ?>
							</div>

							<div class="starboard-support-control">
								<?php if ($supportDef['type'] === 'boolean') : ?>
									<select name="supports[<?= esc_attr($supportKey); ?>]">
										<option value="">Default<?= $hasBlockDefault ? ' (' . ($blockDefault ? 'On' : 'Off') . ')' : ''; ?></option>
										<option value="1" <?= $currentValue === '1' || $currentValue === true ? 'selected' : ''; ?>>On</option>
										<option value="0" <?= $currentValue === '0' || $currentValue === false ? 'selected' : ''; ?>>Off</option>
									</select>
								<?php elseif ($supportDef['type'] === 'select') : ?>
									<select name="supports[<?= esc_attr($supportKey); ?>]">
										<option value="">Default</option>
										<?php foreach ($supportDef['options'] as $optValue => $optLabel) : ?>
											<option value="<?= esc_attr($optValue); ?>" <?= $currentValue === $optValue ? 'selected' : ''; ?>>
												<?= esc_html($optLabel); ?>
											</option>
										<?php endforeach; ?>
									</select>
								<?php endif; ?>
							</div>

							<div class="starboard-support-desc">
								<?= esc_html($supportDef['description']); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<div class="starboard-supports-actions">
				<button type="submit" class="button button-primary">Save Supports</button>
				<button type="submit" name="reset" value="1" class="button">Reset to Defaults</button>
			</div>
		</div>
	</form>
	<?php endif; ?>
<?php endforeach; ?>

<script>
	document.querySelectorAll('.starboard-category').forEach(category => {
		const selects = category.querySelectorAll('.starboard-support-control select');

		category.querySelector('.starboard-cat-all-on')?.addEventListener('click', () => {
			selects.forEach(select => {
				const options = Array.from(select.options);
				const onOption = options.find(opt => opt.value === '1');
				if (onOption) {
					select.value = '1';
				} else {
					const trueOption = options.find(opt => opt.value === 'true');
					if (trueOption) {
						select.value = 'true';
					}
				}
			});
		});

		category.querySelector('.starboard-cat-all-off')?.addEventListener('click', () => {
			selects.forEach(select => {
				const options = Array.from(select.options);
				const offOption = options.find(opt => opt.value === '0');
				if (offOption) {
					select.value = '0';
				} else {
					const falseOption = options.find(opt => opt.value === 'false');
					if (falseOption) {
						select.value = 'false';
					}
				}
			});
		});

		category.querySelector('.starboard-cat-reset')?.addEventListener('click', () => {
			selects.forEach(select => {
				select.value = '';
			});
		});
	});
</script>
