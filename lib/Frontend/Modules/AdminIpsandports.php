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
use Froxlor\Settings;
use Froxlor\Api\Commands\IpsAndPorts as IpsAndPorts;

class AdminIpsandports extends FeModule
{

	public function overview()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		try {
			$json_result = IpsAndPorts::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$ips = $result['list'];
		foreach ($ips as $index => $ip) {
			if (filter_var($ip['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$ip['ip'] = '[' . $ip['ip'] . ']';
			}
			$result['list'][$index] = $ip;
		}

		\Froxlor\PhpHelper::sortListBy($result['list'], 'ip');

		// add ip/port form
		$ipsandports_add_form = $this->ipportForm();

		\Froxlor\Frontend\UI::TwigBuffer('admin/ipsandports/index.html.twig', array(
			'page_title' => $this->lng['admin']['ipsandports']['ipsandports'],
			'ips' => $result,
			'form_data' => $ipsandports_add_form
		));
	}

	public function edit()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = IpsAndPorts::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				IpsAndPorts::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminIpsandports"
			));
		} else {

			// edit ip/port form
			$ipsandports_edit_form = $this->ipportForm($result);

			\Froxlor\Frontend\UI::TwigBuffer('admin/ipsandports/ipandport.html.twig', array(
				'page_title' => $this->lng['admin']['ipsandports']['ipsandports'],
				'ip' => $result,
				'form_data' => $ipsandports_edit_form
			));
		}
	}

	private function ipportForm($result = array())
	{
		// Do not display attributes that are not used by the current webserver
		$websrv = Settings::Get('system.webserver');
		$is_nginx = ($websrv == 'nginx');
		$is_apache = ($websrv == 'apache2');
		$is_apache24 = $is_apache && (Settings::Get('system.apache24') === '1');

		if (! empty($result) && isset($result['id'])) {
			$ipsandports_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/ipsandports/formfield.ipsandports_edit.php';
		} else {
			$ipsandports_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/ipsandports/formfield.ipsandports_add.php';
		}
		$ipsandports_form = \Froxlor\UI\HtmlForm::genHTMLForm($ipsandports_data);
		return $ipsandports_form;
	}
}
/*
 elseif ($action == 'delete' && $id != 0) {
		try {
			$json_result = IpsAndPorts::getLocal($userinfo, array(
				'id' => $id
			))->get();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($result['id']) && $result['id'] == $id) {
			if (isset($_POST['send']) && $_POST['send'] == 'send') {

				try {
					IpsAndPorts::getLocal($userinfo, array(
						'id' => $id
					))->delete();
				} catch (Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}

				\Froxlor\UI\Response::redirectTo($filename, array(
					'page' => $page,
					's' => $s
				));
			} else {
				\Froxlor\UI\HTML::askYesNo('admin_ip_reallydelete', $filename, array(
					'id' => $id,
					'page' => $page,
					'action' => $action
				), $result['ip'] . ':' . $result['port']);
			}
		}


}
*/