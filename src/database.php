<?php

$f3 = \Base::instance();

$f3->set("DB", new DB\SQL($_ENV["DB_CONNECTION_STR"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]));
new DB\SQL\Session($f3->get("DB"));

$f3->set("ILGAR", [
	"path" => $_ENV["MIGRATIONS_DIR"]
]);

\Chez14\Ilgar\Boot::now();
