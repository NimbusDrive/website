<?php

use NimbusDrive\Models\User;

$f3 = \Base::instance();

$f3->route(
	"GET /login",
	function ($f3)
	{
		echo \Template::instance()->render("login.htm");
	}
);

$f3->route(
	"POST /login",
	function ($f3)
	{
		$email_address = $f3->get("POST.email_address");
		$password = $f3->get("POST.password");

		$errors = [];

		if (empty($email_address) || !filter_var($email_address, FILTER_VALIDATE_EMAIL))
		{
			$errors["email"] = "Invalid email address provided.";
		}

		if (empty($password))
		{
			$errors["password"] = "No password provided.";
		}

		if (!empty($errors))
		{
			$f3->set("errors", $errors);
			$f3->reroute("/login");

			return;
		}

		$User = new User($f3->get("DB"));
		$User->load(array("email_address=?", $email_address));

		if ($User->dry() || !password_verify($password, $User->password))
		{
			$errors["login"] = "Invalid credentials entered.";

			$f3->set("errors", $errors);
			$f3->reroute("/login");

			return;
		}

		$Session = new DB\SQL\Session($f3->get("DB"), "sessions", true, null, "CSRF");
		$f3->copy("CSRF", "SESSION.csrf");
		$f3->set("SESSION.user", $User->cast());

		$f3->reroute("/drive");
	}
);
