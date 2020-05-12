<?php

function processForm() {
	$old = $_POST;

	$notices = array();

	// $nonce = $_REQUEST['_wpnonce'];

	// if (! wp_verify_nonce($nonce, 'collections-collect')) {
	// 	$notices['error'][] = 'Cross-site request forgery.';
	// 	return array('old' => $old, 'notices' => $notices);
	// }

	if (! isset($_POST['party-id'])) {
		$notices['error'][] = 'Party ID is required.';
		return array('old' => $old, 'notices' => $notices);
	}

	if (! isset($_POST['amount'])) {
		$notices['error'][] = 'Amount is required.';
		return array('old' => $old, 'notices' => $notices);
	}

	if (! isset($_POST['payer-message'])) {
		$notices['error'][] = 'Payer message is required.';
		return array('old' => $old, 'notices' => $notices);
	}

	$transaction_id = fn_mtn_momo_array_get($_POST, 'transaction_id', wp_generate_uuid4());

	$party_id = $_POST['party-id'];

	$amount = floatval($_POST['amount']);

	$payer_message = substr($_POST['payer-message'], 0, 150);

	// .................................................................................................................

	$collection = new MTN_MOMO_Collection();

	$momo_transaction_id = $collection->request_to_pay($transaction_id, $party_id, $amount, $payer_message);

	if (! $momo_transaction_id) {
		$notices['error'][] = 'Error occurred while processing transaction.';
		return array('old' => $old, 'notices' => $notices);
	}

	$old['momo-transaction-id'] = $momo_transaction_id;

	$notices['success'][] = 'Transaction initiated...';

	$notices['info'][] = 'Dail *165# to complete payment.';

	// ....................................................

	(new MTN_MOMO_Transaction)->create($momo_transaction_id, $transaction_id, 'collection', $amount, $party_id);

	// ....................................................

	return array('old' => $old, 'notices' => $notices);
}

$old = array();

if (isset($_POST['submit'])) {
	$form = processForm();

	$old = $form['old'];

	foreach ($form['notices'] as $class => $messages) {
		echo fn_mtn_momo_notify($messages, $class);
	}

	// var_dump($old);
}

?>

<table class="form-table" role="presentation">

	<tr>
        <th>
            <label>Transaction ID</label>
        </th>
        <td>
            <label><?php echo fn_mtn_momo_array_get($old, 'transaction-id'); ?></label>
        </td>
    </tr>

    <tr>
        <th>
            <label>MOMO Transaction ID</label>
        </th>
        <td>
            <label id="momo_transaction_id">
            	<?php echo fn_mtn_momo_array_get($old, 'momo-transaction-id'); ?>
            </label>
        </td>
    </tr>

    <tr>
        <th>
            <label>Phone Number</label>
        </th>
        <td>
            <label><?php echo fn_mtn_momo_array_get($old, 'party-id'); ?></label>
        </td>
    </tr>

    <tr>
        <th>
            <label>Amount</label>
        </th>
        <td>
            <label><?php echo fn_mtn_momo_array_get($old, 'amount'); ?></label>
        </td>
    </tr>

	<tr>
        <th>
            <label>Transaction Status</label>
        </th>
        <td>
            <label id="transaction_status">Pending</label>
        </td>
    </tr>

	<tr>
        <th>
            &nbsp;
        </th>
        <td>
            <input type="button" name="refresh" class="button button-primary" value="Refresh">
        </td>
    </tr>

</table>
