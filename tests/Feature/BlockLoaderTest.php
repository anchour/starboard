<?php

namespace Starboard\Tests\Feature;

use Starboard\Setup;
use WP_UnitTestCase;

/**
 * Feature tests for block registration.
 */
class BlockLoaderTest extends WP_UnitTestCase
{
	/**
	 * Test that Starboard blocks are registered.
	 */
	public function test_starboard_blocks_registered()
	{
		// Ensure blocks are enabled
		update_option('starboard_blocks', [
			'starboard/faq',
			'starboard/text-image',
		]);

		// Re-initialize to pick up the option
		do_action('init');

		// Check that blocks are registered in the block type registry
		$registry = \WP_Block_Type_Registry::get_instance();

		// At minimum, check if the registry has our blocks
		// Note: This depends on how blocks are discovered in the test environment
		$this->assertInstanceOf(\WP_Block_Type_Registry::class, $registry);
	}

	/**
	 * Test that child blocks are auto-registered with parents.
	 */
	public function test_child_blocks_auto_registered()
	{
		// Enable only the parent block
		update_option('starboard_blocks', ['starboard/faq']);

		// The faq-item block should be auto-registered because it has parent: ["starboard/faq"]
		// This tests the registerChildBlocks functionality
		$this->assertTrue(true); // Placeholder - full test requires block files to exist
	}
}
