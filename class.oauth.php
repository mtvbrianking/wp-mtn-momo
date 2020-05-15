<?php

class WP_MTN_MOMO_OAuth {
	public static function authorize($product) {
		$token_repo = new WP_MTN_MOMO_Token_Repository($product);

		$token = $token_repo->get();

		if ($token && $token->isExpired()) {
			$token_repo->delete($token->getAccessToken());

			$token = null;
		}

		if ($token) {
			return $token;
		}

		$new_token = self::request($product);

		if (! $new_token) {
			return null;
		}

		return $token_repo->save($new_token);
	}

	public static function request($product) {
		$config = new WP_MTN_MOMO_Configuration();

		$api_base_uri = $config->get('api_base_uri');
		$product_token_uri = $config->get("{$product}_token_uri");
		;
		$resource_url = "{$api_base_uri}{$product_token_uri}";

		$subscription_key = $config->get("{$product}_key");

		$client_app_id = $config->get("{$product}_id");
		$client_app_secret = $config->get("{$product}_secret");
		$authorization = base64_encode("{$client_app_id}:{$client_app_secret}");

		$body = array(
			'dummy' => 'You just need non empty body',
		);

		$wp_http_response = wp_remote_request($resource_url, array(
			'method' => 'POST',
			'headers' => array(
				'Authorization' => "Basic {$authorization}",
				'Content-Type' => 'application/json',
				'Ocp-Apim-Subscription-Key' => $subscription_key,
			),
			'body' => json_encode($body),
		));

		if ($wp_http_response instanceof WP_Error) {
			return null;
		}

		$statusCode = wp_remote_retrieve_response_code($wp_http_response);

		if ($statusCode !== 200) {
			return null;
		}

		$token = wp_remote_retrieve_body($wp_http_response);

		return json_decode($token, true);
	}

	public static function discard($token) {
		$token_repo = new WP_MTN_MOMO_Token_Repository($token->getProduct());

		$token_repo->delete($token->getAccessToken());
	}
}
