<?php

class WP_MTN_MOMO_Token_Model {
	/**
	 * Product.
	 *
	 * @var string
	 */
	protected $product;

	/**
	 * Access token.
	 *
	 * @var string
	 */
	protected $access_token;

	/**
	 * Refresh token.
	 *
	 * @var string
	 */
	protected $refresh_token;

	/**
	 * Token type.
	 *
	 * @var string
	 */
	protected $token_type;

	/**
	 * Expires at.
	 *
	 * @var string
	 */
	protected $expires_at = null;

	/**
	 * Constructor.
	 *
	 * @param array $attrs
	 */
	public function __construct($attrs) {
		$attrs = is_object($attrs) ? (array) $attrs : $attrs;

		$this->product = $attrs['product'];

		$this->access_token = $attrs['access_token'];

		$this->refresh_token = $attrs['refresh_token'];

		$this->token_type = $attrs['token_type'];

		$this->expires_at = $attrs['expires_at'];
	}

	/**
	 * Set product.
	 *
	 * @param string $product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * Get product.
	 *
	 * @return string
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * Set access token.
	 *
	 * @param string $access_token
	 */
	public function setAccessToken($access_token) {
		$this->access_token = $access_token;
	}

	/**
	 * Get access token.
	 *
	 * @return string
	 */
	public function getAccessToken() {
		return $this->access_token;
	}

	/**
	 * Set refresh token.
	 *
	 * @param string $refresh_token
	 */
	public function setRefreshToken($refresh_token) {
		$this->refresh_token = $refresh_token;
	}

	/**
	 * Get refresh token.
	 *
	 * @return string|null
	 */
	public function getRefreshToken() {
		return $this->refresh_token;
	}

	/**
	 * Set token type.
	 *
	 * @param string $token_type
	 */
	public function setTokenType($token_type) {
		$this->token_type = $token_type;
	}

	/**
	 * Get token type.
	 *
	 * @return string
	 */
	public function getTokenType() {
		return $this->token_type;
	}

	/**
	 * Set expires at.
	 *
	 * @param string $expires_at
	 */
	public function setExpiresAt($expires_at) {
		$this->expires_at = $expires_at;
	}

	/**
	 * Get expires at.
	 *
	 * @return string
	 */
	public function getExpiresAt() {
		return $this->expires_at;
	}

	/**
	 * Determine if a token is expired.
	 *
	 * @return bool
	 */
	public function isExpired() {
		if (is_null($this->expires_at)) {
			return false;
		}

		$expires_at = \DateTime::createFromFormat('Y-m-d H:i:s', $this->expires_at);

		$now = new \DateTime();

		return $now > $expires_at;
	}
}
