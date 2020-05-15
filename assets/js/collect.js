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

		if (document.getElementById('transaction_status').textContent.trim() == 'PENDING') {
			return "Transaction is't complete. Are you sure, you want to leave!"
		}
	}

	window.checkTransactionStatus = function() {
		var momo_transaction_id = document.getElementById('momo_transaction_id').textContent.trim();

		$.ajax({
			url: ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'get_transaction_status',
				product: 'collection',
				momo_transaction_id: momo_transaction_id
			},
			success: function(transaction) {
				console.log({status: transaction.status});

				document.getElementById('transaction_status').textContent = transaction.status;

				if(transaction.status != 'PENDING') {
					var redirect_uri = document.getElementById('redirect_uri').textContent.trim();

					if(redirect_uri.length) {
						window.location.replace(redirect_uri + '?momo_transaction_id=' + momo_transaction_id + '&' + $.param(transaction));
					}
				}
			},
			error: function(xhr) {
				console.error(xhr.responseJSON);
			}
		});
	}

	// Register variables...

	var ajax_url = params.ajax.url;

	$(document).ready(function() {
		// Register events...

		onbeforeunload();

		$('button[name=refresh]').on('click', function(){
			checkTransactionStatus();
		});
	});
})(jQuery);
