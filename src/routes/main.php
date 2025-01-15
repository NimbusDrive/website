<?php

require "../vendor/autoload.php";

$f3 = \Base::instance();

$f3->route(
	"GET /",
	function($f3)
	{
		$f3->set("testvar", $f3->get("DB")->exec("select * from testtable"));

		echo \Template::instance()->render("main.htm");
	}
);
