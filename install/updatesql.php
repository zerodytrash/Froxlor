<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Florian Lippert <flo@syscp.org> (2003-2009)
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Install
 *
 */
use Froxlor\FroxlorLogger;
use Froxlor\Install\Updates;

if (! defined('_CRON_UPDATE')) {
	if (empty(\Froxlor\CurrentUser::getField('loginname')) || (! empty(\Froxlor\CurrentUser::getField('loginname')) && \Froxlor\CurrentUser::getField('adminsession') != '1')) {
		header('Location: ../index.php');
		exit();
	}
}

if (\Froxlor\Froxlor::isFroxlor()) {
	include_once (\Froxlor\FileDir::makeCorrectFile(dirname(__FILE__) . '/updates/froxlor/0.9/update_0.9.inc.php'));
	include_once (\Froxlor\FileDir::makeCorrectFile(dirname(__FILE__) . '/updates/froxlor/0.10/update_0.10.inc.php'));

	// Check Froxlor - database integrity (only happens after all updates are done, so we know the db-layout is okay)
	Updates::showUpdateStep("Checking database integrity");

	$integrity = new \Froxlor\Database\IntegrityCheck();
	if (! $integrity->checkAll()) {
		Updates::lastStepStatus(1, 'Monkeys ate the integrity');
		Updates::showUpdateStep("Trying to remove monkeys, feeding bananas");
		if (! $integrity->fixAll()) {
			Updates::lastStepStatus(2, 'Some monkeys just would not move, you should contact team@froxlor.org');
		} else {
			Updates::lastStepStatus(0, 'Integrity restored');
		}
	} else {
		Updates::lastStepStatus(0);
	}
}
