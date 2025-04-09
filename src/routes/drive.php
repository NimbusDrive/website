<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

$f3 = \Base::instance();

function BuildFileList(array $Files, string $CurrentFolder = "")
{
	$Tree = [];

	foreach ($Files as $File)
	{
		$Path = trim($File["storage_path"], "/");
		$Parts = explode("/", $Path);
		$CurrentLevel = &$Tree;

		foreach ($Parts as $Index => $Part)
		{
			if ($Index === count($Parts) - 1)
			{
				$CurrentLevel[$Part] = ["Data" => $File];
			} else
			{
				if (!isset($CurrentLevel[$Part]))
				{
					$CurrentLevel[$Part] = [];
				}

				$CurrentLevel = &$CurrentLevel[$Part];
			}
		}
	}

	if ($CurrentFolder !== "")
	{
		$RequestedFolder = explode("/", trim($CurrentFolder, "/"));
		$CurrentLevel = &$Tree;
		foreach ($RequestedFolder as $Folder)
		{
			if (isset($CurrentLevel[$Folder]))
			{
				$CurrentLevel = &$CurrentLevel[$Folder];
			} else
			{
				return [];
			}
		}
		return $CurrentLevel;
	}

	return $Tree;
}

$f3->route(
	"GET /drive",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

		$f3->reroute("/drive/home");
	}
);

$f3->route(
	"GET /drive/home",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

		$f3->set("DriveTab", "Home");

		echo \Template::instance()->render("drive/home.htm");
	}
);

function RouteDriveMain($f3, $Params)
{
	if (!$f3->exists("SESSION.user"))
	{
		$f3->reroute("/");
		return;
	}

	$f3->set("DriveTab", "Main");

	$Folder = isset($Params["subdir"]) ? $Params["subdir"] : "";

	$Files = $f3->get("DB")->exec("select * from `files` where `user_id` = ?", $f3->get("SESSION.user.id"));
	$f3->set("FileList", BuildFileList($Files, $Folder));

	echo \Template::instance()->render("drive/main.htm");
}

$f3->route(
	"GET /drive/main",
	"RouteDriveMain"
);

$f3->route(
	"GET /drive/main/@subdir*",
	"RouteDriveMain"
);

$f3->route(
	"GET /drive/shared",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

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
			$f3->error(401, "Unauthorized");
			return;
		}

		$User = new User($f3->get("DB"));
		$User->load(array("email_address=?", $f3->get("SESSION.user.email_address")));

		if ($User->dry())
		{
			$f3->error(401, "Unauthorized");
			return;
		}

		$Token = $f3->get("POST.token");
		$CSRF = $f3->get("SESSION.csrf");

		if (empty($Token) || empty($CSRF) || $Token !== $CSRF)
		{
			$f3->reroute("/drive"); // CSRF Attack detected
			return;
		}

		$Files = Web::instance()->receive(function ($UploadedFile) use ($f3, $User)
		{
			// TODO: File size?
			// TODO: Extension blacklist?

			$FileHash = hash_file("sha256", $UploadedFile["tmp_name"]);
			$FileExt = strtolower(pathinfo($UploadedFile["name"], PATHINFO_EXTENSION));

			$FileName = $FileHash . "." . $FileExt;
			$Path = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $FileName;

			if (!move_uploaded_file($UploadedFile["tmp_name"], $Path))
			{
				return false;
			}

			$DBFile = new File($f3->get("DB"));
			$DBFile->user_id = $User->id;
			$DBFile->hash = $FileHash;
			$DBFile->storage_path = "/" . basename($UploadedFile["name"]); // TODO:
			$DBFile->internal_path = $Path;
			$DBFile->status = "None";

			try
			{
				$DBFile->save();
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
