<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

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

$f3->route(
	"POST /drive/upload",
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
			$f3->reroute("/drive"); // CSRF Attack detected
			return;
		}

		$Files = Web::instance()->receive(function($File) use ($f3, $User)
		{
			// TODO: File size?
			// TODO: Extension blacklist?

			$FileHash = hash_file("sha256", $File["tmp_name"]);
			$FileExt = strtolower(pathinfo($File["name"], PATHINFO_EXTENSION));

			$FileName = $FileHash . "." . $FileExt;
			$Path = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $FileName;

			if (!move_uploaded_file($File["tmp_name"], $Path))
				return false;

			$File = new File($f3->get("DB"));
			$File->user_id = $User->id;
			$File->hash = $FileHash;
			$File->storage_path = "C:\\"; // TODO:
			$File->internal_path = $Path;
			$File->status = "None";

			try
			{
				$File->save();
			} catch (Exception $Exception)
			{
				return false;
			}

			return true;
		}, true, false);

		if (!$Files)
		{
			$f3->error(500, "File upload failed");
			return;
		}

		$f3->error(200, "File upload succeeded");
	}
);
