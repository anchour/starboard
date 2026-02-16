<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Block Usage Analytics - Tracks block and variant usage across the site.
 */
class BlockUsage
{
	/**
	 * Cache key for usage data.
	 */
	const CACHE_KEY = 'starboard_block_usage';

	/**
	 * Cache expiration in seconds (1 hour).
	 */
	const CACHE_EXPIRATION = HOUR_IN_SECONDS;

	/**
	 * Get usage statistics for all Starboard blocks.
	 *
	 * @param bool $forceRefresh Force recalculation instead of using cache
	 * @return array
	 */
	public static function getUsage(bool $forceRefresh = false): array
	{
		if (!$forceRefresh) {
			$cached = get_transient(self::CACHE_KEY);
			if ($cached !== false) {
				return $cached;
			}
		}

		$usage = self::calculateUsage();
		set_transient(self::CACHE_KEY, $usage, self::CACHE_EXPIRATION);

		return $usage;
	}

	/**
	 * Calculate block usage across all posts.
	 *
	 * @return array
	 */
	private static function calculateUsage(): array
	{
		global $wpdb;

		$usage = [
			'blocks' => [],
			'variants' => [],
			'posts' => [],
			'last_updated' => current_time('mysql'),
		];

		// Get all post types that support the editor
		$postTypes = get_post_types(['show_in_rest' => true], 'names');
		$postTypes = array_filter($postTypes, function ($type) {
			return post_type_supports($type, 'editor');
		});

		if (empty($postTypes)) {
			return $usage;
		}

		$postTypesPlaceholder = implode(',', array_fill(0, count($postTypes), '%s'));

		// Query posts containing Starboard blocks
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query = $wpdb->prepare(
			"SELECT ID, post_title, post_type, post_status, post_content
			FROM {$wpdb->posts}
			WHERE post_status IN ('publish', 'draft', 'pending', 'future', 'private')
			AND post_type IN ($postTypesPlaceholder)
			AND post_content LIKE %s",
			array_merge($postTypes, ['%<!-- wp:starboard/%'])
		);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$posts = $wpdb->get_results($query);

		if (empty($posts)) {
			return $usage;
		}

		foreach ($posts as $post) {
			$blocks = self::parseBlocksFromContent($post->post_content);

			foreach ($blocks as $blockName => $blockData) {
				// Initialize block usage
				if (!isset($usage['blocks'][$blockName])) {
					$usage['blocks'][$blockName] = [
						'count' => 0,
						'posts' => [],
					];
				}

				$usage['blocks'][$blockName]['count'] += $blockData['count'];
				$usage['blocks'][$blockName]['posts'][] = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'type' => $post->post_type,
					'status' => $post->post_status,
					'edit_url' => get_edit_post_link($post->ID, 'raw'),
				];

				// Track variant usage for text-image
				if ($blockName === 'starboard/text-image' && !empty($blockData['variants'])) {
					foreach ($blockData['variants'] as $variant => $variantCount) {
						if (!isset($usage['variants'][$variant])) {
							$usage['variants'][$variant] = [
								'count' => 0,
								'posts' => [],
							];
						}
						$usage['variants'][$variant]['count'] += $variantCount;
						$usage['variants'][$variant]['posts'][] = [
							'id' => $post->ID,
							'title' => $post->post_title,
						];
					}
				}
			}
		}

		// Sort by usage count
		uasort($usage['blocks'], function ($a, $b) {
			return $b['count'] - $a['count'];
		});

		uasort($usage['variants'], function ($a, $b) {
			return $b['count'] - $a['count'];
		});

		return $usage;
	}

	/**
	 * Parse Starboard blocks from post content.
	 *
	 * @param string $content Post content
	 * @return array Block usage data
	 */
	private static function parseBlocksFromContent(string $content): array
	{
		$usage = [];

		// Use WordPress's built-in block parser
		$parsedBlocks = parse_blocks($content);
		self::countBlocksRecursive($parsedBlocks, $usage);

		return $usage;
	}

	/**
	 * Recursively count blocks including inner blocks.
	 *
	 * @param array $blocks Parsed blocks
	 * @param array &$usage Usage data (passed by reference)
	 * @return void
	 */
	private static function countBlocksRecursive(array $blocks, array &$usage): void
	{
		foreach ($blocks as $block) {
			// Skip empty blocks
			if (empty($block['blockName'])) {
				continue;
			}

			$blockName = $block['blockName'];

			// Only count Starboard blocks
			if (strpos($blockName, 'starboard/') === 0) {
				if (!isset($usage[$blockName])) {
					$usage[$blockName] = [
						'count' => 0,
						'variants' => [],
					];
				}

				$usage[$blockName]['count']++;

				// Track text-image variants
				$attrs = $block['attrs'] ?? [];
				if ($blockName === 'starboard/text-image' && !empty($attrs['selectedVariationName'])) {
					$variantName = $attrs['selectedVariationName'];
					// Extract slug from "text-image__<slug>" format
					$variantSlug = preg_replace('/^text-image__/', '', $variantName);

					if (!isset($usage[$blockName]['variants'][$variantSlug])) {
						$usage[$blockName]['variants'][$variantSlug] = 0;
					}
					$usage[$blockName]['variants'][$variantSlug]++;
				}
			}

			// Recursively process inner blocks
			if (!empty($block['innerBlocks'])) {
				self::countBlocksRecursive($block['innerBlocks'], $usage);
			}
		}
	}

	/**
	 * Get usage count for a specific block.
	 *
	 * @param string $blockName Full block name (e.g., 'starboard/text-image')
	 * @return int
	 */
	public static function getBlockUsageCount(string $blockName): int
	{
		$usage = self::getUsage();
		return $usage['blocks'][$blockName]['count'] ?? 0;
	}

	/**
	 * Get posts using a specific block.
	 *
	 * @param string $blockName Full block name
	 * @return array
	 */
	public static function getPostsUsingBlock(string $blockName): array
	{
		$usage = self::getUsage();
		return $usage['blocks'][$blockName]['posts'] ?? [];
	}

	/**
	 * Get usage count for a specific variant.
	 *
	 * @param string $variantSlug Variant slug
	 * @return int
	 */
	public static function getVariantUsageCount(string $variantSlug): int
	{
		$usage = self::getUsage();
		return $usage['variants'][$variantSlug]['count'] ?? 0;
	}

	/**
	 * Check if a block is in use.
	 *
	 * @param string $blockName Full block name
	 * @return bool
	 */
	public static function isBlockInUse(string $blockName): bool
	{
		return self::getBlockUsageCount($blockName) > 0;
	}

	/**
	 * Check if a variant is in use.
	 *
	 * @param string $variantSlug Variant slug
	 * @return bool
	 */
	public static function isVariantInUse(string $variantSlug): bool
	{
		return self::getVariantUsageCount($variantSlug) > 0;
	}

	/**
	 * Clear the usage cache.
	 *
	 * @return bool
	 */
	public static function clearCache(): bool
	{
		return delete_transient(self::CACHE_KEY);
	}

	/**
	 * Get the last updated timestamp.
	 *
	 * @return string|null
	 */
	public static function getLastUpdated(): ?string
	{
		$usage = self::getUsage();
		return $usage['last_updated'] ?? null;
	}
}
