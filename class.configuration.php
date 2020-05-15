<?php

class WP_MTN_MOMO_Configuration {
	protected $envs = array();

	public function __construct(array $envs = array()) {
		if ($envs) {
			$this->envs = $envs;
		}

		$this->envs = $this->fn_db_get_configurations();
	}

	public function set($key, $value) {
		if (! isset($this->envs[$key])) {
			return;
		}

		$this->fn_db_update_configuration($key, $value);

		$this->envs[$key] = $value;
	}

	public function get($key, $default = null) {
		if (! isset($this->envs[$key])) {
			return $default;
		}

		return $this->envs[$key];
	}

	public function sync(array $envs) {
		$this->fn_db_update_configurations($envs);

		$this->envs = $envs;
	}

	public function refresh(array $envs) {
		$this->envs = $this->fn_db_get_configurations();
	}

	protected function fn_db_get_configurations() {
		global $wpdb;

		$table = "{$wpdb->prefix}mtn_momo_configurations";

		$sql = "SELECT * FROM `{$table}`;";

		$configurations = $wpdb->get_results($sql);

		return array_reduce($configurations, function ($carry, $item) {
			$carry[$item->name] = $item->value;
			return $carry;
		}, array());
	}

	protected function fn_db_update_configurations(array $configurations) {
		global $wpdb;

		if (! $table) {
			$table = "{$wpdb->prefix}mtn_momo_configurations";
		}

		foreach ($configurations as $name => $value) {
			$wpdb->update(
				$table,              // Table name
				array('value' => $value), // Set columns
				array('name' => $name),   // Where columns
				array('%s'),              // Set bindings
				array('%s')               // Where bind
			);
		}
	}

	protected function fn_db_update_configuration($name, $value) {
		global $wpdb;

		$table = "{$wpdb->prefix}mtn_momo_configurations";

		return $wpdb->update(
			$table,              // Table name
			array('value' => $value), // Set columns
			array('name' => $name),   // Where columns
			array('%s'),              // Set bindings
			array('%s')               // Where bind
		);
	}
}
