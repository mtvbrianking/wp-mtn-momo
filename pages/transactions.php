<?php

global $wp;

function fn_db_get_transactions() {
	global $wpdb;

	$table = "{$wpdb->prefix}mtn_momo_transactions";

	return $wpdb->get_results("SELECT * FROM `{$table}`;");
}

// $collection = new WP_MTN_MOMO_Collection();

// $momo_transaction_id = $collection->request_to_pay('12345678', '46733123453', 100);

// $momo_transaction_status = $collection->get_transaction_status($momo_transaction_id);

// $payer_is_active = $collection->is_active('46733123453');

// $merchant_account_balance = $collection->get_account_balance();

// ........................

// $transaction = new WP_MTN_MOMO_Transaction();

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
	<h2>Transactions</h2>
	<hr class="wp-header-end">
	<h2 class="screen-reader-text">Filter products list</h2>
	<ul class="subsubsub">
		<li class="all">
			<a href="transactions.php" class="current">
				All <span class="count">(10)</span>
			</a>&nbsp;|
		</li>
		<li class="administrator">
			<a href="transactions.php?product=collection">
				Collections <span class="count">(1)</span>
			</a>&nbsp;|
		</li>
		<li class="administrator">
			<a href="transactions.php?product=disbursement">
				Disbursements <span class="count">(0)</span>
			</a>&nbsp;|
		</li>
		<li class="administrator">
			<a href="transactions.php?product=remittance">
				Remittances <span class="count">(0)</span>
			</a>
		</li>
	</ul>
	<p class="search-box">
		<label class="screen-reader-text" for="product-search-input">Search Products:</label>
		<input type="search" id="product-search-input" name="s" value="">
		<input type="submit" id="search-submit" class="button" value="Search Products">
	</p>
	<br class="clear">
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
			<select name="action" id="bulk-action-selector-top">
				<option value="-1">Bulk Actions</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" id="doaction" class="button action" value="Apply">
		</div>
		<div class="alignleft actions">
			<label class="screen-reader-text" for="new_role">Change product to…</label>
			<select name="new_product" id="new_product">
				<option value="">Change product to…</option>
				<option value="collection">Collections</option>
				<option value="disbursement">Disbursements</option>
				<option value="remittance">Remittances</option>
			</select>
			<input type="submit" name="changeit" id="changeit" class="button" value="Change">
		</div>
		<br class="clear">
	</div>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th class="">External ID</th>
				<th class="">Internal ID</th>
				<th class="">Financial ID</th>
				<!-- <th class="">Product</th> -->
				<th class="">Payer</th>
				<th class="">Payee</th>
				<th class="">Amount</th>
				<th class="">Status</th>
				<!-- <th class="">Reason</th> -->
			</tr>
		</thead>
		<?php foreach (fn_db_get_transactions() as $idx => $transaction) { ?>
			<tr>
				<th scope="row" class="check-column">
					<!-- <label class="screen-reader-text" for="product_1">Select bmatovu</label> -->
					<input type="checkbox" name="transactions[]" value="<?php echo $transaction->external_id; ?>">
				</th>
				<td class=""><?php echo $transaction->external_id; ?></td>
				<td class=""><?php echo $transaction->internal_id; ?></td>
				<td class=""><?php echo $transaction->financial_id; ?></td>
				<!-- <td class=""><?php echo $transaction->product; ?></td> -->
				<td class=""><?php echo $transaction->payer; ?></td>
				<td class=""><?php echo $transaction->payee; ?></td>
				<td class=""><?php echo $transaction->amount; ?></td>
				<td class=""><?php echo $transaction->status; ?></td>
				<!-- <td class=""><?php echo $transaction->reason; ?></td> -->
			</tr>
		<?php } ?>
	</table>
</div>
