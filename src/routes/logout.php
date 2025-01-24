<?php

$f3 = \Base::instance();

$f3->route(
	"GET /logout",
	function ($f3)
	{
		$f3->clear("SESSION");
		$f3->reroute("/");
	}
);
