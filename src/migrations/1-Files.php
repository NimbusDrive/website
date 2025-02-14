<?php

namespace Migration;

class Files extends \Chez14\Ilgar\MigrationPacket
{
	public function on_migrate()
	{
		$f3 = \F3::instance();

		$f3->get("DB")->exec(
			"
			CREATE TABLE IF NOT EXISTS `files` (
				`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				`user_id` INT UNSIGNED NOT NULL,
				`hash` TEXT(255),
				`storage_path` TEXT(256) NOT NULL,
				`internal_path` TEXT(256) NOT NULL UNIQUE,
				`status` ENUM(?, ?, ?) NOT NULL,

				FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)

				ON DELETE RESTRICT ON UPDATE CASCADE
			)
		",
			array(
			1 => "None",
			2 => "Uploading",
			3 => "Deleted"
		)
		);
	}

	public function on_failed(\Exception $e)
	{

	}
}
