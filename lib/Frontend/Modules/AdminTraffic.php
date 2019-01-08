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
 * @author Froxlor team <team@froxlor.org> (2009-)
 * @license GPLv2 http://files.syscp.org/misc/COPYING.txt
 * @package Panel
 *         
 */
use Froxlor\Api\Commands\Traffic;
use Froxlor\Frontend\FeModule;

class AdminTraffic extends FeModule
{

	public function overview()
	{
		// get traffic for admin user (current year and month)
		try {
			$json_result = Traffic::getLocal(\Froxlor\CurrentUser::getData(), array(
				'year' => date('Y'),
				'month' => date('n'),
				'date_from' => true
			))->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		var_dump($result);
	}
}
