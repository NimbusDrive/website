<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

$f3 = \Base::instance();

$f3->route(
	"GET /upload",
	function ($f3)
	{
		echo \Template::instance()->render("upload.htm");
	}
);

$f3->route(
	"POST /upload",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

		$User = new User($f3->get("DB"));
		$User->load(array("email_address=?", $f3->get("SESSION.user.email_address")));

		if ($User->dry())
		{
			$f3->reroute("/");
			return;
		}

		$Token = $f3->get("POST.token");
		$CSRF = $f3->get("SESSION.csrf");

		if (empty($Token) || empty($CSRF) || $Token !== $CSRF)
		{
			$f3->reroute("/upload"); // CSRF Attack detected
			return;
		}

		// TrustMeBro
		// $FileData = $f3->get("POST.file_data");

		// $File = new File($f3->get("DB"));
		// $File->user_id = $User->id;
		// $File->file_data = $FileData;
		// $File->save();

		$f3->reroute("/");
	}
);
