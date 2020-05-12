<?php

function fn_mtn_momo_greeting($args) {
	$name = fn_mtn_momo_array_get($args, 'name');

	return "<h1>Hi, {$name}</h1>";
}

function fn_mtn_momo_collect($args) {
	$party_type_id = fn_mtn_momo_array_get($args, 'party_type_id', 'msisdn');
	$party_id = fn_mtn_momo_array_get($args, 'party_id');

	if (! $party_type_id || ! $party_id) {
		return;
	}

	$internal_id = base_convert(microtime(true), 10, 16);

	// requestToPay
}
