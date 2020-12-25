<?php

namespace App\Globals;

class FrontEndParametersFacade
{
	public static function createNewFrontEndParametersInstance()
	{
		$GLOBALS['FrontEndParameters'] = new FrontEndParameters();
	}

	private static function setter($key, $value)
	{
		$GLOBALS['FrontEndParameters']->_setter($key, $value);
	}

	private static function getter($key)
	{
		return $GLOBALS['FrontEndParameters']->_getter($key);
	}

	/* == Setters == */

	public static function setBaseUrl($url)
	{
		self::setter('baseUrl', $url);
	}

	public static function setIsAuthenticated($value)
	{
		self::setter('isAuthenticated', $value);
	}

	public static function setCsrfTokens($tokens)
	{
		self::setter('csrfTokens', $tokens);
	}

	public static function setPreviousParameters($params)
	{
		self::setter('previousParameters', $params);
	}

	public static function setErrors($errors)
	{
		self::setter('errors', $errors);
	}

	public static function setUserData($data)
	{
		self::setter('userData', $data);
	}

	public static function setFlash($flash)
	{
		self::setter('flash', $flash);
	}

	/* == Getters == */

	public static function getBaseUrl()
	{
		return self::getter('baseUrl');
	}

	public static function getIsAuthenticated()
	{
		return self::getter('isAuthenticated');
	}

	public static function getCsrfTokens()
	{
		return self::getter('csrfTokens');
	}

	public static function getPreviousParameters()
	{
		return self::getter('previousParameters');
	}

	public static function getErrors()
	{
		return self::getter('errors');
	}

	public static function getUserData()
	{
		return self::getter('userData');
	}

	public static function getFlash()
	{
		return self::getter('flash');
	}
}
