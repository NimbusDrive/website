<?php

$f3 = \Base::instance();

$f3->route(
	"GET /drive",
	function ($f3)
	{
		$f3->reroute("/drive/home");
	}
);

$f3->route(
	"GET /drive/home",
	function ($f3)
	{
		echo \Template::instance()->render("drive/home.htm");
	}
);
