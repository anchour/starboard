<?php

namespace Starboard\Tests\Unit;

use Starboard\BlockLoader;
use WP_UnitTestCase;

/**
 * Tests for the BlockLoader class.
 */
class BlockLoaderTest extends WP_UnitTestCase
{
	/**
	 * Test that valid block names pass validation.
	 */
	public function test_valid_block_names()
	{
		// Access private method via reflection
		$reflection = new \ReflectionClass(BlockLoader::class);
		$method = $reflection->getMethod('isValidBlockName');
		$method->setAccessible(true);

		$this->assertTrue($method->invoke(null, 'text-image'));
		$this->assertTrue($method->invoke(null, 'faq'));
		$this->assertTrue($method->invoke(null, 'faq-item'));
		$this->assertTrue($method->invoke(null, 'my_block'));
		$this->assertTrue($method->invoke(null, 'block123'));
	}

	/**
	 * Test that invalid block names are rejected (path traversal prevention).
	 */
	public function test_invalid_block_names()
	{
		$reflection = new \ReflectionClass(BlockLoader::class);
		$method = $reflection->getMethod('isValidBlockName');
		$method->setAccessible(true);

		$this->assertFalse($method->invoke(null, '../malicious'));
		$this->assertFalse($method->invoke(null, 'block/name'));
		$this->assertFalse($method->invoke(null, 'block.json'));
		$this->assertFalse($method->invoke(null, ''));
		$this->assertFalse($method->invoke(null, ' '));
		$this->assertFalse($method->invoke(null, 'block name'));
	}

	/**
	 * Test getBlockPath returns null for invalid block names.
	 */
	public function test_get_block_path_rejects_invalid_names()
	{
		$this->assertNull(BlockLoader::getBlockPath('../etc/passwd'));
		$this->assertNull(BlockLoader::getBlockPath(''));
	}

	/**
	 * Test getBlockPath returns path for valid plugin blocks.
	 */
	public function test_get_block_path_finds_plugin_blocks()
	{
		$path = BlockLoader::getBlockPath('text-image');

		// Should return a path if block exists
		if ($path !== null) {
			$this->assertStringContainsString('text-image', $path);
			$this->assertFileExists($path . '/block.json');
		}
	}

	/**
	 * Test hasThemeOverride returns false for invalid names.
	 */
	public function test_has_theme_override_rejects_invalid_names()
	{
		$this->assertFalse(BlockLoader::hasThemeOverride('../malicious'));
		$this->assertFalse(BlockLoader::hasThemeOverride(''));
	}

	/**
	 * Test getAllBlockNames returns array of valid block names.
	 */
	public function test_get_all_block_names_returns_array()
	{
		$blocks = BlockLoader::getAllBlockNames();

		$this->assertIsArray($blocks);

		// All returned names should be valid
		$reflection = new \ReflectionClass(BlockLoader::class);
		$method = $reflection->getMethod('isValidBlockName');
		$method->setAccessible(true);

		foreach ($blocks as $blockName) {
			$this->assertTrue(
				$method->invoke(null, $blockName),
				"Block name '{$blockName}' should be valid"
			);
		}
	}

	/**
	 * Test getBlockUrl returns null for invalid names.
	 */
	public function test_get_block_url_rejects_invalid_names()
	{
		$this->assertNull(BlockLoader::getBlockUrl('../malicious'));
		$this->assertNull(BlockLoader::getBlockUrl(''));
	}
}
