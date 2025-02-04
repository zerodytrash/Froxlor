<?php
namespace Froxlor\Api\Commands;

use Froxlor\Database\Database;
use Froxlor\Settings;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Froxlor team <team@froxlor.org> (2010-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package API
 * @since 0.10.0
 *       
 */
class DirProtections extends \Froxlor\Api\ApiCommand implements \Froxlor\Api\ResourceEntity
{

	/**
	 * add htaccess protection to a given directory
	 *
	 * @param int $customerid
	 *        	optional, required when called as admin (if $loginname is not specified)
	 * @param string $loginname
	 *        	optional, required when called as admin (if $customerid is not specified)
	 * @param string $path
	 * @param string $username
	 * @param string $directory_password
	 * @param string $directory_authname
	 *        	optional name/description for the protection
	 *        	
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array
	 */
	public function add()
	{
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras')) {
			throw new \Exception("You cannot access this resource", 405);
		}
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras.directoryprotection')) {
			throw new \Exception("You cannot access this resource", 405);
		}

		// get needed customer info to reduce the email-address-counter by one
		$customer = $this->getCustomerData();

		// required parameters
		$path = $this->getParam('path');
		$username = $this->getParam('username');
		$password = $this->getParam('directory_password');

		// parameters
		$authname = $this->getParam('directory_authname', true, '');

		// validation
		$path = \Froxlor\FileDir::makeCorrectDir(\Froxlor\Validate\Validate::validate($path, 'path', \Froxlor\Validate\Validate::REGEX_DIR, '', array(), true));
		$path = \Froxlor\FileDir::makeCorrectDir($customer['documentroot'] . '/' . $path);
		$username = \Froxlor\Validate\Validate::validate($username, 'username', '/^[a-zA-Z0-9][a-zA-Z0-9\-_]+\$?$/', '', array(), true);
		$authname = \Froxlor\Validate\Validate::validate($authname, 'directory_authname', '/^[a-zA-Z0-9][a-zA-Z0-9\-_ ]+\$?$/', '', array(), true);
		\Froxlor\Validate\Validate::validate($password, 'password', '', '', array(), true);

		// check for duplicate usernames for the path
		$username_path_check_stmt = Database::prepare("
			SELECT `id`, `username`, `path` FROM `" . TABLE_PANEL_HTPASSWDS . "`
			WHERE `username`= :username AND `path`= :path AND `customerid`= :customerid
		");
		$params = array(
			"username" => $username,
			"path" => $path,
			"customerid" => $customer['customerid']
		);
		$username_path_check = Database::pexecute_first($username_path_check_stmt, $params, true, true);

		$password_enc = \Froxlor\System\Crypt::makeCryptPassword($password, true);

		// duplicate check
		if ($username_path_check && $username_path_check['username'] == $username && $username_path_check['path'] == $path) {
			\Froxlor\UI\Response::standard_error('userpathcombinationdupe', '', true);
		} elseif ($password == $username) {
			\Froxlor\UI\Response::standard_error('passwordshouldnotbeusername', '', true);
		}

		// insert the entry
		$stmt = Database::prepare("
			INSERT INTO `" . TABLE_PANEL_HTPASSWDS . "` SET
			`customerid` = :customerid,
			`username` = :username,
			`password` = :password,
			`path` = :path,
			`authname` = :authname
		");
		$params = array(
			"customerid" => $customer['customerid'],
			"username" => $username,
			"password" => $password_enc,
			"path" => $path,
			"authname" => $authname
		);
		Database::pexecute($stmt, $params, true, true);
		$id = Database::lastInsertId();
		$this->logger()->logAction($this->isAdmin() ? \Froxlor\FroxlorLogger::ADM_ACTION : \Froxlor\FroxlorLogger::USR_ACTION, LOG_INFO, "[API] added directory-protection for '" . $username . " (" . $path . ")'");
		\Froxlor\System\Cronjob::inserttask('1');

		$result = $this->apiCall('DirProtections.get', array(
			'id' => $id
		));
		return $this->response(200, "successful", $result);
	}

	/**
	 * return a directory-protection entry by either id or username
	 *
	 * @param int $id
	 *        	optional, the directory-protection-id
	 * @param string $username
	 *        	optional, the username
	 *        	
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array
	 */
	public function get()
	{
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras')) {
			throw new \Exception("You cannot access this resource", 405);
		}

		$id = $this->getParam('id', true, 0);
		$un_optional = ($id <= 0 ? false : true);
		$username = $this->getParam('username', $un_optional, '');

		$params = array();
		if ($this->isAdmin()) {
			if ($this->getUserDetail('customers_see_all') == false) {
				// if it's a reseller or an admin who cannot see all customers, we need to check
				// whether the database belongs to one of his customers
				$_custom_list_result = $this->apiCall('Customers.listing');
				$custom_list_result = $_custom_list_result['list'];
				$customer_ids = array();
				foreach ($custom_list_result as $customer) {
					$customer_ids[] = $customer['customerid'];
				}
				$result_stmt = Database::prepare("
					SELECT * FROM `" . TABLE_PANEL_HTPASSWDS . "`
					WHERE `customerid` IN (" . implode(", ", $customer_ids) . ")
					AND (`id` = :idun OR `username` = :idun)
				");
			} else {
				$result_stmt = Database::prepare("
					SELECT * FROM `" . TABLE_PANEL_HTPASSWDS . "`
					WHERE (`id` = :idun OR `username` = :idun)
				");
			}
		} else {
			if (Settings::IsInList('panel.customer_hide_options', 'extras.directoryprotection')) {
				throw new \Exception("You cannot access this resource", 405);
			}
			$result_stmt = Database::prepare("
				SELECT * FROM `" . TABLE_PANEL_HTPASSWDS . "`
				WHERE `customerid` = :customerid
				AND (`id` = :idun OR `username` = :idun)
			");
			$params['customerid'] = $this->getUserDetail('customerid');
		}
		$params['idun'] = ($id <= 0 ? $username : $id);
		$result = Database::pexecute_first($result_stmt, $params, true, true);
		if ($result) {
			$this->logger()->logAction($this->isAdmin() ? \Froxlor\FroxlorLogger::ADM_ACTION : \Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, "[API] get directory protection for '" . $result['path'] . "'");
			return $this->response(200, "successful", $result);
		}
		$key = ($id > 0 ? "id #" . $id : "username '" . $username . "'");
		throw new \Exception("Directory protection with " . $key . " could not be found", 404);
	}

	/**
	 * update htaccess protection of a given directory
	 *
	 * @param int $id
	 *        	optional the directory-protection-id
	 * @param string $username
	 *        	optional, the username
	 * @param int $customerid
	 *        	optional, required when called as admin (if $loginname is not specified)
	 * @param string $loginname
	 *        	optional, required when called as admin (if $customerid is not specified)
	 * @param string $directory_password
	 *        	optional, leave empty for no change
	 * @param string $directory_authname
	 *        	optional name/description for the protection
	 *        	
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array
	 */
	public function update()
	{
		$id = $this->getParam('id', true, 0);
		$un_optional = ($id <= 0 ? false : true);
		$username = $this->getParam('username', $un_optional, '');

		// validation
		$result = $this->apiCall('DirProtections.get', array(
			'id' => $id,
			'username' => $username
		));
		$id = $result['id'];

		// parameters
		$password = $this->getParam('directory_password', true, '');
		$authname = $this->getParam('directory_authname', true, $result['authname']);

		// get needed customer info
		$customer = $this->getCustomerData();

		// validation
		$authname = \Froxlor\Validate\Validate::validate($authname, 'directory_authname', '/^[a-zA-Z0-9][a-zA-Z0-9\-_ ]+\$?$/', '', array(), true);
		\Froxlor\Validate\Validate::validate($password, 'password', '', '', array(), true);

		$upd_query = "";
		$upd_params = array(
			"id" => $result['id'],
			"cid" => $customer['customerid']
		);
		if (! empty($password)) {
			if ($password == $result['username']) {
				\Froxlor\UI\Response::standard_error('passwordshouldnotbeusername', '', true);
			}
			$password_enc = \Froxlor\System\Crypt::makeCryptPassword($password, true);

			$upd_query .= "`password`= :password_enc";
			$upd_params['password_enc'] = $password_enc;
		}
		if ($authname != $result['authname']) {
			if (! empty($upd_query)) {
				$upd_query .= ", ";
			}
			$upd_query .= "`authname` = :authname";
			$upd_params['authname'] = $authname;
		}

		// build update query
		if (! empty($upd_query)) {
			$upd_stmt = Database::prepare("
				UPDATE `" . TABLE_PANEL_HTPASSWDS . "` SET " . $upd_query . " WHERE `id` = :id AND `customerid`= :cid
			");
			Database::pexecute($upd_stmt, $upd_params, true, true);
			\Froxlor\System\Cronjob::inserttask('1');
		}

		$this->logger()->logAction($this->isAdmin() ? \Froxlor\FroxlorLogger::ADM_ACTION : \Froxlor\FroxlorLogger::USR_ACTION, LOG_INFO, "[API] updated directory-protection '" . $result['username'] . " (" . $result['path'] . ")'");
		$result = $this->apiCall('DirProtections.get', array(
			'id' => $result['id']
		));
		return $this->response(200, "successful", $result);
	}

	/**
	 * list all directory-protections, if called from an admin, list all directory-protections of all customers you are allowed to view, or specify id or loginname for one specific customer
	 *
	 * @param int $customerid
	 *        	optional, admin-only, select directory-protections of a specific customer by id
	 * @param string $loginname
	 *        	optional, admin-only, select directory-protections of a specific customer by loginname
	 * @param array $sql_search
	 *        	optional array with index = fieldname, and value = array with 'op' => operator (one of <, > or =), LIKE is used if left empty and 'value' => searchvalue
	 * @param int $sql_limit
	 *        	optional specify number of results to be returned
	 * @param int $sql_offset
	 *        	optional specify offset for resultset
	 * @param array $sql_orderby
	 *        	optional array with index = fieldname and value = ASC|DESC to order the resultset by one or more fields
	 *
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array count|list
	 */
	public function listing()
	{
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras')) {
			throw new \Exception("You cannot access this resource", 405);
		}
		$customer_ids = $this->getAllowedCustomerIds('extras.directoryprotection');

		$result = array();
		$query_fields = array();
		$result_stmt = Database::prepare("
			SELECT * FROM `" . TABLE_PANEL_HTPASSWDS . "`
			WHERE `customerid` IN (" . implode(', ', $customer_ids) . ")" . $this->getSearchWhere($query_fields, true) . $this->getOrderBy() . $this->getLimit());
		Database::pexecute($result_stmt, $query_fields, true, true);
		while ($row = $result_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}
		$this->logger()->logAction($this->isAdmin() ? \Froxlor\FroxlorLogger::ADM_ACTION : \Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, "[API] list directory-protections");
		return $this->response(200, "successful", array(
			'count' => count($result),
			'list' => $result
		));
	}

	/**
	 * returns the total number of accessible directory protections
	 *
	 * @param int $customerid
	 *        	optional, admin-only, select directory-protections of a specific customer by id
	 * @param string $loginname
	 *        	optional, admin-only, select directory-protections of a specific customer by loginname
	 *        	
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array count|list
	 */
	public function listingCount()
	{
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras')) {
			throw new \Exception("You cannot access this resource", 405);
		}
		$customer_ids = $this->getAllowedCustomerIds('extras.directoryprotection');

		$result = array();
		$result_stmt = Database::prepare("
			SELECT COUNT(*) as num_htpasswd FROM `" . TABLE_PANEL_HTPASSWDS . "`
			WHERE `customerid` IN (" . implode(', ', $customer_ids) . ")
		");
		$result = Database::pexecute_first($result_stmt, null, true, true);
		if ($result) {
			return $this->response(200, "successful", $result['num_htpasswd']);
		}
	}

	/**
	 * delete a directory-protection by either id or username
	 *
	 * @param int $id
	 *        	optional, the directory-protection-id
	 * @param string $username
	 *        	optional, the username
	 *        	
	 * @access admin, customer
	 * @throws \Exception
	 * @return string json-encoded array
	 */
	public function delete()
	{
		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras')) {
			throw new \Exception("You cannot access this resource", 405);
		}

		$id = $this->getParam('id', true, 0);
		$un_optional = ($id <= 0 ? false : true);
		$username = $this->getParam('username', $un_optional, '');

		if ($this->isAdmin() == false && Settings::IsInList('panel.customer_hide_options', 'extras.directoryprotection')) {
			throw new \Exception("You cannot access this resource", 405);
		}

		// get directory protection
		$result = $this->apiCall('DirProtections.get', array(
			'id' => $id,
			'username' => $username
		));
		$id = $result['id'];

		if ($this->isAdmin()) {
			// get customer-data
			$customer_data = $this->apiCall('Customers.get', array(
				'id' => $result['customerid']
			));
		} else {
			$customer_data = $this->getUserData();
		}

		$stmt = Database::prepare("
			DELETE FROM `" . TABLE_PANEL_HTPASSWDS . "` WHERE `customerid`= :customerid	AND `id`= :id
		");
		Database::pexecute($stmt, array(
			"customerid" => $customer_data['customerid'],
			"id" => $id
		));

		$this->logger()->logAction($this->isAdmin() ? \Froxlor\FroxlorLogger::ADM_ACTION : \Froxlor\FroxlorLogger::USR_ACTION, LOG_INFO, "[API] deleted htpasswd for '" . $result['username'] . " (" . $result['path'] . ")'");
		\Froxlor\System\Cronjob::inserttask('1');
		return $this->response(200, "successful", $result);
	}
}
