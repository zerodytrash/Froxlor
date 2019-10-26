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
 * @package    Language
 *
 */
return array(
	'groups' => array(
		'panel' => array(
			'title' => \Froxlor\Frontend\UI::getLng('admin.panelsettings'),
			'fields' => array(
				'panel_standardlanguage' => array(
					'label' => array(
						'title' => \Froxlor\Frontend\UI::getLng('login.language'),
						'description' => \Froxlor\Frontend\UI::getLng('serversettings.language.description')
					),
					'settinggroup' => 'panel',
					'varname' => 'standardlanguage',
					'type' => 'option',
					'default' => 'English',
					'option_mode' => 'one',
					'option_options_method' => array(
						'\\Froxlor\\User',
						'getLanguages'
					),
					'save_method' => 'storeSettingField'
				),
				'panel_default_theme' => array(
					'label' => array(
						'title' => \Froxlor\Frontend\UI::getLng('panel.theme'),
						'description' => \Froxlor\Frontend\UI::getLng('serversettings.default_theme')
					),
					'settinggroup' => 'panel',
					'varname' => 'default_theme',
					'type' => 'option',
					'default' => 'Sparkle',
					'option_mode' => 'one',
					'option_options_method' => array(
						'\\Froxlor\\Frontend\\UI',
						'getThemes'
					),
					'save_method' => 'storeSettingDefaultTheme'
				),
				'panel_allow_theme_change_customer' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_allow_theme_change_customer'),
					'settinggroup' => 'panel',
					'varname' => 'allow_theme_change_customer',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_allow_theme_change_admin' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_allow_theme_change_admin'),
					'settinggroup' => 'panel',
					'varname' => 'allow_theme_change_admin',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_natsorting' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.natsorting'),
					'settinggroup' => 'panel',
					'varname' => 'natsorting',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_no_robots' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.no_robots'),
					'settinggroup' => 'panel',
					'varname' => 'no_robots',
					'type' => 'bool',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_paging' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.paging'),
					'settinggroup' => 'panel',
					'varname' => 'paging',
					'type' => 'int',
					'int_min' => 0,
					'default' => 0,
					'save_method' => 'storeSettingField'
				),
				'panel_pathedit' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.pathedit'),
					'settinggroup' => 'panel',
					'varname' => 'pathedit',
					'type' => 'option',
					'default' => 'Manual',
					'option_mode' => 'one',
					'option_options' => array(
						'Manual' => \Froxlor\Frontend\UI::getLng('serversettings.manual'),
						'Dropdown' => \Froxlor\Frontend\UI::getLng('serversettings.dropdown')
					),
					'save_method' => 'storeSettingField'
				),
				'panel_adminmail' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.adminmail'),
					'settinggroup' => 'panel',
					'varname' => 'adminmail',
					'type' => 'string',
					'string_type' => 'mail',
					'string_emptyallowed' => false,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'panel_adminmail_defname' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.adminmail_defname'),
					'settinggroup' => 'panel',
					'varname' => 'adminmail_defname',
					'type' => 'string',
					'default' => 'Froxlor Administrator',
					'save_method' => 'storeSettingField'
				),
				'panel_adminmail_return' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.adminmail_return'),
					'settinggroup' => 'panel',
					'varname' => 'adminmail_return',
					'type' => 'string',
					'string_type' => 'mail',
					'string_emptyallowed' => true,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'panel_decimal_places' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.decimal_places'),
					'settinggroup' => 'panel',
					'varname' => 'decimal_places',
					'type' => 'int',
					'int_min' => 0,
					'int_max' => 15,
					'default' => 4,
					'save_method' => 'storeSettingField'
				),
				'panel_phpmyadmin_url' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpmyadmin_url'),
					'settinggroup' => 'panel',
					'varname' => 'phpmyadmin_url',
					'type' => 'string',
					'string_type' => 'url',
					'string_emptyallowed' => true,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'panel_webmail_url' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.webmail_url'),
					'settinggroup' => 'panel',
					'varname' => 'webmail_url',
					'type' => 'string',
					'string_type' => 'url',
					'string_emptyallowed' => true,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'panel_webftp_url' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.webftp_url'),
					'settinggroup' => 'panel',
					'varname' => 'webftp_url',
					'type' => 'string',
					'string_type' => 'url',
					'string_emptyallowed' => true,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'admin_show_version_login' => array(
					'label' => \Froxlor\Frontend\UI::getLng('admin.show_version_login'),
					'settinggroup' => 'admin',
					'varname' => 'show_version_login',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'admin_show_version_footer' => array(
					'label' => \Froxlor\Frontend\UI::getLng('admin.show_version_footer'),
					'settinggroup' => 'admin',
					'varname' => 'show_version_footer',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'admin_show_news_feed' => array(
					'label' => \Froxlor\Frontend\UI::getLng('admin.show_news_feed'),
					'settinggroup' => 'admin',
					'varname' => 'show_news_feed',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'customer_show_news_feed' => array(
					'label' => \Froxlor\Frontend\UI::getLng('admin.customer_show_news_feed'),
					'settinggroup' => 'customer',
					'varname' => 'show_news_feed',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'customer_news_feed_url' => array(
					'label' => \Froxlor\Frontend\UI::getLng('admin.customer_news_feed_url'),
					'settinggroup' => 'customer',
					'varname' => 'news_feed_url',
					'type' => 'string',
					'string_type' => 'url',
					'string_emptyallowed' => true,
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'panel_allow_domain_change_admin' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_allow_domain_change_admin'),
					'settinggroup' => 'panel',
					'varname' => 'allow_domain_change_admin',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_allow_domain_change_customer' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_allow_domain_change_customer'),
					'settinggroup' => 'panel',
					'varname' => 'allow_domain_change_customer',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_phpconfigs_hidestdsubdomain' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_phpconfigs_hidestdsubdomain'),
					'settinggroup' => 'panel',
					'varname' => 'phpconfigs_hidestdsubdomain',
					'type' => 'bool',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_customer_hide_options' => array(
					'label' => \Froxlor\Frontend\UI::getLng('serversettings.panel_customer_hide_options'),
					'settinggroup' => 'panel',
					'varname' => 'customer_hide_options',
					'type' => 'option',
					'default' => '',
					'option_mode' => 'multiple',
					'option_emptyallowed' => true,
					'option_options' => array(
						'email' => \Froxlor\Frontend\UI::getLng('menue.email.email'),
						'mysql' => \Froxlor\Frontend\UI::getLng('menue.mysql.mysql'),
						'domains' => \Froxlor\Frontend\UI::getLng('menue.domains.domains'),
						'ftp' => \Froxlor\Frontend\UI::getLng('menue.ftp.ftp'),
						'extras' => \Froxlor\Frontend\UI::getLng('menue.extras.extras'),
						'extras.directoryprotection' => \Froxlor\Frontend\UI::getLng('menue.extras.extras') . " / " . \Froxlor\Frontend\UI::getLng('menue.extras.directoryprotection'),
						'extras.pathoptions' => \Froxlor\Frontend\UI::getLng('menue.extras.extras') . " / " . \Froxlor\Frontend\UI::getLng('menue.extras.pathoptions'),
						'extras.logger' => \Froxlor\Frontend\UI::getLng('menue.extras.extras') . " / " . \Froxlor\Frontend\UI::getLng('menue.logger.logger'),
						'extras.backup' => \Froxlor\Frontend\UI::getLng('menue.extras.extras') . " / " . \Froxlor\Frontend\UI::getLng('menue.extras.backup'),
						'traffic' => \Froxlor\Frontend\UI::getLng('menue.traffic.traffic'),
						'traffic.http' => \Froxlor\Frontend\UI::getLng('menue.traffic.traffic') . " / HTTP",
						'traffic.ftp' => \Froxlor\Frontend\UI::getLng('menue.traffic.traffic') . " / FTP",
						'traffic.mail' => \Froxlor\Frontend\UI::getLng('menue.traffic.traffic') . " / Mail"
					),
					'save_method' => 'storeSettingField'
				)
			)
		)
	)
);
