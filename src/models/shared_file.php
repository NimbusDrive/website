<?php

namespace NimbusDrive\Models;

/**
* @property int $file_id
* @property int $shared_user_id
*/
class SharedFile extends \DB\SQL\Mapper
{
	public function __construct($db)
	{
		parent::__construct($db, "shared_files");
	}
}
