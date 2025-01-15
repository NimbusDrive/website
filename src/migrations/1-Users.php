<?php

namespace Migration;

class Users extends \Chez14\Ilgar\MigrationPacket
{
	public function on_migrate()
	{
		error_log("hi");

		$f3 = \F3::instance();

		$f3->get("DB")->exec("
			CREATE TABLE IF NOT EXISTS `users` (
				`id` int unsigned auto_increment primary key,
				`first_name` varchar(128) not null,
				`last_name` varchar(128) not null,
				`email_address` varchar(255) not null unique,
				`password` blob not null
			)
		");
	}

	public function on_failed(\Exception $e)
	{
		error_log("Failed to create users " . $e->getMessage());
	}
}
