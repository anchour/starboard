<?php
/**
 * Documentation tab content.
 */

defined('ABSPATH') || exit;

$docsPath = STARBOARD_PLUGIN_PATH . 'CUSTOMIZATION.md';
$docsContent = file_exists($docsPath) ? file_get_contents($docsPath) : 'Documentation file not found.';

/**
 * Simple markdown to HTML conversion.
 *
 * @param string $text Markdown text
 * @return string HTML output
 */
function starboard_parse_markdown($text) {
	// Extract code blocks first to protect them
	$codeBlocks = [];
	$text = preg_replace_callback('/```(\w+)?\n([\s\S]*?)```/', function($matches) use (&$codeBlocks) {
		$placeholder = '<!--CODEBLOCK' . count($codeBlocks) . '-->';
		$codeBlocks[] = '<pre><code>' . htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8') . '</code></pre>';
		return $placeholder;
	}, $text);

	// Extract inline code
	$inlineCodes = [];
	$text = preg_replace_callback('/`([^`]+)`/', function($matches) use (&$inlineCodes) {
		$placeholder = '<!--INLINECODE' . count($inlineCodes) . '-->';
		$inlineCodes[] = '<code>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</code>';
		return $placeholder;
	}, $text);

	// Escape remaining HTML
	$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

	// Restore code blocks and inline code
	foreach ($codeBlocks as $i => $block) {
		$text = str_replace('&lt;!--CODEBLOCK' . $i . '--&gt;', $block, $text);
	}
	foreach ($inlineCodes as $i => $code) {
		$text = str_replace('&lt;!--INLINECODE' . $i . '--&gt;', $code, $text);
	}

	// Headers with IDs for anchor linking
	$text = preg_replace_callback('/^(#{1,4}) (.+)$/m', function($matches) {
		$level = strlen($matches[1]);
		$text = $matches[2];
		$id = strtolower(trim($text));
		$id = preg_replace('/[^a-z0-9\s-]/', '', $id);
		$id = preg_replace('/[\s]+/', '-', $id);
		return "<h{$level} id=\"{$id}\">{$text}</h{$level}>";
	}, $text);

	// Bold (**text** or __text__)
	$text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
	$text = preg_replace('/__([^_]+)__/', '<strong>$1</strong>', $text);

	// Links [text](url) - use callback to properly escape URLs
	$text = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function($matches) {
		$linkText = $matches[1]; // Already escaped via htmlspecialchars above
		$url = esc_url($matches[2]); // Sanitize URL to prevent javascript: and other XSS
		return '<a href="' . $url . '">' . $linkText . '</a>';
	}, $text);

	// Horizontal rules
	$text = preg_replace('/^---+$/m', '<hr>', $text);

	// Tables
	$text = preg_replace_callback('/(\|.+\|\n)+/', function($matches) {
		$tableContent = trim($matches[0]);
		$rows = explode("\n", $tableContent);
		$html = '<table>';
		$isHeader = true;

		foreach ($rows as $row) {
			if (preg_match('/^\|[\s\-:]+\|$/', $row)) {
				$isHeader = false;
				continue;
			}

			$cells = array_filter(array_map('trim', explode('|', $row)));
			if (empty($cells)) continue;

			$html .= '<tr>';
			$tag = $isHeader ? 'th' : 'td';
			foreach ($cells as $cell) {
				$html .= "<{$tag}>{$cell}</{$tag}>";
			}
			$html .= '</tr>';

			if ($isHeader) {
				$isHeader = false;
			}
		}

		$html .= '</table>';
		return $html;
	}, $text);

	// Lists (simple)
	$text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);

	// Wrap consecutive <li> in <ul>
	$text = preg_replace('/(<li>.*<\/li>\n?)+/', '<ul>$0</ul>', $text);

	// Paragraphs (double newlines)
	$text = preg_replace('/\n\n+/', '</p><p>', $text);
	$text = '<p>' . $text . '</p>';

	// Clean up empty paragraphs and fix block element wrapping
	$text = preg_replace('/<p>\s*<\/p>/', '', $text);
	$text = preg_replace('/<p>\s*(<(?:h[1-6]|pre|ul|ol|table|hr)[^>]*>)/', '$1', $text);
	$text = preg_replace('/(<\/(?:h[1-6]|pre|ul|ol|table|hr)>)\s*<\/p>/', '$1', $text);

	return $text;
}
?>

<div class="starboard-docs">
	<?= starboard_parse_markdown($docsContent); ?>
</div>
