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
use Froxlor\Frontend\FeModule;
use Froxlor\Settings;
use Froxlor\Api\Commands\Certificates as Certificates;

class SslCertificates extends FeModule
{

	public function overview()
	{
		try {
			$json_result = Certificates::getLocal(\Froxlor\CurrentUser::getData())->listing();
		} catch (\Exception $e) {
			\Froxlor\UI\Response::dynamic_error($e->getMessage());
		}
		$result = json_decode($json_result, true)['data'];

		$certificates = $result['list'];
		foreach ($certificates as $index => $cert) {
			// respect froxlor-hostname
			if ($cert['domainid'] == 0) {
				$cert['domain'] = Settings::Get('system.hostname');
				$cert['letsencrypt'] = Settings::Get('system.le_froxlor_enabled');
				$cert['loginname'] = 'froxlor.panel';
			}
			// read certificate data
			$cert['data'] = openssl_x509_parse($cert['ssl_cert_file']);
			// idna convert domain
			$idna_convert = new \Froxlor\Idna\IdnaWrapper();
			$cert['domain'] = $idna_convert->decode($cert['domain']);
			// validate certificate-data
			if ($cert['data']) {
				$cert['isValid'] = true;
				if ($cert['data']['validTo_time_t'] < time()) {
					$cert['isValid'] = false;
				}

				$cert['sanlist'] = array();
				if (isset($cert['data']['extensions']['subjectAltName']) && ! empty($cert['data']['extensions']['subjectAltName'])) {
					$SANs = explode(",", $cert['data']['extensions']['subjectAltName']);
					$SANs = array_map('trim', $SANs);
					foreach ($SANs as $san) {
						$san = str_replace("DNS:", "", $san);
						if ($san != $cert['data']['subject']['CN'] && strpos($san, "othername:") === false) {
							$cert['sanlist'][] = $san;
						}
					}
				}
			} else {
				$cert['cert_error'] = sprintf($this->lng['domains']['ssl_certificate_error'], $cert['domain']);
			}
			// add back to result-list
			$result['list'][$index] = $cert;
		}

		\Froxlor\PhpHelper::sortListBy($result['list'], 'domain');

		\Froxlor\Frontend\UI::TwigBuffer('ssl_certificates/index.html.twig', array(
			'page_title' => $this->lng['domains']['ssl_certificates'],
			'certificates' => $result
		));
	}

	public function delete()
	{
		// do the delete and then just show a success-message and the certificates list again
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		if ($id > 0) {
			try {
				Certificates::getLocal(\Froxlor\CurrentUser::getData(), array(
					'id' => $id
				))->delete();
				$success_message = sprintf($this->lng['domains']['ssl_certificate_removed'], $id);
				\Froxlor\UI\Response::dynamic_success($success_message, $this->lng['success']['success'], array(
					'filename' => 'index.php?module=SslCertificates'
				));
			} catch (\Exception $e) {
				\Froxlor\UI\Response::dynamic_error($e->getMessage());
			}
		}
	}
}
