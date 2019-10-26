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
use Froxlor\Settings;
use Froxlor\Frontend\FeModule;

class CustomerIndex extends FeModule
{

	public function logout()
	{
		\Froxlor\FroxlorLogger::getLog()->addNotice("logged out");

		$params = array(
			"customerid" => \Froxlor\CurrentUser::getField('customerid')
		);

		if (Settings::Get('session.allow_multiple_login') == '1') {
			$stmt = Database::prepare("DELETE FROM `" . TABLE_PANEL_SESSIONS . "`
			WHERE `userid` = :customerid
			AND `adminsession` = '0'
		");
		} else {
			$stmt = Database::prepare("DELETE FROM `" . TABLE_PANEL_SESSIONS . "`
			WHERE `userid` = :customerid
			AND `adminsession` = '0'");
		}
		Database::pexecute($stmt, $params);
		unset($_SESSION['userinfo']);
		\Froxlor\CurrentUser::setData(array());

		\Froxlor\UI\Response::redirectTo('index.php');
	}

	public function overview()
	{
		$domain_stmt = Database::prepare("SELECT `domain` FROM `" . TABLE_PANEL_DOMAINS . "`
			WHERE `customerid` = :customerid
			AND `parentdomainid` = '0'
			AND `id` <> :standardsubdomain
		");
		Database::pexecute($domain_stmt, array(
			"customerid" => \Froxlor\CurrentUser::getField('customerid'),
			"standardsubdomain" => \Froxlor\CurrentUser::getField('standardsubdomain')
		));

		$domains = '';
		$domainArray = array();
		$idna_convert = new \Froxlor\Idna\IdnaWrapper();
		while ($row = $domain_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$domainArray[] = $idna_convert->decode($row['domain']);
		}

		natsort($domainArray);
		$domains = implode(',<br />', $domainArray);

		// standard-subdomain
		$stdsubdomain = '';
		if (\Froxlor\CurrentUser::getField('standardsubdomain') != '0') {
			$std_domain_stmt = Database::prepare("
				SELECT `domain` FROM `" . TABLE_PANEL_DOMAINS . "`
				WHERE `customerid` = :customerid
				AND `id` = :standardsubdomain
			");
			$std_domain = Database::pexecute_first($std_domain_stmt, array(
				"customerid" => \Froxlor\CurrentUser::getField('customerid'),
				"standardsubdomain" => \Froxlor\CurrentUser::getField('standardsubdomain')
			));
			$stdsubdomain = $std_domain['domain'];
		}

		// get disk-space usages for web, mysql and mail
		$usages_stmt = Database::prepare("SELECT * FROM `" . TABLE_PANEL_DISKSPACE . "` WHERE `customerid` = :cid ORDER BY `stamp` DESC LIMIT 1");
		$usages = Database::pexecute_first($usages_stmt, array(
			'cid' => \Froxlor\CurrentUser::getField('customerid')
		));

		$overview = array(
			'subdomains',
			'diskspace',
			'traffic',
			'mysqls',
			'ftps',
			'emails',
			'email_accounts',
			'email_forwarders'
		);

		if (\Froxlor\Settings::Get('system.mail_quota_enabled') == '1') {
			$overview[] = 'email_quota';
		}

		// calculate percentage
		$overview_data = array();
		foreach ($overview as $entity) {
			$overview_data[$entity] = array(
				'avail' => \Froxlor\CurrentUser::getField($entity),
				'used' => \Froxlor\CurrentUser::getField($entity . '_used'),
				'perc' => (\Froxlor\CurrentUser::getField($entity) > 0) ? floor(\Froxlor\CurrentUser::getField($entity . '_used') / \Froxlor\CurrentUser::getField($entity)) : 0
			);
			if ($entity == 'email_accounts') {
				$overview_data[$entity]['hdd'] = $usages['mail'];
			} else if ($entity == 'mysqls') {
				$overview_data[$entity]['hdd'] = $usages['mysql'];
			}
		}

		$services_enabled = "";
		$se = array();
		if (\Froxlor\CurrentUser::getField('imap') == '1') {
			$se[] = "IMAP";
		}
		if (\Froxlor\CurrentUser::getField('pop3') == '1') {
			$se[] = "POP3";
		}
		if (\Froxlor\CurrentUser::getField('phpenabled') == '1') {
			$se[] = "PHP";
		}
		if (\Froxlor\CurrentUser::getField('perlenabled') == '1') {
			$se[] = "Perl/CGI";
		}
	        if (\Froxlor\CurrentUser::getField('api_allowed') == '1')
	                $se[] = '<a href="index.php?module=ApiKeys">API</a>';
		}
		$services_enabled = implode(", ", $se);

		\Froxlor\Frontend\UI::TwigBuffer('customer/index/index.html.twig', array(
			'page_title' => "Dashboard",
			'usage_data' => $overview_data,
			'disk_usage' => $usages,
			'domains' => $domains,
			'stdsubdomain' => $stdsubdomain,
			'services_enabled' => $services_enabled
		));
	}

	public function myAccount()
	{
		\Froxlor\Frontend\UI::TwigBuffer('myaccount.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('menue.main.username') . \Froxlor\CurrentUser::getField('loginname')
		));
	}
}
/*
} elseif ($page == 'change_password') {
	if (isset($_POST['send']) && $_POST['send'] == 'send') {
		$old_password = \Froxlor\Validate\Validate::validate($_POST['old_password'], 'old password');
		if (! \Froxlor\System\Crypt::validatePasswordLogin($userinfo, $old_password, TABLE_PANEL_CUSTOMERS, 'customerid')) {
			\Froxlor\UI\Response::standard_error('oldpasswordnotcorrect');
		}

		$new_password = \Froxlor\System\Crypt::validatePassword($_POST['new_password'], 'new password');
		$new_password_confirm = \Froxlor\System\Crypt::validatePassword($_POST['new_password_confirm'], 'new password confirm');

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
			// Update user password
			try {
				Customers::getLocal($userinfo, array(
					'id' => $userinfo['customerid'],
					'new_customer_password' => $new_password
				))->update();
			} catch (Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			$log->logAction(\Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, 'changed password');

			// Update ftp password
			if (isset($_POST['change_main_ftp']) && $_POST['change_main_ftp'] == 'true') {
				$cryptPassword = \Froxlor\System\Crypt::makeCryptPassword($new_password);
				$stmt = Database::prepare("UPDATE `" . TABLE_FTP_USERS . "`
					SET `password` = :password
					WHERE `customerid` = :customerid
					AND `username` = :username");
				$params = array(
					"password" => $cryptPassword,
					"customerid" => $userinfo['customerid'],
					"username" => $userinfo['loginname']
				);
				Database::pexecute($stmt, $params);
				$log->logAction(\Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, 'changed main ftp password');
			}

			// Update statistics password
			if (isset($_POST['change_stats']) && $_POST['change_stats'] == 'true') {
				$new_stats_password = \Froxlor\System\Crypt::makeCryptPassword($new_password, true);

				$stmt = Database::prepare("UPDATE `" . TABLE_PANEL_HTPASSWDS . "`
					SET `password` = :password
					WHERE `customerid` = :customerid
					AND `username` = :username");
				$params = array(
					"password" => $new_stats_password,
					"customerid" => $userinfo['customerid'],
					"username" => $userinfo['loginname']
				);
				Database::pexecute($stmt, $params);
			}

			\Froxlor\UI\Response::redirectTo($filename, array(
				's' => $s
			));
		}
	} else {
		eval("echo \"" . \Froxlor\UI\Template::getTemplate('index/change_password') . "\";");
	}
} elseif ($page == 'change_language') {
	if (isset($_POST['send']) && $_POST['send'] == 'send') {
		$def_language = \Froxlor\Validate\Validate::validate($_POST['def_language'], 'default language');
		if (isset($languages[$def_language])) {
			try {
				Customers::getLocal($userinfo, array(
					'id' => $userinfo['customerid'],
					'def_language' => $def_language
				))->update();
			} catch (Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}

			// also update current session
			$stmt = Database::prepare("UPDATE `" . TABLE_PANEL_SESSIONS . "`
				SET `language` = :lang
				WHERE `hash` = :hash");
			Database::pexecute($stmt, array(
				"lang" => $def_language,
				"hash" => $s
			));
		}
		$log->logAction(\Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, "changed default language to '" . $def_language . "'");
		\Froxlor\UI\Response::redirectTo($filename, array(
			's' => $s
		));
	} else {
		$default_lang = Settings::Get('panel.standardlanguage');
		if ($userinfo['def_language'] != '') {
			$default_lang = $userinfo['def_language'];
		}

		$language_options = '';
		foreach ($languages as $language_file => $language_name) {
			$language_options .= \Froxlor\UI\HTML::makeoption($language_name, $language_file, $default_lang, true);
		}

		eval("echo \"" . \Froxlor\UI\Template::getTemplate('index/change_language') . "\";");
	}
} elseif ($page == 'change_theme') {
	if (isset($_POST['send']) && $_POST['send'] == 'send') {
		$theme = \Froxlor\Validate\Validate::validate($_POST['theme'], 'theme');
		try {
			Customers::getLocal($userinfo, array(
				'id' => $userinfo['customerid'],
				'theme' => $theme
			))->update();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}

		// also update current session
		$stmt = Database::prepare("UPDATE `" . TABLE_PANEL_SESSIONS . "`
			SET `theme` = :theme
			WHERE `hash` = :hash");
		Database::pexecute($stmt, array(
			"theme" => $theme,
			"hash" => $s
		));

		$log->logAction(\Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, "changed default theme to '" . $theme . "'");
		\Froxlor\UI\Response::redirectTo($filename, array(
			's' => $s
		));
	} else {
		$default_theme = Settings::Get('panel.default_theme');
		if ($userinfo['theme'] != '') {
			$default_theme = $userinfo['theme'];
		}

		$theme_options = '';
		$themes_avail = \Froxlor\Frontend\UI::getThemes();
		foreach ($themes_avail as $t => $d) {
			$theme_options .= \Froxlor\UI\HTML::makeoption($d, $t, $default_theme, true);
		}

		eval("echo \"" . \Froxlor\UI\Template::getTemplate('index/change_theme') . "\";");
	}
} elseif ($page == 'send_error_report' && Settings::Get('system.allow_error_report_customer') == '1') {

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
			$mail_html = str_replace("\n", "<br />", $mail_body);

			// send actual report to dev-team
			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				// send mail and say thanks
				$_mailerror = false;
				try {
					$mail->Subject = '[Froxlor] Error report by user';
					$mail->AltBody = $mail_body;
					$mail->MsgHTML($mail_html);
					$mail->AddAddress('error-reports@froxlor.org', 'Froxlor Developer Team');
					$mail->Send();
				} catch (\PHPMailer\PHPMailer\Exception $e) {
					$mailerr_msg = $e->errorMessage();
					$_mailerror = true;
				} catch (Exception $e) {
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
			eval("echo \"" . \Froxlor\UI\Template::getTemplate("index/send_error_report") . "\";");
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
*/
