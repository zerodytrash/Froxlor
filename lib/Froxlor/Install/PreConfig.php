<?php
namespace Froxlor\Install;

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
 * @package Language
 *         
 */
class PreConfig
{

	/**
	 * Function getPreConfig
	 *
	 * outputs various content before the update process
	 * can be continued (askes for agreement whatever is being asked)
	 *
	 * @param string $current_version
	 * @param int $current_db_version
	 *
	 * @return string
	 */
	public static function getPreConfig($current_version, $current_db_version)
	{

		$preconfig_items = array();
		include_once \Froxlor\FileDir::makeCorrectFile(\Froxlor\Froxlor::getInstallDir() . '/install/updates/preconfig/0.9/preconfig_0.9.inc.php');
		include_once \Froxlor\FileDir::makeCorrectFile(\Froxlor\Froxlor::getInstallDir() . '/install/updates/preconfig/0.10/preconfig_0.10.inc.php');

		if (count($preconfig_items)) {
			return $preconfig_items;
		} else {
			return '';
		}
	}

	public static function versionInUpdate($current_version, $version_to_check)
	{
		if (! \Froxlor\Froxlor::isFroxlor()) {
			return true;
		}

		return (\Froxlor\Froxlor::versionCompare2($current_version, $version_to_check) == - 1 ? true : false);
	}
}
