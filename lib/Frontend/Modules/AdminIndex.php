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
use Froxlor\Database\Database;
use Froxlor\Frontend\FeModule;
use Froxlor\Settings;
use Froxlor\Api\Commands\Froxlor as Froxlor;
use Froxlor\Api\Commands\Admins as Admins;

class AdminIndex extends FeModule
{

	public function logout()
	{
		\Froxlor\FroxlorLogger::getLog()->addNotice("logged out");

		$params = array(
			'adminid' => (int) \Froxlor\CurrentUser::getField('adminid')
		);

		if (Settings::Get('session.allow_multiple_login') == '1') {
			$stmt = Database::prepare("DELETE FROM `" . TABLE_PANEL_SESSIONS . "`
				WHERE `userid` = :adminid
				AND `adminsession` = '1'
			");
		} else {
			$stmt = Database::prepare("DELETE FROM `" . TABLE_PANEL_SESSIONS . "`
				WHERE `userid` = :adminid
				AND `adminsession` = '1'
			");
		}
		Database::pexecute($stmt, $params);
		unset($_SESSION['userinfo']);
		\Froxlor\CurrentUser::setData(array());

		\Froxlor\UI\Response::redirectTo('index.php');
	}

	public function overview()
	{
		$overview_stmt = Database::prepare("SELECT COUNT(*) AS `customers`,
			SUM(`diskspace_used`) AS `diskspace`,
			SUM(`traffic_used`) AS `traffic`,
			SUM(`mysqls_used`) AS `mysqls`,
			SUM(`ftps_used`) AS `ftps`,
			SUM(`emails_used`) AS `emails`,
			SUM(`email_accounts_used`) AS `email_accounts`,
			SUM(`email_forwarders_used`) AS `email_forwarders`,
			SUM(`email_quota_used`) AS `email_quota`,
			SUM(`subdomains_used`) AS `subdomains`
			FROM `" . TABLE_PANEL_CUSTOMERS . "`" . (\Froxlor\CurrentUser::getField('customers_see_all') ? '' : " WHERE `adminid` = :adminid "));
		$overview = Database::pexecute_first($overview_stmt, array(
			'adminid' => \Froxlor\CurrentUser::getField('adminid')
		));

		$number_domains_stmt = Database::prepare("
			SELECT COUNT(*) AS `number_domains` FROM `" . TABLE_PANEL_DOMAINS . "`
			WHERE `parentdomainid`='0'" . (\Froxlor\CurrentUser::getField('customers_see_all') ? '' : " AND `adminid` = :adminid"));
		$number_domains = Database::pexecute_first($number_domains_stmt, array(
			'adminid' => \Froxlor\CurrentUser::getField('adminid')
		));

		$overview['domains'] = $number_domains['number_domains'];

		if (\Froxlor\Settings::Get('system.mail_quota_enabled') == 0) {
			unset($overview['email_quota']);
		}

		// calculate percentage
		$overview_data = array();
		foreach ($overview as $entity => $used) {
			$overview_data[$entity] = array(
				'avail' => \Froxlor\CurrentUser::getField($entity),
				'used' => empty($used) ? 0 : $used,
				'perc' => (\Froxlor\CurrentUser::getField($entity) >= 0) ? floor($used / \Froxlor\CurrentUser::getField($entity)) : 0
			);
		}
		// ksort($overview_data);

		/*
		 * @fixme
		 * $dec_places = Settings::Get('panel.decimal_places');
		 * $userinfo['diskspace'] = round($userinfo['diskspace'] / 1024, $dec_places);
		 * $userinfo['diskspace_used'] = round($userinfo['diskspace_used'] / 1024, $dec_places);
		 * $userinfo['traffic'] = round($userinfo['traffic'] / (1024 * 1024), $dec_places);
		 * $userinfo['traffic_used'] = round($userinfo['traffic_used'] / (1024 * 1024), $dec_places);
		 * $userinfo = \Froxlor\PhpHelper::strReplaceArray('-1', $this->lng['customer']['unlimited'], $userinfo, 'customers domains diskspace traffic mysqls emails email_accounts email_forwarders email_quota ftps subdomains');
		 *
		 * $userinfo['custom_notes'] = ($userinfo['custom_notes'] != '') ? nl2br($userinfo['custom_notes']) : '';
		 */

		$sysinfo = array();
		$sysinfo['phpversion'] = phpversion();
		$sysinfo['mysqlserverversion'] = Database::getAttribute(\PDO::ATTR_SERVER_VERSION);
		$sysinfo['webserverinterface'] = strtoupper(@php_sapi_name());

		$cron_last_runs = \Froxlor\System\Cronjob::getCronjobsLastRun();
		$outstanding_tasks = \Froxlor\System\Cronjob::getOutstandingTasks();

		$sysinfo['hostname'] = gethostname();
		$sysinfo['serversoftware'] = $_SERVER['SERVER_SOFTWARE'];
		$meminfo = explode("\n", @file_get_contents("/proc/meminfo"));
		$sysinfo['memory'] = "";
		for ($i = 0; $i < sizeof($meminfo); ++ $i) {
			if (substr($meminfo[$i], 0, 3) === "Mem") {
				$sysinfo['memory'] .= $meminfo[$i] . '<br>';
			}
		}

		if (function_exists('sys_getloadavg')) {
			$loadArray = sys_getloadavg();
			$sysinfo['sysload'] = number_format($loadArray[0], 2, '.', '') . " / " . number_format($loadArray[1], 2, '.', '') . " / " . number_format($loadArray[2], 2, '.', '');
		} else {
			$sysinfo['sysload'] = @file_get_contents('/proc/loadavg');

			if (! $sysinfo['sysload']) {
				$sysinfo['sysload'] = $this->lng['admin']['noloadavailable'];
			}
		}

		if (function_exists('posix_uname')) {
			$kernel_nfo = posix_uname();
			$sysinfo['kernel'] = $kernel_nfo['release'] . ' (' . $kernel_nfo['machine'] . ')';
		}

		// Try to get the uptime
		// First: With exec (let's hope it's enabled for the Froxlor - vHost)
		$uptime_array = explode(" ", @file_get_contents("/proc/uptime"));

		if (is_array($uptime_array) && isset($uptime_array[0]) && is_numeric($uptime_array[0])) {
			// Some calculatioon to get a nicly formatted display
			$seconds = round($uptime_array[0], 0);
			$minutes = $seconds / 60;
			$hours = $minutes / 60;
			$days = floor($hours / 24);
			$hours = floor($hours - ($days * 24));
			$minutes = floor($minutes - ($days * 24 * 60) - ($hours * 60));
			$seconds = floor($seconds - ($days * 24 * 60 * 60) - ($hours * 60 * 60) - ($minutes * 60));
			$sysinfo['uptime'] = "{$days}d, {$hours}h, {$minutes}m, {$seconds}s";

			// Just cleanup
			unset($uptime_array, $seconds, $minutes, $hours, $days);
		}

		// update check
		try {
			$json_result = Froxlor::getLocal(\Froxlor\CurrentUser::getData())->checkUpdate();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$version_check_result = json_decode($json_result, true)['data'];

		\Froxlor\Frontend\UI::TwigBuffer('admin/index/index.html.twig', array(
			'page_title' => "Dashboard",
			'usage_data' => $overview_data,
			'sysinfo' => $sysinfo,
			'version_check_result' => $version_check_result,
			'cron_last_runs' => $cron_last_runs,
			'outstanding_tasks' => $outstanding_tasks
		));
	}

	public function myAccount()
	{
		// languages
		$default_lang = Settings::Get('panel.standardlanguage');
		if (\Froxlor\CurrentUser::getField('def_language') != '') {
			$default_lang = \Froxlor\CurrentUser::getField('def_language');
		}
		$languages = \Froxlor\User::getLanguages();
		$language_options = "";
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $default_lang, true);
		}

		// themes
		$default_theme = Settings::Get('panel.default_theme');
		if (\Froxlor\CurrentUser::getField('theme') != '') {
			$default_theme = \Froxlor\CurrentUser::getField('theme');
		}
		$themes_avail = \Froxlor\Frontend\UI::getThemes();
		$theme_options = '';
		foreach ($themes_avail as $t => $d) {
			$theme_options .= \Froxlor\UI\HTML::makeoption($d, $t, $default_theme, true);
		}

		// 2fa
		$tfa = new \Froxlor\FroxlorTwoFactorAuth('Froxlor');
		$type_select = "";
		$ga_qrcode = "";
		if (\Froxlor\CurrentUser::getField('type_2fa') == '0') {
			// available types
			$type_select_values = array(
				0 => '-',
				1 => 'E-Mail',
				2 => 'Authenticator'
			);
			asort($type_select_values);
			foreach ($type_select_values as $_val => $_type) {
				$type_select .= \Froxlor\UI\HTML::makeoption($_type, $_val);
			}
		} elseif (\Froxlor\CurrentUser::getField('type_2fa') == '1') {
			// email Edit2FA enabled
		} elseif (\Froxlor\CurrentUser::getField('type_2fa') == '2') {
			// authenticator Edit2FA enabled
			$ga_qrcode = $tfa->getQRCodeImageAsDataUri(\Froxlor\CurrentUser::getField('loginname'), \Froxlor\CurrentUser::getField('data_2fa'));
		}

		// api-keys
		// select all my (accessable) certificates
		$keys_stmt_query = "SELECT ak.*, c.loginname, a.loginname as adminname
		FROM `" . TABLE_API_KEYS . "` ak
		LEFT JOIN `" . TABLE_PANEL_CUSTOMERS . "` c ON `c`.`customerid` = `ak`.`customerid`
		LEFT JOIN `" . TABLE_PANEL_ADMINS . "` a ON `a`.`adminid` = `ak`.`adminid` ";
		$qry_params = array();
		if (\Froxlor\CurrentUser::getField('customers_see_all') == '0') {
			// admin with only customer-specific permissions
			$keys_stmt_query .= "WHERE ak.adminid = :adminid ";
			$qry_params['adminid'] = \Froxlor\CurrentUser::getField('adminid');
		}
		$keys_stmt = Database::prepare($keys_stmt_query);
		Database::pexecute($keys_stmt, $qry_params);
		$all_keys = $keys_stmt->fetchAll(\PDO::FETCH_ASSOC);
		\Froxlor\PhpHelper::sortListBy($all_keys, 'loginname');

		\Froxlor\Frontend\UI::TwigBuffer('myaccount.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('menue.main.username') . \Froxlor\CurrentUser::getField('loginname'),
			'languages' => $language_options,
			'themes' => $theme_options,
			'type_select' => $type_select,
			'ga_qrcode' => $ga_qrcode,
			'apikeys' => $all_keys
		));
	}

	public function changePassword()
	{
		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			$old_password = \Froxlor\Validate\Validate::validate($_POST['old_password'], 'old password');

			if (! \Froxlor\System\Crypt::validatePasswordLogin(\Froxlor\CurrentUser::getData(), $old_password, TABLE_PANEL_ADMINS, 'adminid')) {
				\Froxlor\UI\Response::standard_error('oldpasswordnotcorrect');
			}

			$new_password = \Froxlor\Validate\Validate::validate($_POST['new_password'], 'new password');
			$new_password_confirm = \Froxlor\Validate\Validate::validate($_POST['new_password_confirm'], 'new password confirm');

			if ($old_password == '') {
				\Froxlor\UI\Response::standard_error(array(
					'stringisempty',
					'oldpassword'
				));
			} elseif ($new_password == '') {
				\Froxlor\UI\Response::standard_error(array(
					'stringisempty',
					'newpassword'
				));
			} elseif ($new_password_confirm == '') {
				\Froxlor\UI\Response::standard_error(array(
					'stringisempty',
					'newpasswordconfirm'
				));
			} elseif ($new_password != $new_password_confirm) {
				\Froxlor\UI\Response::standard_error('newpasswordconfirmerror');
			} else {
				try {
					Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
						'id' => \Froxlor\CurrentUser::getField('adminid'),
						'admin_password' => $new_password
					))->update();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\FroxlorLogger::getLog()->addNotice('changed password');
			}
		}
		\Froxlor\UI\Response::redirectTo("index.php?module=AdminIndex&view=myAccount");
	}

