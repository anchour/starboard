<?php

namespace Starboard\Tests\Unit;

use Starboard\GetBlocks;
use WP_UnitTestCase;

/**
 * Tests for the GetBlocks class.
 */
class GetBlocksTest extends WP_UnitTestCase
{
	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void
	{
		parent::tearDown();
		delete_option('starboard_blocks');
	}

	/**
	 * Test run() returns array.
	 */
	public function test_run_returns_array()
	{
		$blocks = GetBlocks::run();

		$this->assertIsArray($blocks);
	}

	/**
	 * Test run() returns objects with name property.
	 */
	public function test_run_returns_block_objects()
	{
		$blocks = GetBlocks::run();

		foreach ($blocks as $block) {
			$this->assertIsObject($block);
			$this->assertObjectHasProperty('name', $block);
			$this->assertStringStartsWith('starboard/', $block->name);
		}
	}

	/**
	 * Test enabled() returns all blocks by default.
	 */
	public function test_enabled_returns_all_by_default()
	{
		// Ensure option doesn't exist
		delete_option('starboard_blocks');

		$enabled = GetBlocks::enabled();
		$all = GetBlocks::run();

		$this->assertIsArray($enabled);
		$this->assertCount(count($all), $enabled);
	}

	/**
	 * Test enabled() returns saved blocks when option exists.
	 */
	public function test_enabled_returns_saved_blocks()
	{
		$saved = ['starboard/text-image', 'starboard/faq'];
		update_option('starboard_blocks', $saved);

		$enabled = GetBlocks::enabled();

		$this->assertEquals($saved, $enabled);
	}

	/**
	 * Test enabled() returns empty array when option is empty.
	 */
	public function test_enabled_returns_empty_when_none_selected()
	{
		update_option('starboard_blocks', []);

		$enabled = GetBlocks::enabled();

		$this->assertIsArray($enabled);
		$this->assertEmpty($enabled);
	}

	/**
	 * Test enabled() handles invalid option value.
	 */
	public function test_enabled_handles_invalid_option()
	{
		update_option('starboard_blocks', 'not-an-array');

		$enabled = GetBlocks::enabled();

		$this->assertIsArray($enabled);
		$this->assertEmpty($enabled);
	}

	/**
	 * Test block objects have required properties.
	 */
	public function test_block_objects_have_required_properties()
	{
		$blocks = GetBlocks::run();

		foreach ($blocks as $block) {
			// Every block.json should have at minimum: name, title
			$this->assertObjectHasProperty('name', $block);
			$this->assertObjectHasProperty('title', $block);
		}
	}
}
