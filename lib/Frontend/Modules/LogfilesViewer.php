<?php
namespace Froxlor\Frontend\Modules;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2016 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Froxlor team <team@froxlor.org> (2016-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Panel
 *         
 */
use Froxlor\Settings;
use Froxlor\Api\Commands\SubDomains as SubDomains;
use Froxlor\Frontend\FeModule;

class LogfilesViewer extends FeModule
{

	public function overview()
	{
		$domain_id = isset($_GET['domain_id']) ? (int) $_GET['domain_id'] : null;
		$last_n = isset($_GET['number_of_lines']) ? (int) $_GET['number_of_lines'] : 100;

		// user's with logviewenabled = false
		if (! \Froxlor\CurrentUser::isAdmin() && \Froxlor\CurrentUser::getField('logviewenabled') != '1') {
			// back to domain overview
			\Froxlor\UI\Response::redirectTo("index.php?module=CustomerDomains");
		}

		if (function_exists('exec')) {

			// get domain-info
			try {
				$json_result = SubDomains::getLocal(\Froxlor\CurrentUser::getData(), array(
					'id' => $domain_id
				))->get();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			$domain = json_decode($json_result, true)['data'];

			$speciallogfile = '';
			if ($domain['speciallogfile'] == '1') {
				if ($domain['parentdomainid'] == '0') {
					$speciallogfile = '-' . $domain['domain'];
				} else {
					$speciallogfile = '-' . $domain['parentdomain'];
				}
			}
			// The normal access/error - logging is enabled
			$error_log = \Froxlor\FileDir::makeCorrectFile(Settings::Get('system.logfiles_directory') . \Froxlor\Customer\Customer::getCustomerDetail($domain['customerid'], 'loginname') . $speciallogfile . '-error.log');
			$access_log = \Froxlor\FileDir::makeCorrectFile(Settings::Get('system.logfiles_directory') . \Froxlor\Customer\Customer::getCustomerDetail($domain['customerid'], 'loginname') . $speciallogfile . '-access.log');

			// error log
			if (file_exists($error_log)) {
				$result = \Froxlor\FileDir::safe_exec('tail -n ' . $last_n . ' ' . escapeshellarg($error_log));
				$error_log_content = implode("\n", $result);
			} else {
				$error_log_content = "Error-Log" . (\Froxlor\CurrentUser::isAdmin() ? " '" . $error_log . "'" : "") . " does not seem to exist";
			}

			// access log
			if (file_exists($access_log)) {
				$result = \Froxlor\FileDir::safe_exec('tail -n ' . $last_n . ' ' . escapeshellarg($access_log));
				$access_log_content = implode("\n", $result);
			} else {
				$access_log_content = "Access-Log" . (\Froxlor\CurrentUser::isAdmin() ? " '" . $access_log . "'" : "") . " does not seem to exist";
			}

			// show content
			\Froxlor\Frontend\UI::TwigBuffer('logfiles_viewer/index.html.twig', array(
				'page_title' => "Dashboard",
				'error_log_content' => $error_log_content,
				'access_log_content' => $access_log_content
			));

		} else {
			if (\Froxlor\CurrentUser::isAdmin()) {
				\Froxlor\UI\Response::dynamic_error('You need to allow the exec() function in the froxlor-vhost php-config');
			} else {
				\Froxlor\UI\Response::dynamic_error('Required function exec() is not allowed. Please contact the system administrator.');
			}
		}
	}
}
