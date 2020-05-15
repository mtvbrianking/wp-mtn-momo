<?php

class WP_MTN_MOMO_Client_App {
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param WP_MTN_MOMO_Configuration $config
	 */
	public function __construct(WP_MTN_MOMO_Configuration $config) {
		$this->config = $config;
	}

	/**
	 * Register client app ID.
	 *
	 * @param  string      $product
	 * @param  string      $client_app_id UUID format 4
	 *
	 * @return bool        True if successful.
	 */
	public function register_id($product, $client_app_id) {
		$api_base_uri = $this->config->get('api_base_uri');
		$api_register_id_uri = $this->config->get('api_register_id_uri');
		$resource_url = "{$api_base_uri}{$api_register_id_uri}";

		$subscription_key = $this->config->get("{$product}_key");

		$app_callback_uri = $this->config->get('app_callback_uri');

		$body = array(
			'providerCallbackHost' => $app_callback_uri,
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'POST',
			'headers' => array(
				'Content-Type' => 'application/json',
				'Ocp-Apim-Subscription-Key' => $subscription_key,
				'X-Reference-Id' => $client_app_id,
			),
			'body' => json_encode($body),
		));

		if (is_wp_error($wp_http_response)) {
			return false;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode !== 201) {
			return false;
		}

		return true;
	}

	/**
	 * Request client app secret.
	 *
	 * @param  string      $product
	 *
	 * @return string|null Secret, or null on failure.
	 */
	public function request_secret($product) {
		$api_base_uri = $this->config->get('api_base_uri');
		$api_request_secret_uri = $this->config->get('api_request_secret_uri');
		$client_app_id = $this->config->get("{$product}_id");

		$api_request_secret_uri = str_replace('{client_id}', $client_app_id, $api_request_secret_uri);
		$resource_url = "{$api_base_uri}{$api_request_secret_uri}";

		$subscription_key = $this->config->get("{$product}_key");

		$body = array(
			'dummy' => 'You just need non empty body',
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'POST',
			'headers' => array(
				'Content-Type' => 'application/json',
				'Ocp-Apim-Subscription-Key' => $subscription_key,
			),
			'body' => json_encode($body),
		));

		if (is_wp_error($wp_http_response)) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode !== 201) {
			return null;
		}

		$wp_http_response_body = wp_remote_retrieve_body($wp_http_response);

		return json_decode($wp_http_response_body, true)['apiKey'];
	}
}
