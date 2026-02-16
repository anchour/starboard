<?php

namespace Starboard\Tests\Unit;

use Starboard\ThemeOverride;
use WP_UnitTestCase;

/**
 * Tests for the ThemeOverride class.
 */
class ThemeOverrideTest extends WP_UnitTestCase
{
	/**
	 * Test valid slug validation.
	 */
	public function test_valid_slugs()
	{
		$this->assertTrue(ThemeOverride::isValidSlug('text-image'));
		$this->assertTrue(ThemeOverride::isValidSlug('faq'));
		$this->assertTrue(ThemeOverride::isValidSlug('my_block'));
		$this->assertTrue(ThemeOverride::isValidSlug('block123'));
		$this->assertTrue(ThemeOverride::isValidSlug('ABC'));
	}

	/**
	 * Test invalid slug validation (path traversal prevention).
	 */
	public function test_invalid_slugs()
	{
		$this->assertFalse(ThemeOverride::isValidSlug('../malicious'));
		$this->assertFalse(ThemeOverride::isValidSlug('block/subdir'));
		$this->assertFalse(ThemeOverride::isValidSlug('..'));
		$this->assertFalse(ThemeOverride::isValidSlug('.htaccess'));
		$this->assertFalse(ThemeOverride::isValidSlug(''));
		$this->assertFalse(ThemeOverride::isValidSlug(' '));
		$this->assertFalse(ThemeOverride::isValidSlug('block name'));
	}

	/**
	 * Test getThemeBlocksPath returns expected path format.
	 */
	public function test_get_theme_blocks_path_format()
	{
		$path = ThemeOverride::getThemeBlocksPath();

		$this->assertStringEndsWith('/starboard/blocks/', $path);
		$this->assertStringContainsString(get_stylesheet_directory(), $path);
	}

	/**
	 * Test copyBlockToTheme rejects invalid slugs.
	 */
	public function test_copy_block_rejects_invalid_slug()
	{
		$result = ThemeOverride::copyBlockToTheme('../malicious');

		$this->assertFalse($result['success']);
		$this->assertStringContainsString('Invalid', $result['message']);
	}

	/**
	 * Test copyBlockToTheme handles missing block.
	 */
	public function test_copy_block_handles_missing_block()
	{
		$result = ThemeOverride::copyBlockToTheme('nonexistent-block-xyz');

		$this->assertFalse($result['success']);
		$this->assertStringContainsString('not found', $result['message']);
	}

	/**
	 * Test removeBlockFromTheme rejects invalid slugs.
	 */
	public function test_remove_block_rejects_invalid_slug()
	{
		$result = ThemeOverride::removeBlockFromTheme('../malicious');

		$this->assertFalse($result['success']);
		$this->assertStringContainsString('Invalid', $result['message']);
	}

	/**
	 * Test removeBlockFromTheme handles non-existent override.
	 */
	public function test_remove_block_handles_non_existent()
	{
		$result = ThemeOverride::removeBlockFromTheme('nonexistent-override');

		$this->assertFalse($result['success']);
		$this->assertStringContainsString('not found', $result['message']);
	}

	/**
	 * Test canModifyTheme returns boolean.
	 */
	public function test_can_modify_theme_returns_boolean()
	{
		$result = ThemeOverride::canModifyTheme();

		$this->assertIsBool($result);
	}

	/**
	 * Test getDisabledReason returns null or string.
	 */
	public function test_get_disabled_reason_type()
	{
		$reason = ThemeOverride::getDisabledReason();

		$this->assertTrue(
			is_null($reason) || is_string($reason),
			'getDisabledReason should return null or string'
		);
	}

	/**
	 * Test DISALLOW_FILE_MODS check.
	 */
	public function test_disallow_file_mods_check()
	{
		// If DISALLOW_FILE_MODS is defined and true, canModifyTheme should be false
		if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
			$this->assertFalse(ThemeOverride::canModifyTheme());
			$this->assertNotNull(ThemeOverride::getDisabledReason());
		}
	}
}
