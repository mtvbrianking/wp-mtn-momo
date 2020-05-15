<?php

global $wp;

// $collection = new MTN_MOMO_Collection();

// $momo_transaction_id = $collection->request_to_pay('12345678', '46733123453', 100);

// $momo_transaction_status = $collection->get_transaction_status($momo_transaction_id);

// $payer_is_active = $collection->is_active('46733123453');

// $merchant_account_balance = $collection->get_account_balance();

// ........................

// $transaction = new MTN_MOMO_Transaction();

// $transaction->create('e996501c-e721-4ac1-97ff-dc6887b85e8c', 'OrderNo#12345678', 'collection', 10.052);

?>

<form id="collections-collect" method="POST"
	action="http://htdocs.local/wordpress/5.3/wp-admin/admin.php?page=wp-mtn-momo/pages/collect.php">

    <?php wp_nonce_field('collections-collect', '_wpnonce'); ?>

    <?php wp_referer_field(); ?>

    <table class="form-table" role="presentation">

		<tr class="transaction-id-wrap">
            <th>
                <label for="redirect-uri">Redirect URI</label>
            </th>
            <td>
                <input type="url" id="redirect-uri" name="redirect-uri" style="width: 25em;"
                	value="<?php
					echo plugin_dir_url(__DIR__).'mtn-momo/transactions.php';
					// echo home_url($wp->request);
					// echo add_query_arg($wp->query_vars, home_url($wp->request));
					?>"/>
            </td>
        </tr>

    	<tr class="transaction-id-wrap">
            <th>
                <label for="transaction-id">Transaction ID</label>
            </th>
            <td>
                <input type="text" id="transaction-id" name="transaction-id" style="width: 25em;"
                	value="<?php echo wp_generate_uuid4(); ?>" readonly />
            </td>
        </tr>

        <tr class="party-id-wrap">
            <th>
                <label for="party-id">Phone Number</label>
            </th>
            <td>
                <input type="tel" id="party-id" name="party-id" style="width: 25em;" value="" required />
                <p class="description" id="party-id-description">
                    Format: Preceeded by country code
                </p>
            </td>
        </tr>

        <tr class="amount-wrap">
            <th>
                <label for="id">Amount <?php echo 'EUR'; ?></label>
            </th>
            <td>
                <input type="tel" id="amount" name="amount" style="width: 25em;" value="100" />
            </td>
        </tr>

        <tr class="payer-message-wrap">
            <th>
                <label for="payer-message">Payer Message</label>
            </th>
            <td>
                <textarea id="payer-message" name="payer-message" rows="3" style="width: 25em;" readonly>Complete EUR 100 to Joe's Boutique for OrderNo.12345678</textarea>
            </td>
        </tr>

        <tr>
            <th>
                &nbsp;
            </th>
            <td>
                <?php submit_button(__('Transact with MTN Mobile Money')); ?>
            </td>
        </tr>

    </table>

</form>

<div class="wrap">
    <h1>Transactions</h1>
    <p>Some transactions and there status here...</p>
</div>
