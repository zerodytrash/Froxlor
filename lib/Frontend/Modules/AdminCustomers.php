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
			\Froxlor\UI\Response::standard_error('noaccess', __CLASS__ . '::' . __METHOD__);
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
		$customer_add_form = $this->customersAddForm();

		\Froxlor\Frontend\UI::TwigBuffer('admin/customers/index.html.twig', array(
			'page_title' => $this->lng['panel']['customers'],
			'accounts' => $result,
			'form_data' => $customer_add_form
		));
	}
	
	private function customersAddForm() {
		$languages = \Froxlor\User::getLanguages();
		$language_options = '';
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, Settings::Get('panel.standardlanguage'), true);
		}
		
		$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		
		$gender_options = \Froxlor\UI\HTML::makeoption($this->lng['gender']['undef'], 0, true, true, true);
		$gender_options .= \Froxlor\UI\HTML::makeoption($this->lng['gender']['male'], 1, null, true, true);
		$gender_options .= \Froxlor\UI\HTML::makeoption($this->lng['gender']['female'], 2, null, true, true);
		
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
		 $plans = Database::query("
		 SELECT *
		 FROM `" . TABLE_PANEL_PLANS . "`
		 ORDER BY name ASC
		 ");
		 if (Database::num_rows() > 0) {
		 $hosting_plans .= \Froxlor\UI\HTML::makeoption("---", 0, 0, true, true);
		 }
		 while ($row = $plans->fetch(PDO::FETCH_ASSOC)) {
		 $hosting_plans .= \Froxlor\UI\HTML::makeoption($row['name'], $row['id'], 0, true, true);
		 }
		 */
		$customer_add_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/customer/formfield.customer_add.php';
		$customer_add_form = \Froxlor\UI\HtmlForm::genHTMLForm($customer_add_data);
		
		return $customer_add_form;
	}
}
/*
	} elseif ($action == 'su' && $id != 0) {
		try {
			$json_result = Customers::getLocal($userinfo, array(
				'id' => $id
			))->get();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$destination_user = $result['loginname'];

		if ($destination_user != '') {

			if ($result['deactivated'] == '1') {
				\Froxlor\UI\Response::standard_error("usercurrentlydeactivated", $destination_user);
			}
			$result_stmt = Database::prepare("
				SELECT * FROM `" . TABLE_PANEL_SESSIONS . "`
				WHERE `userid` = :id
				AND `hash` = :hash");
			$result = Database::pexecute_first($result_stmt, array(
				'id' => $userinfo['userid'],
				'hash' => $s
			));

			$s = md5(uniqid(microtime(), 1));
			$insert = Database::prepare("
				INSERT INTO `" . TABLE_PANEL_SESSIONS . "` SET
					`hash` = :hash,
					`userid` = :id,
					`ipaddress` = :ip,
					`useragent` = :ua,
					`lastactivity` = :lastact,
					`language` = :lang,
					`adminsession` = '0'");
			Database::pexecute($insert, array(
				'hash' => $s,
				'id' => $id,
				'ip' => $result['ipaddress'],
				'ua' => $result['useragent'],
				'lastact' => time(),
				'lang' => $result['language']
			));
			\Froxlor\FroxlorLogger::getLog()->addInfo("switched user and is now '" . $destination_user . "'");

			$target = (isset($_GET['target']) ? $_GET['target'] : 'index');
			$redirect = "customer_" . $target . ".php";
			if (! file_exists(\Froxlor\Froxlor::getInstallDir() . "/" . $redirect)) {
				$redirect = "customer_index.php";
			}
			\Froxlor\UI\Response::redirectTo($redirect, array(
				's' => $s
			), true);
		} else {
			\Froxlor\UI\Response::redirectTo('index.php', array(
				'action' => 'login'
			));
		}
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
	} elseif ($action == 'delete' && $id != 0) {
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
					'id' => $id,
					'delete_userfiles' => (isset($_POST['delete_userfiles']) ? (int) $_POST['delete_userfiles'] : 0)
				))->delete();
			} catch (Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo($filename, array(
				'page' => $page,
				's' => $s
			));
		} else {
			\Froxlor\UI\HTML::askYesNoWithCheckbox('admin_customer_reallydelete', 'admin_customer_alsoremovefiles', $filename, array(
				'id' => $id,
				'page' => $page,
				'action' => $action
			), $result['loginname']);
		}
	} elseif ($action == 'add') {

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				Customers::getLocal($userinfo, $_POST)->add();
			} catch (Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo($filename, array(
				'page' => $page,
				's' => $s
			));
		} else {
			$language_options = '';

			foreach ($languages as $language_file => $language_name) {
				$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, Settings::Get('panel.standardlanguage'), true);
			}

			$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);
			$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $lng['customer']['unlimited'], '-1', false, '0', true, true);

			$gender_options = \Froxlor\UI\HTML::makeoption($lng['gender']['undef'], 0, true, true, true);
			$gender_options .= \Froxlor\UI\HTML::makeoption($lng['gender']['male'], 1, null, true, true);
			$gender_options .= \Froxlor\UI\HTML::makeoption($lng['gender']['female'], 2, null, true, true);

			$phpconfigs = array();
			$configs = Database::query("
				SELECT c.*, fc.description as interpreter
				FROM `" . TABLE_PANEL_PHPCONFIGS . "` c
				LEFT JOIN `" . TABLE_PANEL_FPMDAEMONS . "` fc ON fc.id = c.fpmsettingid
			");
			while ($row = $configs->fetch(PDO::FETCH_ASSOC)) {
				if ((int) Settings::Get('phpfpm.enabled') == 1) {
					$phpconfigs[] = array(
						'label' => $row['description'] . " [" . $row['interpreter'] . "]<br />",
						'value' => $row['id']
					);
				} else {
					$phpconfigs[] = array(
						'label' => $row['description'] . "<br />",
						'value' => $row['id']
					);
				}
			}

			// hosting plans
			$hosting_plans = "";
			$plans = Database::query("
				SELECT *
				FROM `" . TABLE_PANEL_PLANS . "`
				ORDER BY name ASC
			");
			if (Database::num_rows() > 0) {
				$hosting_plans .= \Froxlor\UI\HTML::makeoption("---", 0, 0, true, true);
			}
			while ($row = $plans->fetch(PDO::FETCH_ASSOC)) {
				$hosting_plans .= \Froxlor\UI\HTML::makeoption($row['name'], $row['id'], 0, true, true);
			}

			$customer_add_data = include_once dirname(__FILE__) . '/lib/formfields/admin/customer/formfield.customer_add.php';
			$customer_add_form = \Froxlor\UI\HtmlForm::genHTMLForm($customer_add_data);

			$title = $customer_add_data['customer_add']['title'];
			$image = $customer_add_data['customer_add']['image'];

			eval("echo \"" . \Froxlor\UI\Template::getTemplate("customers/customers_add") . "\";");
		}
	} elseif ($action == 'edit' && $id != 0) {

		try {
			$json_result = Customers::getLocal($userinfo, array(
				'id' => $id
			))->get();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		// information for moving customer
		$available_admins_stmt = Database::prepare("
			SELECT * FROM `" . TABLE_PANEL_ADMINS . "`
			WHERE (`customers` = '-1' OR `customers` > `customers_used`)");
		Database::pexecute($available_admins_stmt);
		$admin_select = \Froxlor\UI\HTML::makeoption("-----", 0, true, true, true);
		$admin_select_cnt = 0;
		while ($available_admin = $available_admins_stmt->fetch()) {
			$admin_select .= \Froxlor\UI\HTML::makeoption($available_admin['name'] . " (" . $available_admin['loginname'] . ")", $available_admin['adminid'], null, true, true);
			$admin_select_cnt ++;
		}
		// end of moving customer stuff

		if ($result['loginname'] != '') {

			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					Customers::getLocal($userinfo, $_POST)->update();
				} catch (Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo($filename, array(
					'page' => $page,
					's' => $s
				));
			} else {
				$language_options = '';

				foreach ($languages as $language_file => $language_name) {
					$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $result['def_language'], true);
				}

				$dec_places = Settings::Get('panel.decimal_places');
				$result['traffic'] = round($result['traffic'] / (1024 * 1024), $dec_places);
				$result['diskspace'] = round($result['diskspace'] / 1024, $dec_places);
				$result['email'] = $idna_convert->decode($result['email']);

				$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $lng['customer']['unlimited'], '-1', false, $result['diskspace'], true, true);
				if ($result['diskspace'] == '-1') {
					$result['diskspace'] = '';
				}

				$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $lng['customer']['unlimited'], '-1', false, $result['traffic'], true, true);
				if ($result['traffic'] == '-1') {
					$result['traffic'] = '';
				}

				$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $lng['customer']['unlimited'], '-1', false, $result['subdomains'], true, true);
				if ($result['subdomains'] == '-1') {
					$result['subdomains'] = '';
				}

				$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $lng['customer']['unlimited'], '-1', false, $result['emails'], true, true);
				if ($result['emails'] == '-1') {
					$result['emails'] = '';
				}

				$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $lng['customer']['unlimited'], '-1', false, $result['email_accounts'], true, true);
				if ($result['email_accounts'] == '-1') {
					$result['email_accounts'] = '';
				}

				$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $lng['customer']['unlimited'], '-1', false, $result['email_forwarders'], true, true);
				if ($result['email_forwarders'] == '-1') {
					$result['email_forwarders'] = '';
				}

				$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $lng['customer']['unlimited'], '-1', false, $result['email_quota'], true, true);
				if ($result['email_quota'] == '-1') {
					$result['email_quota'] = '';
				}

				$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $lng['customer']['unlimited'], '-1', false, $result['ftps'], true, true);
				if ($result['ftps'] == '-1') {
					$result['ftps'] = '';
				}

				$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $lng['customer']['unlimited'], '-1', false, $result['mysqls'], true, true);
				if ($result['mysqls'] == '-1') {
					$result['mysqls'] = '';
				}

				$result = \Froxlor\PhpHelper::htmlentitiesArray($result);

				$gender_options = \Froxlor\UI\HTML::makeoption($lng['gender']['undef'], 0, ($result['gender'] == '0' ? true : false), true, true);
				$gender_options .= \Froxlor\UI\HTML::makeoption($lng['gender']['male'], 1, ($result['gender'] == '1' ? true : false), true, true);
				$gender_options .= \Froxlor\UI\HTML::makeoption($lng['gender']['female'], 2, ($result['gender'] == '2' ? true : false), true, true);

				$phpconfigs = array();
				$configs = Database::query("
					SELECT c.*, fc.description as interpreter
					FROM `" . TABLE_PANEL_PHPCONFIGS . "` c
					LEFT JOIN `" . TABLE_PANEL_FPMDAEMONS . "` fc ON fc.id = c.fpmsettingid
				");
				while ($row = $configs->fetch(PDO::FETCH_ASSOC)) {
					if ((int) Settings::Get('phpfpm.enabled') == 1) {
						$phpconfigs[] = array(
							'label' => $row['description'] . " [" . $row['interpreter'] . "]<br />",
							'value' => $row['id']
						);
					} else {
						$phpconfigs[] = array(
							'label' => $row['description'] . "<br />",
							'value' => $row['id']
						);
					}
				}

				// hosting plans
				$hosting_plans = "";
				$plans = Database::query("
					SELECT *
					FROM `" . TABLE_PANEL_PLANS . "`
					ORDER BY name ASC
				");
				if (Database::num_rows() > 0) {
					$hosting_plans .= \Froxlor\UI\HTML::makeoption("---", 0, 0, true, true);
				}
				while ($row = $plans->fetch(PDO::FETCH_ASSOC)) {
					$hosting_plans .= \Froxlor\UI\HTML::makeoption($row['name'], $row['id'], 0, true, true);
				}

				$customer_edit_data = include_once dirname(__FILE__) . '/lib/formfields/admin/customer/formfield.customer_edit.php';
				$customer_edit_form = \Froxlor\UI\HtmlForm::genHTMLForm($customer_edit_data);

				$title = $customer_edit_data['customer_edit']['title'];
				$image = $customer_edit_data['customer_edit']['image'];

				eval("echo \"" . \Froxlor\UI\Template::getTemplate("customers/customers_edit") . "\";");
			}
		}
	}
}
*/