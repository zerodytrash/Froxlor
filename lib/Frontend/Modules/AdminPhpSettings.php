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
use Froxlor\Api\Commands\PhpSettings as PhpSettings;
use Froxlor\Frontend\FeModule;

class AdminPhpSettings extends FeModule
{

	public function overview()
	{
		try {
			$json_result = PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), array(
				'with_subdomains' => true
			))->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$phpconfig_add_form = "";
		if (\Froxlor\CurrentUser::getField('change_serversettings') == '1') {
			$configs_stmt = Database::query("SELECT * FROM `" . TABLE_PANEL_PHPCONFIGS . "` WHERE `id` = 1");
			$default_config = $configs_stmt->fetch(\PDO::FETCH_ASSOC);

			$fpmconfigs = '';
			$configs = Database::query("SELECT * FROM `" . TABLE_PANEL_FPMDAEMONS . "` ORDER BY `description` ASC");
			while ($row = $configs->fetch(\PDO::FETCH_ASSOC)) {
				$fpmconfigs .= \Froxlor\UI\HTML::makeoption($row['description'], $row['id'], 1, true, true);
			}

			$pm_select = \Froxlor\UI\HTML::makeoption('static', 'static', 'static', true, true);
			$pm_select .= \Froxlor\UI\HTML::makeoption('dynamic', 'dynamic', 'static', true, true);
			$pm_select .= \Froxlor\UI\HTML::makeoption('ondemand', 'ondemand', 'static', true, true);

			$phpconfig_add_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/phpconfig/formfield.phpconfig_add.php';
			$phpconfig_add_form = \Froxlor\UI\HtmlForm::genHTMLForm($phpconfig_add_data);
		}

		\Froxlor\Frontend\UI::TwigBuffer('admin/phpconfigs/index.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('menue.phpsettings.maintitle'),
			'phpconfigs' => $result,
			'form_data' => $phpconfig_add_form
		));
	}

	public function add()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->add();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php?module=AdminPhpSettings");
		}
		\Froxlor\UI\Response::redirectTo('index.php?module=AdminPhpSettings');
	}

	public function delete()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		// cannot delete the default php.config (id = 1)
		if ($result['id'] != 0 && $result['id'] == $id && $id != 1) {
			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), array(
						'id' => $id
					))->delete();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo("index.php?module=AdminPhpSettings");
			} else {
				\Froxlor\UI\HTML::askYesNo('phpsetting_reallydelete', "index.php?module=AdminPhpSettings", array(
					'id' => $id,
					'view' => __FUNCTION__
				), $result['description']);
			}
		} else {
			\Froxlor\UI\Response::standard_error('nopermissionsorinvalidid');
		}
	}

	public function edit()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($result['id'] != 0 && $result['id'] == $id) {

			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					PhpSettings::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo("index.php?module=AdminPhpSettings");
			} else {

				$fpmconfigs = '';
				$configs = Database::query("SELECT * FROM `" . TABLE_PANEL_FPMDAEMONS . "` ORDER BY `description` ASC");
				while ($row = $configs->fetch(\PDO::FETCH_ASSOC)) {
					$fpmconfigs .= \Froxlor\UI\HTML::makeoption($row['description'], $row['id'], $result['fpmsettingid'], true, true);
				}

				$pm_select = \Froxlor\UI\HTML::makeoption('static', 'static', $result['pm'], true, true);
				$pm_select .= \Froxlor\UI\HTML::makeoption('dynamic', 'dynamic', $result['pm'], true, true);
				$pm_select .= \Froxlor\UI\HTML::makeoption('ondemand', 'ondemand', $result['pm'], true, true);

				$phpconfig_edit_data = include_once dirname(__FILE__) . '/lib/formfields/admin/phpconfig/formfield.phpconfig_edit.php';
				$phpconfig_edit_form = \Froxlor\UI\HtmlForm::genHTMLForm($phpconfig_edit_data);

				$title = $phpconfig_edit_data['phpconfig_edit']['title'];
				$image = $phpconfig_edit_data['phpconfig_edit']['image'];

				eval("echo \"" . \Froxlor\UI\Template::getTemplate("phpconfig/overview_edit") . "\";");
			}
		} else {
			\Froxlor\UI\Response::standard_error('nopermissionsorinvalidid');
		}
	}

	public function phpinfo()
	{
		ob_start();
		phpinfo();
		$phpinforesult = ob_get_clean();

		$phpinfo = array(
			'phpinfo' => array()
		);

		$matches = null;
		if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $phpinforesult, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$end = array_keys($phpinfo);
				$end = end($end);
				if (strlen($match[1])) {
					$phpinfo[$match[1]] = array();
				} elseif (isset($match[3])) {
					$phpinfo[$end][$match[2]] = isset($match[4]) ? array(
						$match[3],
						$match[4]
					) : $match[3];
				} else {
					$phpinfo[$end][] = $match[2];
				}
			}
			$phpinfo;
		} else {
			\Froxlor\UI\Response::standard_error(\Froxlor\Frontend\UI::getLng('error.no_phpinfo'));
		}

		\Froxlor\Frontend\UI::TwigBuffer('admin/phpconfigs/phpinfo.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('admin.phpinfo'),
			'phpinfo' => $phpinfo,
			'phpversion' => PHP_VERSION
		));
	}
}
