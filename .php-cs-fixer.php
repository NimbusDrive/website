<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__);

return (new PhpCsFixer\Config())
	->setRules([
		"@PSR12" => true,
		"braces_position" => [
			"anonymous_classes_opening_brace" => "next_line_unless_newline_at_signature_end",
			"anonymous_functions_opening_brace" => "next_line_unless_newline_at_signature_end",
			"classes_opening_brace" => "next_line_unless_newline_at_signature_end",
			"control_structures_opening_brace" => "next_line_unless_newline_at_signature_end",
			"functions_opening_brace" => "next_line_unless_newline_at_signature_end"
		],
		"no_empty_statement" => true
	])
	->setIndent("\t")
	->setFinder($finder);
