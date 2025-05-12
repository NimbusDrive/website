<?php

$f3 = \Base::instance();

$f3->route(
	"GET /logout",
	function ($f3)
	{
		$Session = new Session();
		$Session->destroy(session_id());

		if (ini_get("session.use_cookies"))
		{
			$CookieParams = session_get_cookie_params();

			setcookie(
				session_name(),
				"",
				time() - 3600,
				$CookieParams["path"],
				$CookieParams["domain"],
				$CookieParams["secure"],
				$CookieParams["httponly"]
			);
		}

		$f3->clear("SESSION");
		$f3->reroute("/");
	}
);

