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
	'customer_edit' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.customer_edit'),
		'image' => 'icons/user_edit.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.accountdata'),
				'image' => 'icons/user_edit.png',
				'fields' => array(
					'loginname' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.username'),
						'type' => 'label',
						'value' => $result['loginname']
					),
					'documentroot' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.documentroot'),
						'type' => 'label',
						'value' => $result['documentroot']
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
							($result['standardsubdomain'] != '0') ? '1' : '0'
						)
					),
					'deactivated' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.deactivated_user'),
						'type' => 'checkbox',
						'values' => array(
							array(
								'label' => \Froxlor\Frontend\UI::getLng('panel.yes'),
								'value' => '1'
							)
						),
						'value' => array(
							$result['deactivated']
						)
					),
					'new_customer_password' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.password') . '&nbsp;(' . \Froxlor\Frontend\UI::getLng('panel.emptyfornochanges') . ')',
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
							$result['api_allowed']
						),
						'visible' => (\Froxlor\Settings::Get('api.enabled') == '1' ? true : false)
					)
				)
			),
			'section_b' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.contactdata'),
				'image' => 'icons/user_edit.png',
				'fields' => array(
					'name' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.name'),
						'type' => 'text',
						'mandatory_ex' => true,
						'value' => $result['name']
					),
					'firstname' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.firstname'),
						'type' => 'text',
						'mandatory_ex' => true,
						'value' => $result['firstname']
					),
					'gender' => array(
						'label' => \Froxlor\Frontend\UI::getLng('gender.title'),
						'type' => 'select',
						'select_var' => $gender_options
					),
					'company' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.company'),
						'type' => 'text',
						'mandatory_ex' => true,
						'value' => $result['company']
					),
					'street' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.street'),
						'type' => 'text',
						'value' => $result['street']
					),
					'zipcode' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.zipcode'),
						'type' => 'text',
						'value' => $result['zipcode']
					),
					'city' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.city'),
						'type' => 'text',
						'value' => $result['city']
					),
					'phone' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.phone'),
						'type' => 'text',
						'value' => $result['phone']
					),
					'fax' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.fax'),
						'type' => 'text',
						'value' => $result['fax']
					),
					'email' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email'),
						'type' => 'text',
						'mandatory' => true,
						'value' => $result['email']
					),
					'customernumber' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.customernumber'),
						'type' => 'text',
						'value' => $result['customernumber']
					),
					'custom_notes' => array(
						'style' => 'align-top',
						'label' => \Froxlor\Frontend\UI::getLng('usersettings.custom_notes.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('usersettings.custom_notes.description'),
						'type' => 'textarea',
						'cols' => 60,
						'rows' => 12,
						'value' => $result['custom_notes']
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
						'value' => array(
							$result['custom_notes_show']
						)
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
				'image' => 'icons/user_edit.png',
				'fields' => array(
					'diskspace' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.diskspace'),
						'type' => 'textul',
						'value' => $result['diskspace'],
						'maxlength' => 16,
						'mandatory' => true,
						'ul_field' => $diskspace_ul
					),
					'traffic' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.traffic'),
						'type' => 'textul',
						'value' => $result['traffic'],
						'maxlength' => 14,
						'mandatory' => true,
						'ul_field' => $traffic_ul
					),
					'subdomains' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.subdomains'),
						'type' => 'textul',
						'value' => $result['subdomains'],
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $subdomains_ul
					),
					'emails' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.emails'),
						'type' => 'textul',
						'value' => $result['emails'],
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $emails_ul
					),
					'email_accounts' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_accounts'),
						'type' => 'textul',
						'value' => $result['email_accounts'],
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $email_accounts_ul
					),
					'email_forwarders' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_forwarders'),
						'type' => 'textul',
						'value' => $result['email_forwarders'],
						'maxlength' => 9,
						'mandatory' => true,
						'ul_field' => $email_forwarders_ul
					),
					'email_quota' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.email_quota'),
						'type' => 'textul',
						'value' => $result['email_quota'],
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
							$result['imap']
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
							$result['pop3']
						),
						'mandatory' => true
					),
					'ftps' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.ftps'),
						'type' => 'textul',
						'value' => $result['ftps'],
						'maxlength' => 9,
						'ul_field' => $ftps_ul
					),
					'mysqls' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.mysqls'),
						'type' => 'textul',
						'value' => $result['mysqls'],
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
							$result['phpenabled']
						)
					),
					'allowed_phpconfigs' => array(
						'visible' => (((int) \Froxlor\Settings::Get('system.mod_fcgid') == 1 || (int) \Froxlor\Settings::Get('phpfpm.enabled') == 1) ? true : false),
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.title'),
						'type' => 'checkbox',
						'values' => $phpconfigs,
						'value' => isset($result['allowed_phpconfigs']) && ! empty($result['allowed_phpconfigs']) ? json_decode($result['allowed_phpconfigs'], JSON_OBJECT_AS_ARRAY) : array(),
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
						),
						'value' => array(
							$result['perlenabled']
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
						'value' => array(
							$result['dnsenabled']
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
						),
						'value' => array(
							$result['logviewenabled']
						)
					)
				)
			),
			'section_d' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.movetoadmin'),
				'image' => 'icons/user_edit.png',
				'visible' => ($admin_select_cnt > 1),
				'fields' => array(
					'move_to_admin' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.movecustomertoadmin'),
						'type' => 'select',
						'select_var' => $admin_select
					)
				)
			)
		)
	)
);
