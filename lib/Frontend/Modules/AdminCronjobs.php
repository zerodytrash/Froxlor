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
use Froxlor\Api\Commands\Cronjobs as Cronjobs;
use Froxlor\Frontend\FeModule;

class AdminCronjobs extends FeModule
{

	public function overview()
	{
		try {
			$json_result = Cronjobs::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$cronjobs = $result['list'];
		foreach ($cronjobs as $index => $cronjob) {
			$cronjob['description'] = \Froxlor\Frontend\UI::getLng('crondesc.' . $cronjob['desc_lng_key']);
			$result['list'][$index] = $cronjob;
		}
		\Froxlor\PhpHelper::sortListBy($result['list'], 'module');

		\Froxlor\Frontend\UI::TwigBuffer('admin/cronjobs/index.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('admin.cron.cronsettings'),
			'cronjobs' => $result
		));
	}

	public function edit()
	{
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		try {
			$json_result = Cronjobs::getLocal(\Froxlor\CurrentUser::getData(), array(
				'id' => $id
			))->get();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];
		if ($result['cronfile'] != '') {
			if (isset($_POST['send']) && $_POST['send'] == 'send') {
				try {
					Cronjobs::getLocal($userinfo, $_POST)->update();
				} catch (\Exception $e) {
					\Froxlor\UI\Response::dynamic_error($e->getMessage());
				}
				\Froxlor\UI\Response::redirectTo($filename, array(
					'page' => $page,
					's' => $s
				));
			} else {

				// interval
				$interval_nfo = explode(' ', $result['interval']);
				$interval_value = $interval_nfo[0];

				$interval_interval = '';
				$interval_interval .= \Froxlor\UI\HTML::makeoption($lng['cronmgmt']['minutes'], 'MINUTE', $interval_nfo[1]);
				$interval_interval .= \Froxlor\UI\HTML::makeoption($lng['cronmgmt']['hours'], 'HOUR', $interval_nfo[1]);
				$interval_interval .= \Froxlor\UI\HTML::makeoption($lng['cronmgmt']['days'], 'DAY', $interval_nfo[1]);
				$interval_interval .= \Froxlor\UI\HTML::makeoption($lng['cronmgmt']['weeks'], 'WEEK', $interval_nfo[1]);
				$interval_interval .= \Froxlor\UI\HTML::makeoption($lng['cronmgmt']['months'], 'MONTH', $interval_nfo[1]);
				// end of interval

				$change_cronfile = false;
				if (substr($result['module'], 0, strpos($result['module'], '/')) != 'froxlor') {
					$change_cronfile = true;
				}

				$cronjobs_edit_data = include_once dirname(__FILE__) . '/lib/formfields/admin/cronjobs/formfield.cronjobs_edit.php';
				$cronjobs_edit_form = \Froxlor\UI\HtmlForm::genHTMLForm($cronjobs_edit_data);

				$title = $cronjobs_edit_data['cronjobs_edit']['title'];
				$image = $cronjobs_edit_data['cronjobs_edit']['image'];

				eval("echo \"" . \Froxlor\UI\Template::getTemplate('cronjobs/cronjob_edit') . "\";");
			}
		}
	}
}
