<?php

namespace Migration;

class Users extends \Chez14\Ilgar\MigrationPacket
{
	public function on_migrate()
	{
		$f3 = \F3::instance();

		$f3->get("DB")->exec("
			CREATE TABLE IF NOT EXISTS `users` (
				`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				`first_name` VARCHAR(128) NOT NULL,
				`last_name` varchar(128) NOT NULL,
				`email_address` varchar(255) NOT NULL UNIQUE,
				`password` BLOB NOT NULL,
				`is_administrator` BOOLEAN DEFAULT 0,
				`creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
			)
		");
	}

	public function on_failed(\Exception $e)
	{

	}
}
