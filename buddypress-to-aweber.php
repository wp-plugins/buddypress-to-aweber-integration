<?php
/**
 * Buddypress to Aweber
 *
 * Aweber to buddypress integration
 *
 * @package   buddypress-to-aweber
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-15-2015 BAWD
 *
 * @wordpress-plugin
 * Plugin Name: Buddypress to Aweber
 * Plugin URI:  http://buildawebdoctor.com
 * Description: Aweber to buddypress integration
 * Version:     1.0.1
 * Author:      vimes1984
 * Author URI:  http://buildawebdoctor.com
 * Text Domain: buddypress-to-aweber-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */


// If this file is called directly, abort.
if (!defined("WPINC")) {
	die;
}
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require_once(plugin_dir_path(__FILE__) . "BuddypressToAweber.php");
require_once(plugin_dir_path(__FILE__) . "classes/class-admin-functions.php");
require_once(plugin_dir_path(__FILE__) . "classes/class-basic-auth-options.php");
require_once(plugin_dir_path(__FILE__) . "classes/class-list-options.php");
require_once(plugin_dir_path(__FILE__) . "classes/class-ajax-functions.php");
require_once(plugin_dir_path(__FILE__) . "classes/class-new-user-send-data.php");

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array("BuddypressToAweber", "activate"));
register_deactivation_hook(__FILE__, array("BuddypressToAweber", "deactivate"));
BuddypressToAweber::get_instance();
AdminFunctions::get_instance();
BasicAuthOptions::get_instance();
ListOptions::get_instance();
AjaxFunctions::get_instance();
NewUserSendData::get_instance();
/*
*/
