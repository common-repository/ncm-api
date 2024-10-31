<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.nepalcangroup.com/
 * @since             1.0.0
 * @package           Ncm_Api
 *
 * @wordpress-plugin
 * Plugin Name:       NCM API
 * Plugin URI:        https://www.nepalcanmove.com/
 * Description:       NCM API Plugin helps all the NCM Vendors to connect their wordpress website with the NCM Portal.
 * Version:           1.0.0
 * Author:            Nepal Can Move
 * Author URI:        https://www.nepalcangroup.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ncm-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NCM_API_VERSION', '1.0.0' );
define( 'NCM_API_PLUGIN_URI', plugin_dir_url(__FILE__));
define( 'NCM_API_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ncm-api-plugin-activator.php
 */

require_once(NCM_API_PLUGIN_PATH.'includes/upload-order.php');
require_once(NCM_API_PLUGIN_PATH.'includes/custom-input-order-edit.php');
require_once(NCM_API_PLUGIN_PATH.'includes/order-details.php');
require_once(NCM_API_PLUGIN_PATH.'includes/view-order-comment.php');
require_once(NCM_API_PLUGIN_PATH.'includes/order-list-column.php');

function activate_ncm_api() {
	require_once NCM_API_PLUGIN_PATH . 'includes/class-ncm-api-activator.php';
	Ncm_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ncm-api-deactivator.php
 */
function deactivate_ncm_api() {
	require_once NCM_API_PLUGIN_PATH . 'includes/class-ncm-api-deactivator.php';
	Ncm_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ncm_api' );
register_deactivation_hook( __FILE__, 'deactivate_ncm_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require NCM_API_PLUGIN_PATH . 'includes/class-ncm-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ncm_api() {

	$plugin = new Ncm_Api();
	$plugin->run();

}
run_ncm_api();
