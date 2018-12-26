<?php
	function onLogin($user) {
		$token = GenerateRandomToken(); // generate a token, should be 128 - 256 bit
		storeTokenForUser($user, $token);
		$cookie = $user . ':' . $token;
		$mac = hash_hmac('sha256', $cookie, SECRET_KEY);
		$cookie .= ':' . $mac;
		setcookie('rememberme', $cookie);
	}

	function rememberMe() {
		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
		if ($cookie) {
			list ($user, $token, $mac) = explode(':', $cookie);
			if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY), $mac)) {
				return false;
			}
			$usertoken = fetchTokenByUserName($user);
			if (hash_equals($usertoken, $token)) {
				logUserIn($user);
			}
		}
	}
?>