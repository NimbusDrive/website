<?php

use NimbusDrive\Models\User;

$f3 = \Base::instance();

$f3->route(
	"GET /signup",
	function ($f3)
	{
		echo \Template::instance()->render("signup.htm");
	}
);

$f3->route(
	"POST /signup",
	function ($f3)
	{
		$first_name = $f3->get("POST.first_name");
		$last_name = $f3->get("POST.last_name");
		$email_address = $f3->get("POST.email_address");
		$password = $f3->get("POST.password");
		$password_verify = $f3->get("POST.password_verify");

		$errors = [];

		if (empty($first_name))
			$errors["first_name"] = "First name is required.";

		if (empty($last_name))
			$errors["last_name"] = "Last name is required.";

		if (empty($email_address) || !filter_var($email_address, FILTER_VALIDATE_EMAIL))
			$errors["email"] = "Invalid email address provided.";

		if (empty($password) || empty($password_verify))
			$errors["password"] = "No password provided.";

		if (strcmp($password, $password_verify))
			$errors["password_verify"] = "Passwords don't match.";

		if (!empty($errors))
		{
			$f3->set("errors", $errors);
			$f3->reroute("/signup");

			return;
		}

		$User = new User($f3->get("DB"));
		$User->load(array("email_address=?", $email_address));

		if (!$User->dry())
		{
			$errors["login"] = "A user with this email address already exists.";

			$f3->set("errors", $errors);
			$f3->reroute("/signup");

			return;
		}

		$password = password_hash($password, PASSWORD_DEFAULT);

		$User->first_name = $first_name;
		$User->last_name = $last_name;
		$User->email_address = $email_address;
		$User->password = $password;
		$User->save();

		new Session();

		$f3->reroute("/login");
	}
);
