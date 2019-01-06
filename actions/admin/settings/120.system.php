<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Florian Lippert <flo@syscp.org> (2003-2009)
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Settings
 *
 */
return array(
	'groups' => array(
		'system' => array(
			'title' => \Froxlor\Frontend\UI::getLng('admin.systemsettings'),
			'fields' => array(
				'system_documentroot_prefix' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.documentroot_prefix'),
					'settinggroup' => 'system',
					'varname' => 'documentroot_prefix',
					'type' => 'string',
					'string_type' => 'dir',
					'default' => '/var/customers/webs/',
					'save_method' => 'storeSettingField',
					'plausibility_check_method' => array(
						'\\Froxlor\\Validate\\Check',
						'checkPathConflicts'
					)
				),
				'system_documentroot_use_default_value' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.documentroot_use_default_value'),
					'settinggroup' => 'system',
					'varname' => 'documentroot_use_default_value',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'system_ipaddress' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.ipaddress'),
					'settinggroup' => 'system',
					'varname' => 'ipaddress',
					'type' => 'option',
					'option_mode' => 'one',
					'option_options_method' => array(
						'\\Froxlor\\Domain\\IpAddr',
						'getIpAddresses'
					),
					'default' => '',
					'save_method' => 'storeSettingIpAddress'
				),
				'system_defaultip' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.defaultip'),
					'settinggroup' => 'system',
					'varname' => 'defaultip',
					'type' => 'option',
					'option_mode' => 'multiple',
					'option_options_method' => array(
						'\\Froxlor\\Domain\\IpAddr',
						'getIpPortCombinations'
					),
					'default' => '',
					'save_method' => 'storeSettingDefaultIp'
				),
				'system_defaultsslip' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.defaultsslip'),
					'settinggroup' => 'system',
					'varname' => 'defaultsslip',
					'type' => 'option',
					'option_mode' => 'multiple',
					'option_options_method' => array(
						'\\Froxlor\\Domain\\IpAddr',
						'getSslIpPortCombinations'
					),
					'default' => '',
					'save_method' => 'storeSettingDefaultSslIp'
				),
				'system_hostname' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.hostname'),
					'settinggroup' => 'system',
					'varname' => 'hostname',
					'type' => 'string',
					'default' => '',
					'save_method' => 'storeSettingHostname',
					'plausibility_check_method' => array(
						'\\Froxlor\\Validate\\Check',
						'checkHostname'
					)
				),
				'api_enabled' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.enable_api'),
					'settinggroup' => 'api',
					'varname' => 'enabled',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'system_validatedomain' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.validate_domain'),
					'settinggroup' => 'system',
					'varname' => 'validate_domain',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'system_stdsubdomain' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.stdsubdomainhost'),
					'settinggroup' => 'system',
					'varname' => 'stdsubdomain',
					'type' => 'string',
					'default' => '',
					'save_method' => 'storeSettingHostname'
				),
				'system_mysql_access_host' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mysql_access_host'),
					'settinggroup' => 'system',
					'varname' => 'mysql_access_host',
					'type' => 'string',
					'default' => '127.0.0.1,localhost',
					'plausibility_check_method' => array(
						'\\Froxlor\\Validate\\Check',
						'checkMysqlAccessHost'
					),
					'save_method' => 'storeSettingMysqlAccessHost'
				),
				'system_nssextrausers' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.nssextrausers'),
					'settinggroup' => 'system',
					'varname' => 'nssextrausers',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'system_index_file_extension' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.index_file_extension'),
					'settinggroup' => 'system',
					'varname' => 'index_file_extension',
					'type' => 'string',
					'string_regexp' => '/^[a-zA-Z0-9]{1,6}$/',
					'default' => 'html',
					'save_method' => 'storeSettingField'
				),
				'system_store_index_file_subs' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.system_store_index_file_subs'),
					'settinggroup' => 'system',
					'varname' => 'store_index_file_subs',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'system_report_enable' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.report.report'),
					'settinggroup' => 'system',
					'varname' => 'report_enable',
					'type' => 'bool',
					'default' => true,
					'cronmodule' => 'froxlor/reports',
					'save_method' => 'storeSettingField'
				),
				'system_report_webmax' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.report.webmax'),
					'settinggroup' => 'system',
					'varname' => 'report_webmax',
					'type' => 'int',
					'int_min' => 0,
					'int_max' => 150,
					'default' => 90,
					'save_method' => 'storeSettingField'
				),
				'system_report_trafficmax' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.report.trafficmax'),
					'settinggroup' => 'system',
					'varname' => 'report_trafficmax',
					'type' => 'int',
					'int_min' => 0,
					'int_max' => 150,
					'default' => 90,
					'save_method' => 'storeSettingField'
				),

				'system_mail_use_smtp' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_use_smtp'),
					'settinggroup' => 'system',
					'varname' => 'mail_use_smtp',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_host' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_host'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_host',
					'type' => 'string',
					'default' => 'localhost',
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_port' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_port'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_port',
					'type' => 'int',
					'int_min' => 1,
					'int_max' => 65535,
					'default' => 25,
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_usetls' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_usetls'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_usetls',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_auth' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_auth'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_auth',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_user' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_user'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_user',
					'type' => 'string',
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'system_mail_smtp_passwd' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.mail_smtp_passwd'),
					'settinggroup' => 'system',
					'varname' => 'mail_smtp_passwd',
					'type' => 'hiddenString',
					'default' => '',
					'save_method' => 'storeSettingField'
				)
			)
		)
	)
);
