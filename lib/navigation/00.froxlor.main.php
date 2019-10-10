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
 * @package    Navigation
 *
 */
return array(
	'customer' => array(
		'index' => array(
			'icon' => 'user',
			'label' => \Froxlor\Frontend\UI::getLng('admin.mydata'),
			'elements' => array(
				array(
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.username') . \Froxlor\CurrentUser::getField('loginname')
				),
				array(
					'url' => 'index.php?module=CustomerIndex&view=change_password',
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.changepassword'),
					'icon' => 'user-lock'
				),
				array(
					'url' => 'index.php?module=Edit2FA',
					'label' => \Froxlor\Frontend\UI::getLng('2fa.2fa'),
					'show_element' => (\Froxlor\Settings::Get('2fa.enabled') == true),
					'icon' => 'shield-alt'
				),
				array(
					'url' => 'index.php?module=CustomerIndex&view=change_language',
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.changelanguage'),
					'icon' => 'flag'
				),
				array(
					'url' => 'index.php?module=CustomerIndex&view=change_theme',
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.changetheme'),
					'show_element' => (\Froxlor\Settings::Get('panel.allow_theme_change_customer') == true),
					'icon' => 'images'
				),
				array(
					'url' => 'index.php?module=ApiKeys',
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.apikeys'),
					'show_element' => (\Froxlor\Settings::Get('api.enabled') == true),
					'icon' => 'key'
				),
				array(
					'url' => 'https://api.froxlor.org/doc/?v='.\Froxlor\Froxlor::getVersion(),
					'label' => \Froxlor\Frontend\UI::getLng('menue.main.apihelp'),
					'show_element' => (\Froxlor\Settings::Get('api.enabled') == true),
					'icon' => 'question-circle'
				)
			)
		),
		'email' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.email.email'),
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'email')),
			'icon' => 'envelope',
			'elements' => array(
				array(
					'url' => 'index.php?module=CustomerEmail',
					'label' => \Froxlor\Frontend\UI::getLng('menue.email.emails'),
					'required_resources' => 'emails',
					'icon' => 'envelope'
				),
				array(
					'url' => 'index.php?module=CustomerEmail#tab-add',
					'label' => \Froxlor\Frontend\UI::getLng('emails.emails_add'),
					'required_resources' => 'emails',
					'icon' => 'folder-plus'
				),
				array(
					'url' => \Froxlor\Settings::Get('panel.webmail_url'),
					'new_window' => true,
					'label' => \Froxlor\Frontend\UI::getLng('menue.email.webmail'),
					'required_resources' => 'emails_used',
					'show_element' => (\Froxlor\Settings::Get('panel.webmail_url') != ''),
					'icon' => 'external-link-alt'
				)
			)
		),
		'mysql' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.mysql.mysql'),
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'mysql')),
			'icon' => 'database',
			'elements' => array(
				array(
					'url' => 'index.php?module=CustomerMysqls',
					'label' => \Froxlor\Frontend\UI::getLng('menue.mysql.databases'),
					'required_resources' => 'mysqls',
					'icon' => 'database'
				),
				array(
					'url' => \Froxlor\Settings::Get('panel.phpmyadmin_url'),
					'new_window' => true,
					'label' => \Froxlor\Frontend\UI::getLng('menue.mysql.phpmyadmin'),
					'required_resources' => 'mysqls_used',
					'show_element' => (\Froxlor\Settings::Get('panel.phpmyadmin_url') != ''),
					'icon' => 'external-link-alt'
				)
			)
		),
		'domains' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.domains.domains'),
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'domains')),
			'icon' => 'globe',
			'elements' => array(
				array(
					'url' => 'index.php?module=CustomerDomains',
					'label' => \Froxlor\Frontend\UI::getLng('menue.domains.settings'),
					'icon' => 'globe'
				),
				array(
					'url' => 'index.php?module=SslCertificates',
					'label' => \Froxlor\Frontend\UI::getLng('domains.ssl_certificates'),
					'icon' => 'certificate'
				)
			)
		),
		'ftp' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.ftp.ftp'),
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'ftp')),
			'icon' => 'user-tag',
			'elements' => array(
				array(
					'url' => 'index.php?module=CustomerFtp',
					'label' => \Froxlor\Frontend\UI::getLng('menue.ftp.accounts'),
					'icon' => 'user-tag'
				),
				array(
					'url' => \Froxlor\Settings::Get('panel.webftp_url'),
					'new_window' => true,
					'label' => \Froxlor\Frontend\UI::getLng('menue.ftp.webftp'),
					'show_element' => (\Froxlor\Settings::Get('panel.webftp_url') != ''),
					'icon' => 'external-link-alt'
				)
			)
		),
		'extras' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.extras.extras'),
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'extras')),
			'elements' => array(
				array(
					'url' => 'index.php?module=CustomerExtras&view=htpasswds',
					'label' => \Froxlor\Frontend\UI::getLng('menue.extras.directoryprotection'),
					'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'extras.directoryprotection')),
					'icon' => 'user-secret'
				),
				array(
					'url' => 'index.php?module=CustomerExtras&view=htaccess',
					'label' => \Froxlor\Frontend\UI::getLng('menue.extras.pathoptions'),
					'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'extras.pathoptions')),
					'icon' => 'lock'
				),
				array(
					'url' => 'index.php?module=CustomerLogger',
					'label' => \Froxlor\Frontend\UI::getLng('menue.logger.logger'),
					'show_element' => (\Froxlor\Settings::Get('logger.enabled') == true) && (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'extras.logger')),
					'icon' => 'eye'
				),
				array(
					'url' => 'index.php?module=CustomerExtras&view=backup',
					'label' => \Froxlor\Frontend\UI::getLng('menue.extras.backup'),
					'show_element' => (\Froxlor\Settings::Get('system.backupenabled') == true) && (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'extras.backup')),
					'icon' => 'hdd'
				)
			)
		),
		'traffic' => array(
			'label' => \Froxlor\Frontend\UI::getLng('menue.traffic.traffic'),
			'url' => 'index.php?module=CustomerTraffic',
			'show_element' => (! \Froxlor\Settings::IsInList('panel.customer_hide_options', 'traffic')),
			'icon' => 'chart-bar'
		)
	),
	'admin' => array(
		'index' => array(
			'url' => 'index.php?module=AdminIndex&view=myAccount',
			'icon' => 'user',
			'label' => \Froxlor\Frontend\UI::getLng('admin.mydata'),
			'elements' => array()
		),
		'resources' => array(
			'label' => \Froxlor\Frontend\UI::getLng('admin.resources'),
			'required_resources' => 'customers',
			'icon' => 'boxes',
			'elements' => array(
				array(
					'url' => 'index.php?module=AdminCustomers',
					'label' => \Froxlor\Frontend\UI::getLng('panel.customers'),
					'required_resources' => 'customers',
					'icon' => 'users'
				),
				array(
					'url' => 'index.php?module=AdminAdmins',
					'label' => \Froxlor\Frontend\UI::getLng('admin.admins'),
					'required_resources' => 'change_serversettings',
					'icon' => 'user-shield'
				),
				array(
					'url' => 'index.php?module=AdminDomains',
					'label' => \Froxlor\Frontend\UI::getLng('admin.domains'),
					'required_resources' => 'domains',
					'icon' => 'globe'
				),
				array(
					'url' => 'index.php?module=SslCertificates',
					'label' => \Froxlor\Frontend\UI::getLng('domains.ssl_certificates'),
					'required_resources' => 'domains',
					'icon' => 'certificate'
				),
				array(
					'url' => 'index.php?module=AdminIpsandports',
					'label' => \Froxlor\Frontend\UI::getLng('admin.ipsandports.ipsandports'),
					'required_resources' => 'change_serversettings',
					'icon' => 'network-wired'
				),
				array(
					'url' => 'index.php?module=AdminPlans',
					'label' => \Froxlor\Frontend\UI::getLng('admin.plans.plans'),
					'required_resources' => 'customers',
					'icon' => 'sticky-note'
				),
				array(
					'url' => 'index.php?module=AdminSettings&view=updatecounters',
					'label' => \Froxlor\Frontend\UI::getLng('admin.updatecounters'),
					'required_resources' => 'change_serversettings',
					'icon' => 'sync'
				)
			)
		),
		'traffic' => array(
			'label' => \Froxlor\Frontend\UI::getLng('admin.traffic'),
			'required_resources' => 'customers',
			'url' => 'index.php?module=AdminTraffic',
			'icon' => 'chart-bar'
/*
			'elements' => array(
				array(
					
					'label' => \Froxlor\Frontend\UI::getLng('admin.customertraffic'),
					'required_resources' => 'customers',
					'icon' => 'chart-bar'
				)
			)
*/
		),
		'server' => array(
			'label' => \Froxlor\Frontend\UI::getLng('admin.server'),
			'required_resources' => 'change_serversettings',
			'icon' => 'toolbox',
			'elements' => array(
				array(
					'url' => 'index.php?module=AdminConfigfiles',
					'label' => \Froxlor\Frontend\UI::getLng('admin.configfiles.serverconfiguration'),
					'required_resources' => 'change_serversettings',
					'icon' => 'cogs'
				),
				array(
					'url' => 'index.php?module=AdminSettings',
					'label' => \Froxlor\Frontend\UI::getLng('admin.serversettings'),
					'required_resources' => 'change_serversettings',
					'icon' => 'wrench'
				),
				array(
					'url' => 'index.php?module=AdminCronjobs',
					'label' => \Froxlor\Frontend\UI::getLng('admin.cron.cronsettings'),
					'required_resources' => 'change_serversettings',
					'icon' => 'redo'
				),
				array(
					'url' => 'index.php?module=AdminLogger',
					'label' => \Froxlor\Frontend\UI::getLng('menue.logger.logger'),
					'required_resources' => 'change_serversettings',
					'show_element' => (\Froxlor\Settings::Get('logger.enabled') == true),
					'icon' => 'eye'
				),
				array(
					'url' => 'index.php?module=AdminSettings&view=rebuildconfigs',
					'label' => \Froxlor\Frontend\UI::getLng('admin.rebuildconf'),
					'required_resources' => 'change_serversettings',
					'icon' => 'play'
				),
				array(
					'url' => 'index.php?module=AdminAutoupdate',
					'label' => \Froxlor\Frontend\UI::getLng('admin.autoupdate'),
					'required_resources' => 'change_serversettings',
					'show_element' => extension_loaded('zip'),
					'icon' => 'arrow-circle-down'
				),
				array(
					'url' => 'index.php?module=AdminSettings&view=wipecleartextmailpws',
					'label' => \Froxlor\Frontend\UI::getLng('admin.wipecleartextmailpwd'),
					'required_resources' => 'change_serversettings',
					'show_element' => (\Froxlor\Settings::Get('system.mailpwcleartext') == true),
					'icon' => 'broom'
				)
			)
		),
		'server_php' => array(
			'label' => \Froxlor\Frontend\UI::getLng('admin.server_php'),
			'required_resources' => 'change_serversettings',
			'icon' => 'server',
			'elements' => array(
				array(
					'url' => 'index.php?module=AdminPhpSettings',
					'label' => \Froxlor\Frontend\UI::getLng('menue.phpsettings.maintitle'),
					'show_element' => (\Froxlor\Settings::Get('system.mod_fcgid') == true || \Froxlor\Settings::Get('phpfpm.enabled') == true),
					'icon' => 'cogs'
				),
				array(
					'url' => 'index.php?module=AdminFpmDaemons',
					'label' => \Froxlor\Frontend\UI::getLng('menue.phpsettings.fpmdaemons'),
					'required_resources' => 'change_serversettings',
					'show_element' => \Froxlor\Settings::Get('phpfpm.enabled') == true,
					'icon' => 'cogs'
				),
				array(
					'url' => 'index.php?module=AdminPhpSettings&view=phpinfo',
					'label' => \Froxlor\Frontend\UI::getLng('admin.phpinfo'),
					'required_resources' => 'change_serversettings',
					'icon' => 'info-circle'
				),
				array(
					'url' => 'index.php?module=AdminApcuInfo',
					'label' => \Froxlor\Frontend\UI::getLng('admin.apcuinfo'),
					'required_resources' => 'change_serversettings',
					'show_element' => (function_exists('apcu_cache_info') === true),
					'icon' => 'info-circle'
				),
				array(
					'url' => 'index.php?module=AdminOpcacheInfo',
					'label' => \Froxlor\Frontend\UI::getLng('admin.opcacheinfo'),
					'required_resources' => 'change_serversettings',
					'show_element' => (function_exists('opcache_get_configuration') === true),
					'icon' => 'info-circle'
				)
			)
		),
		'misc' => array(
			'label' => \Froxlor\Frontend\UI::getLng('admin.misc'),
			'icon' => 'asterisk',
			'elements' => array(
				array(
					'url' => 'index.php?module=AdminSettings&view=integritycheck',
					'label' => \Froxlor\Frontend\UI::getLng('admin.integritycheck'),
					'required_resources' => 'change_serversettings',
					'icon' => 'check-circle'
				),
				array(
					'url' => 'index.php?module=AdminTemplates',
					'label' => \Froxlor\Frontend\UI::getLng('admin.templates.email'),
					'icon' => 'user-edit'
				),
				array(
					'url' => 'index.php?module=AdminMessage',
					'label' => \Froxlor\Frontend\UI::getLng('admin.message'),
					'icon' => 'envelope'
				),
				array(
					'url' => 'index.php?module=AdminSettings&view=testmail',
					'label' => \Froxlor\Frontend\UI::getLng('admin.testmail'),
					'icon' => 'clipboard-check'
				)
			)
		)
	)
);
