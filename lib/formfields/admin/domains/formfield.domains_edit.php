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
		'title' => \Froxlor\Frontend\UI::getLng('admin.domain_edit'),
		'image' => 'icons/domain_edit.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('domains.domainsettings'),
				'image' => 'icons/domain_edit.png',
				'fields' => array(
					'domain' => array(
						'label' => 'Domain',
						'type' => 'label',
						'value' => $result['domain'],
						'mandatory' => true
					),
					'customerid' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.customer'),
						'type' => (\Froxlor\Settings::Get('panel.allow_domain_change_customer') == '1' ? 'select' : 'label'),
						'select_var' => (isset($customers) ? $customers : null),
						'value' => (isset($result['customername']) ? $result['customername'] : null),
						'mandatory' => true
					),
					'adminid' => array(
						'visible' => (\Froxlor\CurrentUser::getField('customers_see_all') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.admin'),
						'type' => (\Froxlor\Settings::Get('panel.allow_domain_change_admin') == '1' ? 'select' : 'label'),
						'select_var' => (isset($admins) ? $admins : null),
						'value' => (isset($result['adminname']) ? $result['adminname'] : null),
						'mandatory' => true
					),
					'alias' => array(
						'visible' => ($alias_check == '0' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('domains.aliasdomain'),
						'type' => 'select',
						'select_var' => $domains
					),
					'issubof' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.issubof'),
						'desc' => \Froxlor\Frontend\UI::getLng('domains.issubofinfo'),
						'type' => 'select',
						'select_var' => $subtodomains
					),
					'associated_info' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.associated_with_domain'),
						'type' => 'label',
						'value' => $subdomains . ' ' . \Froxlor\Frontend\UI::getLng('panel.subdomains') . ', ' . $alias_check . ' ' . \Froxlor\Frontend\UI::getLng('domains.aliasdomains') . ', ' . $emails . ' ' . \Froxlor\Frontend\UI::getLng('panel.emails') . ', ' . $email_accounts . ' ' . \Froxlor\Frontend\UI::getLng('panel.email_accounts') . ', ' . $email_forwarders . ' ' . \Froxlor\Frontend\UI::getLng('panel.email_forwarders')
					),
					'caneditdomain' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_editable.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_editable.desc'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['caneditdomain']
						)
					),
					'add_date' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.add_date'),
						'desc' => \Froxlor\Frontend\UI::getLng('panel.dateformat'),
						'type' => 'label',
						'value' => $result['add_date']
					),
					'registration_date' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.registration_date'),
						'desc' => \Froxlor\Frontend\UI::getLng('panel.dateformat'),
						'type' => 'text',
						'value' => $result['registration_date'],
						'size' => 10
					),
					'termination_date' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.termination_date'),
						'desc' => \Froxlor\Frontend\UI::getLng('panel.dateformat'),
						'type' => 'text',
						'value' => $result['termination_date'],
						'size' => 10
					)
				)
			),
			'section_b' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.webserversettings'),
				'image' => 'icons/domain_edit.png',
				'fields' => array(
					'documentroot' => array(
						'visible' => (\Froxlor\CurrentUser::getField('change_serversettings') == '1' ? true : false),
						'label' => 'DocumentRoot',
						'desc' => \Froxlor\Frontend\UI::getLng('panel.emptyfordefault'),
						'type' => 'text',
						'value' => $result['documentroot']
					),
					'ipandport' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.ipandport_multi.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('domains.ipandport_multi.description'),
						'type' => 'checkbox',
						'values' => $ipsandports,
						'value' => $usedips,
						'is_array' => 1,
						'mandatory' => true
					),
					'selectserveralias' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.selectserveralias'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.selectserveralias_desc'),
						'type' => 'select',
						'select_var' => $serveraliasoptions
					),
					'speciallogfile' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.speciallogfile.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.speciallogfile.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['speciallogfile']
						)
					),
					'specialsettings' => array(
						'visible' => (\Froxlor\CurrentUser::getField('change_serversettings') == '1' ? true : false),
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('admin.ownvhostsettings'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.default_vhostconf.description'),
						'type' => 'textarea',
						'value' => $result['specialsettings'],
						'cols' => 60,
						'rows' => 12
					),
					'specialsettingsforsubdomains' => array(
						'visible' => (\Froxlor\CurrentUser::getField('change_serversettings') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.specialsettingsforsubdomains'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.specialsettingsforsubdomains.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							\Froxlor\Settings::Get('system.apply_specialsettings_default') == 1 ? '1' : ''
						)
					),
					'notryfiles' => array(
						'visible' => (\Froxlor\Settings::Get('system.webserver') == 'nginx' && \Froxlor\CurrentUser::getField('change_serversettings') == '1'),
						'label' => \Froxlor\Frontend\UI::getLng('admin.notryfiles.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.notryfiles.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['notryfiles']
						)
					),
					'writeaccesslog' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.writeaccesslog.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.writeaccesslog.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['writeaccesslog']
						)
					),
					'writeerrorlog' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.writeerrorlog.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.writeerrorlog.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['writeerrorlog']
						)
					)
				)
			),
			'section_bssl' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.webserversettings_ssl'),
				'image' => 'icons/domain_edit.png',
				'visible' => \Froxlor\Settings::Get('system.use_ssl') == '1' ? true : false,
				'fields' => array(
					'no_ssl_available_info' => array(
						'visible' => ($ssl_ipsandports == '' ? true : false),
						'label' => 'SSL',
						'type' => 'label',
						'value' => \Froxlor\Frontend\UI::getLng('panel.nosslipsavailable')
					),
					'ssl_ipandport' => array(
						'label' => \Froxlor\Frontend\UI::getLng('domains.ipandport_ssl_multi.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('domains.ipandport_ssl_multi.description'),
						'type' => 'checkbox',
						'values' => $ssl_ipsandports,
						'value' => $usedips,
						'is_array' => 1
					),
					'ssl_redirect' => array(
						'visible' => ($ssl_ipsandports != '' ? true : false),
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
						'visible' => (\Froxlor\Settings::Get('system.leenabled') == '1' ? ($ssl_ipsandports != '' ? true : false) : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.letsencrypt.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.letsencrypt.description'),
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
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_http2.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_http2.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['http2']
						)
					),
					'override_tls' => array(
						'visible' => (($ssl_ipsandports != '' ? true : false) && \Froxlor\CurrentUser::getUser('change_serversettings') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_override_tls'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['override_tls']
						)
					),
					'ssl_protocols' => array(
						'visible' => (($ssl_ipsandports != '' ? true : false) && \Froxlor\CurrentUser::getUser('change_serversettings') == '1' && \Froxlor\Settings::Get('system.webserver') != 'lighttpd' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.ssl_protocols.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.ssl_protocols.description'),
						'type' => 'checkbox',
						'value' => !empty($result['ssl_protocols']) ? explode(",", $result['ssl_protocols']) : explode(",", \Froxlor\Settings::Get('system.ssl_protocols')),
						'values' => array(
							array(
								'value' => 'TLSv1',
								'label' => 'TLSv1<br />'
							),
							array(
								'value' => 'TLSv1.1',
								'label' => 'TLSv1.1<br />'
							),
							array(
								'value' => 'TLSv1.2',
								'label' => 'TLSv1.2<br />'
							),
							array(
								'value' => 'TLSv1.3',
								'label' => 'TLSv1.3<br />'
							)
						),
						'is_array' => 1
					),
					'ssl_cipher_list' => array(
						'visible' => (($ssl_ipsandports != '' ? true : false) && \Froxlor\CurrentUser::getUser('change_serversettings') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.ssl_cipher_list.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.ssl_cipher_list.description'),
						'type' => 'text',
						'value' => !empty($result['ssl_cipher_list']) ? $result['ssl_cipher_list'] : \Froxlor\Settings::Get('system.ssl_cipher_list')
					),
					'tlsv13_cipher_list' => array(
						'visible' => (($ssl_ipsandports != '' ? true : false) && \Froxlor\CurrentUser::getUser('change_serversettings') == '1' && \Froxlor\Settings::Get('system.webserver') == "apache2" && \Froxlor\Settings::Get('system.apache24') == 1 ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.tlsv13_cipher_list.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.ssl.tlsv13_cipher_list.description'),
						'type' => 'text',
						'value' => !empty($result['tlsv13_cipher_list']) ? $result['tlsv13_cipher_list'] : \Froxlor\Settings::Get('system.tlsv13_cipher_list')
					),
					'ssl_specialsettings' => array(
						'visible' => (\Froxlor\CurrentUser::getUser('change_serversettings') == '1' ? true : false),
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('admin.ownsslvhostsettings'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.default_vhostconf.description'),
						'type' => 'textarea',
						'cols' => 60,
						'rows' => 12,
						'value' => $result['ssl_specialsettings']
					),
					'include_specialsettings' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.include_ownvhostsettings'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['include_specialsettings']
						)
					),
					'hsts_maxage' => array(
						'visible' => ($ssl_ipsandports != '' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_maxage.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_hsts_maxage.description'),
						'type' => 'int',
						'min' => 0,
						'max' => 94608000, // 3-years
						'value' => $result['hsts']
					),
					'hsts_sub' => array(
						'visible' => ($ssl_ipsandports != '' ? true : false),
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
						'visible' => ($ssl_ipsandports != '' ? true : false),
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
					),
					'ocsp_stapling' => array(
						'visible' => ($ssl_ipsandports != '' ? true : false) && \Froxlor\Settings::Get('system.webserver') != 'lighttpd',
						'label' => \Froxlor\Frontend\UI::getLng('admin.domain_ocsp_stapling.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.domain_ocsp_stapling.description') . (\Froxlor\Settings::Get('system.webserver') == 'nginx' ? \Froxlor\Frontend\UI::getLng('admin.domain_ocsp_stapling.nginx_version_warning') : ""),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['ocsp_stapling']
						)
					)
				)
			),
			'section_c' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.phpserversettings'),
				'image' => 'icons/domain_edit.png',
				'visible' => ((\Froxlor\CurrentUser::getField('change_serversettings') == '1' || \Froxlor\CurrentUser::getField('caneditphpsettings') == '1') ? true : false),
				'fields' => array(
					'openbasedir' => array(
						'label' => 'OpenBasedir',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['openbasedir']
						)
					),
					'phpenabled' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpenabled'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['phpenabled']
						)
					),
					'phpsettingid' => array(
						'visible' => (((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 || (int) \Froxlor\Settings::Get('phpfpm.enabled') == 1) ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.title'),
						'type' => 'select',
						'select_var' => $phpconfigs
					),
					'phpsettingsforsubdomains' => array(
						'visible' => (\Froxlor\CurrentUser::getField('change_serversettings') == '1' ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettingsforsubdomains'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpsettingsforsubdomains.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							\Froxlor\Settings::Get('system.apply_phpconfigs_default') == 1 ? '1' : ''
						)
					),
					'mod_fcgid_starter' => array(
						'visible' => ((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.mod_fcgid_starter.title'),
						'type' => 'text',
						'value' => ((int) $result['mod_fcgid_starter'] != - 1 ? $result['mod_fcgid_starter'] : '')
					),
					'mod_fcgid_maxrequests' => array(
						'visible' => ((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.mod_fcgid_maxrequests.title'),
						'type' => 'text',
						'value' => ((int) $result['mod_fcgid_maxrequests'] != - 1 ? $result['mod_fcgid_maxrequests'] : '')
					)
				)
			),
			'section_d' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.nameserversettings'),
				'image' => 'icons/domain_edit.png',
				'visible' => (\Froxlor\Settings::Get('system.bind_enable') == '1' && \Froxlor\CurrentUser::getField('change_serversettings') == '1' ? true : false),
				'fields' => array(
					'isbinddomain' => array(
						'label' => 'Nameserver',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['isbinddomain']
						)
					),
					'zonefile' => array(
						'label' => 'Zonefile',
						'desc' => \Froxlor\Frontend\UI::getLng('admin.bindzonewarning'),
						'type' => 'text',
						'value' => $result['zonefile']
					)
				)
			),
			'section_e' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.mailserversettings'),
				'image' => 'icons/domain_edit.png',
				'fields' => array(
					'isemaildomain' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.emaildomain'),
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
					'email_only' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.email_only'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['email_only']
						)
					),
					'subcanemaildomain' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.subdomainforemail'),
						'type' => 'select',
						'select_var' => $subcanemaildomain
					),
					'dkim' => array(
						'visible' => (\Froxlor\Settings::Get('dkim.use_dkim') == '1' ? true : false),
						'label' => 'DomainKeys',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['dkim']
						)
					)
				)
			)
		)
	)
);
