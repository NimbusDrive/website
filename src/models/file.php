<?php

namespace NimbusDrive\Models;

class File extends \DB\SQL\Mapper
{
	public function __construct($db)
	{
		parent::__construct($db, "files");
	}
}
