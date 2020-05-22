<?php

function fn_db_get_configurations($table = '') {
	global $wpdb;

	if (! $table) {
		$table = "{$wpdb->prefix}mtn_momo_configurations";
	}

	return $wpdb->get_results("SELECT `label`, `name`, `value`, `description` FROM `{$table}`;");
}

function fn_db_update_configurations(array $configurations, $table = '') {
	global $wpdb;

	if (! $table) {
		$table = "{$wpdb->prefix}mtn_momo_configurations";
	}

	foreach ($configurations as $name => $value) {
		$wpdb->update(
			$table,                   // Table name
			array('value' => $value), // Set columns
			array('name' => $name),   // Where columns
			array('%s'),              // Set bindings
			array('%s')               // Where bind
		);
	}
}

if (isset($_POST['_wpnonce'])) {
	$nonce = $_REQUEST['_wpnonce'];

	$error_msg = '';

	if (! wp_verify_nonce($nonce, 'update-configurations')) {
		$error_msg = __('Unable to submit this form, please refresh and try again.');
	}

	if (! isset($_POST['config'])) {
		$error_msg = __('No configurations submitted.');
	}

	if ($error_msg) {
		print("<div class='error'>{$error_msg}</div>");
		exit;
	}

	$configurations = array_filter($_POST['config'], 'is_string', ARRAY_FILTER_USE_KEY);

	$configurations = array_map('sanitize_text_field', $configurations);

	fn_db_update_configurations($configurations);
}
?>

<form id="mtn-momo-configurations" method="POST" action="" novalidate="novalidate">

    <?php wp_nonce_field('update-configurations', '_wpnonce'); ?>

    <?php wp_referer_field(); ?>

    <h1><?php _e('Configurations'); ?></h1>

    <table class="form-table" role="presentation">
        <?php foreach (fn_db_get_configurations() as $config) { ?>
            <tr>
                <th>
                    <label for="<?php echo $config->name; ?>">
                        <?php _e($config->label); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                        style="width: 25em;"
                        id="<?php echo $config->name; ?>"
                        name="config[<?php echo $config->name; ?>]"
                        value="<?php echo esc_attr($config->value); ?>"/>
                    <p class="description">
                        <?php echo $config->description; ?>
                    </p>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php submit_button(__('Update Configurations')); ?>

</form>
