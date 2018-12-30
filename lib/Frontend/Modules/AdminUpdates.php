<?php
namespace Froxlor\Frontend\Modules;

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
 * @package Panel
 *         
 */
use Froxlor\Database\Database;
use Froxlor\Frontend\FeModule;
use Froxlor\Settings;

class AdminUpdates extends FeModule
{

	public function overview()
	{
		\Froxlor\FroxlorLogger::getLog()->addNotice("viewed AdminUpdates");

		/**
		 * this is a dirty hack but syscp 1.4.2.1 does not
		 * have any version/dbversion in the database (don't know why)
		 * so we have to set them both to run a correct upgrade
		 */
		if (! \Froxlor\Froxlor::isFroxlor()) {
			if (Settings::Get('panel.version') == null || Settings::Get('panel.version') == '') {
				Settings::Set('panel.version', '1.4.2.1');
			}
			if (Settings::Get('system.dbversion') == null || Settings::Get('system.dbversion') == '') {
				/**
				 * for syscp-stable (1.4.2.1) this value has to be 0
				 * so the required table-fields are added correctly
				 * and the svn-version has its value in the database
				 * -> bug #54
				 */
				$result_stmt = Database::query("
					SELECT `value` FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `varname` = 'dbversion'
				");
				$result = $result_stmt->fetch(\PDO::FETCH_ASSOC);

				if (isset($result['value'])) {
					Settings::Set('system.dbversion', (int) $result['value'], false);
				} else {
					Settings::Set('system.dbversion', 0, false);
				}
			}
		}

		if (\Froxlor\Froxlor::hasDbUpdates() || \Froxlor\Froxlor::hasUpdates()) {
			$successful_update = false;
			$message = '';

			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				if ((isset($_POST['update_preconfig']) && isset($_POST['update_changesagreed']) && intval($_POST['update_changesagreed']) != 0) || ! isset($_POST['update_preconfig'])) {

					ob_start();
					include_once \Froxlor\Froxlor::getInstallDir() . 'install/updatesql.php';
					$update_process = ob_get_contents();
					ob_end_clean();
					$redirect_url = 'index.php?module=AdminIndex';

					\Froxlor\Frontend\UI::TwigBuffer('admin/update/update.html.twig', array(
						'page_title' => $this->lng['update']['update'],
						'update_process' => $update_process,
						'redirect_url' => $redirect_url
					));

					\Froxlor\User::updateCounters();
					\Froxlor\System\Cronjob::inserttask('1');
					@chmod(\Froxlor\Froxlor::getInstallDir() . '/lib/userdata.inc.php', 0440);

					$successful_update = true;
				} else {
					$message = 'You have to agree that you have read the update notifications.';
				}
			}

			if (! $successful_update) {
				$current_version = Settings::Get('panel.version');
				$current_db_version = Settings::Get('panel.db_version');
				if (empty($current_db_version)) {
					$current_db_version = "0";
				}
				$new_version = \Froxlor\Froxlor::VERSION;
				$new_db_version = \Froxlor\Froxlor::DBVERSION;

				$ui_text = $this->lng['update']['update_information']['part_a'];
				if (\Froxlor\Froxlor::VERSION != $current_version) {
					$ui_text = str_replace('%curversion', $current_version, $ui_text);
					$ui_text = str_replace('%newversion', $new_version, $ui_text);
				} else {
					// show db version
					$ui_text = str_replace('%curversion', $current_db_version, $ui_text);
					$ui_text = str_replace('%newversion', $new_db_version, $ui_text);
				}
				$update_information = $ui_text;

				$preconfig = \Froxlor\Install\PreConfig::getPreConfig($current_version, $current_db_version);

				\Froxlor\Frontend\UI::TwigBuffer('admin/update/index.html.twig', array(
					'page_title' => $this->lng['update']['update'],
					'message' => $message,
					'update_information' => $update_information,
					'preconfig' => $preconfig
				));
			}
		} else {
			\Froxlor\UI\Response::standard_success($this->lng['update']['noupdatesavail'], '', array(
				'module' => 'AdminIndex'
			));
		}
	}
}
