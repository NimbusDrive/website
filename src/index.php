<?php

require "../vendor/autoload.php";

require "env.php";
require "database.php";

require "routes/main.php";

$f3 = \Base::instance();
$f3->run();
