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

	/**
	 * do the delete and then just show a success-message
	 */
	public function delete()
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

		Database::pexecute($upd_stmt, array(
			't2fa' => 0,
			'd2fa' => "",
			'id' => $uid
		));
		\Froxlor\CurrentUser::setField('type_2fa', 0);
		\Froxlor\CurrentUser::setField('data_2fa', "");
		\Froxlor\UI\Response::standard_success($this->lng['2fa']['2fa_removed'], "", array('filename' => "index.php?module=" . (\Froxlor\CurrentUser::isAdmin() ? "Admin" : "Customer") . "Index&view=myAccount"));
	}

	public function add()
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
		\Froxlor\CurrentUser::setField('type_2fa', $type);
		\Froxlor\CurrentUser::setField('data_2fa', $data);
		\Froxlor\UI\Response::dynamic_success($this->lng['2fa']['2fa_added'], "", array('filename' => "index.php?module=" . (\Froxlor\CurrentUser::isAdmin() ? "Admin" : "Customer") . "Index&view=myAccount"));
	}
}
