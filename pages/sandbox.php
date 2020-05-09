<?php

function processForm() {
	$old = $notices = array();

	$nonce = $_REQUEST['_wpnonce'];

	if (! wp_verify_nonce($nonce, 'sandbox')) {
		$notices['error'][] = 'Cross-site request forgery.';
		return array('old' => $_POST, 'notices' => $notices);
	}

	// .................................................................................................................

	$config = new MTN_MOMO_Configuration();

	$client_app = new MTN_MOMO_Client_App($config);

	// Update subscription key..........................................................................................

	$product = $_POST['product'];

	$config->set("{$product}_key", $_POST['key']);

	// Register client app id...........................................................................................

	$client_app_id = wp_generate_uuid4();

	$registered = $client_app->register_id($product, $client_app_id);

	if (! $registered) {
		$notices['error'][] = 'Client app ID registration has failed.';
		return array('old' => $_POST, 'notices' => $notices);
	}

	$config->set("{$product}_id", $client_app_id);

	// Request client app secret........................................................................................

	$client_app_secret = $client_app->request_secret($product);

	if (! $client_app_secret) {
		$notices['error'][] = 'Client app secret registration has failed.';
		return array('old' => $_POST, 'notices' => $notices);
	}

	$config->set("{$product}_secret", $client_app_secret);

	$notices['success'][] = 'Sandbox credentials updated...';

	return array('old' => $_POST, 'notices' => $notices);
}

$old = array();

if (isset($_POST['submit'])) {
	$form = processForm();

	$old = $form['old'];

	foreach ($form['notices'] as $class => $messages) {
		echo fn_mtn_momo_notify($messages, $class);
	}
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
