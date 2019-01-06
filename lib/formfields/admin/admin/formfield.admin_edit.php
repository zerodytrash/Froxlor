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
	'admin_edit' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.admin_edit'),
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
						),
						'visible' => ($result['adminid'] == $userinfo['userid'] ? false : true)
					),
					'admin_password' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.password') . '&nbsp;(' . \Froxlor\Frontend\UI::getLng('panel.emptyfornochanges') . ')',
						'type' => 'password',
						'autocomplete' => 'off',
						'visible' => ($result['adminid'] == $userinfo['userid'] ? false : true)
					),
					'admin_password_suggestion' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.generated_pwd'),
						'type' => 'text',
						'value' => \Froxlor\System\Crypt::generatePassword(),
						'visible' => (\Froxlor\Settings::Get('panel.password_regex') == '' && ($result['adminid'] == $userinfo['userid'] ? false : true))
					),
					'def_language' => array(
						'label' => \Froxlor\Frontend\UI::getLng('login.language'),
						'type' => 'select',
						'select_var' => $language_options,
						'visible' => ($result['adminid'] == $userinfo['userid'] ? false : true)
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
						'mandatory' => true,
						'value' => $result['name']
					),
					'email' => array(
						'label' => \Froxlor\Frontend\UI::getLng('customer.email'),
						'type' => 'text',
						'mandatory' => true,
						'value' => $result['email']
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
			'section_c' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.servicedata'),
				'image' => 'icons/user_add.png',
				'visible' => ($result['adminid'] != $userinfo['userid'] ? true : false),
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
						'value' => array(
							$result['change_serversettings']
						)
					),
					'customers' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.customers'),
						'type' => 'textul',
						'value' => $result['customers'],
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
						'value' => array(
							$result['customers_see_all']
						)
					),
					'domains' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.domains'),
						'type' => 'textul',
						'value' => $result['domains'],
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
						'value' => array(
							$result['domains_see_all']
						)
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
						'value' => array(
							$result['caneditphpsettings']
						)
					),
					'diskspace' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.diskspace'),
						'type' => 'textul',
						'value' => $result['diskspace'],
						'maxlength' => 6,
						'mandatory' => true,
						'ul_field' => $diskspace_ul
					),
					'traffic' => array(
						'label' => \Froxlor\Frontend\UI::getLng('panel.traffic'),
						'type' => 'textul',
						'value' => $result['traffic'],
						'maxlength' => 4,
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
						'label' => \Froxlor\Frontend\UI::getLng('customer.email_quota'),
						'type' => 'textul',
						'value' => $result['email_quota'],
						'maxlength' => 9,
						'visible' => (\Froxlor\Settings::Get('system.mail_quota_enabled') == '1' ? true : false),
						'mandatory' => true,
						'ul_field' => $email_quota_ul
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
					)
				)
			)
		)
	)
);
