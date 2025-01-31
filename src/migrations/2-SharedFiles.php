<?php

namespace Migration;

class SharedFiles extends \Chez14\Ilgar\MigrationPacket
{
	public function on_migrate()
	{
		$f3 = \F3::instance();

		$f3->get("DB")->exec("
			CREATE TABLE IF NOT EXISTS `shared_files` (
				`file_id` INT UNSIGNED NOT NULL,
				`shared_user_id` INT UNSIGNED NOT NULL,

				FOREIGN KEY (`file_id`) REFERENCES `files`(`id`),
				FOREIGN KEY (`shared_user_id`) REFERENCES `users`(`id`)

				ON DELETE RESTRICT ON UPDATE CASCADE
			)
		");
	}

	public function on_failed(\Exception $e)
	{

	}
}
