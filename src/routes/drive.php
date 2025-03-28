<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

$f3 = \Base::instance();

function BuildFileHTML($Tree)
{
	$Result = "<div class=\"ui list\">";

	foreach ($Tree as $Name => $Sub)
	{
		if ($Name == "_file")
		{
			$Result .= "<div class=\"item\"><i class=\"file icon\"></i><div class=\"content\">";
			$Result .= "<div class=\"header\">" . htmlspecialchars($Sub["storage_path"], ENT_QUOTES, "UTF-8") . "</div>";
			$Result .= "</div></div>";
		}
		else
		{
			$Result .= "<div class=\"item\"><i class=\"folder icon\"></i><div class=\"content\">";
			$Result .= "<div class=\"header\">" . htmlspecialchars($Name, ENT_QUOTES, "UTF-8") . "</div>";
			$Result .= BuildFileHTML($Sub);
			$Result .= "</div></div>";
		}
	}

	$Result .= "</div>";

	return $Result;
}

function BuildFileList($Files)
{
	$Tree = [];

	foreach ($Files as $File)
	{
		$Parts = explode("/", trim($File["storage_path"], "/"));
		$Current = &$Tree;

		foreach ($Parts as $Part)
		{
			if (!isset($Current[$Part]))
				$Current[$Part] = [];

			$Current = &$Current[$Part];
		}

		$Current["_file"] = $File;
	}

	return BuildFileHTML($Tree);
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

$f3->route(
	"GET /drive/main",
	function ($f3)
	{
		if (!$f3->exists("SESSION.user"))
		{
			$f3->reroute("/");
			return;
		}

		$f3->set("DriveTab", "Main");

		$Files = $f3->get("DB")->exec("select * from `files` where `user_id` = ?", $f3->get("SESSION.user.id"));
		$f3->set("FileDisplay", View::instance()->raw(BuildFileList($Files)));

		echo \Template::instance()->render("drive/main.htm");
	}
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

		$Files = Web::instance()->receive(function($UploadedFile) use ($f3, $User)
		{
			// TODO: File size?
			// TODO: Extension blacklist?

			$FileHash = hash_file("sha256", $UploadedFile["tmp_name"]);
			$FileExt = strtolower(pathinfo($UploadedFile["name"], PATHINFO_EXTENSION));

			$FileName = $FileHash . "." . $FileExt;
			$Path = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $FileName;

			if (!move_uploaded_file($UploadedFile["tmp_name"], $Path))
				return false;

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
