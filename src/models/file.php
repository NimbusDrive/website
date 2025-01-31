<?php

namespace NimbusDrive\Models;

/**
* @property int $id
* @property int $user_id
* @property string $hash
* @property string $storage_path
* @property string $internal_path
* @property string $status
*/
class File extends \DB\SQL\Mapper
{
	public function __construct($db)
	{
		parent::__construct($db, "files");
	}
}
