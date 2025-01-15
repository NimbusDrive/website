<?php

require "../vendor/autoload.php";

$f3 = \Base::instance();

$f3->route(
	"GET /",
	function ()
	{
		echo "Hello, World!";
	}
);
