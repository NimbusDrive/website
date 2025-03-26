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
		$f3->set("DriveTab", "Home");

		echo \Template::instance()->render("drive/home.htm");
	}
);

$f3->route(
	"GET /drive/main",
	function ($f3)
	{
		$f3->set("DriveTab", "Main");

		echo \Template::instance()->render("drive/main.htm");
	}
);

$f3->route(
	"GET /drive/shared",
	function ($f3)
	{
		$f3->set("DriveTab", "Shared");

		echo \Template::instance()->render("drive/shared.htm");
	}
);
