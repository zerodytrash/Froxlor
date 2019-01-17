<?php
namespace Froxlor;

use Froxlor\Database\Database;

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
		return (self::getField('adminsession') == 1 && self::getField('adminid') > 0);
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
	public static function setField($index, $data)
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

	/**
	 * re-read in the user data if a valid session exists
	 *
	 * @return boolean
	 */
	public static function reReadUserData()
	{
		$table = self::isAdmin() ? TABLE_PANEL_ADMINS : TABLE_PANEL_CUSTOMERS;
		$userinfo_stmt = Database::prepare("
			SELECT * FROM `" . $table . "` WHERE `loginname`= :loginname AND `deactivated` = '0'
		");
		$userinfo = Database::pexecute_first($userinfo_stmt, array(
			"loginname" => self::getField('loginname')
		));
		if ($userinfo) {
			// dont just set the data, we need to merge with current data
			// array_merge is a right-reduction - value existing in getData() will be overwritten with $userinfo,
			// other than the union-operator (+) which would keep the values already existing from getData()
			$newuserinfo = array_merge(self::getData(), $userinfo);
			self::setData($newuserinfo);
			return true;
		}
		// unset / logout
		unset($_SESSION['userinfo']);
		self::setData(array());
		return false;
	}
}
