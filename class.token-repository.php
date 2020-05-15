<?php

class WP_MTN_MOMO_Token_Repository {
	protected $tbl_tokens;

	protected $product;

	public function __construct($product) {
		global $wpdb;

		$this->product = $product;

		$this->tbl_tokens = "{$wpdb->prefix}mtn_momo_tokens";
	}

	public function save($attrs) {
		global $wpdb;

		$attrs = is_object($attrs) ? (array) $attrs : $attrs;

		$expires_at = $expires_in = fn_mtn_momo_array_get($attrs, 'expires_in');

		if (! is_null($expires_in)) {
			$di = new \DateInterval("PT{$expires_in}S");

			$expires_at = (new \DateTime())->add($di)->format('Y-m-d H:i:s');
		}

		$data = array(
			'product' => $this->product,
			'access_token' => fn_mtn_momo_array_get($attrs, 'access_token'),
			'refresh_token' => fn_mtn_momo_array_get($attrs, 'refresh_token'),
			// 'token_type' => fn_mtn_momo_array_get($attrs, 'token_type'),
			'token_type' => 'Bearer',
			'expires_at' => $expires_at,
		);

		$format = array('%s', '%s', '%s', '%s', '%s');

		$wpdb->insert($this->tbl_tokens, $data, $format);

		// return $wpdb->insert_id;

		return new WP_MTN_MOMO_Token_Model($data);
	}

	public function get($access_token = null) {
		global $wpdb;

		$filter = '';

		if ($access_token) {
			$filter .= " AND `access_token` = {$access_token} ";
		}

		$sql = "SELECT * FROM {$this->tbl_tokens} WHERE `product` = '{$this->product}' {$filter} ORDER BY `created_at` DESC LIMIT 1;";

		$db_tokens = $wpdb->get_results($sql);

		$db_token = array_shift($db_tokens);

		return $db_token ? new WP_MTN_MOMO_Token_Model($db_token) : null;
	}

	public function delete($access_token) {
		global $wpdb;

		$wpdb->delete($this->tbl_tokens, array(
			'access_token' => $access_token
		), array('%s'));
	}
}
