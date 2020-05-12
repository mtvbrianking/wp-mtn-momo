<?php

/**
 * Integer division.
 * @see https://www.php.net/manual/en/function.intdiv.php#117626 Source
 * @param  int $dividend
 * @param  int $divisor
 * @return int
 */
function fn_mtn_momo_intdiv(int $dividend, int $divisor) {
	return ($dividend - $dividend % $divisor) / $divisor;
}

/**
 * Strip all symbols from a string.
 *
 * @see https://stackoverflow.com/a/16791863/2732184 Source
 *
 * @param  string $str
 *
 * @return string
 */
function fn_mtn_momo_alphanumeric($str) {
	return preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
}

/**
 * Get array value by key or default.
 *
 * ```
 * fn_mtn_momo_array_get($user, 'role.name', null);
 * ```
 *
 * @param array  $haystack The array
 * @param string $needle   The searched value
 * @param mixed  $default
 *
 * @return mixed
 */
function fn_mtn_momo_array_get(array $haystack, $needle, $default = null) {
	$keys = explode('.', $needle);

	foreach ($keys as $idx => $needle) {
		if (! isset($haystack[$needle])) {
			return $default;
		}

		if ($idx === (sizeof($keys) - 1)) {
			return $haystack[$needle];
		}

		$haystack = $haystack[$needle];
	}

	return $default;
}

/**
 * Write to log
 */
function fn_mtn_momo_log($log) {
	if (WP_DEBUG === true) {
		if (is_array($log) || is_object($log)) {
			error_log(print_r($log, true));
		} else {
			error_log($log);
		}
	}
}

/**
 * Dump and die.
 *
 * @see https://gist.github.com/james2doyle/abfbd4dc5754712bac022faf4e2881a6 Source
 *
 * @param  mixed $data
 *
 * @return void
 */
function fn_mtn_momo_dd($data) {
	ini_set("highlight.comment", "#969896; font-style: italic");
	ini_set("highlight.default", "#FFFFFF");
	ini_set("highlight.html", "#D16568");
	ini_set("highlight.keyword", "#7FA3BC; font-weight: bold");
	ini_set("highlight.string", "#F2C47E");
	$output = highlight_string("<?php\n\n" . var_export($data, true), true);
	echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div><br\>";
	wp_die();
}

/**
 * Generate html notice
 *
 * @see https://developer.wordpress.org/reference/hooks/admin_notices/#comment-2411 Source
 *
 * @param  string|array  $message
 * @param  string  $class           Statues: info, warning, error, success
 * @param  boolean $is_dismissible
 *
 * @return string                   HTML notice
 */
function fn_mtn_momo_notify($message, $class = 'info', $is_dismissible = true) {
	$is_dismissible = $is_dismissible ? 'is-dismissible' : '';

	$message = is_array($message) ? implode('</p><p>', $message) : $message;

	printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>', esc_attr($class), esc_attr($is_dismissible), $message);
}
