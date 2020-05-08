<?php

/**
 * Load configurations from the database.
 *
 * @see https://makitweb.com/how-to-send-ajax-request-from-plugin-wordpress
 */
function fn_mtn_momo_ajax_get_configurations_ajax() {
	global $wpdb;

	$table = "{$wpdb->prefix}mtn_momo_configurations";

	$filter = '';

	if (isset($_POST['product'])) {
		$product = $_POST['product'];
		$filter .= " WHERE `name` LIKE '{$product}%' ";
	}

	$sql = "SELECT `name`, `value` FROM {$table} {$filter};";

	$configurations = $wpdb->get_results($sql);

	echo json_encode(array('configurations' => $configurations));

	wp_die();
}
