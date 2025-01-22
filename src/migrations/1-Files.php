<?php

namespace Migration;

class Files extends \Chez14\Ilgar\MigrationPacket
{
	public function on_migrate()
	{
		$f3 = \F3::instance();

		$f3->get("DB")->exec("
			CREATE TABLE IF NOT EXISTS `files` (
				`user_id` int unsigned not null,
				`file_data` longblob not null,

				foreign key (`user_id`) references `users`(`id`)
			)
		");
	}

	public function on_failed(\Exception $e)
	{

	}
}
