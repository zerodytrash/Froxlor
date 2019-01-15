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
use Froxlor\Settings;
use Froxlor\Api\Commands\Admins;
use Froxlor\Api\Commands\Customers;
use Froxlor\Api\Commands\PhpSettings;
use Froxlor\Database\Database;
use Froxlor\Frontend\FeModule;
use Froxlor\Api\Commands\Domains as Domains;
use Froxlor\Api\Commands\IpsAndPorts;

class AdminDomains extends FeModule
{

	public function overview()
	{
		try {
			$json_result = Domains::getLocal(\Froxlor\CurrentUser::getData(), array(
				'extended' => true
			))->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$idna = new \Froxlor\Idna\IdnaWrapper();
		$domains = $result['list'];
		$domain_array = array();
		foreach ($domains as $domain) {
			// idna convert
			$domain['aliasdomain'] = $idna->decode($domain['aliasdomain']);
			// customername
			$domain['customername'] = \Froxlor\User::getCorrectFullUserDetails($domain);
			// alias-wubba-lubba-dubdub
			if (! isset($domain_array[$domain['domain']])) {
				$domain_array[$domain['domain']] = $domain;
			} else {
				$domain_array[$domain['domain']] = array_merge($domain, $domain_array[$domain['domain']]);
			}
			if (isset($domain['aliasdomainid']) && $domain['aliasdomainid'] != null && isset($domain['aliasdomain']) && $domain['aliasdomain'] != '') {
				if (! isset($domain_array[$domain['aliasdomain']])) {
					$domain_array[$domain['aliasdomain']] = array();
				}
				$domain_array[$domain['aliasdomain']]['domainaliasid'] = $domain['id'];
				$domain_array[$domain['aliasdomain']]['domainalias'] = $domain['domain'];
			}
		}
		$result['list'] = $domain_array;

		/*
		 * $domain_array = array();
		 * foreach ($result['list'] as $row) {
		 *
		 * // formatDomainEntry($row, $idna_convert);
		 *
		 * if (! isset($domain_array[$row['domain']])) {
		 * $domain_array[$row['domain']] = $row;
		 * } else {
		 * $domain_array[$row['domain']] = array_merge($row, $domain_array[$row['domain']]);
		 * }
		 *
		 * if (isset($row['aliasdomainid']) && $row['aliasdomainid'] != null && isset($row['aliasdomain']) && $row['aliasdomain'] != '') {
		 * if (! isset($domain_array[$row['aliasdomain']])) {
		 * $domain_array[$row['aliasdomain']] = array();
		 * }
		 * $domain_array[$row['aliasdomain']]['domainaliasid'] = $row['id'];
		 * $domain_array[$row['aliasdomain']]['domainalias'] = $row['domain'];
		 * }
		 * }
		 */
		\Froxlor\PhpHelper::sortListBy($result['list'], 'domain');

		// domain add form
		$domain_add_form = "";
		if (\Froxlor\CurrentUser::getField('domains') != 0) {
			$domain_add_form = $this->domainForm();
		}

		\Froxlor\Frontend\UI::TwigBuffer('admin/domains/index.html.twig', array(
			'page_title' => $this->lng['panel']['domains'],
			'domains' => $result,
			'form_data' => $domain_add_form
		));
	}

	public function edit()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		try {
			$json_result = Domains::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			try {
				Domains::getLocal(\Froxlor\CurrentUser::getData(), $_POST)->update();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::redirectTo("index.php", array(
				'module' => "AdminDomains"
			));
		} else {
			$speciallogfile = ($result['speciallogfile'] == 1 ? $this->lng['panel']['yes'] : $this->lng['panel']['no']);
			$speciallogwarning = sprintf($this->lng['admin']['speciallogwarning'], $this->lng['admin']['delete_statistics']);

			$domain_edit_form = $this->domainForm($result);

			\Froxlor\Frontend\UI::TwigBuffer('admin/domains/domain.html.twig', array(
				'page_title' => $this->lng['admin']['domain_edit'],
				'domain' => $result,
				'form_data' => $domain_edit_form
			));
		}
	}

	private function domainForm($result = array())
	{
		$idna_convert = new \Froxlor\Idna\IdnaWrapper();
		$customers = \Froxlor\UI\HTML::makeoption($this->lng['panel']['please_choose'], 0, 0, true);
		try {
			$json_result = Customers::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$jresult = json_decode($json_result, true)['data'];

		foreach ($jresult['list'] as $row_customer) {
			$customers .= \Froxlor\UI\HTML::makeoption(\Froxlor\User::getCorrectFullUserDetails($row_customer) . ' (' . $row_customer['loginname'] . ')', $row_customer['customerid']);
		}

		$admins = '';
		if (\Froxlor\CurrentUser::getField('customers_see_all') == '1') {
			if (Settings::Get('panel.allow_domain_change_admin') == '1') {
				$sel_value = ! empty($result) && isset($result['adminid']) ? $result['adminid'] : \Froxlor\CurrentUser::getField('adminid');
				try {
					$json_result = Admins::getLocal(\Froxlor\CurrentUser::getData())->listing();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				$jresult = json_decode($json_result, true)['data'];

				foreach ($jresult['list'] as $row_admin) {
					$admins .= \Froxlor\UI\HTML::makeoption(\Froxlor\User::getCorrectFullUserDetails($row_admin) . ' (' . $row_admin['loginname'] . ')', $row_admin['adminid'], $sel_value);
				}
			} else {
				$admin_stmt = Database::prepare("
							SELECT `adminid`, `loginname`, `name` FROM `" . TABLE_PANEL_ADMINS . "` WHERE `adminid` = :adminid
						");
				$admin = Database::pexecute_first($admin_stmt, array(
					'adminid' => $result['adminid']
				));
				$result['adminname'] = \Froxlor\User::getCorrectFullUserDetails($admin) . ' (' . $admin['loginname'] . ')';
			}
		}

		try {
			$json_result = IpsAndPorts::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$jresult = json_decode($json_result, true)['data'];

		// Build array holding all IPs and Ports available to this admin
		$ipsandports = array();
		$ssl_ipsandports = array();
		foreach ($jresult['list'] as $row_ipandport) {

			if (filter_var($row_ipandport['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$row_ipandport['ip'] = '[' . $row_ipandport['ip'] . ']';
			}

			if ($row_ipandport['ssl'] == '1') {
				$ssl_ipsandports[] = array(
					'label' => $row_ipandport['ip'] . ':' . $row_ipandport['port'] . '<br />',
					'value' => $row_ipandport['id']
				);
			} else {
				$ipsandports[] = array(
					'label' => $row_ipandport['ip'] . ':' . $row_ipandport['port'] . '<br />',
					'value' => $row_ipandport['id']
				);
			}
		}

		$standardsubdomains = array();
		$result_standardsubdomains_stmt = Database::query("
			SELECT `id` FROM `" . TABLE_PANEL_DOMAINS . "` `d`, `" . TABLE_PANEL_CUSTOMERS . "` `c` WHERE `d`.`id` = `c`.`standardsubdomain`
		");

		while ($row_standardsubdomain = $result_standardsubdomains_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$standardsubdomains[] = $row_standardsubdomain['id'];
		}

		if (count($standardsubdomains) > 0) {
			$standardsubdomains = " AND `d`.`id` NOT IN (" . join(',', $standardsubdomains) . ") ";
		} else {
			$standardsubdomains = '';
		}

		$sel_value = ! empty($result) && isset($result['aliasdomain']) ? $result['aliasdomain'] : null;
		$domains = \Froxlor\UI\HTML::makeoption($this->lng['domains']['noaliasdomain'], 0, NULL, true);
		$result_domains_stmt = Database::prepare("
					SELECT `d`.`id`, `d`.`domain`, `c`.`loginname` FROM `" . TABLE_PANEL_DOMAINS . "` `d`, `" . TABLE_PANEL_CUSTOMERS . "` `c`
					WHERE `d`.`aliasdomain` IS NULL AND `d`.`parentdomainid` = 0" . $standardsubdomains . (\Froxlor\CurrentUser::getField('customers_see_all') ? '' : " AND `d`.`adminid` = :adminid") . "
					AND `d`.`customerid`=`c`.`customerid` ORDER BY `loginname`, `domain` ASC
				");
		$params = array();
		if (\Froxlor\CurrentUser::getField('customers_see_all') == '0') {
			$params['adminid'] = \Froxlor\CurrentUser::getField('adminid');
		}
		Database::pexecute($result_domains_stmt, $params);

		while ($row_domain = $result_domains_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$domains .= \Froxlor\UI\HTML::makeoption($idna_convert->decode($row_domain['domain']) . ' (' . $row_domain['loginname'] . ')', $row_domain['id'], $sel_value);
		}

		$sel_value = ! empty($result) && isset($result['ismainbutsubto']) ? $result['ismainbutsubto'] : null;
		$subtodomains = \Froxlor\UI\HTML::makeoption($this->lng['domains']['nosubtomaindomain'], 0, NULL, true);
		$result_domains_stmt = Database::prepare("
					SELECT `d`.`id`, `d`.`domain`, `c`.`loginname` FROM `" . TABLE_PANEL_DOMAINS . "` `d`, `" . TABLE_PANEL_CUSTOMERS . "` `c`
					WHERE `d`.`aliasdomain` IS NULL AND `d`.`parentdomainid` = 0 AND `d`.`ismainbutsubto` = 0 " . $standardsubdomains . (\Froxlor\CurrentUser::getField('customers_see_all') ? '' : " AND `d`.`adminid` = :adminid") . "
					AND `d`.`customerid`=`c`.`customerid` ORDER BY `loginname`, `domain` ASC
				");
		// params from above still valid
		Database::pexecute($result_domains_stmt, $params);

		while ($row_domain = $result_domains_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$subtodomains .= \Froxlor\UI\HTML::makeoption($idna_convert->decode($row_domain['domain']) . ' (' . $row_domain['loginname'] . ')', $row_domain['id'], $sel_value);
		}

		$phpconfigs = "";
		try {
			$json_result = PhpSettings::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$php_result = json_decode($json_result, true)['data'];
		$sel_value = ! empty($result) && isset($result['phpsettingid']) ? $result['phpsettingid'] : null;
		foreach ($php_result['list'] as $row) {
			$label = $row['description'];
			if ((int) Settings::Get('phpfpm.enabled') == 1) {
				$label .= " [" . $row['fpmdesc'] . "]";
			}
			$phpconfigs .= \Froxlor\UI\HTML::makeoption($label, $row['id'], $sel_value);
		}

		// create serveralias options
		$sel_value = '0';
		if (! empty($result) && isset($result['iswildcarddomain']) && isset($result['wwwserveralias'])) {
			$sel_value = '2';
			if ($result['iswildcarddomain'] == '1') {
				$sel_value = '0';
			} elseif ($result['wwwserveralias'] == '1') {
				$sel_value = '1';
			}
		}
		$serveraliasoptions = "";
		$serveraliasoptions .= \Froxlor\UI\HTML::makeoption($this->lng['domains']['serveraliasoption_wildcard'], '0', $sel_value, true, true);
		$serveraliasoptions .= \Froxlor\UI\HTML::makeoption($this->lng['domains']['serveraliasoption_www'], '1', $sel_value, true, true);
		$serveraliasoptions .= \Froxlor\UI\HTML::makeoption($this->lng['domains']['serveraliasoption_none'], '2', $sel_value, true, true);

		$sel_value = ! empty($result) && isset($result['phpsettingid']) ? $result['subcanemaildomain'] : '0';
		$subcanemaildomain = \Froxlor\UI\HTML::makeoption($this->lng['admin']['subcanemaildomain']['never'], '0', $sel_value, true, true);
		$subcanemaildomain .= \Froxlor\UI\HTML::makeoption($this->lng['admin']['subcanemaildomain']['choosableno'], '1', $sel_value, true, true);
		$subcanemaildomain .= \Froxlor\UI\HTML::makeoption($this->lng['admin']['subcanemaildomain']['choosableyes'], '2', $sel_value, true, true);
		$subcanemaildomain .= \Froxlor\UI\HTML::makeoption($this->lng['admin']['subcanemaildomain']['always'], '3', $sel_value, true, true);

		$add_date = ! empty($result) && isset($result['add_date']) ? $result['add_date'] : date('Y-m-d');

		if (! empty($result) && isset($result['domain'])) {
			// idna convert domain
			$result['domain'] = $idna_convert->decode($result['domain']);
			// check for tmp-ssl-redirect
			$result['temporary_ssl_redirect'] = $result['ssl_redirect'];
			// get number of alias domains
			$alias_check_stmt = Database::prepare("
					SELECT COUNT(`id`) AS count FROM `" . TABLE_PANEL_DOMAINS . "` WHERE
					`aliasdomain` = :resultid
				");
			$alias_check = Database::pexecute_first($alias_check_stmt, array(
				'resultid' => $result['id']
			));
			$alias_check = $alias_check['count'];
			// get used ips
			$ipsresult_stmt = Database::prepare("
				SELECT `id_ipandports` FROM `" . TABLE_DOMAINTOIP . "` WHERE `id_domain` = :id
			");
			Database::pexecute($ipsresult_stmt, array(
				'id' => $result['id']
			));

			$usedips = array();
			while ($ipsresultrow = $ipsresult_stmt->fetch(\PDO::FETCH_ASSOC)) {
				$usedips[] = $ipsresultrow['id_ipandports'];
			}
			// get used subdomains
			$subdomains_stmt = Database::prepare("
					SELECT COUNT(`id`) AS count FROM `" . TABLE_PANEL_DOMAINS . "` WHERE
					`parentdomainid` = :resultid
				");
			$subdomains = Database::pexecute_first($subdomains_stmt, array(
				'resultid' => $result['id']
			));
			$subdomains = $subdomains['count'];
			// get used mail/accounts/forwarders
			$domain_emails_result_stmt = Database::prepare("
					SELECT `email`, `email_full`, `destination`, `popaccountid` AS `number_email_forwarders`
					FROM `" . TABLE_MAIL_VIRTUAL . "` WHERE `customerid` = :customerid AND `domainid` = :id
				");
			Database::pexecute($domain_emails_result_stmt, array(
				'customerid' => $result['customerid'],
				'id' => $result['id']
			));

			$emails = Database::num_rows();
			$email_forwarders = 0;
			$email_accounts = 0;

			while ($domain_emails_row = $domain_emails_result_stmt->fetch(\PDO::FETCH_ASSOC)) {

				if ($domain_emails_row['destination'] != '') {

					$domain_emails_row['destination'] = explode(' ', \Froxlor\FileDir::makeCorrectDestination($domain_emails_row['destination']));
					$email_forwarders += count($domain_emails_row['destination']);

					if (in_array($domain_emails_row['email_full'], $domain_emails_row['destination'])) {
						$email_forwarders -= 1;
						$email_accounts ++;
					}
				}
			}
			// customer select if allowed to switch admins
			if (Settings::Get('panel.allow_domain_change_customer') == '1') {
				// get customers list
				$customers = '';
				$result_customers_stmt = Database::prepare("
						SELECT `customerid`, `loginname`, `name`, `firstname`, `company` FROM `" . TABLE_PANEL_CUSTOMERS . "`
						WHERE ( (`subdomains_used` + :subdomains <= `subdomains` OR `subdomains` = '-1' )
						AND (`emails_used` + :emails <= `emails` OR `emails` = '-1' )
						AND (`email_forwarders_used` + :forwarders <= `email_forwarders` OR `email_forwarders` = '-1' )
						AND (`email_accounts_used` + :accounts <= `email_accounts` OR `email_accounts` = '-1' ) " . (\Froxlor\CurrentUser::getField('customers_see_all') ? '' : " AND `adminid` = :adminid ") . ")
						OR `customerid` = :customerid ORDER BY `name` ASC
					");
				$params = array(
					'subdomains' => $subdomains,
					'emails' => $emails,
					'forwarders' => $email_forwarders,
					'accounts' => $email_accounts,
					'customerid' => $result['customerid']
				);
				if (\Froxlor\CurrentUser::getField('customers_see_all') == '0') {
					$params['adminid'] = \Froxlor\CurrentUser::getField('adminid');
				}
				Database::pexecute($result_customers_stmt, $params);

				while ($row_customer = $result_customers_stmt->fetch(\PDO::FETCH_ASSOC)) {
					$customers .= \Froxlor\UI\HTML::makeoption(\Froxlor\User::getCorrectFullUserDetails($row_customer) . ' (' . $row_customer['loginname'] . ')', $row_customer['customerid'], $result['customerid']);
				}
			} else {
				$customer_stmt = Database::prepare("
						SELECT `customerid`, `loginname`, `name`, `firstname`, `company` FROM `" . TABLE_PANEL_CUSTOMERS . "`
						WHERE `customerid` = :customerid
					");
				$customer = Database::pexecute_first($customer_stmt, array(
					'customerid' => $result['customerid']
				));
				$result['customername'] = \Froxlor\User::getCorrectFullUserDetails($customer) . ' (' . $customer['loginname'] . ')';
			}
			$domain_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/domains/formfield.domains_edit.php';
		} else {
			$domain_data = include_once \Froxlor\Froxlor::getInstallDir() . '/lib/formfields/admin/domains/formfield.domains_add.php';
		}
		$domain_form = \Froxlor\UI\HtmlForm::genHTMLForm($domain_data);

		return $domain_form;
	}
}
/*
	elseif ($action == 'delete' && $id != 0) {

		try {
			$json_result = Domains::getLocal($userinfo, array(
				'id' => $id,
				'no_std_subdomain' => true
			))->get();
		} catch (Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$alias_check_stmt = Database::prepare("
			SELECT COUNT(`id`) AS `count` FROM `" . TABLE_PANEL_DOMAINS . "`
			WHERE `aliasdomain`= :id");
		$alias_check = Database::pexecute_first($alias_check_stmt, array(
			'id' => $id
		));

		if ($result['domain'] != '') {
			if (isset($_POST['send']) && $_POST['send'] == 'send' && $alias_check['count'] == 0) {

				try {
					Domains::getLocal($userinfo, $_POST)->delete();
				} catch (Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}

				\Froxlor\UI\Response::redirectTo($filename, array(
					'page' => $page,
					's' => $s
				));
			} elseif ($alias_check['count'] > 0) {
				\Froxlor\UI\Response::standard_error('domains_cantdeletedomainwithaliases');
			} else {

				$showcheck = false;
				if (\Froxlor\Domain\Domain::domainHasMainSubDomains($id)) {
					$showcheck = true;
				}
				\Froxlor\UI\HTML::askYesNoWithCheckbox('admin_domain_reallydelete', 'remove_subbutmain_domains', $filename, array(
					'id' => $id,
					'page' => $page,
					'action' => $action
				), $idna_convert->decode($result['domain']), $showcheck);
			}
		}


	} elseif ($action == 'jqGetCustomerPHPConfigs') {

		$customerid = intval($_POST['customerid']);
		$allowed_phpconfigs = \Froxlor\Customer\Customer::getCustomerDetail($customerid, 'allowed_phpconfigs');
		echo ! empty($allowed_phpconfigs) ? $allowed_phpconfigs : json_encode(array());
		exit();
	} elseif ($action == 'import') {

		if (isset($_POST['send']) && $_POST['send'] == 'send') {

			$customerid = intval($_POST['customerid']);
			$separator = \Froxlor\Validate\Validate::validate($_POST['separator'], 'separator');
			$offset = (int) \Froxlor\Validate\Validate::validate($_POST['offset'], 'offset', "/[0-9]/i");

			$file_name = $_FILES['file']['tmp_name'];

			$result = array();

			try {
				$bulk = new \Froxlor\Bulk\DomainBulkAction($file_name, $customerid);
				$result = $bulk->doImport($separator, $offset);
			} catch (Exception $e) {
				\Froxlor\UI\Response::standard_error('domain_import_error', $e->getMessage());
			}

			if (! empty($bulk->getErrors())) {
				\Froxlor\UI\Response::dynamic_error(implode("<br>", $bulk->getErrors()));
			}

			// update customer/admin counters
			\Froxlor\User::updateCounters(false);
			\Froxlor\System\Cronjob::inserttask('1');
			\Froxlor\System\Cronjob::inserttask('4');

			$result_str = $result['imported'] . ' / ' . $result['all'] . (! empty($result['note']) ? ' (' . $result['note'] . ')' : '');
			\Froxlor\UI\Response::standard_success('domain_import_successfully', $result_str, array(
				'filename' => $filename,
				'action' => '',
				'page' => 'domains'
			));
		} else {
			$customers = \Froxlor\UI\HTML::makeoption($this->lng['panel']['please_choose'], 0, 0, true);
			$result_customers_stmt = Database::prepare("
				SELECT `customerid`, `loginname`, `name`, `firstname`, `company`
				FROM `" . TABLE_PANEL_CUSTOMERS . "` " . ($userinfo['customers_see_all'] ? '' : " WHERE `adminid` = '" . (int) $userinfo['adminid'] . "' ") . " ORDER BY `name` ASC");
			$params = array();
			if ($userinfo['customers_see_all'] == '0') {
				$params['adminid'] = $userinfo['adminid'];
			}
			Database::pexecute($result_customers_stmt, $params);

			while ($row_customer = $result_customers_stmt->fetch(PDO::FETCH_ASSOC)) {
				$customers .= \Froxlor\UI\HTML::makeoption(\Froxlor\User::getCorrectFullUserDetails($row_customer) . ' (' . $row_customer['loginname'] . ')', $row_customer['customerid']);
			}

			$domain_import_data = include_once dirname(__FILE__) . '/lib/formfields/admin/domains/formfield.domains_import.php';
			$domain_import_form = \Froxlor\UI\HtmlForm::genHTMLForm($domain_import_data);

			$title = $domain_import_data['domain_import']['title'];
			$image = $domain_import_data['domain_import']['image'];

			eval("echo \"" . \Froxlor\UI\Template::getTemplate("domains/domains_import") . "\";");
		}
	}
} elseif ($page == 'domaindnseditor' && Settings::Get('system.dnsenabled') == '1') {

	require_once __DIR__ . '/dns_editor.php';
} elseif ($page == 'sslcertificates') {

	require_once __DIR__ . '/ssl_certificates.php';
} elseif ($page == 'logfiles') {

	require_once __DIR__ . '/logfiles_viewer.php';
}

function formatDomainEntry(&$row, &$idna_convert)
{
	$row['domain'] = $idna_convert->decode($row['domain']);
	$row['aliasdomain'] = $idna_convert->decode($row['aliasdomain']);

	$resultips_stmt = Database::prepare("
		SELECT `ips`.* FROM `" . TABLE_DOMAINTOIP . "` AS `dti`, `" . TABLE_PANEL_IPSANDPORTS . "` AS `ips`
		WHERE `dti`.`id_ipandports` = `ips`.`id` AND `dti`.`id_domain` = :domainid
	");

	Database::pexecute($resultips_stmt, array(
		'domainid' => $row['id']
	));

	$row['ipandport'] = '';
	while ($rowip = $resultips_stmt->fetch(PDO::FETCH_ASSOC)) {

		if (filter_var($rowip['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$row['ipandport'] .= '[' . $rowip['ip'] . ']:' . $rowip['port'] . "\n";
		} else {
			$row['ipandport'] .= $rowip['ip'] . ':' . $rowip['port'] . "\n";
		}
	}
	$row['ipandport'] = substr($row['ipandport'], 0, - 1);
	$row['termination_date'] = str_replace("0000-00-00", "", $row['termination_date']);

	$row['termination_css'] = "";
	if ($row['termination_date'] != "") {
		$cdate = strtotime($row['termination_date'] . " 23:59:59");
		$today = time();

		if ($cdate < $today) {
			$row['termination_css'] = 'domain-expired';
		} else {
			$row['termination_css'] = 'domain-canceled';
		}
	}
}
*/