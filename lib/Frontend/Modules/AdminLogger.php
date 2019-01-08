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
use Froxlor\Database\Database;
use Froxlor\Frontend\FeModule;

class AdminLogger extends FeModule
{

	public function overview()
	{
		if (\Froxlor\CurrentUser::getField('change_serversettings') != '1') {
			// not allowed
			\Froxlor\UI\Response::standard_error('noaccess', __METHOD__);
		}

		$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;
		$result_stmt = Database::query('SELECT * FROM `' . TABLE_PANEL_LOG . '` ORDER BY `date` DESC LIMIT ' . ($page * Settings::Get('panel.paging')) . ', ' . Settings::Get('panel.paging'));
		$all_entries_stmt = Database::query('SELECT COUNT(*) as entries FROM `' . TABLE_PANEL_LOG . '`');
		$all_entries = $all_entries_stmt->fetch(\PDO::FETCH_ASSOC);

		$log_entries = array();
		while ($row = $result_stmt->fetch(\PDO::FETCH_ASSOC)) {
			$row['action'] = \Froxlor\FroxlorLogger::getActionTypeDesc($row['action']);
			$row['type'] = \Froxlor\FroxlorLogger::getLogLevelDesc($row['type']);
			$log_entries[] = $row;
		}

		\Froxlor\Frontend\UI::TwigBuffer('admin/logger/index.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('menue.logger.logger'),
			'logentries' => $log_entries,
			'page' => $page,
			'all_entries' => $all_entries['entries']
		));
	}

	public function truncate()
	{
		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			$truncatedate = time() - (60 * 10);
			$trunc_stmt = Database::prepare("
				DELETE FROM `" . TABLE_PANEL_LOG . "` WHERE `date` < :trunc
			");
			Database::pexecute($trunc_stmt, array(
				'trunc' => $truncatedate
			));
			\Froxlor\FroxlorLogger::getLog()->addWarning('truncated the system-log (mysql)');
			\Froxlor\UI\Response::redirectTo('index.php?module=AdminLogger');
		} else {
			\Froxlor\UI\HTML::askYesNo('logger_reallytruncate', 'index.php?module=AdminLogger&view=' . __FUNCTION__, array(), TABLE_PANEL_LOG);
		}
	}
}
