<?php
/**
 * Text/Image Variant Add/Edit Form
 */

defined('ABSPATH') || exit;
?>
<style>
	.starboard-variant-form {
		background: #fff;
		border: 1px solid #c3c4c7;
		padding: 24px;
		max-width: 800px;
	}
	.starboard-variant-form h3 {
		margin-top: 0;
	}
	.starboard-grid-inputs {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 24px;
		margin: 24px 0;
	}
	.starboard-breakpoint-section {
		background: #f6f7f7;
		border: 1px solid #c3c4c7;
		padding: 16px;
	}
	.starboard-breakpoint-section h4 {
		margin: 0 0 16px 0;
		padding-bottom: 8px;
		border-bottom: 1px solid #c3c4c7;
		font-size: 13px;
	}
	.starboard-grid-row {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 12px;
		margin-bottom: 12px;
	}
	.starboard-grid-row label {
		display: block;
		font-size: 12px;
		color: #646970;
		margin-bottom: 4px;
	}
	.starboard-grid-row input {
		width: 100%;
	}
	.starboard-column-group {
		margin-bottom: 16px;
	}
	.starboard-column-group h5 {
		margin: 0 0 8px 0;
		font-size: 11px;
		text-transform: uppercase;
		color: #50575e;
	}
	.starboard-grid-preview {
		display: grid;
		grid-template-columns: repeat(12, 1fr);
		gap: 2px;
		height: 32px;
		margin-top: 12px;
		background: #e0e0e0;
	}
	.starboard-grid-preview-cell {
		background: #f6f7f7;
	}
	.starboard-grid-preview-copy {
		background: #2271b1;
	}
	.starboard-grid-preview-img {
		background: #72aee6;
	}
</style>

<form method="post" action="<?= esc_url(admin_url('admin-post.php')); ?>" class="starboard-variant-form">
	<?php wp_nonce_field('starboard-save-variant'); ?>
	<input type="hidden" name="action" value="starboard_save_variant">
	<?php if ($editSlug) : ?>
		<input type="hidden" name="variant_slug" value="<?= esc_attr($editSlug); ?>">
	<?php endif; ?>

	<h3><?= $editVariant ? 'Edit Variant' : 'Add New Text/Image Variant'; ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="variant_slug">Slug</label></th>
			<td>
				<?php if ($editSlug) : ?>
					<code><?= esc_html($editSlug); ?></code>
				<?php else : ?>
					<input type="text" id="variant_slug" name="variant_slug" required
						   pattern="[a-z0-9-]+" placeholder="my-custom-variant"
						   style="width: 300px;">
					<p class="description">Lowercase letters, numbers, and hyphens only.</p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="title">Title</label></th>
			<td>
				<input type="text" id="title" name="title" required
					   value="<?= esc_attr($editVariant['title'] ?? ''); ?>"
					   style="width: 300px;">
			</td>
		</tr>
		<tr>
			<th><label for="description">Description</label></th>
			<td>
				<input type="text" id="description" name="description"
					   value="<?= esc_attr($editVariant['description'] ?? ''); ?>"
					   style="width: 300px;">
				<p class="description">Optional description shown in block picker.</p>
			</td>
		</tr>
	</table>

	<h4>Grid Layout (12-column grid)</h4>
	<p class="description">Configure how the copy and image columns are positioned at each breakpoint. Values are for "Copy Left" layout; "Copy Right" is calculated automatically.</p>

	<div class="starboard-grid-inputs">
		<?php foreach (['lg' => 'Large (1024px+)', 'xl' => 'Extra Large (1280px+)'] as $bp => $bpLabel) : ?>
			<div class="starboard-breakpoint-section" data-breakpoint="<?= $bp; ?>">
				<h4><?= $bpLabel; ?></h4>

				<div class="starboard-column-group">
					<h5>Copy Column</h5>
					<div class="starboard-grid-row">
						<div>
							<label for="<?= $bp; ?>_copy_start">Start</label>
							<input type="number" id="<?= $bp; ?>_copy_start" name="<?= $bp; ?>_copy_start"
								   min="1" max="12"
								   value="<?= esc_attr($editVariant[$bp]['copy']['start'] ?? ($bp === 'xl' ? 2 : 1)); ?>"
								   class="grid-input" data-type="copy-start">
						</div>
						<div>
							<label for="<?= $bp; ?>_copy_span">Span</label>
							<input type="number" id="<?= $bp; ?>_copy_span" name="<?= $bp; ?>_copy_span"
								   min="1" max="12"
								   value="<?= esc_attr($editVariant[$bp]['copy']['span'] ?? ($bp === 'xl' ? 4 : 5)); ?>"
								   class="grid-input" data-type="copy-span">
						</div>
					</div>
				</div>

				<div class="starboard-column-group">
					<h5>Image Column</h5>
					<div class="starboard-grid-row">
						<div>
							<label for="<?= $bp; ?>_img_start">Start</label>
							<input type="number" id="<?= $bp; ?>_img_start" name="<?= $bp; ?>_img_start"
								   min="1" max="12"
								   value="<?= esc_attr($editVariant[$bp]['img']['start'] ?? 7); ?>"
								   class="grid-input" data-type="img-start">
						</div>
						<div>
							<label for="<?= $bp; ?>_img_span">Span</label>
							<input type="number" id="<?= $bp; ?>_img_span" name="<?= $bp; ?>_img_span"
								   min="1" max="12"
								   value="<?= esc_attr($editVariant[$bp]['img']['span'] ?? ($bp === 'xl' ? 5 : 6)); ?>"
								   class="grid-input" data-type="img-span">
						</div>
					</div>
				</div>

				<div class="starboard-grid-preview" data-bp="<?= $bp; ?>">
					<?php for ($i = 1; $i <= 12; $i++) : ?>
						<div class="starboard-grid-preview-cell" data-col="<?= $i; ?>"></div>
					<?php endfor; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="submit" class="button button-primary">Save Variant</button>
		<a href="?page=starboard&tab=blocks&block=text-image" class="button">Cancel</a>
	</p>
</form>

<script>
	// Live preview of grid layout
	function updateGridPreview(bp) {
		const section = document.querySelector(`[data-breakpoint="${bp}"]`);
		const preview = section.querySelector('.starboard-grid-preview');
		const cells = preview.querySelectorAll('.starboard-grid-preview-cell');

		const copyStart = parseInt(section.querySelector('[data-type="copy-start"]').value) || 1;
		const copySpan = parseInt(section.querySelector('[data-type="copy-span"]').value) || 1;
		const imgStart = parseInt(section.querySelector('[data-type="img-start"]').value) || 1;
		const imgSpan = parseInt(section.querySelector('[data-type="img-span"]').value) || 1;

		cells.forEach((cell, i) => {
			const col = i + 1;
			cell.classList.remove('starboard-grid-preview-copy', 'starboard-grid-preview-img');

			if (col >= copyStart && col < copyStart + copySpan) {
				cell.classList.add('starboard-grid-preview-copy');
			}
			if (col >= imgStart && col < imgStart + imgSpan) {
				cell.classList.add('starboard-grid-preview-img');
			}
		});
	}

	// Initialize previews
	['lg', 'xl'].forEach(bp => {
		updateGridPreview(bp);

		const section = document.querySelector(`[data-breakpoint="${bp}"]`);
		section.querySelectorAll('.grid-input').forEach(input => {
			input.addEventListener('input', () => updateGridPreview(bp));
		});
	});
</script>
