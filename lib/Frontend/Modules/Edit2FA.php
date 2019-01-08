<?php
namespace Froxlor\Frontend\Modules;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2018 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Froxlor team <team@froxlor.org> (2018-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Panel
 * @since 0.10.0
 *       
 */
use Froxlor\Database\Database;
use Froxlor\Settings;
use Froxlor\Frontend\FeModule;

class Edit2FA extends FeModule
{

	public function overview()
	{
		if (Settings::Get('2fa.enabled') != '1') {
			\Froxlor\UI\Response::dynamic_error("2FA not activated");
		}

		if (\Froxlor\CurrentUser::isAdmin()) {
			$upd_stmt = Database::prepare("UPDATE `" . TABLE_PANEL_ADMINS . "` SET `type_2fa` = :t2fa, `data_2fa` = :d2fa WHERE adminid = :id");
			$uid = \Froxlor\CurrentUser::getField('adminid');
		} else {
			$upd_stmt = Database::prepare("UPDATE `" . TABLE_PANEL_CUSTOMERS . "` SET `type_2fa` = :t2fa, `data_2fa` = :d2fa WHERE customerid = :id");
			$uid = \Froxlor\CurrentUser::getField('customerid');
		}

		$tfa = new \Froxlor\FroxlorTwoFactorAuth('Froxlor');

		$log->logAction(\Froxlor\FroxlorLogger::USR_ACTION, LOG_NOTICE, "viewed Edit2FA::overview");

		if ($userinfo['type_2fa'] == '0') {

			// available types
			$type_select_values = array(
				0 => '-',
				1 => 'E-Mail',
				2 => 'Authenticator'
			);
			asort($type_select_values);
			$type_select = "";
			foreach ($type_select_values as $_val => $_type) {
				$type_select .= \Froxlor\UI\HTML::makeoption($_type, $_val);
			}
		} elseif ($userinfo['type_2fa'] == '1') {
			// email Edit2FA enabled
		} elseif ($userinfo['type_2fa'] == '2') {
			// authenticator Edit2FA enabled
			$ga_qrcode = $tfa->getQRCodeImageAsDataUri($userinfo['loginname'], $userinfo['data_2fa']);
		}
		eval("echo \"" . \Froxlor\UI\Template::getTemplate("2fa/overview", true) . "\";");
	}

	/**
	 * do the delete and then just show a success-message
	 */
	public function delete()
	{
		Database::pexecute($upd_stmt, array(
			't2fa' => 0,
			'd2fa' => "",
			'id' => $uid
		));
		\Froxlor\UI\Response::standard_success($lng['2fa']['2fa_removed']);
	}

	public function add()
	{
		$type = isset($_POST['type_2fa']) ? $_POST['type_2fa'] : '0';

		if ($type == 0 || $type == 1) {
			$data = "";
		}
		if ($type == 2) {
			// generate secret for TOTP
			$data = $tfa->createSecret();
		}
		Database::pexecute($upd_stmt, array(
			't2fa' => $type,
			'd2fa' => $data,
			'id' => $uid
		));
		\Froxlor\UI\Response::standard_success(sprintf($lng['2fa']['2fa_added'], $filename, $s));
	}
}
