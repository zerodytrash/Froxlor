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
	'customer_add' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.customer_add'),
		'image' => 'icons/user_add.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.accountdata'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'new_loginname' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.username'),
						'type' => 'text'
					),
					'createstdsubdomain' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.stdsubdomain_add') . '?',
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
					'store_defaultindex' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.store_defaultindex') . '?',
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
					'new_customer_password' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.password'),
						'type' => 'password',
						'autocomplete' => 'off'
					),
					'new_customer_password_suggestion' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.generated_pwd'),
						'type' => 'text',
						'visible' => (\Froxlor\Settings::Get('panel.password_regex') == ''),
						'value' => \Froxlor\System\Crypt::generatePassword(),
						'readonly' => true
					),
					'sendpassword' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.sendpassword'),
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
							(\Froxlor\Settings::Get('api.enabled') == '1' ? '1' : '0')
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
						'mandatory_ex' => true
					),
					'firstname' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.firstname'),
						'type' => 'text',
						'mandatory_ex' => true
					),
					'gender' => array(
						'label' => \Froxlor\Frontend\UI::getLng('gender.title'),
						'type' => 'select',
						'select_var' => $gender_options
					),
					'company' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.company'),
						'type' => 'text',
						'mandatory_ex' => true
					),
					'street' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.street'),
						'type' => 'text'
					),
					'zipcode' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.zipcode'),
						'type' => 'text'
					),
					'city' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.city'),
						'type' => 'text'
					),
					'phone' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.phone'),
						'type' => 'text'
					),
					'fax' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.fax'),
						'type' => 'text'
					),
					'email' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email'),
						'type' => 'text',
						'mandatory' => true
					),
					'customernumber' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.customernumber'),
						'type' => 'text'
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
			'section_cpre' => array(
				'visible' => ! empty($hosting_plans),
				'title' => \Froxlor\Frontend\UI::getLng('admin.plans.use_plan'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'use_plan' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.plans.use_plan'),
						'type' => 'select',
						'select_var' => $hosting_plans
					)
				)
			),
			'section_c' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.servicedata'),
				'image' => 'icons/user_add.png',
				'fields' => array(
					'diskspace' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.diskspace'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 16,
						'mandatory' => true,
						'ul_field' => $diskspace_ul
					),
					'traffic' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.traffic'),
						'type' => 'textul',
						'value' => 0,
						'maxlength' => 14,
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
					'email_imap' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email_imap'),
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
						'mandatory' => true
					),
					'email_pop3' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email_pop3'),
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
						'mandatory' => true
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
					),
					'phpenabled' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpenabled') . '?',
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
					'allowed_phpconfigs' => array(
						'visible' => (((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 || (int) \Froxlor\Settings::Get('phpfpm.enabled') == 1) ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.title'),
						'type' => 'checkbox',
						'values' => $phpconfigs,
						'value' => ((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 ? array(
							\Froxlor\Settings::Get('system.mod_fcgid_defaultini')
						) : ((int) \Froxlor\Settings::Get('phpfpm.enabled') == 1 ? array(
							\Froxlor\Settings::Get('phpfpm.defaultini')
						) : array())),
						'is_array' => 1
					),
					'perlenabled' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.perlenabled') . '?',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						)
					),
					'dnsenabled' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.dnsenabled') . '?',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'visible' => (\Froxlor\Settings::Get('system.dnsenabled') == '1' ? true : false)
					),
					'logviewenabled' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.logviewenabled') . '?',
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						)
					)
				)
			)
		)
	)
);
