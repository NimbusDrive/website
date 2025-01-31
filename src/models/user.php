<?php

namespace NimbusDrive\Models;

/**
* @property int $id
* @property string $first_name
* @property string $last_name
* @property string $email_address
* @property string $password
* @property bool $is_administrator
* @property string $creation_date
*/
class User extends \DB\SQL\Mapper
{
	public function __construct($db)
	{
		parent::__construct($db, "users");
	}
}
