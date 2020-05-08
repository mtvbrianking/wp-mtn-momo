<?php

$old = $errors = array();

if (isset($_POST['submit'])) {
	$nonce = $_REQUEST['_wpnonce'];

	$error_msg = '';

	if (! wp_verify_nonce($nonce, 'sandbox')) {
		$error_msg = __('Cross-site request forgery.');
	}

	if ($error_msg) {
		print("<div class='error'>{$error_msg}</div>");
		exit;
	}

	$config = new MTN_MOMO_Configuration();

	$client_app = new MTN_MOMO_Client_App($config);

	// Update subscription key..........................................................................................

	$old['product'] = $product = $_POST['product'];

	$config->set("{$product}_key", $_POST['key']);

	// Register client app id...........................................................................................

	$client_app_id = wp_generate_uuid4();

	$wp_http_response = $client_app->register_id($product, $client_app_id);

	$statusCode = wp_remote_retrieve_response_code($wp_http_response);

	if (! $statusCode || fn_mtn_momo_intdiv($statusCode, 100) > 3) {
		print("<h4 class='error'>Client APP ID registration has failed.</h4>");
		wp_die();
	}

	$config->set("{$product}_id", $client_app_id);

	// Request client app secret........................................................................................

	$wp_http_response = $client_app->request_secret($product);

	$statusCode = wp_remote_retrieve_response_code($wp_http_response);

	if (! $statusCode || fn_mtn_momo_intdiv($statusCode, 100) > 3) {
		print("<h4 class='error'>Client APP Secret request has failed.</h4>");
		wp_die();
	}

	$api_response = json_decode(wp_remote_retrieve_body($wp_http_response), false);

	$config->set("{$product}_secret", $api_response->apiKey);
}

?>

<form id="sandbox" method="POST" action="">

    <?php wp_nonce_field('sandbox', '_wpnonce'); ?>

    <?php wp_referer_field(); ?>

    <table class="form-table" role="presentation">

        <tr class="config-product">
            <th>
                <label for="app_name">Product *</label>
            </th>
            <td>
                <select id="product" name="product" style="width: 25em;"
                    data-select="<?php echo fn_mtn_momo_array_get($old, 'product'); ?>" required>
                    <option value="" disabled selected>Choose product...</option>
                    <option value="collection">Collections</option>
                    <option value="disbursement">Disbursements</option>
                    <option value="remittance">Remittances</option>
                </select>
                <p class="description" id="app-name-description">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                </p>
            </td>
        </tr>

        <tr class="config-subscription-key-wrap">
            <th>
                <label for="id">Subscription Key *</label>
            </th>
            <td>
                <input type="text" id="key" name="key" style="width: 25em;" value="" required/>
            </td>
        </tr>

        <tr class="config-app-id-wrap">
            <th>
                <label for="id">Client App ID</label>
            </th>
            <td>
                <input type="text" id="id" name="id" style="width: 25em;" value="" readonly/>
            </td>
        </tr>

        <tr class="config-app-secret-wrap">
            <th>
                <label for="id">Client App Secret</label>
            </th>
            <td>
                <input type="text" id="secret" name="secret" style="width: 25em;" value="" readonly />
            </td>
        </tr>

        <tr class="config-app-secret-wrap">
            <th>
                &nbsp;
            </th>
            <td>
                <?php submit_button(__('Update Sandbox Credentials')); ?>
            </td>
        </tr>

    </table>

</form>
