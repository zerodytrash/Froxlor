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
 * @package    Updater
 *
 */
use Froxlor\Database\Database;
use Froxlor\Settings;
use Froxlor\Install\PreConfig;
use PHPMailer\PHPMailer;

if (PreConfig::versionInUpdate($current_db_version, '202004140')) {
	$item = [
		'description' => 'Froxlor can now optionally validate the dns entries of domains that request Lets Encrypt certificates to reduce dns-related problems (e.g. freshly registered domain or updated a-record).',
		'question' => [
			'title' => 'Validate DNS of domains when using Lets Encrypt',
			'form' => \Froxlor\UI\HTML::makeyesno('system_le_domain_dnscheck', '1', '0', '1')
		]
	];
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201901110')) {
	$item = array(
		'description' => '<div class="alert alert-warning" role="alert">Due to the new directory-layout and for security reasons froxlor\'s vhost should now point to ' . \Froxlor\Froxlor::getInstallDir() . 'app/.<br>If you let froxlor create its vhost you do not have to do anything. As the "Access Froxlor directly via the hostname" setting is enforced now, http://' . Settings::Get('system.hostname') . '/' . basename(\Froxlor\Froxlor::getInstallDir()) . '/ will <strong>NOT</strong> work anymore.</div>',
		'question' => array()
	);
	array_push($preconfig_items, $item);
}

