<?php
/**
 * The settings page for the Starboard plugin.
 */

defined('ABSPATH') || exit;

use Starboard\GetBlocks;
use Starboard\BlockSupports;
use Starboard\AccessControl;
use Starboard\ThemeOverride;

// Common data for all tabs
$blocks = GetBlocks::run();
$enabled = GetBlocks::enabled();
$currentTab = sanitize_key($_GET['tab'] ?? 'blocks');

// Access control data
$restrictAccess = AccessControl::isRestrictionEnabled();
$currentUserEmail = wp_get_current_user()->user_email ?? '';
$isAnchourUser = AccessControl::isAnchourEmail($currentUserEmail);

// Theme modification data
$canModifyTheme = ThemeOverride::canModifyTheme();
$themeModifyDisabledReason = ThemeOverride::getDisabledReason();

// Block supports data (for supports tab)
$allSupports = BlockSupports::getAvailableSupports();
$categories = BlockSupports::getCategories();
$savedSupports = BlockSupports::getAllBlockSupports();
?>

<div class="wrap">
	<h1>Starboard Settings</h1>

	<nav class="nav-tab-wrapper" style="margin-bottom: 20px;">
		<a href="?page=starboard&tab=blocks"
		   class="nav-tab <?= $currentTab === 'blocks' ? 'nav-tab-active' : ''; ?>">
			Blocks
		</a>
		<a href="?page=starboard&tab=supports"
		   class="nav-tab <?= $currentTab === 'supports' ? 'nav-tab-active' : ''; ?>">
			Block Supports
		</a>
		<a href="?page=starboard&tab=docs"
		   class="nav-tab <?= $currentTab === 'docs' ? 'nav-tab-active' : ''; ?>">
			Documentation
		</a>
		<a href="?page=starboard&tab=settings"
		   class="nav-tab <?= $currentTab === 'settings' ? 'nav-tab-active' : ''; ?>">
			Settings
		</a>
	</nav>

	<?php if ($currentTab === 'blocks') : ?>
		<?php include __DIR__ . '/partials/tab-blocks.php'; ?>

	<?php elseif ($currentTab === 'supports') : ?>
		<?php include __DIR__ . '/partials/tab-supports.php'; ?>

	<?php elseif ($currentTab === 'docs') : ?>
		<?php include __DIR__ . '/partials/tab-docs.php'; ?>

	<?php elseif ($currentTab === 'settings') : ?>
		<?php include __DIR__ . '/partials/tab-settings.php'; ?>

	<?php endif; ?>
</div>
