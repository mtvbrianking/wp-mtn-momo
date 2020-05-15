<?php

class WP_MTN_MOMO_Transaction {
	public function __construct() {
		// ...
	}

	/**
	 * Create new transaction.
	 *
	 * @param  string $external_id  MTN MOMO transaction id
	 * @param  string $internal_id  Your internal transaction id
	 * @param  string $product      Products: collection, disbursement, remittance
	 * @param  int    $amount
	 * @param  string $payer        Payer identifier
	 * @param  string $payee        Payee identifier
	 * @param  string $status       Statuses: PENDING, SUCCESSFUL, FAILED
	 * @param  string $reason       Reason for status
	 * @return int|bool             The number of rows inserted, or false on error
	 */
	public function create($external_id, $internal_id, $product, $amount, $payer = '', $payee = '', $status = null, $reason = null) {
		global $wpdb;

		$tbl_transactions = "{$wpdb->prefix}mtn_momo_transactions";

		$data = array(
			'external_id' => $external_id,
			'internal_id' => $internal_id,
			'product' => $product,
			'amount' => $amount,
			'payer' => $payer,
			'payee' => $payee,
			'status' => $status,
			'reason' => $reason,
		);

		$format = array('%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s');

		return $wpdb->insert($tbl_transactions, $data, $format);
	}
}
