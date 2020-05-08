(function($) {
    'use strict';

    console.log('Loaded: assets/js/sandbox.js');

    // Regsiter functions...

    function render(product, configurations) {
        $('input[type=text]').val('');

        $.each(configurations, function(name, value) {
            var parts = name.split(product+'_');

            if(typeof parts[1] == 'undefined') {
                return;
            }

            var field = parts[1];

            $('input[name='+field+']').val(value);
        });

        // loop through inputs instead
        // set val == null for those missing on configs
    }

    // Register variables...

    var ajax_url = params.ajax.url;

    $(document).ready(function() {
        // Register events...

        $('select[id=product]').on('change', function(){
            var product = $(this).val();

            $('input[name=product]').val(product);

            $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_configurations',
                    product: product
                },
                success: function(response) {
                    var configurations = response.configurations.reduce((configurations, config) => {
                        configurations[config.name] = config.value;
                        return configurations;
                    }, {});

                    render(product, configurations);
                }
            });
        });

        // Manipulation DOM

        var product = $('select[id=product]').data('select');

        $('select[id=product]').val(product).change();
    });
})(jQuery);