	public function changeLanguage()
	{
		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			$def_language = \Froxlor\Validate\Validate::validate($_POST['def_language'], 'default language');

			$languages = \Froxlor\User::getLanguages();
			if (isset($languages[$def_language])) {
				try {
					Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
						'id' => \Froxlor\CurrentUser::getField('adminid'),
						'def_language' => $def_language
					))->update();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				// also update current session
				\Froxlor\CurrentUser::setField('language', $def_language);
				\Froxlor\CurrentUser::setField('def_language', $def_language);
			}
			\Froxlor\FroxlorLogger::getLog()->addNotice("changed his/her default language to '" . $def_language . "'");
		}
		\Froxlor\UI\Response::redirectTo("index.php?module=AdminIndex&view=myAccount");
	}

	public function changeTheme()
	{
		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			$theme = \Froxlor\Validate\Validate::validate($_POST['theme'], 'theme');
			try {
				Admins::getLocal(\Froxlor\CurrentUser::getData(), array(
					'id' => \Froxlor\CurrentUser::getField('adminid'),
					'theme' => $theme
				))->update();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			// also update current session
			\Froxlor\CurrentUser::setField('theme', $theme);
			\Froxlor\FroxlorLogger::getLog()->addNotice("changed his/her theme to '" . $theme . "'");
		}
		\Froxlor\UI\Response::redirectTo("index.php?module=AdminIndex&view=myAccount");
	}

	/**
	 *
	 * @todo rename to CamelCase
	 */
	public function send_error_report()
	{
		if (Settings::Get('system.allow_error_report_admin') != '1') {
			// @fixme error
		}

		// only show this if we really have an exception to report
		if (isset($_GET['errorid']) && $_GET['errorid'] != '') {

			$errid = $_GET['errorid'];
			// read error file
			$err_dir = \Froxlor\FileDir::makeCorrectDir(\Froxlor\Froxlor::getInstallDir() . "/logs/");
			$err_file = \Froxlor\FileDir::makeCorrectFile($err_dir . "/" . $errid . "_sql-error.log");

			if (file_exists($err_file)) {

				$error_content = file_get_contents($err_file);
				$error = explode("|", $error_content);

				$_error = array(
					'code' => str_replace("\n", "", substr($error[1], 5)),
					'message' => str_replace("\n", "", substr($error[2], 4)),
					'file' => str_replace("\n", "", substr($error[3], 5 + strlen(\Froxlor\Froxlor::getInstallDir()))),
					'line' => str_replace("\n", "", substr($error[4], 5)),
					'trace' => str_replace(\Froxlor\Froxlor::getInstallDir(), "", substr($error[5], 6))
				);

				// build mail-content
				$mail_body = "Dear froxlor-team,\n\n";
				$mail_body .= "the following error has been reported by a user:\n\n";
				$mail_body .= "-------------------------------------------------------------\n";
				$mail_body .= $_error['code'] . ' ' . $_error['message'] . "\n\n";
				$mail_body .= "File: " . $_error['file'] . ':' . $_error['line'] . "\n\n";
				$mail_body .= "Trace:\n" . trim($_error['trace']) . "\n\n";
				$mail_body .= "-------------------------------------------------------------\n\n";
				$mail_body .= "Froxlor-version: " . $version . "\n";
				$mail_body .= "DB-version: " . $dbversion . "\n\n";
				$mail_body .= "End of report";
				$mail_html = nl2br($mail_body);

				// send actual report to dev-team
				if (isset($_POST['send']) && $_POST['send'] == 'send') {
					// send mail and say thanks
					$_mailerror = false;
					$mailerr_msg = "";
					try {
						$this->mail->Subject = '[Froxlor] Error report by user';
						$this->mail->AltBody = $mail_body;
						$this->mail->MsgHTML($mail_html);
						$this->mail->AddAddress('error-reports@froxlor.org', 'Froxlor Developer Team');
						$this->mail->Send();
					} catch (\PHPMailer\PHPMailer\Exception $e) {
						$mailerr_msg = $e->errorMessage();
						$_mailerror = true;
					} catch (\Exception $e) {
						$mailerr_msg = $e->getMessage();
						$_mailerror = true;
					}

					if ($_mailerror) {
						// error when reporting an error...LOLFUQ
						\Froxlor\UI\Response::standard_error('send_report_error', $mailerr_msg);
					}

					// finally remove error from fs
					@unlink($err_file);
					\Froxlor\UI\Response::redirectTo($filename, array(
						's' => $s
					));
				}
				// show a nice summary of the error-report
				// before actually sending anything

			} else {
				\Froxlor\UI\Response::redirectTo($filename, array(
					's' => $s
				));
			}
		} else {
			\Froxlor\UI\Response::redirectTo($filename, array(
				's' => $s
			));
		}
	}
}
