<?php

$f3 = \Base::instance();

$f3->route(
	"GET /",
	function ($f3)
	{
		if ($f3->exists("SESSION.user"))
		{
			$f3->set("first_name", $f3->get("SESSION.user.first_name"));
			$f3->set("last_name", $f3->get("SESSION.user.last_name"));
		}

		echo \Template::instance()->render("main.htm");
	}
);
