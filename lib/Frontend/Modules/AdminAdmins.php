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
use Froxlor\Settings;
use Froxlor\Frontend\FeModule;
use Froxlor\Api\Commands\Admins as Admins;
use Froxlor\Api\Commands\IpsAndPorts as IpsAndPorts;

class AdminAdmins extends FeModule
{

	public function overview()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		try {
			$json_result = Admins::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$admins = $result['list'];
		foreach ($admins as $index => $admin) {
			// add some extra fields
			$admin['diskspace_perc'] = 0;
			$admin['traffic_perc'] = 0;
			if ($admin['diskspace'] >= 0) {
				// not unlimited
				$admin['diskspace_perc'] = floor(($admin['diskspace_used'] * 100) / $admin['diskspace']);
			}
			if ($admin['traffic'] >= 0) {
				// not unlimited
				$admin['traffic_perc'] = floor(($admin['traffic_used'] * 100) / $admin['traffic']);
			}
			$result['list'][$index] = $admin;
		}

		\Froxlor\PhpHelper::sortListBy($result['list'], 'loginname');

		// add admin form
		$admin_add_form = $this->adminForm();

		\Froxlor\Frontend\UI::TwigBuffer('admin/admins/index.html.twig', array(
			'page_title' => $this->lng['admin']['admins'],
			'accounts' => $result,
			'form_data' => $admin_add_form
		));
	}

	private function adminForm($result = array())
	{
		$languages = \Froxlor\User::getLanguages();
		$language_options = '';
		$lang_default = ! empty($result) && isset($result['def_language']) ? $result['def_language'] : \Froxlor\CurrentUser::getField('language');
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $lang_default, true);
		}

		try {
			$json_result = IpsAndPorts::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$ips_result = json_decode($json_result, true)['data'];
		$ipaddress = '';
		if (\Froxlor\CurrentUser::getField('ip') == '-1') {
			$ipaddress .= \Froxlor\UI\HTML::makeoption($this->lng['admin']['allips'], "-1");
		}

		$known_ips = array();
		foreach ($ips_result['list'] as $row) {
			if (! in_array($row['ip'], $known_ips)) {
				$ipaddress .= \Froxlor\UI\HTML::makeoption($row['ip'], $row['id']);
				$known_ips[] = $row['ip'];
			}
		}

		$ul_defaults = array();
		foreach (array(
			'customers',
			'diskspace',
			'traffic',
			'domains',
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

		$customers_ul = \Froxlor\UI\HTML::makecheckbox('customers_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['customers'], true, true);
		$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['diskspace'], true, true);
		$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['traffic'], true, true);
		$domains_ul = \Froxlor\UI\HTML::makecheckbox('domains_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['domains'], true, true);
		$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['subdomains'], true, true);
		$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['emails'], true, true);
		$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_accounts'], true, true);
		$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_forwarders'], true, true);
		$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['email_quota'], true, true);
		$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['ftps'], true, true);
		$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $this->lng['customer']['unlimited'], '-1', false, $ul_defaults['mysqls'], true, true);

		if (! empty($result) && isset($result['loginname'])) {
			foreach (array(
				'customers',
				'diskspace',
				'traffic',
				'domains',
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
			$admin_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/admin/formfield.admin_edit.php';
		} else {
			$admin_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/admin/formfield.admin_add.php';
		}
		$admin_form = \Froxlor\UI\HtmlForm::genHTMLForm($admin_data);

		return $admin_form;
	}

	public function add()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __CLASS__ . '::' . __METHOD__);
		}

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				Admins::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->add();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
		}
		\Froxlor\UI\Response::redirectTo('index.php?module=AdminsAdmins');
	}

	public function edit()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				Admins::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminAdmins"
			));
		} else {

			$dec_places = Settings::Get('panel.decimal_places');
			$result['traffic'] = round($result['traffic'] / (1024 * 1024), $dec_places);
			$result['diskspace'] = round($result['diskspace'] / 1024, $dec_places);
			$idna_convert = new \Froxlor\Idna\IdnaWrapper();
			$result['email'] = $idna_convert->decode($result['email']);

			// admin edit form
			$admin_edit_form = $this->adminForm($result);

			\Froxlor\Frontend\UI::TwigBuffer('admin/admins/admin.html.twig', array(
				'page_title' => $this->lng['admin']['admin_edit'],
				'account' => $result,
				'form_data' => $admin_edit_form
			));
		}
	}

	public function su()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($result) {
			$result['switched_user'] = \Froxlor\CurrentUser::getData();
			$result['adminsession'] = 1;
			\Froxlor\CurrentUser::setData($result);

			\Froxlor\FroxlorLogger::getLog()->addInfo("switched admin and is now '" . $result['loginname'] . "'");

			$target = (isset($_GET['target']) ? $_GET['target'] : 'Index');
			$redirect = "index.php?module=Admin" . $target;
			\Froxlor\UI\Response::redirectTo($redirect);
		} else {
			\Froxlor\UI\Response::dynamic_error("something went wrong when reading the admin data");
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
			\Froxlor\UI\Response::dynamic_error("Cannot change back - I've never switched to another admin :-)");
		}
	}

	public function delete()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		try {
			$json_result = Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($result['adminid'] == \Froxlor\CurrentUser::getField('userid')) {
			\Froxlor\UI\Response::standard_error('youcantdeleteyourself');
		}

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->delete();
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminAdmins"
			));
		} else {
			\Froxlor\UI\HTML::askYesNoWithCheckbox('admin_admin_reallydelete', "index.php", array(
				'module' => "AdminAdmins",
				'view' => 'delete',
				'id' => $id
			), $result['loginname']);
		}
	}
}
