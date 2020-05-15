<?php

class WP_MTN_MOMO_Collection {
	public function __construct() {
		// ...
	}

	/**
	 * Request payee to pay.
	 *
	 * @see https://momodeveloper.mtn.com/docs/services/collection/operations/requesttopay-POST Documentation
	 *
	 * @param  string $transactionId Your transaction reference ID, Say: order number.
	 * @param  string $partyId       Account holder. Usually phone number if type is MSISDN.
	 * @param  int    $amount        How much to debit the payer.
	 * @param  string $payerMessage  Payer transaction history message.
	 * @param  string $payeeNote     Payee transaction history message.
	 *
	 * @return string|null           Auto generated payment reference. Format: UUID
	 */
	public function request_to_pay($transactionId, $partyId, $amount, $payerMessage = '', $payeeNote = '') {
		$config = new WP_MTN_MOMO_Configuration();

		$api_base_uri = $config->get('api_base_uri');
		$collection_transaction_uri = $config->get('collection_transaction_uri');
		$resource_url = "{$api_base_uri}{$collection_transaction_uri}";

		$momoTransactionId = wp_generate_uuid4();

		$token = WP_MTN_MOMO_OAuth::authorize('collection');

		if (! $token) {
			return null;
		}

		$headers = array(
			'Authorization' => $token->getTokenType().' '.$token->getAccessToken(),
			'Content-Type' => 'application/json',
			'Ocp-Apim-Subscription-Key' => $config->get('collection_key'),
			'X-Target-Environment' => $config->get('app_environment'),
			'X-Reference-Id' => $momoTransactionId,
		);

		if ($config->get('app_environment') != 'sandbox') {
			$headers['X-Callback-Url'] = $config->get('app_callback_uri');
		}

		$body = array(
			'amount' => $amount,
			'currency' => $config->get('app_currency'),
			'externalId' => $transactionId,
			'payer' => array(
				'partyIdType' => $config->get('collection_party_id_type'),
				'partyId' => $partyId,
			),
			'payerMessage' => fn_mtn_momo_alphanumeric($payerMessage),
			'payeeNote' => fn_mtn_momo_alphanumeric($payeeNote),
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'POST',
			'headers' => $headers,
			'body' => json_encode($body),
		));

		if (is_wp_error($wp_http_response)) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode === 401) {
			WP_MTN_MOMO_OAuth::discard($token);
			return null;
		}

		if ($statusCode !== 202) {
			return null;
		}

		return $momoTransactionId;
	}

	/**
	 * Get transaction status.
	 *
	 * @see https://momodeveloper.mtn.com/docs/services/collection/operations/requesttopay-referenceId-GET Documentation
	 *
	 * @param  string $momoTransactionId MTN Momo transaction ID. Returned from transact (requestToPay)
	 *
	 * @return array|null
	 */
	public function get_transaction_status($momoTransactionId) {
		$config = new WP_MTN_MOMO_Configuration();

		$api_base_uri = $config->get('api_base_uri');
		$collection_transaction_status_uri = str_replace(
			'{momo_transaction_id}',
			$momoTransactionId,
			$config->get('collection_transaction_status_uri')
		);
		$resource_url = "{$api_base_uri}{$collection_transaction_status_uri}";

		$token = WP_MTN_MOMO_OAuth::authorize('collection');

		if (! $token) {
			return null;
		}

		$headers = array(
			'Authorization' => $token->getTokenType().' '.$token->getAccessToken(),
			'Ocp-Apim-Subscription-Key' => $config->get('collection_key'),
			'X-Target-Environment' => $config->get('app_environment'),
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'GET',
			'headers' => $headers,
		));

		if ($wp_http_response instanceof WP_Error) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode === 401) {
			WP_MTN_MOMO_OAuth::discard($token);
			return null;
		}

		if ($statusCode !== 200) {
			return null;
		}

		$wp_http_response_body = wp_remote_retrieve_body($wp_http_response);

		return json_decode($wp_http_response_body, true);
	}

	/**
	 * Determine if the payer is registered and active.
	 *
	 * @see https://momodeveloper.mtn.com/docs/services/collection/operations/get-v1_0-accountholder-accountholderidtype-accountholderid-active Documentation
	 *
	 * @param  string $partyId     Party number - MSISDN, email, or code - UUID.
	 * @param  string $partyIdType Allowed values [msisdn, email, party_code].
	 *
	 * @return bool|null           True if account holder is registered and active, false if the account holder is not active or not found
	 */
	public function is_active($partyId, $partyIdType = null) {
		$config = new WP_MTN_MOMO_Configuration();

		$api_base_uri = $config->get('api_base_uri');
		$partyIdType = $config->get('collection_party_id_type', $partyIdType);
		$collection_account_status_uri = strtr($config->get('collection_account_status_uri'), array(
			'{party_id_type}' => strtolower($partyIdType),
			'{party_id}' => urlencode($partyId)
		));
		$resource_url = "{$api_base_uri}{$collection_account_status_uri}";

		$token = WP_MTN_MOMO_OAuth::authorize('collection');

		if (! $token) {
			return null;
		}

		$headers = array(
			'Authorization' => $token->getTokenType().' '.$token->getAccessToken(),
			'Ocp-Apim-Subscription-Key' => $config->get('collection_key'),
			'X-Target-Environment' => $config->get('app_environment'),
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'GET',
			'headers' => $headers,
		));

		if ($wp_http_response instanceof WP_Error) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode === 401) {
			WP_MTN_MOMO_OAuth::discard($token);
			return null;
		}

		if ($statusCode !== 200) {
			return null;
		}

		$wp_http_response_body = wp_remote_retrieve_body($wp_http_response);

		return json_decode($wp_http_response_body, true)['result'];
	}

	/**
	 * Get merchant's account balance.
	 *
	 * @see https://momodeveloper.mtn.com/docs/services/collection/operations/get-v1_0-account-balance Documentation
	 *
	 * ```php
	 * $collection->get_account_balance();
	 * ```
	 *
	 * @return array|null Account balance.
	 *
	 * ```json
	 * {
	 *   "availableBalance": "string",
	 *   "currency": "string"
	 * }
	 * ```
	 */
	public function get_account_balance() {
		$config = new WP_MTN_MOMO_Configuration();

		$api_base_uri = $config->get('api_base_uri');
		$collection_account_balance_uri = $config->get('collection_account_balance_uri');
		$resource_url = "{$api_base_uri}{$collection_account_balance_uri}";

		$token = WP_MTN_MOMO_OAuth::authorize('collection');

		if (! $token) {
			return null;
		}

		$headers = array(
			'Authorization' => $token->getTokenType().' '.$token->getAccessToken(),
			'Ocp-Apim-Subscription-Key' => $config->get('collection_key'),
			'X-Target-Environment' => $config->get('app_environment'),
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'GET',
			'headers' => $headers,
		));

		if ($wp_http_response instanceof WP_Error) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode === 401) {
			WP_MTN_MOMO_OAuth::discard($token);
			return null;
		}

		if ($statusCode !== 200) {
			return null;
		}

		$wp_http_response_body = wp_remote_retrieve_body($wp_http_response);

		return json_decode($wp_http_response_body, true);
	}
}
