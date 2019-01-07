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

use Froxlor\Api\Commands\FpmDaemons as FpmDaemons;
use Froxlor\Frontend\FeModule;

class AdminFpmDaemons extends FeModule
{

	public function overview()
	{
		try {
			$json_result = FpmDaemons::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$fpmconfig_add_form = "";
		if (\Froxlor\CurrentUser::getField('change_serversettings') == '1') {
			$pm_select = \Froxlor\UI\HTML::makeoption('static', 'static', 'static', true, true);
			$pm_select .= \Froxlor\UI\HTML::makeoption('dynamic', 'dynamic', 'static', true, true);
			$pm_select .= \Froxlor\UI\HTML::makeoption('ondemand', 'ondemand', 'static', true, true);

			$fpmconfig_add_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/phpconfig/formfield.fpmconfig_add.php';
			$fpmconfig_add_form = \Froxlor\UI\HtmlForm::genHTMLForm($fpmconfig_add_data);
		}

		\Froxlor\Frontend\UI::TwigBuffer('admin/fpmdaemons/index.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('menue.phpsettings.maintitle'),
			'fpmdaemons' => $result,
			'form_data' => $fpmconfig_add_form
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
				FpmDaemons::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->add();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php?module=AdminFpmDaemons");
		}
		\Froxlor\UI\Response::redirectTo('index.php?module=AdminFpmDaemons');
	}

	public function delete()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = FpmDaemons::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($id == 1) {
			\Froxlor\UI\Response::standard_error('cannotdeletedefaultphpconfig');
		}

		// cannot delete the default php.config (id = 1)
		if ($result['id'] != 0 && $result['id'] == $id && $id != 1) {
			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					FpmDaemons::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->delete();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo("index.php?module=AdminFpmDaemons");
			} else {
				\Froxlor\UI\HTML::askYesNo('fpmsetting_reallydelete', "index.php?module=AdminFpmDaemons", array(
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
			$json_result = FpmDaemons::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if ($result['id'] != 0 && $result['id'] == $id) {

			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					FpmDaemons::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo("index.php?module=AdminFpmDaemons");
			} else {

				$pm_select = \Froxlor\UI\HTML::makeoption('static', 'static', $result['pm'], true, true);
				$pm_select .= \Froxlor\UI\HTML::makeoption('dynamic', 'dynamic', $result['pm'], true, true);
				$pm_select .= \Froxlor\UI\HTML::makeoption('ondemand', 'ondemand', $result['pm'], true, true);

				$fpmconfig_edit_data = include_once dirname(__FILE__) . '/lib/formfields/admin/phpconfig/formfield.fpmconfig_edit.php';
				$fpmconfig_edit_form = \Froxlor\UI\HtmlForm::genHTMLForm($fpmconfig_edit_data);

				$title = $fpmconfig_edit_data['fpmconfig_edit']['title'];
				$image = $fpmconfig_edit_data['fpmconfig_edit']['image'];

				eval("echo \"" . \Froxlor\UI\Template::getTemplate("phpconfig/fpmconfig_edit") . "\";");
			}
		} else {
			\Froxlor\UI\Response::standard_error('nopermissionsorinvalidid');
		}
	}
}
