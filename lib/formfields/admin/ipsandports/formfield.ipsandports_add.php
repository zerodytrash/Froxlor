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
	'ipsandports_add' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.add'),
		'image' => 'icons/ipsports_add.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ipandport'),
				'image' => 'icons/ipsports_add.png',
				'fields' => array(
					'ip' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ip'),
						'type' => 'text'
					),
					'port' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.port'),
						'type' => 'text',
						'size' => 5
					)
				)
			),
			'section_b' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.webserverdefaultconfig'),
				'image' => 'icons/ipsports_add.png',
				'fields' => array(
					'listen_statement' => array(
						'visible' => ! $is_nginx,
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.create_listen_statement'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							'1'
						)
					),
					'namevirtualhost_statement' => array(
						'visible' => $is_apache && ! $is_apache24,
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.create_namevirtualhost_statement'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							'1'
						)
					),
					'vhostcontainer' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.create_vhostcontainer'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							'1'
						)
					),
					'docroot' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.docroot.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.docroot.description'),
						'type' => 'text'
					),
					'specialsettings' => array(
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('admin.ownvhostsettings'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.default_vhostconf.description'),
						'type' => 'textarea',
						'cols' => 60,
						'rows' => 12
					),
					'vhostcontainer_servername_statement' => array(
						'visible' => $is_apache,
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.create_vhostcontainer_servername_statement'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							'1'
						)
					)
				)
			),
			'section_c' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.webserverdomainconfig'),
				'image' => 'icons/ipsports_add.png',
				'fields' => array(
					'default_vhostconf_domain' => array(
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.default_vhostconf_domain'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.default_vhostconf_domain.description'),
						'type' => 'textarea',
						'cols' => 60,
						'rows' => 12
					)
				)
			),
			'section_d' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.webserverssldomainconfig'),
				'image' => 'icons/ipsports_add.png',
				'visible' => (\Froxlor\Settings::Get('system.use_ssl') == 1 ? true : false),
				'fields' => array(
					'ssl' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.enable_ssl'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					),
					'ssl_cert_file' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ssl_cert_file'),
						'type' => 'text'
					),
					'ssl_key_file' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ssl_key_file'),
						'type' => 'text'
					),
					'ssl_ca_file' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ssl_ca_file'),
						'type' => 'text'
					),
					'ssl_cert_chainfile' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ssl_cert_chainfile.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ssl_cert_chainfile.description'),
						'type' => 'text'
					)
				)
			)
		)
	)
);
