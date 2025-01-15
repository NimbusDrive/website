<?php

$f3 = \Base::instance();

$f3->route(
	"GET /",
	function ($f3)
	{
		echo \Template::instance()->render("main.htm");
	}
);
