<?php

function fn_mtn_momo_greeting($args) {
	$name = fn_mtn_momo_array_get((array) $args, 'name', 'John Doe');

	return "<h1>Hi, {$name}</h1>";
}
