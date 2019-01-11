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

if (PreConfig::versionInUpdate($current_db_version, '201812190')) {
	$item = array(
		'description' => '<div class="alert alert-warning" role="alert">The ticketsystem has been removed completely. Please backup any contents from the froxlor database if you need them.</div>',
		'question' => array()
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201901110')) {
	$item = array(
		'description' => '<div class="alert alert-warning" role="alert">Due to the new directory-layout and for security reasons froxlor\'s vhost should now point to ' . \Froxlor\Froxlor::getInstallDir() . 'app/.<br>If you let froxlor create its vhost you do not have to do anything. As the "Access Froxlor directly via the hostname" setting is enforced now, http://' . Settings::Get('system.hostname') . '/' . basename(\Froxlor\Froxlor::getInstallDir()) . '/ will <strong>NOT</strong> work anymore.</div>',
		'question' => array()
	);
	array_push($preconfig_items, $item);
}
