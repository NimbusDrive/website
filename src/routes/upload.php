<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

$f3 = \Base::instance();

$f3->route(
	"GET /upload",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

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

		$FileData = $f3->get("POST.file_data");

		if (empty($FileData))
		{
			// TODO: Error message
			$f3->reroute("/");
			return;
		}

		$FileHash = hash("sha256", $FileData);

		$FileName = basename($FileHash);
		$Path = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $FileName;
		$Directory = dirname($Path);

		if (!is_dir($Directory))
		{
			if (!mkdir($Directory, recursive: true))
			{
				// TODO: Error message
				$f3->reroute("/");
				return;
			}
		}

		$File = new File($f3->get("DB"));
		$File->user_id = $User->id;
		$File->hash = $FileHash;
		$File->storage_path = "C:\\"; // TODO:
		$File->internal_path = $Path;
		$File->status = "None";

		try
		{
			$File->save();

			$Written = file_put_contents($Path, $FileData);

			if ($Written === false)
			{
				// TODO: Error message
				$f3->reroute("/");
				return;
			}
		} catch (Exception $Exception)
		{
			// TODO: Error message
			error_log("!! Failed to save file !!");
			error_log($Exception->getMessage());
		}

		$f3->reroute("/");
	}
);
