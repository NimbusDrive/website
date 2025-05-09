<?php

use NimbusDrive\Models\User;
use NimbusDrive\Models\File;

$f3 = \Base::instance();

function SortTree(array $Tree): array
{
	$Folders = [];
	$Files = [];

	foreach ($Tree as $key => $value)
	{
		if (isset($value["Data"]))
		{
			$Files[$key] = $value;
		} else
		{
			$Folders[$key] = sortTree($value);
		}
	}

	uksort($Folders, "strcasecmp");
	uksort($Files, "strcasecmp");

	return $Folders + $Files;
}

function BuildFileList(array $Files, string $CurrentFolder = ""): array
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

		return SortTree($CurrentLevel);
	}

	return SortTree($Tree);
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

		$UserDir = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $User->id;

		if (!is_dir($UserDir))
		{
			mkdir($UserDir, 0777, true);
		}

		$Files = Web::instance()->receive(function ($UploadedFile) use ($f3, $User, $UserDir)
		{
			// TODO: File size?
			// TODO: Extension blacklist?
			$FileHash = hash_file("sha256", $UploadedFile["tmp_name"]);
			$FileExt = strtolower(pathinfo($UploadedFile["name"], PATHINFO_EXTENSION));

			$FileName = $FileHash . "." . $FileExt;
			$Path = $UserDir . DIRECTORY_SEPARATOR . $FileName;

			if (!move_uploaded_file($UploadedFile["tmp_name"], $Path))
			{
				return false;
			}

			$SubPathRaw = $f3->get("POST.path") ?? ""; // TODO: Might have an exploit of some sorts? Path traversal? Meh who cares
			$SubPath = trim(urldecode($SubPathRaw), "/");

			$StoragePath = ($SubPath !== "" ? "/" . $SubPath : "") . "/" . basename($UploadedFile["name"]);
			$StoragePath = preg_replace("#/+#", "/", $StoragePath);

			$DBFile = new File($f3->get("DB"));
			$DBFile->user_id = $User->id;
			$DBFile->hash = $FileHash;
			$DBFile->storage_path = $StoragePath;
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

$f3->route("POST /drive/folder/create", function ($f3)
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

	$FolderPath = trim($f3->get("POST.path"), "/");

	if (empty($FolderPath))
	{
		$f3->error(500, "Folder creation failed");
		return;
	}

	$DB = $f3->get("DB");

	$Exists = $DB->exec(
		"select count(*) as `count` from `files` where user_id = ? and storage_path = ?",
		[$User->id, $FolderPath]
	)[0]["count"];

	if ($Exists > 0)
	{
		$f3->error(500, "Folder already exists");
		return;
	}

	$UserDir = $_ENV["INTERNAL_STORAGE_DIR"] . DIRECTORY_SEPARATOR . $User->id;
	$FolderDir = $UserDir . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $FolderPath);

	if (!is_dir($FolderDir))
	{
		mkdir($FolderDir, 0777, true);
	}

	$DummyPath = $FolderDir . DIRECTORY_SEPARATOR . "__finit__";

	if (!file_exists($DummyPath))
	{
		file_put_contents($DummyPath, "");
	}

	$VirtualPath = "/" . ltrim($FolderPath . "/__finit__", "/");

	$DB->exec(
		"insert into `files` (`user_id`, `hash`, `storage_path`, `internal_path`, `status`) values (?, ?, ?, ?, ?)",
		[$User->id, null, $VirtualPath, $DummyPath, "None"]
	);

	$f3->error(200, "Folder creation succeeded");
});

$f3->route("POST /drive/delete", function ($f3)
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

	$FileID = $f3->get("POST.id");

	if (empty($FileID))
	{
		$f3->error(400, "Missing file ID");
		return;
	}

	$File = new File($f3->get("DB"));
	$File->load(array("id = ? AND user_id = ?", $FileID, $User->id));

	if ($File->dry())
	{
		$f3->error(404, "File not found");
		return;
	}

	if (!empty($File->internal_path) && file_exists($File->internal_path))
	{
		if (!unlink($File->internal_path))
		{
			$f3->error(500, "Failed to delete file from filesystem");
			return;
		}
	}

	try
	{
		$File->erase();
	} catch (Exception $e)
	{
		$f3->error(500, "Failed to delete file from database");
		return;
	}

	$f3->error(200, "File deleted successfully");
});

$f3->route("POST /drive/rename", function ($f3)
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

	$FileID = $f3->get("POST.id");

	if (empty($FileID))
	{
		$f3->error(400, "Missing file ID");
		return;
	}

	$File = new File($f3->get("DB"));
	$File->load(array("id = ? AND user_id = ?", $FileID, $User->id));

	if ($File->dry())
	{
		$f3->error(404, "File not found");
		return;
	}

	$NewName = $f3->get("POST.name");

	if (empty($NewName))
	{
		$f3->error(400, "Missing file Name");
		return;
	}

	$OldStoragePath = $File->storage_path;
	$StorageDir = dirname($OldStoragePath);

	$NewStoragePath = $StorageDir . "/" . $NewName;
	$NewStoragePath = preg_replace("#/+#", "/", $NewStoragePath);

	$File->storage_path = stripcslashes($NewStoragePath);

	try
	{
		$File->save();
	} catch (Exception $e)
	{
		$f3->error(500, "Failed to update file record in database");
		return;
	}

	$f3->error(200, "File renamed successfully");
});

$f3->route("GET /drive/download/@id", function ($f3, $Params)
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

	$FileID = $Params["id"];

	if (empty($FileID))
	{
		$f3->error(400, "Missing file ID");
		return;
	}

	$File = new File($f3->get("DB"));
	$File->load(array("id = ? AND user_id = ?", $FileID, $User->id));

	if ($File->dry())
	{
		$f3->error(404, "File not found");
		return;
	}

	$FileName = basename($File->internal_path);

	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=" . $FileName);
	header("Content-Transfer-Encoding: binary");
	header("Expires: 0");
	header("Cache-Control: must-revalidate");
	header("Pragma: public");
	header("Content-Length: " . filesize($File->internal_path));

	ob_clean();
	flush();

	readfile($File->internal_path);
});

$f3->route("POST /drive/share", function ($f3)
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

	$FileID = $f3->get("POST.id");

	if (empty($FileID))
	{
		$f3->error(400, "Missing file ID");
		return;
	}

	$File = new File($f3->get("DB"));
	$File->load(array("id = ? AND user_id = ?", $FileID, $User->id));

	if ($File->dry())
	{
		$f3->error(404, "File not found");
		return;
	}

	$SharedEmail = $f3->get("POST.email");

	if (empty($SharedEmail))
	{
		$f3->error(400, "Missing emaill address");
		return;
	}

	$ShareUser = new User($f3->get("DB"));
	$ShareUser->load(array("email_address=?", $SharedEmail));

	if ($ShareUser->dry())
	{
		$f3->error(404, "Share user not found");
		return;
	}

	error_log("yay!");
});
