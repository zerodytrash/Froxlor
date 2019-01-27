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
use Froxlor\Database\Database;
use Froxlor\Frontend\FeModule;
use Froxlor\Api\Commands\DomainZones as DomainZones;

class DnsEditor extends FeModule
{

	public function overview()
	{
		$domain_id = isset($_GET['domain_id']) ? (int) $_GET['domain_id'] : null;
		// get domain-name
		$domain = \Froxlor\Dns\Dns::getAllowedDomainEntry($domain_id);
		// select all entries
		$sel_stmt = Database::prepare("SELECT * FROM `" . TABLE_DOMAIN_DNS . "` WHERE domain_id = :did");
		Database::pexecute($sel_stmt, array(
			'did' => $domain_id
		));
		$dom_entries = $sel_stmt->fetchAll(\PDO::FETCH_ASSOC);

		// show editor
		$existing_entries = array();
		$type_select = "";

		if (! empty($dom_entries)) {
			foreach ($dom_entries as $entry) {
				$entry['content'] = wordwrap($entry['content'], 100, '<br>', true);
				$existing_entries[] = $entry;
			}
		}

		// available types
		$type_select_values = array(
			'A',
			'AAAA',
			'NS',
			'MX',
			'SRV',
			'TXT',
			'CNAME'
		);
		asort($type_select_values);
		$type_select = "";
		foreach ($type_select_values as $_type) {
			$type_select .= \Froxlor\UI\HTML::makeoption($_type, $_type, 'A');
		}

		try {
			$json_result = DomainZones::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $domain_id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];
		$zonefile = implode("\n", $result);

		\Froxlor\Frontend\UI::TwigBuffer('dns_editor/index.html.twig', array(
			'page_title' => "DNS Editor",
			'zone_entries' => $existing_entries,
			'domain' => $domain,
			'domain_id' => $domain_id,
			'type_select' => $type_select,
			'zonecontent' => $zonefile
		));
	}

	public function addEntry()
	{
		if (! empty($_POST)) {
			$domain_id = isset($_GET['domain_id']) ? (int) $_GET['domain_id'] : null;
			$record = isset($_POST['record']['record']) ? trim($_POST['record']['record']) : null;
			$type = isset($_POST['record']['type']) ? $_POST['record']['type'] : 'A';
			$prio = isset($_POST['record']['prio']) ? (int) $_POST['record']['prio'] : null;
			$content = isset($_POST['record']['content']) ? trim($_POST['record']['content']) : null;
			$ttl = isset($_POST['record']['ttl']) ? (int) $_POST['record']['ttl'] : Settings::Get('system.defaultttl');

			try {
				DomainZones::getLocal(\Froxlor\CurrentUser::getData(), array(
					'id' => $domain_id,
					'record' => $record,
					'type' => $type,
					'prio' => $prio,
					'content' => $content,
					'ttl' => $ttl
				))->add();
				\Froxlor\UI\Response::standard_success('dns_record_added', '', array(
					'filename' => "index.php?module=DnsEditor&domain_id=" . $domain_id
				));
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
		}
	}

	public function deleteEntry()
	{
		// remove entry
		$domain_id = isset($_GET['domain_id']) ? (int) $_GET['domain_id'] : null;
		$entry_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		if ($entry_id > 0) {
			try {
				DomainZones::getLocal(\Froxlor\CurrentUser::getData(), array(
					'entry_id' => $entry_id,
					'id' => $domain_id
				))->delete();
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
			\Froxlor\UI\Response::standard_success('dns_record_deleted', '', array(
				'filename' => "index.php?module=DnsEditor&domain_id=" . $domain_id
			));
		}
	}
}
