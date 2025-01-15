<?php

require "../vendor/autoload.php";

require "env.php";
require "database.php";

require "routes/main.php";

$f3 = \Base::instance();

$f3->set("UI", "views/");

$f3->run();
