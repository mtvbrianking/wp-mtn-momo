<?php

// https://mlocati.github.io/php-cs-fixer-configurator

$rules = array(
	'@PSR2' => true,
	'array_syntax' => array('syntax' => 'long'),
    'no_multiline_whitespace_before_semicolons' => true,
    'no_short_echo_tag' => true,
    'not_operator_with_successor_space' => true,
    'braces' => array('position_after_functions_and_oop_constructs' => 'same')
);

$excludes = array(
	// 'tests'
);

return PhpCsFixer\Config::create()
	->setRules($rules)
	->setFinder(
		PhpCsFixer\Finder::create()
			->exclude($excludes)
			->in(__DIR__)
			->name('*.php')
			->ignoreDotFiles(true)
			->ignoreVCS(true)
	);
