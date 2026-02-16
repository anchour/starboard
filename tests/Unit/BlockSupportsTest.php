<?php

namespace Starboard\Tests\Unit;

use Starboard\BlockSupports;
use WP_UnitTestCase;

/**
 * Tests for the BlockSupports class.
 */
class BlockSupportsTest extends WP_UnitTestCase
{
	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void
	{
		parent::tearDown();
		delete_option('starboard_block_supports');
	}

	/**
	 * Test saving and retrieving block supports.
	 */
	public function test_save_and_get_block_supports()
	{
		$supports = [
			'color.background' => false,
			'color.text' => true,
			'spacing.margin' => false,
		];

		BlockSupports::saveBlockSupports('test-block', $supports);
		$retrieved = BlockSupports::getBlockSupports('test-block');

		$this->assertEquals($supports, $retrieved);
	}

	/**
	 * Test clearing block supports.
	 */
	public function test_clear_block_supports()
	{
		$supports = ['color.background' => false];

		BlockSupports::saveBlockSupports('test-block', $supports);
		$this->assertNotNull(BlockSupports::getBlockSupports('test-block'));

		BlockSupports::clearBlockSupports('test-block');
		$this->assertNull(BlockSupports::getBlockSupports('test-block'));
	}

	/**
	 * Test getting supports for non-existent block returns null.
	 */
	public function test_get_non_existent_block_returns_null()
	{
		$this->assertNull(BlockSupports::getBlockSupports('non-existent-block'));
	}

	/**
	 * Test getAllBlockSupports returns all saved supports.
	 */
	public function test_get_all_block_supports()
	{
		BlockSupports::saveBlockSupports('block-1', ['color.background' => true]);
		BlockSupports::saveBlockSupports('block-2', ['spacing.margin' => false]);

		$all = BlockSupports::getAllBlockSupports();

		$this->assertArrayHasKey('block-1', $all);
		$this->assertArrayHasKey('block-2', $all);
		$this->assertEquals(['color.background' => true], $all['block-1']);
		$this->assertEquals(['spacing.margin' => false], $all['block-2']);
	}

	/**
	 * Test buildSupportsArray with flat keys.
	 */
	public function test_build_supports_array_flat_keys()
	{
		$supports = [
			'anchor' => true,
			'html' => false,
		];

		$built = BlockSupports::buildSupportsArray($supports);

		$this->assertEquals(['anchor' => true, 'html' => false], $built);
	}

	/**
	 * Test buildSupportsArray with nested keys (dot notation).
	 */
	public function test_build_supports_array_nested_keys()
	{
		$supports = [
			'color.background' => true,
			'color.text' => false,
			'spacing.margin' => true,
		];

		$built = BlockSupports::buildSupportsArray($supports);

		$this->assertArrayHasKey('color', $built);
		$this->assertArrayHasKey('spacing', $built);
		$this->assertEquals(['background' => true, 'text' => false], $built['color']);
		$this->assertEquals(['margin' => true], $built['spacing']);
	}

	/**
	 * Test available supports includes common WordPress block supports.
	 */
	public function test_available_supports_structure()
	{
		$available = BlockSupports::getAvailableSupports();

		$this->assertIsArray($available);
		$this->assertNotEmpty($available);

		// Check for common support categories
		$hasColorCategory = false;
		$hasSpacingCategory = false;

		foreach ($available as $item) {
			if (isset($item['label']) && $item['label'] === 'Color') {
				$hasColorCategory = true;
			}
			if (isset($item['label']) && $item['label'] === 'Spacing') {
				$hasSpacingCategory = true;
			}
		}

		$this->assertTrue($hasColorCategory, 'Should have Color support category');
		$this->assertTrue($hasSpacingCategory, 'Should have Spacing support category');
	}
}
