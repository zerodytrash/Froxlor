<?php
namespace Froxlor\Frontend\Modules;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Florian Lippert <flo@syscp.org> (2003-2009)
 * @author Froxlor team <team@froxlor.org> (2010-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Panel
 *         
 */
use Froxlor\Api\Commands\PhpSettings;
use Froxlor\Database\Database;
use Froxlor\Settings;
use Froxlor\Api\Commands\Customers as Customers;
use Froxlor\Frontend\FeModule;

class AdminCustomers extends FeModule
{

	public function overview()
	{
		if (\Froxlor\CurrentUser::getField('customers') == 0) {
			// no customers - not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		try {
			$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$domains_stmt = Database::prepare("
			SELECT COUNT(`id`) AS `domains`
			FROM `" . TABLE_PANEL_DOMAINS . "`
			WHERE `customerid` = :cid
			AND `parentdomainid` = '0'
			AND `id`<> :stdd
		");

		$customers = $result['list'];
		foreach ($customers as $index => $customer) {
			// count domains
			Database::pexecute($domains_stmt, array(
				'cid' => $customer['customerid'],
				'stdd' => $customer['standardsubdomain']
			));
			$domains = $domains_stmt->fetch(\PDO::FETCH_ASSOC);
			$customer['domains'] = intval($domains['domains']);
			// checked whether is locked
			$customer['islocked'] = 0;
			if ($customer['loginfail_count'] >= Settings::Get('login.maxloginattempts') && $customer['lastlogin_fail'] > (time() - Settings::Get('login.deactivatetime'))) {
				$customer['islocked'] = 1;
			}
			// add some extra fields
			$customer['diskspace_perc'] = 0;
			$customer['traffic_perc'] = 0;
			if ($customer['diskspace'] >= 0) {
				// not unlimited
				$customer['diskspace_perc'] = floor(($customer['diskspace_used'] * 100) / $customer['diskspace']);
			}
			if ($customer['traffic'] >= 0) {
				// not unlimited
				$customer['traffic_perc'] = floor(($customer['traffic_used'] * 100) / $customer['traffic']);
			}
			$result['list'][$index] = $customer;
		}

		\Froxlor\PhpHelper::sortListBy($result['list'], 'loginname');

		// customer add form
		$customer_add_form = $this->customersForm();

		\Froxlor\Frontend\UI::TwigBuffer('admin/customers/index.html.twig', array(
			'page_title' => $this->lng['panel']['customers'],
			'accounts' => $result,
			'form_data' => $customer_add_form
		));
	}

	public function edit()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				Customers::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminCustomers",
				'view' => "edit"
			));
		} else {

			$dec_places = Settings::Get('panel.decimal_places');
			$result['traffic'] = round($result['traffic'] / (1024 * 1024), $dec_places);
			$result['diskspace'] = round($result['diskspace'] / 1024, $dec_places);
			$idna_convert = new \Froxlor\Idna\IdnaWrapper();
			$result['email'] = $idna_convert->decode($result['email']);

			// customer edit form
			$customer_edit_form = $this->customersForm($result);

			\Froxlor\Frontend\UI::TwigBuffer('admin/customers/customer.html.twig', array(
				'page_title' => $this->lng['panel']['customers'],
				'account' => $result,
				'form_data' => $customer_edit_form
			));
		}
	}

	private function customersForm($result = array())
	{
		$languages = \Froxlor\User::getLanguages();
		$language_options = '';
		$lang_default = ! empty($result) && isset($result['def_language']) ? $result['def_language'] : Settings::Get('panel.standardlanguage');
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $lang_default, true);
		}

		$ul_defaults = array();
		foreach (array(
			'diskspace',
			'traffic',
			'subdomains',
			'emails',
			'email_accounts',
			'email_forwarders',
			'email_quota',
			'ftps',
			'mysqls'
		) as $resource) {
			$ul_defaults[$resource] = ! empty($result) && isset($result[$resource]) ? $result[$resource] : '0';
		}

		$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['diskspace'], true, true);
		$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['traffic'], true, true);
		$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['subdomains'], true, true);
		$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['emails'], true, true);
		$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_accounts'], true, true);
		$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_forwarders'], true, true);
		$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_quota'], true, true);
		$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['ftps'], true, true);
		$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['mysqls'], true, true);

		$gender_opts = array(
			true,
			null,
			null
		);
		if (! empty($result) && isset($result['gender'])) {
			$gender_opts[0] = $result['gender'] == '0' ? true : false;
			$gender_opts[1] = $result['gender'] == '1' ? true : false;
			$gender_opts[2] = $result['gender'] == '2' ? true : false;
		}
		$gender_options = \Froxlor\UI\HTML::makeoption($this->lng['gender']['undef'], 0, $gender_opts[0], true, true);
		$gender_options .= \Froxlor\UI\HTML::makeoption($this->lng['gender']['male'], 1, $gender_opts[1], true, true);
		$gender_options .= \Froxlor\UI\HTML::makeoption($this->lng['gender']['female'], 2, $gender_opts[2], true, true);

		$phpconfigs = array();
		try {
			$json_result = PhpSettings::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$php_result = json_decode($json_result, true)['data'];
		foreach ($php_result['list'] as $row) {
			if ((int) Settings::Get('phpfpm.enabled') == 1) {
				$phpconfigs[] = array(
					'label' => $row['description'] . " [" . $row['fpmdesc'] . "]",
					'value' => $row['id']
				);
			} else {
				$phpconfigs[] = array(
					'label' => $row['description'],
					'value' => $row['id']
				);
			}
		}

		// hosting plans
		$hosting_plans = "";
		/*
		 * $plans = Database::query("
		 * SELECT *
		 * FROM `" . TABLE_PANEL_PLANS . "`
		 * ORDER BY name ASC
		 * ");
		 * if (Database::num_rows() > 0) {
		 * $hosting_plans .= \Froxlor\UI\HTML::makeoption("---", 0, 0, true, true);
		 * }
		 * while ($row = $plans->fetch(PDO::FETCH_ASSOC)) {
		 * $hosting_plans .= \Froxlor\UI\HTML::makeoption($row['name'], $row['id'], 0, true, true);
		 * }
		 */

		if (! empty($result) && isset($result['loginname'])) {
			// information for moving customer
			$available_admins_stmt = Database::prepare("
				SELECT * FROM `" . TABLE_PANEL_ADMINS . "`
				WHERE (`customers` = '-1' OR `customers` > `customers_used`)
			");
			Database::pexecute($available_admins_stmt);
			$admin_select = \Froxlor\UI\HTML::makeoption("-----", 0, true, true, true);
			$admin_select_cnt = 0;
			while ($available_admin = $available_admins_stmt->fetch()) {
				$admin_select .= \Froxlor\UI\HTML::makeoption($available_admin['name'] . " (" . $available_admin['loginname'] . ")", $available_admin['adminid'], null, true, true);
				$admin_select_cnt ++;
			}
			foreach (array(
				'diskspace',
				'traffic',
				'subdomains',
				'emails',
				'email_accounts',
				'email_forwarders',
				'email_quota',
				'ftps',
				'mysqls'
			) as $resource) {
				if ($result[$resource] == '-1') {
					$result[$resource] = '';
				}
			}
			// end of moving customer stuff
			$customer_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/customer/formfield.customer_edit.php';
		} else {
			$customer_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/customer/formfield.customer_add.php';
		}
		$customer_form = \Froxlor\UI\HtmlForm::genHTMLForm($customer_data);

		return $customer_form;
	}

	public function su()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($result) {
			$result['switched_user'] = \Froxlor\CurrentUser::getData();
			$result['adminsession'] = 0;
			\Froxlor\CurrentUser::setData($result);

			\Froxlor\FroxlorLogger::getLog()->addInfo("switched user and is now '" . $result['loginname'] . "'");

			$target = (isset($_GET['target']) ? $_GET['target'] : 'Index');
			$redirect = "index.php?module=Customer" . $target;
			\Froxlor\UI\Response::redirectTo($redirect);
		} else {
			\Froxlor\UI\Response::dynamic_error("something went wrong when reading the customer data");
		}
	}

	public function suBack()
	{
		if (is_array(\Froxlor\CurrentUser::getField('switched_user'))) {
			$result = \Froxlor\CurrentUser::getData();
			$result = $result['switched_user'];
			unset($result['switched_user']);
			\Froxlor\CurrentUser::setData($result);

			$target = (isset($_GET['target']) ? $_GET['target'] : 'Index');
			$redirect = "index.php?module=Admin" . $target;
			\Froxlor\UI\Response::redirectTo($redirect);
		} else {
			\Froxlor\UI\Response::dynamic_error("Cannot change back - I've never switched to another user :-)");
		}
	}

	public function delete()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData(), array(
					'id' => $id,
					'delete_userfiles' => (isset($_POST['delete_userfiles']) ? (int) $_POST['delete_userfiles'] : 0)
				))->delete();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminCustomers"
			));
		} else {
			\Froxlor\UI\HTML::askYesNoWithCheckbox('admin_customer_reallydelete', 'admin_customer_alsoremovefiles', "index.php", array(
				'module' => "AdminCustomers",
				'view' => 'delete',
				'id' => $id
			), $result['loginname']);
		}
	}
}
/*
	} elseif ($action == 'unlock' && $id != 0) {
		try {
			$json_result = Customers::getLocal($userinfo, array(
				'id' => $id
			))->get();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				$json_result = Customers::getLocal($userinfo, array(
					'id' => $id
				))->unlock();
			} catch (Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo($filename, array(
				'page' => $page,
				's' => $s
			));
		} else {
			\Froxlor\UI\HTML::askYesNo('customer_reallyunlock', $filename, array(
				'id' => $id,
				'page' => $page,
				'action' => $action
			), $result['loginname']);
		}



}
*/