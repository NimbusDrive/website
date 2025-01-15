<?php

require "../vendor/autoload.php";

require "env.php";
require "database.php";

foreach (glob($_ENV["MODELS_DIR"] . "/*.php") as $ModelFile)
	require_once $ModelFile;

foreach (glob($_ENV["ROUTES_DIR"] . "/*.php") as $RouteFile)
	require_once $RouteFile;

$f3 = \Base::instance();

$f3->set("BASE", $_ENV["BASE_URL"]);
$f3->set("UI", $_ENV["VIEWS_DIR"]);

$f3->run();
