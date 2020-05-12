(function($) {
	'use strict';

	console.log('Loaded: assets/js/collect.js');

	// Regsiter functions...

	/**
	 * @see https://stackoverflow.com/a/53658190/2732184
	 * @see https://stackoverflow.com/a/28627390/2732184
	 * @see https://stackoverflow.com/q/38879742/2732184
	 */
	window.onbeforeunload = function() {
		var statuses = [
			'PENDING',
			'SUCCESSFUL',
			'FAILED',
		];

		if (status == 'PENDING') {
			return "Transaction is't complete. Are you sure, you want to leave!"
		}
	}

	// Register variables...

	var ajax_url = params.ajax.url;

	$(document).ready(function() {
		// Register events...

		console.log(params);

		// $.ajax({
		//     url: ajax_url,
		//     type: 'POST',
		//     dataType: 'json',
		//     data: {
		//         action: 'get_transaction_status',
		//         product: 'collection',
		//         momo_transaction_id: '1a39e22b-94db-4f30-9564-4670e1c7e5b6'
		//     },
		//     success: function(transaction) {
		//         console.log({transaction: transaction});
		//     },
		//     error: function(xhr) {
		//         console.error(xhr.responseJSON);
		//     }
		// });
	});
})(jQuery);
