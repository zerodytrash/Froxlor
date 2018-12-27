<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Language
 *
 */
use Froxlor\Database\Database;
use Froxlor\Settings;
use Froxlor\Install\PreConfig;
use PHPMailer\PHPMailer;

if (PreConfig::versionInUpdate($current_db_version, '201901010')) {
	$item = array(
		'description' => 'OMG so many changes...please note:',
		'question' => array(
			0 => array(
				'title' => 'Activate API interface?',
				'form' => \Froxlor\UI\HTML::makeyesno('system_activate_api', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}
