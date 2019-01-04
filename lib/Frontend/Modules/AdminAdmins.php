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
		$admin_add_form = $this->adminAddForm();

		\Froxlor\Frontend\UI::TwigBuffer('admin/admins/index.html.twig', array(
			'page_title' => $this->lng['admin']['admins'],
			'accounts' => $result,
			'form_data' => $admin_add_form
		));
	}

	private function adminAddForm()
	{
		$languages = \Froxlor\User::getLanguages();
		$language_options = '';
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, \Froxlor\CurrentUser::getField('language'), true);
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
		
		$customers_ul = \Froxlor\UI\HTML::makecheckbox('customers_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$domains_ul = \Froxlor\UI\HTML::makecheckbox('domains_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$subdomains_ul = \Froxlor\UI\HTML::makecheckbox('subdomains_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$emails_ul = \Froxlor\UI\HTML::makecheckbox('emails_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_accounts_ul = \Froxlor\UI\HTML::makecheckbox('email_accounts_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_forwarders_ul = \Froxlor\UI\HTML::makecheckbox('email_forwarders_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$email_quota_ul = \Froxlor\UI\HTML::makecheckbox('email_quota_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$ftps_ul = \Froxlor\UI\HTML::makecheckbox('ftps_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		$mysqls_ul = \Froxlor\UI\HTML::makecheckbox('mysqls_ul', $this->lng['customer']['unlimited'], '-1', false, '0', true, true);
		
		$admin_add_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/admin/formfield.admin_add.php';
		$admin_add_form = \Froxlor\UI\HtmlForm::genHTMLForm($admin_add_data);
		
		return $admin_add_form;
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
}
/*
 } elseif ($action == 'su') {
 
 try {
 $json_result = Admins::getLocal($userinfo, array(
 'id' => $id
 ))->get();
 } catch (Exception $e) {
 \Froxlor\UI\Response::dynamic_error($e->getMessage());
 }
 $result = json_decode($json_result, true)['data'];
 $destination_admin = $result['loginname'];
 
 if ($destination_admin != '' && $result['adminid'] != $userinfo['userid']) {
 $result_stmt = Database::prepare("
 SELECT * FROM `" . TABLE_PANEL_SESSIONS . "` WHERE `userid` = :userid
 ");
 $result = Database::pexecute_first($result_stmt, array(
 'userid' => $userinfo['userid']
 ));
 
 $s = md5(uniqid(microtime(), 1));
 $ins_stmt = Database::prepare("
 INSERT INTO `" . TABLE_PANEL_SESSIONS . "` SET
 `hash` = :hash, `userid` = :userid, `ipaddress` = :ip,
 `useragent` = :ua, `lastactivity` = :la,
 `language` = :lang, `adminsession` = '1'
 ");
 $ins_data = array(
 'hash' => $s,
 'userid' => $id,
 'ip' => $result['ipaddress'],
 'ua' => $result['useragent'],
 'la' => time(),
 'lang' => $result['language']
 );
 Database::pexecute($ins_stmt, $ins_data);
 \Froxlor\FroxlorLogger::getLog()->addInfo("switched adminuser and is now '" . $destination_admin . "'");
 \Froxlor\UI\Response::redirectTo('admin_index.php', array(
 's' => $s
 ));
 } else {
 \Froxlor\UI\Response::redirectTo('index.php', array(
 'action' => 'login'
 ));
 }
 } elseif ($action == 'delete' && $id != 0) {
 try {
 $json_result = Admins::getLocal($userinfo, array(
 'id' => $id
 ))->get();
 } catch (Exception $e) {
 \Froxlor\UI\Response::dynamic_error($e->getMessage());
 }
 $result = json_decode($json_result, true)['data'];
 
 if ($result['loginname'] != '') {
 if ($result['adminid'] == $userinfo['userid']) {
 \Froxlor\UI\Response::standard_error('youcantdeleteyourself');
 }
 
 if (isset($_POST['send']) && $_POST['send'] == 'send') {
 Admins::getLocal($this->getUserData(), array(
 'id' => $id
 ))->delete();
 \Froxlor\UI\Response::redirectTo($filename, array(
 'page' => $page,
 's' => $s
 ));
 } else {
 \Froxlor\UI\HTML::askYesNo('admin_admin_reallydelete', $filename, array(
 'id' => $id,
 'page' => $page,
 'action' => $action
 ), $result['loginname']);
 }
 }
 
 } elseif ($action == 'edit' && $id != 0) {
 try {
 $json_result = Admins::getLocal($userinfo, array(
 'id' => $id
 ))->get();
 } catch (Exception $e) {
 \Froxlor\UI\Response::dynamic_error($e->getMessage());
 }
 $result = json_decode($json_result, true)['data'];
 
 if ($result['loginname'] != '') {
 
 if (isset($_POST['send']) && $_POST['send'] == 'send') {
 try {
 Admins::getLocal($userinfo, $_POST)->update();
 } catch (Exception $e) {
 \Froxlor\UI\Response::dynamic_error($e->getMessage());
 }
 \Froxlor\UI\Response::redirectTo($filename, array(
 'page' => $page,
 's' => $s
 ));
 } else {
 
 $dec_places = Settings::Get('panel.decimal_places');
 $result['traffic'] = round($result['traffic'] / (1024 * 1024), $dec_places);
 $result['diskspace'] = round($result['diskspace'] / 1024, $dec_places);
 $result['email'] = $idna_convert->decode($result['email']);
 
 $customers_ul = \Froxlor\UI\HTML::makecheckbox('customers_ul', $lng['customer']['unlimited'], '-1', false, $result['customers'], true, true);
 if ($result['customers'] == '-1') {
 $result['customers'] = '';
 }
 
 $diskspace_ul = \Froxlor\UI\HTML::makecheckbox('diskspace_ul', $lng['customer']['unlimited'], '-1', false, $result['diskspace'], true, true);
 if ($result['diskspace'] == '-1') {
 $result['diskspace'] = '';
 }
 
 $traffic_ul = \Froxlor\UI\HTML::makecheckbox('traffic_ul', $lng['customer']['unlimited'], '-1', false, $result['traffic'], true, true);
 if ($result['traffic'] == '-1') {
 $result['traffic'] = '';
 }
 
 $domains_ul = \Froxlor\UI\HTML::makecheckbox('domains_ul', $lng['customer']['unlimited'], '-1', false, $result['domains'], true, true);
 if ($result['domains'] == '-1') {
 $result['domains'] = '';
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
 
 $language_options = '';
 foreach ($languages as $language_file => $language_name) {
 $language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $result['def_language'], true);
 }
 
 $ipaddress = \Froxlor\UI\HTML::makeoption($lng['admin']['allips'], "-1", $result['ip']);
 $ipsandports_stmt = Database::query("
 SELECT `id`, `ip` FROM `" . TABLE_PANEL_IPSANDPORTS . "` GROUP BY `id`, `ip` ORDER BY `ip`, `port` ASC
 ");
 
 while ($row = $ipsandports_stmt->fetch(PDO::FETCH_ASSOC)) {
 $ipaddress .= \Froxlor\UI\HTML::makeoption($row['ip'], $row['id'], $result['ip']);
 }
 
 $result = \Froxlor\PhpHelper::htmlentitiesArray($result);
 
 $admin_edit_data = include_once dirname(__FILE__) . '/lib/formfields/admin/admin/formfield.admin_edit.php';
 $admin_edit_form = \Froxlor\UI\HtmlForm::genHTMLForm($admin_edit_data);
 
 $title = $admin_edit_data['admin_edit']['title'];
 $image = $admin_edit_data['admin_edit']['image'];
 
 eval("echo \"" . \Froxlor\UI\Template::getTemplate("admins/admins_edit") . "\";");
 }
 }
 }
 }
 */