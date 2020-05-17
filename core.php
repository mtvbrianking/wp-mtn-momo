<?php

/**
  * On activation: migrate database.
  *
  * @see https://medium.com/enekochan/using-dbdelta-with-wordpress-to-create-and-alter-tables-73883f1db57
  * @see https://gist.github.com/sudar/9927194
 */
function fn_mtn_momo_activation_hook() {
	global $wpdb;

	$charset_collate = '';

	if ($wpdb->has_cap('collation')) {
		$charset_collate = $wpdb->get_charset_collate();
	}

	$tbl_configurations = "{$wpdb->prefix}mtn_momo_configurations";

	$tbl_tokens = "{$wpdb->prefix}mtn_momo_tokens";

	$tbl_transactions = "{$wpdb->prefix}mtn_momo_transactions";

	$callback_uri = get_rest_url(null, 'mtn-momo/v1/transaction');

	$sql = "
    CREATE TABLE {$tbl_configurations} (
        `id` INT(10) NOT NULL AUTO_INCREMENT,
        `label` VARCHAR(191) NOT NULL,
        `name` VARCHAR(191) NOT NULL,
        `value` VARCHAR(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (`id`),
        UNIQUE KEY (`name`)
    ) {$charset_collate};

    CREATE TABLE {$tbl_tokens} (
        `id` INT(10) NOT NULL AUTO_INCREMENT,
        `access_token` TEXT NOT NULL,
        `refresh_token` TEXT DEFAULT NULL,
        `token_type` VARCHAR(255) NOT NULL DEFAULT 'Bearer',
        `product` ENUM('collection','disbursement','remittance') NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `expires_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY  (`id`)
    ) {$charset_collate};

    CREATE TABLE {$tbl_transactions} (
        `external_id` CHAR(36) NOT NULL COMMENT 'MTN MOMO transaction ID',
        `internal_id` VARCHAR(191) NOT NULL COMMENT 'Your business transaction ID. Say: order no.',
        `financial_id` VARCHAR(191) COMMENT 'Financial transaction ID',
        `product` ENUM('collection','disbursement','remittance') NOT NULL,
        `payer` VARCHAR(255),
        `payee` VARCHAR(255),
        `amount` DECIMAL(10, 0),
        `status` ENUM('PENDING', 'SUCCESSFUL', 'FAILED'),
        `reason` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY  (`external_id`),
        UNIQUE KEY (`internal_id`),
        UNIQUE KEY (`financial_id`)
    ) {$charset_collate};

    INSERT INTO {$tbl_configurations} (`label`, `name`, `value`, `description`) VALUES
    ('APP Name', 'app_name', 'WP MTN MOMO', '<strong>Store name</strong>: Identifies your store to the payee.'),
    ('APP Currency', 'app_currency', 'EUR', NULL),
    ('APP Environment', 'app_environment', 'sandbox', NULL),
    ('APP Callback URI', 'app_callback_uri', '{$callback_uri}', '<code>providerCallbackHost</code>'),

    ('API Base URI', 'api_base_uri', 'https://sandbox.momodeveloper.mtn.com/', NULL),
    ('API Register ID URI', 'api_register_id_uri', 'v1_0/apiuser', NULL),
    ('API Validate ID URI', 'api_validate_id_uri', 'v1_0/apiuser/{client_id}', NULL),
    ('API Request Secret URI', 'api_request_secret_uri', 'v1_0/apiuser/{client_id}/apikey', NULL),

    ('Collections Key', 'collection_key', NULL, '<code>Ocp-Apim-Subscription-Key</code>'),
    ('Collections ID', 'collection_id', NULL, '<code>apiUser</code>'),
    ('Collections Secret', 'collection_secret', NULL, '<code>apiKey</code>'),
    ('Collections Party ID Type', 'collection_party_id_type', 'msisdn', 'Options: <code>msisdn</code><code>email</code>'),
    ('Collections Token URI', 'collection_token_uri', 'collection/token/', NULL),
    ('Collections Transaction URI', 'collection_transaction_uri', 'collection/v1_0/requesttopay', NULL),
    ('Collections Transaction Status URI', 'collection_transaction_status_uri', 'collection/v1_0/requesttopay/{momo_transaction_id}', NULL),
    ('Collections Account Balance URI', 'collection_account_balance_uri', 'collection/v1_0/account/balance', NULL),
    ('Collections Account Status URI', 'collection_account_status_uri', 'collection/v1_0/accountholder/{party_id_type}/{party_id}/active', NULL);";

	// Migrate table only when non-existent
	if ($wpdb->get_var("SHOW TABLES LIKE '{$tbl_configurations}';") != $tbl_configurations) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);

		add_option('mtn_momo_db_version', WP_MTN_MOMO_DB_VERSION);
	}
}

/**
 * On uninstallation; drop plugin database.
 *
 * @see https://wordpress.stackexchange.com/questions/169145/delete-tables-from-database-when-deleting-plugin
 */
function fn_mtn_momo_uninstall_hook() {
	global $wpdb;

	$sql = "DROP TABLE IF EXISTS {$wpdb->prefix}mtn_momo_configurations;";

	$sql .= "DROP TABLE IF EXISTS {$wpdb->prefix}mtn_momo_tokens;";

	$sql .= "DROP TABLE IF EXISTS {$wpdb->prefix}mtn_momo_transactions;";

	$wpdb->query($sql);

	delete_option('mtn_momo_db_version');
}

/**
 * Add mtn momo menu to  the admin control panel
 *
 * @see https://developer.wordpress.org/reference/functions/add_submenu_page/#comment-446
 */
function fn_mtn_momo_admin_menu_action() {
	$transactionsPage = WP_MTN_MOMO_PLUGIN_DIR . 'pages/transactions.php';

	add_menu_page('WP MTN MOMO', 'WP MTN MOMO', 'manage_options', $transactionsPage, '', 'dashicons-smartphone');

	add_submenu_page($transactionsPage, 'Transactions', 'Transactions', 'manage_options', $transactionsPage);

	add_submenu_page($transactionsPage, 'Configurations', 'Configurations', 'manage_options', WP_MTN_MOMO_PLUGIN_DIR . 'pages/configurations.php');

	add_submenu_page($transactionsPage, 'Sandbox', 'Sandbox', 'manage_options', WP_MTN_MOMO_PLUGIN_DIR . 'pages/sandbox.php');
}

function fn_mtn_momo_page_scripts() {
	$params = array(
		'ajax' => array(
			'url' => admin_url('admin-ajax.php'),
		)
	);

	if (get_current_screen()->base == 'wp-mtn-momo/pages/sandbox') {
		wp_register_style('sandbox-css', plugin_dir_url(__FILE__) . 'assets/css/sandbox.css', array(), WP_MTN_MOMO_VERSION);

		wp_enqueue_style('sandbox-css');

		wp_register_script('sandbox-js', plugin_dir_url(__FILE__) . 'assets/js/sandbox.js', array('jquery'), WP_MTN_MOMO_VERSION, true);

		wp_enqueue_script('sandbox-js');

		wp_localize_script('sandbox-js', 'params', $params);
	}
}

/**
 * Hook into WP_Http::_dispatch_request()
 *
 * @see https://gist.github.com/hinnerk-a/2846011 Source
 */
function fn_mtn_momo_log_http_requests($wp_http_response, $request, $url) {
	$request = array(
		'method' => $request['method'],
		'url' => $url,
		'headers' => $request['headers'],
		'body' => $request['body'],
	);

	if ($wp_http_response instanceof WP_Error) {
		$response = array(
			'errors' => $wp_http_response->errors,
			'error_data' => $wp_http_response->error_data,
		);
	} else {
		$response = array(
			'status' => array(
				'code' => wp_remote_retrieve_response_code($wp_http_response),
				'message' => wp_remote_retrieve_response_message($wp_http_response),
			),
			'headers' => wp_remote_retrieve_headers($wp_http_response)->getAll(),
			'body' => wp_remote_retrieve_body($wp_http_response),
		);
	}

	$log = json_encode(array(
		'request' => $request,
		'response' => $response,
	));

	fn_mtn_momo_log($log);

	return $wp_http_response;
}
