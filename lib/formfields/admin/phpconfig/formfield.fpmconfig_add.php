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
	'fpmconfig_add' => array(
		'title' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.addsettings'),
		'image' => 'icons/phpsettings_add.png',
		'sections' => array(
			'section_a' => array(
				'title' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.addsettings'),
				'image' => 'icons/phpsettings_add.png',
				'fields' => array(
					'description' => array(
						'label' => \Froxlor\Frontend\UI::getLng('admin.phpsettings.description'),
						'type' => 'text',
						'maxlength' => 50,
						'mandatory' => true
					),
					'reload_cmd' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.reload'),
						'type' => 'text',
						'maxlength' => 255,
						'value' => 'service php7.3-fpm restart',
						'mandatory' => true
					),
					'config_dir' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.configdir'),
						'type' => 'text',
						'maxlength' => 255,
						'value' => '/etc/php/7.3/fpm/pool.d/',
						'mandatory' => true
					),
					'pm' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.pm'),
						'type' => 'select',
						'select_var' => $pm_select
					),
					'max_children' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_children.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_children.description'),
						'type' => 'int',
						'value' => 5
					),
					'start_servers' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.start_servers.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.start_servers.description'),
						'type' => 'int',
						'value' => 2
					),
					'min_spare_servers' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.min_spare_servers.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.min_spare_servers.description'),
						'type' => 'int',
						'value' => 1
					),
					'max_spare_servers' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_spare_servers.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_spare_servers.description'),
						'type' => 'int',
						'value' => 3
					),
					'max_requests' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_requests.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.max_requests.description'),
						'type' => 'int',
						'value' => 0
					),
					'idle_timeout' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.idle_timeout.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.idle_timeout.description'),
						'type' => 'int',
						'value' => 10
					),
					'limit_extensions' => array(
						'label' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.limit_extensions.title'),
						'desc' => \Froxlor\Frontend\UI::getLng('serversettings.phpfpm_settings.limit_extensions.description'),
						'type' => 'text',
						'value' => '.php'
					),
					'custom_config' => array(
						'label' => $lng['serversettings']['phpfpm_settings']['custom_config']['title'],
						'desc' => $lng['serversettings']['phpfpm_settings']['custom_config']['description'],
						'type' => 'textarea',
						'cols' => 50,
						'rows' => 7
					)
				)
			)
		)
	)
);
