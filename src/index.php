<?php

require "../vendor/autoload.php";

require "env.php";
require "database.php";

require "models/user.php";

require "routes/main.php";

$f3 = \Base::instance();

$f3->set("BASE", "/php/capstone_project");
$f3->set("UI", "views/");

$f3->run();
