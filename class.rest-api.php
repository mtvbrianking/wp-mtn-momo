<?php

/**
 * @see https://generatewp.com/snippet/pnkkpve Source
 */
class WP_MTN_MOMO_Rest_Api {
	/**
	 * Register the REST API routes.
	 */
	public static function init() {
		if (! function_exists('register_rest_route')) {
			// The REST API wasn't integrated into core until 4.4, and we support 4.0+ (for now).
			return false;
		}

		register_rest_route('mtn-momo/v1', '/transaction', array(
			'methods' => 'PUT',
			'callback' => array( 'WP_MTN_MOMO_Rest_Api', 'update_transaction' ),
		));
	}

	/**
	 * Update transaction
	 *
	 * @param  WP_REST_Request           $request
	 * @return WP_Error|WP_REST_Request
	 */
	public static function update_transaction($request) {
		return new WP_REST_Response(array(
			'status' => 'Could have updated mtn momo transaction here....'
		), 200);
	}
}
