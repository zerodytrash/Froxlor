<?php
namespace Froxlor;

/**
 * Class to manage the current user / session
 */
class CurrentUser
{

	public static function setData($data = array())
	{
		$_SESSION['userinfo'] = $data;
	}

	/**
	 *
	 * @return bool
	 */
	public static function hasSession()
	{
		return ! empty($_SESSION) && isset($_SESSION['userinfo']) && ! empty($_SESSION['userinfo']);
	}

	/**
	 *
	 * @return bool
	 */
	public static function isAdmin()
	{
		return (self::getField('adminsession') == 1);
	}

	/**
	 *
	 * @param string $index
	 *
	 * @return string|array
	 */
	public static function getField($index = "")
	{
		return isset($_SESSION['userinfo'][$index]) ? $_SESSION['userinfo'][$index] : "";
	}

	/**
	 *
	 * @param string $index
	 * @param mixed $data
	 *
	 * @return boolean
	 */
	public static function setField(string $index, $data)
	{
		$_SESSION['userinfo'][$index] = $data;
		return true;
	}

	/**
	 *
	 * @return array
	 */
	public static function getData()
	{
		return $_SESSION['userinfo'];
	}
}
