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
	'admin_add' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.admin_add'),
		'image' => 'icons/user_add.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.accountdata'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'new_loginname' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.username'),
						'type' => 'text',
						'mandatory' => true
					),
					'admin_password' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.password'),
						'type' => 'password',
						'mandatory' => true,
						'autocomplete' => 'off'
					),
					'admin_password_suggestion' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.generated_pwd'),
						'type' => 'text',
						'visible' => (\Froxlor\Settings::Get('panel.password_regex') == ''),
						'value' => \Froxlor\System\Crypt::generatePassword(),
						'readonly' => true
					),
					'def_language' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.language'),
						'type' => 'select',
						'select_var' => $language_options
					),
					'api_allowed' => array(
						'label' => \Froxlor\Frontend\UI::getLng('usersettings.api_allowed.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('usersettings.api_allowed.description'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							'1'
						),
						'visible' => (\Froxlor\Settings::Get('api.enabled') == '1' ? true : false)
					)
				)
			),
			'section_b' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.contactdata'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'name' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.name'),
						'type' => 'text',
						'mandatory' => true
					),
					'email' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email'),
						'type' => 'text',
						'mandatory' => true
					),
					'custom_notes' => array(
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('usersettings.custom_notes.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('usersettings.custom_notes.description'),
						'type' => 'textarea',
						'cols' => 60,
						'rows' => 12
					),
					'custom_notes_show' => array(
						'label' => \Froxlor\Frontend\UI::getLng('usersettings.custom_notes.show'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					)
				)
			),
			'section_c' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.servicedata'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'ipaddress' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.ipaddress.title'),
						'type' => 'select',
						'multiple' => true,
						'select_var' => $ipaddress
					),
					'change_serversettings' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.change_serversettings'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					),
					'customers' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.customers'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $customers_ul
					),
					'customers_see_all' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.customers_see_all'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					),
					'domains' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domains'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $domains_ul
					),
					'domains_see_all' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.domains_see_all'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					),
					'caneditphpsettings' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.caneditphpsettings'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array()
					),
					'diskspace' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.diskspace'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 6,
						'mandatory' => true,
						'ul_field' => $diskspace_ul
					),
					'traffic' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.traffic'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 4,
						'mandatory' => true,
						'ul_field' => $traffic_ul
					),
					'subdomains' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.subdomains'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $subdomains_ul
					),
					'emails' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.emails'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $emails_ul
					),
					'email_accounts' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_accounts'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $email_accounts_ul
					),
					'email_forwarders' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_forwarders'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $email_forwarders_ul
					),
					'email_quota' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_quota'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'visible' => (\Froxlor\Settings::Get('system.mail_quota_enabled') == '1' ? true : false),
						'mandatory' => true,
						'ul_field' => $email_quota_ul
					),
					'ftps' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.ftps'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'ul_field' => $ftps_ul
					),
					'mysqls' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.mysqls'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $mysqls_ul
					)
				)
			)
		)
	)
);
