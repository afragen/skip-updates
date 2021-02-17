<?php
/**
 * Skip Updates
 *
 * @package skip-updates
 * @author  Andy Fragen
 * @link    https://github.com/afragen/skip-updates
 */

/**
 * Plugin Name:       Skip Updates
 * Plugin URI:        https://github.com/afragen/skip-updates
 * Description:       Skip updates for selected dot org plugins or themes.
 * Version:           0.4.0
 * Author:            Andy Fragen
 * License:           MIT
 * Network:           true
 * Domain Path:       /languages
 * Text Domain:       skip-updates
 * GitHub Plugin URI: https://github.com/afragen/skip-updates
 * Primary Branch:    main
 * Requires at least: 5.2
 * Requires PHP:      5.6
 */

namespace Fragen\Skip_Updates;

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load Autoloader.
require_once __DIR__ . '/vendor/autoload.php';

add_action(
	'plugins_loaded',
	function () {
		( new Bootstrap( __FILE__ ) )->run();
	}
);
