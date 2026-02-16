<?php
/**
 * Plugin Name:     Starboard
 * Plugin URI:      https://www.anchour.com
 * Description:     A collection of commonly used custom blocks created by Anchour.
 * Author:          Anchour
 * Author URI:      https://www.anchour.com
 * Text Domain:     starboard
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Anchour_Blocks
 */

if (!defined('ABSPATH')) {
	exit;
}

define('STARBOARD_BLOCKS_PATH', plugin_dir_path(__FILE__) . 'src/blocks/');
define('STARBOARD_DIST_PATH', plugin_dir_path(__FILE__) . 'dist/blocks/');
define('STARBOARD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STARBOARD_PLUGIN_PATH', plugin_dir_path(__FILE__));

const STARBOARD_VERSION = '0.1.0';

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

\Starboard\Setup::init();
\Starboard\Hooks::init();
