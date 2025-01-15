<?php

namespace NimbusDrive\Models;

class User extends \DB\SQL\Mapper
{
	public function __construct($db)
	{
		parent::__construct($db, "users");
	}
}
