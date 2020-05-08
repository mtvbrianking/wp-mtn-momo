<?php
/**
 * MTN MOMO plugin for WordPress
 *
 * @package   mtn-momo
 * @link      https://github.com/johnbillion/query-monitor
 * @author    Brian Matovu <mtvbrianking@gmail.com>
 * @copyright 2020-2030 Brian Matovu
 * @license   GPL v2 or later
 *
 * Plugin Name:  MTN MOMO
 * Description:  May payments via MTN Mobile Money.
 * Version:      0.0.1
 * Plugin URI:   https://github.com/mtvbrianking/wp-mtn-momo
 * Author:       Brian Matovu
 * Author URI:   http://bmatovu.com
 * Requires PHP: 5.3.6
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

define('MTN_MOMO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MTN_MOMO_VERSION', '0.0.1');
define('MTN_MOMO_DB_VERSION', '0.0.1');

// Functions
require_once(MTN_MOMO_PLUGIN_DIR . 'helpers.php');
require_once(MTN_MOMO_PLUGIN_DIR . 'core.php');
require_once(MTN_MOMO_PLUGIN_DIR . 'ajax.php');

// Classes
require_once(MTN_MOMO_PLUGIN_DIR . 'class.configuration.php');
require_once(MTN_MOMO_PLUGIN_DIR . 'class.client-app.php');

require_once(MTN_MOMO_PLUGIN_DIR . 'class.token-model.php');
require_once(MTN_MOMO_PLUGIN_DIR . 'class.token-repository.php');
require_once(MTN_MOMO_PLUGIN_DIR . 'class.oauth.php');

require_once(MTN_MOMO_PLUGIN_DIR . 'class.collection.php');

require_once(MTN_MOMO_PLUGIN_DIR . 'class.rest-api.php');

register_activation_hook(__FILE__, 'fn_mtn_momo_activation_hook');

register_uninstall_hook(__FILE__, 'fn_mtn_momo_uninstall_hook');

add_filter('http_response', 'fn_mtn_momo_log_http_requests', 10, 3);

add_action('admin_menu', 'fn_mtn_momo_admin_menu_action');

add_action('admin_enqueue_scripts', 'fn_mtn_momo_page_sandbox_scripts');

add_action('rest_api_init', array('MTN_MOMO_Rest_Api', 'init'));

add_action('wp_ajax_get_configurations', 'fn_mtn_momo_ajax_get_configurations_ajax');

add_action('wp_ajax_nopriv_get_configurations', 'fn_mtn_momo_ajax_get_configurations_ajax');