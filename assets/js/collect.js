(function($) {
    'use strict';

    console.log('Loaded: assets/js/collect.js');

    // Regsiter functions...

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
