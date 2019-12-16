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
 * @package    Formfields
 *
 */
return array(
	'domain_edit' => array(
		'title' => \Froxlor\Frontend\UI::getLng('domains.subdomain_edit'),
		'image' => 'icons/domain_edit.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('domains.subdomain_edit'),
				'image' => 'icons/domain_edit.png',
				'fields' => array(
					'domain' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.domainname'),
						'type' => 'label',
						'value' => $result['domain']
					),
					'dns' => array(
						'label' => \Froxlor\Frontend\UI::getLng('dns.destinationip'),
						'type' => 'label',
						'value' => $domainip
					),
					'alias' => array(
						'visible' => ($alias_check == '0' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('domains.aliasdomain'),
						'type' => 'select',
						'select_var' => $domains
					),
					'path' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.path'),
						'desc' => (\Froxlor\Settings::Get('panel.pathedit') != 'Dropdown' ? \Froxlor\Frontend\UI::getLng('panel.pathDescriptionSubdomain') : null) . (isset($pathSelect['note']) ? '<br />' . $pathSelect['value'] : ''),
						'type' => $pathSelect['type'],
						'select_var' => $pathSelect['value'],
						'value' => $pathSelect['value']
					),
					'url' => array(
						'visible' => (\Froxlor\Settings::Get('panel.pathedit') == 'Dropdown' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('panel.urloverridespath'),
						'type' => 'text',
						'value' => $urlvalue
					),
					'redirectcode' => array(
						'visible' => (\Froxlor\Settings::Get('customredirect.enabled') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('domains.redirectifpathisurl'),
						'desc' => \Froxlor\Frontend\UI::getLng('domains.redirectifpathisurlinfo'),
						'type' => 'select',
						'select_var' => $redirectcode
					),
					'selectserveralias' => array(
						'visible' => ((($result['parentdomainid'] == '0' && $userinfo['subdomains'] != '0') || $result['parentdomainid'] != '0') ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.selectserveralias'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.selectserveralias_desc'),
						'type' => 'select',
						'select_var' => $serveraliasoptions
					),
					'isemaildomain' => array(
						'visible' => ((($result['subcanemaildomain'] == '1' || $result['subcanemaildomain'] == '2') && $result['parentdomainid'] != '0') ? true : false),
						'label' => 'Emaildomain',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['isemaildomain']
						)
					),
					'openbasedir_path' => array(
						'visible' => ($result['openbasedir'] == '1') ? true : false,
						'label' => \Froxlor\Frontend\UI::getLng('domain.openbasedirpath'),
						'type' => 'select',
						'select_var' => $openbasedir
					),
					'phpsettingid' => array(
						'visible' => (((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 || (int) \Froxlor\Settings::Get('phpfpm.enabled') == 1) && $has_phpconfigs ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.title'),
						'type' => 'select',
						'select_var' => $phpconfigs
					)
				)
			),
			'section_bssl' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.webserversettings_ssl'),
				'image' => 'icons/domain_edit.png',
				'visible' => \Froxlor\Settings::Get('system.use_ssl') == '1' ? ($ssl_ipsandports != '' ? (\Froxlor\Domain\Domain::domainHasSslIpPort($result['id']) ? true : false) : false) : false,
				'fields' => array(
					'sslenabled' => array(
						'label' => $lng['admin']['domain_sslenabled'],
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => $lng['panel']['yes'],
								'value' => '1'
							)
						),
						'value' => array(
							$result['ssl_enabled']
						)
					),
					'ssl_redirect' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.ssl_redirect.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('domains.ssl_redirect.description') . ($result['temporary_ssl_redirect'] > 1 ? \Froxlor\Frontend\UI::getLng('domains.ssl_redirect_temporarilydisabled') : ''),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['ssl_redirect']
						)
					),
					'letsencrypt' => array(
						'visible' => \Froxlor\Settings::Get('system.leenabled') == '1' ? true : false,
						'label' => \Froxlor\Frontend\UI::getLng('customer.letsencrypt.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('customer.letsencrypt.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['letsencrypt']
						)
					),
					'http2' => array(
						'visible' => ($ssl_ipsandports != '' ? true : false) && \Froxlor\Settings::Get('system.webserver') != 'lighttpd' && \Froxlor\Settings::Get('system.http2_support') == '1',
						'label' => $lng['admin']['domain_http2']['title'],
						'desc' => $lng['admin']['domain_http2']['description'],
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => $lng['panel']['yes'],
								'value' => '1'
							)
						),
						'value' => array(
							$result['http2']
						)
					),
					'hsts_maxage' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_maxage.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_maxage.description'),
						'type' => 'int',
						'min' => 0,
						'max' => 94608000, // 3-years
						'value' => $result['hsts']
					),
					'hsts_sub' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_incsub.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_incsub.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['hsts_sub']
						)
					),
					'hsts_preload' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_preload.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_preload.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['hsts_preload']
						)
					)
				)
			)
		)
	)
);
