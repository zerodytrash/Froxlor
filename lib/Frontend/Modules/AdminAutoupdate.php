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
 * @author Michael Kaufmann <mkaufmann@nutime.de>
 * @author Froxlor team <team@froxlor.org> (2010-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Frontend
 *         
 * @since 0.9.35
 *       
 */
use Froxlor\Api\Commands\Froxlor;
use Froxlor\Frontend\FeModule;
use Froxlor\Http\HttpClient;

class AdminAutoupdate extends FeModule
{

	const RELEASE_URI = "https://autoupdate.froxlor.org/froxlor-{version}.zip";

	const CHECKSUM_URI = "https://autoupdate.froxlor.org/froxlor-{version}.zip.sha256";

	public function overview()
	{

		// check for archive-stuff
		if (! extension_loaded('zip')) {
			\Froxlor\UI\Response::standard_error('autoupdate_2');
		}

		\Froxlor\FroxlorLogger::getLog()->addNotice("checking auto-update");

		// check for new version
		try {
			$json_result = Froxlor::getLocal(\Froxlor\CurrentUser::getData())->checkUpdate();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$version_check_result = json_decode($json_result, true)['data'];

		// anzeige über version-status mit ggfls. formular
		// zum update schritt #1 -> download
		if ($version_check_result['isnewerversion'] == 1) {
			$hiddenparams = '<input type="hidden" name="newversion" value="' . $version_check_result['version'] . '" />';
			$yesfile = 'index.php?module=AdminAutoupdate&amp;page=getdownload';
			\Froxlor\Frontend\UI::TwigBuffer('misc/yesno.html.twig', array(
				'page_title' => $this->lng['question']['question'],
				'yesno_msg' => $version_check_result['message'] . '<br><strong>' . $this->lng['question']['update_now'] . '</strong>',
				'hiddenparams' => $hiddenparams,
				'yesfile' => $yesfile
			));
		} elseif ($version_check_result['isnewerversion'] == 0) {
			// all good
			\Froxlor\UI\Response::standard_success('noupdatesavail');
		} else {
			\Froxlor\UI\Response::standard_error('customized_version');
		}
	}

	public function getdownload()
	{
		// retrieve the new version from the form
		$newversion = isset($_POST['newversion']) ? $_POST['newversion'] : null;

		// valid?
		if ($newversion !== null) {

			// define files to get
			$toLoad = str_replace('{version}', $newversion, RELEASE_URI);
			$toCheck = str_replace('{version}', $newversion, CHECKSUM_URI);

			// check for local destination folder
			if (! is_dir(\Froxlor\Froxlor::getInstallDir() . '/updates/')) {
				mkdir(\Froxlor\Froxlor::getInstallDir() . '/updates/');
			}

			// name archive
			$localArchive = \Froxlor\Froxlor::getInstallDir() . '/updates/' . basename($toLoad);

			\Froxlor\FroxlorLogger::getLog()->addNotice("Downloading " . $toLoad . " to " . $localArchive);

			// remove old archive
			if (file_exists($localArchive)) {
				@unlink($localArchive);
			}

			// get archive data
			try {
				HttpClient::fileGet($toLoad, $localArchive);
			} catch (\Exception $e) {
				\Froxlor\UI\Response::standard_error('autoupdate_4');
			}

			// validate the integrity of the downloaded file
			$_shouldsum = HttpClient::urlGet($toCheck);
			if (! empty($_shouldsum)) {
				$_t = explode(" ", $_shouldsum);
				$shouldsum = $_t[0];
			} else {
				$shouldsum = null;
			}
			$filesum = hash_file('sha256', $localArchive);

			if ($filesum != $shouldsum) {
				\Froxlor\UI\Response::standard_error('autoupdate_9');
			}

			// to the next step
			\Froxlor\UI\Response::redirectTo($filename, array(
				's' => $s,
				'page' => 'extract',
				'archive' => basename($localArchive)
			));
		}
		\Froxlor\UI\Response::standard_error('autoupdate_6');
	}

	/**
	 * extract and install new version
	 */
	public function extract()
	{
		$toExtract = isset($_GET['archive']) ? $_GET['archive'] : null;
		$localArchive = \Froxlor\Froxlor::getInstallDir() . '/updates/' . $toExtract;

		if (isset($_POST['send']) && $_POST['send'] == 'send') {
			// decompress from zip
			$zip = new \ZipArchive();
			$res = $zip->open($localArchive);
			if ($res === true) {
				\Froxlor\FroxlorLogger::getLog()->addNotice("Extracting " . $localArchive . " to " . \Froxlor\Froxlor::getInstallDir());
				$zip->extractTo(\Froxlor\Froxlor::getInstallDir());
				$zip->close();
				// success - remove unused archive
				@unlink($localArchive);
			} else {
				// error
				\Froxlor\UI\Response::standard_error('autoupdate_8');
			}

			// redirect to update-page?
			\Froxlor\UI\Response::redirectTo('admin_updates.php', array(
				's' => $s
			));
		}

		if (! file_exists($localArchive)) {
			\Froxlor\UI\Response::standard_error('autoupdate_7');
		}

		$text = 'Extract downloaded archive "' . $toExtract . '"?';
		$hiddenparams = '';
		$yesfile = $filename . '?s=' . $s . '&amp;page=extract&amp;archive=' . $toExtract;
		eval("echo \"" . \Froxlor\UI\Template::getTemplate("misc/question_yesno", true) . "\";");
	}
}
/*
// display error
elseif ($page == 'error') {

	// retrieve error-number via url-parameter
	$errno = isset($_GET['errno']) ? (int) $_GET['errno'] : 0;

	// 3 = custom version detected
	// 4 = could not store archive to local hdd
	// 5 = some weird value came from version.froxlor.org
	// 6 = download without valid version
	// 7 = local archive does not exist
	// 8 = could not extract archive
	// 9 = checksum mismatch
	\Froxlor\UI\Response::standard_error('autoupdate_' . $errno);
}
*/