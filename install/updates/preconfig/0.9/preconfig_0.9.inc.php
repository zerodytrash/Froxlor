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
 * @package    Language
 *
 */
use Froxlor\Database\Database;
use Froxlor\Settings;
use Froxlor\Install\PreConfig;
use PHPMailer\PHPMailer\PHPMailer;

if (PreConfig::versionInUpdate($current_version, '0.9.4-svn2')) {
	$item = array(
		'description' => 'Froxlor now enables the usage of a domain-wildcard entry and subdomains for this domain at the same time (subdomains are parsed before the main-domain vhost container). This makes it possible to catch all non-existing subdomains with the main vhost but also have the ability to use subdomains for that domain.<br />If you would like Froxlor to do so with your domains, the update script can set the correct values for existing domains for you. Note: future domains will have wildcard-entries enabled by default no matter how you decide here.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to use wildcard-entries for existing domains?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_domainwildcardentry', '1', '0', '1')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.6-svn2')) {
	if (! PHPMailer::ValidateAddress(Settings::Get('panel.adminmail'))) {
		$item = array(
			'description' => 'Froxlor uses a newer version of the phpMailerClass and determined that your current admin-mail address is invalid.',
			'question' => array(
				0 => array(
					'title' => 'Please specify a new admin-email address',
					'form' => '<input type="text" class="form-control" name="update_adminmail" value="' . Settings::Get('panel.adminmail') . '" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.6-svn3')) {
	$item = array(
		'description' => 'You now have the possibility to define default error-documents for your webserver which replace the default webserver error-messages.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable default error-documents?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_deferr_enable', '1', '0', '0')
			),
			1 => array(
				'title' => 'Do you want to enable default error-documents?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_deferr_enable', '1', '0', '0')
			),
			2 => array(
				'title' => 'Path/URL for error 404',
				'form' => '<input type="text" class="form-control" name="update_deferr_404" />'
			)
		)
	);
	if (Settings::Get('system.webserver') == 'apache2') {
		array_push($item['question'], array(
			'title' => 'Path/URL for error 500',
			'form' => '<input type="text" class="form-control" name="update_deferr_500" />'
		));
		array_push($item['question'], array(
			'title' => 'Path/URL for error 401',
			'form' => '<input type="text" class="form-control" name="update_deferr_401" />'
		));
		array_push($item['question'], array(
			'title' => 'Path/URL for error 403',
			'form' => '<input type="text" class="form-control" name="update_deferr_403" />'
		));
	}
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.6-svn5')) {

	$form = '<select name="update_defsys_phpconfig" class="form-control">';
	$configs_array = \Froxlor\Http\PhpConfig::getPhpConfigs();
	foreach ($configs_array as $idx => $desc) {
		$form .= \Froxlor\UI\HTML::makeoption($desc, $idx, '1');
	}
	$form .= '</select>';

	$item = array(
		'description' => 'If you have more than one PHP configurations defined in Froxlor you can now set a default one which will be used for every domain.',
		'question' => array(
			0 => array(
				'title' => 'Select default PHP configuration',
				'form' => $form
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.6-svn6')) {

	$form = '<select name="update_defsys_ftpserver" class="form-control">';
	$form .= \Froxlor\UI\HTML::makeoption('ProFTPd', 'proftpd', 'proftpd');
	$form .= \Froxlor\UI\HTML::makeoption('PureFTPd', 'pureftpd', 'proftpd');
	$form .= '</select>';

	$item = array(
		'description' => 'For the new FTP-quota feature, you can now chose the currently used ftpd-software.',
		'question' => array(
			0 => array(
				'title' => 'Used FTPd-software',
				'form' => $form
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.7-svn1')) {

	$form = '<select name="update_customredirect_default" class="form-control">';
	$form .= \Froxlor\UI\HTML::makeoption('--- (' . \Froxlor\Frontend\UI::getLng('redirect_desc.rc_default') . ')', 1, '1');
	$form .= \Froxlor\UI\HTML::makeoption('301 (' . \Froxlor\Frontend\UI::getLng('redirect_desc.rc_movedperm') . ')', 2, '1');
	$form .= \Froxlor\UI\HTML::makeoption('302 (' . \Froxlor\Frontend\UI::getLng('redirect_desc.rc_found') . ')', 3, '1');
	$form .= \Froxlor\UI\HTML::makeoption('303 (' . \Froxlor\Frontend\UI::getLng('redirect_desc.rc_seeother') . ')', 4, '1');
	$form .= \Froxlor\UI\HTML::makeoption('307 (' . \Froxlor\Frontend\UI::getLng('redirect_desc.rc_tempred') . ')', 5, '1');
	$form .= '</select>';

	$item = array(
		'description' => 'You can now choose whether customers can select the http-redirect code and which of them acts as default.',
		'question' => array(
			0 => array(
				'title' => 'Allow customer chosen redirects?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_customredirect_enable', '1', '0', '1')
			),
			1 => array(
				'title' => 'Select default redirect code (default: empty)',
				'form' => $form
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.7-svn2')) {
	$result = Database::query("SELECT `domain` FROM " . TABLE_PANEL_DOMAINS . " WHERE `documentroot` LIKE '%:%' AND `documentroot` NOT LIKE 'http://%' AND `openbasedir_path` = '0' AND `openbasedir` = '1'");
	$wrongOpenBasedirDomain = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$wrongOpenBasedirDomain[] = $row['domain'];
	}

	if (count($wrongOpenBasedirDomain) > 0) {
		$list = '<ul>';
		$idna_convert = new \Froxlor\Idna\IdnaWrapper();
		foreach ($wrongOpenBasedirDomain as $domain) {
			$list .= '<li>' . $idna_convert->decode($domain) . '</li>';
		}
		$list .= '</ul>';

		$item = array(
			'description' => 'Resetting the open_basedir to customer - root',
			'question' => array(
				0 => array(
					'title' => 'Due to a security - issue regarding open_basedir, Froxlor will set the open_basedir for the following domains to the customers root instead of the chosen documentroot',
					'form' => $list
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.9-svn1')) {
	$item = array(
		'description' => 'When entering MX servers to Froxlor there was no mail-, imap-, pop3- and smtp-"A record" created. You can now chose whether this should be done or not.',
		'question' => array(
			0 => array(
				'title' => 'Do you want these A-records to be created even with MX servers given?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_defdns_mailentry', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.10-svn1')) {
	$has_nouser = false;
	$has_nogroup = false;

	$result_stmt = Database::query("SELECT * FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'httpuser'");
	$result = $result_stmt->fetch(PDO::FETCH_ASSOC);

	if (! isset($result) || ! isset($result['value'])) {
		$has_nouser = true;
		$guessed_user = 'www-data';
		if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
			$_httpuser = posix_getpwuid(posix_getuid());
			$guessed_user = $_httpuser['name'];
		}
	}

	$result_stmt = Database::query("SELECT * FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'httpgroup'");
	$result = $result_stmt->fetch(PDO::FETCH_ASSOC);

	if (! isset($result) || ! isset($result['value'])) {
		$has_nogroup = true;
		$guessed_group = 'www-data';
		if (function_exists('posix_getgid') && function_exists('posix_getgrgid')) {
			$_httpgroup = posix_getgrgid(posix_getgid());
			$guessed_group = $_httpgroup['name'];
		}
	}

	if ($has_nouser || $has_nogroup) {
		$item = array(
			'description' => 'Please enter the correct username/groupname of the webserver on your system We\'re guessing the user but it might not be correct, so please check.',
			'question' => array()
		);
		if ($has_nouser) {
			array_push($item['question'], array(
				'title' => 'Please enter the webservers username',
				'form' => '<input type="text" class="form-control" name="update_httpuser" value="' . $guessed_user . '" />'
			));
		} elseif ($has_nogroup) {
			array_push($item['question'], array(
				'title' => 'Please enter the webservers groupname',
				'form' => '<input type="text" class="form-control" name="update_httpgroup" value="' . $guessed_group . '" />'
			));
		}
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.11-svn1')) {
	$item = array(
		'description' => 'It is possible to enhance security with setting a regular expression to force your customers to enter more complex passwords.',
		'question' => array(
			0 => array(
				'title' => 'Enter a regular expression to force a higher password complexity (leave empty for none)',
				'form' => '<input type="text" class="form-control" name="update_pwdregex" value="" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.11-svn3')) {
	$item = array(
		'description' => 'As Froxlor can now handle perl, you have to specify where the perl executable is (only if you\'re running lighttpd, else just leave empty)',
		'question' => array(
			0 => array(
				'title' => 'Path to perl (default \'/usr/bin/perl\')',
				'form' => '<input type="text" class="form-control" name="update_perlpath" value="/usr/bin/perl" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.12-svn1')) {
	if (Settings::Get('system.mod_fcgid') == 1) {
		$item = array(
			'description' => 'You can chose whether you want Froxlor to use FCGID itself too now.',
			'question' => array()
		);

		array_push($item['question'], array(
			'title' => 'Use FCGID for the Froxlor Panel?',
			'form' => \Froxlor\UI\HTML::makeyesno('update_fcgid_ownvhost', '1', '0', '1')
		));

		array_push($item['question'], array(
			'title' => 'Info',
			'form' => '<div class="alert alert-secondary" role="alert">If \'yes\', please specify local user/group (have to exist, Froxlor does not add them automatically)</div>'
		));

		array_push($item['question'], array(
			'title' => 'Local user',
			'form' => '<input type="text" class="form-control" name="update_fcgid_httpuser" value="froxlorlocal" />'
		));
		array_push($item['question'], array(
			'title' => 'Local group',
			'form' => '<input type="text" class="form-control" name="update_fcgid_httpgroup" value="froxlorlocal" />'
		));
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.12-svn2')) {
	$item = array(
		'description' => 'Many apache user will have problems using perl/CGI as the customer docroots are not within the suexec path. Froxlor provides a simple workaround for that.',
		'question' => array()
	);

	array_push($item['question'], array(
		'title' => 'Enable Apache/SuExec/Perl workaround?',
		'form' => \Froxlor\UI\HTML::makeyesno('update_perl_suexecworkaround', '1', '0', '0')
	));

	array_push($item['question'], array(
		'title' => 'Info',
		'form' => '<div class="alert alert-secondary" role="alert">If \'yes\', please specify a path within the suexec path where Froxlor will create symlinks to customer perl-enabled paths</div>'
	));

	array_push($item['question'], array(
		'title' => 'Path for symlinks (must be within suexec path)',
		'form' => '<input type="text" class="form-control" name="update_perl_suexecpath" value="/var/www/cgi-bin/" />'
	));
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.12-svn4')) {
	if ((int) Settings::Get('system.awstats_enabled') == 1) {
		$item = array(
			'description' => 'Due to different paths of awstats_buildstaticpages.pl and awstats.pl you can set a different path for awstats.pl now.',
			'question' => array(
				0 => array(
					'title' => 'Path to \'awstats.pl\'?',
					'form' => '<input type="text" class="form-control" name="update_awstats_awstatspath" value="' . Settings::Get('system.awstats_path') . '" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.13.1')) {
	if ((int) Settings::Get('system.mod_fcgid_ownvhost') == 1) {
		$form = '<select name="update_defaultini_ownvhost" class="form-control">';
		$configs_array = \Froxlor\Http\PhpConfig::getPhpConfigs();
		foreach ($configs_array as $idx => $desc) {
			$form .= \Froxlor\UI\HTML::makeoption($desc, $idx, '1');
		}
		$form . '</select>';

		$item = array(
			'description' => 'You have FCGID for Froxlor itself activated. You can now specify a PHP-configuration for this.',
			'question' => array(
				0 => array(
					'title' => 'Select Froxlor-vhost PHP configuration',
					'form' => $form
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.14-svn3')) {
	if ((int) Settings::Get('system.awstats_enabled') == 1) {
		$item = array(
			'description' => 'To have icons in AWStats statistic-pages please enter the path to AWStats icons folder.',
			'question' => array(
				0 => array(
					'title' => 'Path to AWSTats icons folder',
					'form' => '<input type="text" class="form-control" name="update_awstats_icons" value="' . Settings::Get('system.awstats_icons') . '" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.14-svn4')) {
	if ((int) Settings::Get('system.use_ssl') == 1) {
		$item = array(
			'description' => 'Froxlor now has the possibility to set \'SSLCertificateChainFile\' for the apache webserver.',
			'question' => array(
				0 => array(
					'title' => 'Enter filename (leave empty for none)',
					'form' => '<input type="text" class="form-control" name="update_ssl_cert_chainfile" value="' . Settings::Get('system.ssl_cert_chainfile') . '" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.14-svn6')) {
	$item = array(
		'description' => 'You can now allow customers to use any of their domains as username for the login.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable domain-login for all customers?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_allow_domain_login', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.16-svn1')) {

	$item = array(
		'description' => 'Froxlor now features support for php-fpm.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable php-fpm?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_phpfpm_enabled', '1', '0', '0')
			)
		)
	);

	array_push($item['question'], array(
		'title' => 'If \'yes\', please specify the configuration directory',
		'form' => '<input type="text" class="form-control" name="update_phpfpm_configdir" value="/etc/php/7.0/fpm/pool.d/" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the temporary files directory',
		'form' => '<input type="text" class="form-control" name="update_phpfpm_tmpdir" value="/var/customers/tmp/" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the PEAR directory',
		'form' => '<input type="text" class="form-control" name="update_phpfpm_peardir" value="/usr/share/php/:/usr/share/php5/" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the php-fpm restart-command',
		'form' => '<input type="text" class="form-control" name="update_phpfpm_reload" value="/etc/init.d/php-fpm restart" />'
	));
	$pm_sel = '<select name="update_phpfpm_pm" class="form-control">';
	$pm_sel .= \Froxlor\UI\HTML::makeoption('static', 'static', 'static');
	$pm_sel .= \Froxlor\UI\HTML::makeoption('dynamic', 'dynamic', 'static');
	$pm_sel .= '</select>';
	array_push($item['question'], array(
		'title' => 'Please specify the php-fpm rocess manager control',
		'form' => $pm_sel
	));
	array_push($item['question'], array(
		'title' => 'Please specify the number of child processes',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_max_children" value="1" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the number of requests per child before respawning',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_max_requests" value="0" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the number of requests per child before respawning',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_max_requests" value="0" />'
	));
	array_push($item['question'], array(
		'title' => 'Info',
		'form' => '<div class="alert alert-secondary" role="alert">The following settings are only required if you chose process manager = dynamic</div>'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the number of child processes created on startup',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_start_servers" value="20" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the desired minimum number of idle server processes',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_min_spare_servers" value="5" />'
	));
	array_push($item['question'], array(
		'title' => 'Please specify the desired maximum number of idle server processes',
		'form' => '<input type="number" class="form-control" name="update_phpfpm_max_spare_servers" value="35" />'
	));
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.16-svn2')) {
	if ((int) Settings::Get('phpfpm.enabled') == 1) {
		$item = array(
			'description' => 'You can chose whether you want Froxlor to use PHP-FPM itself too now.',
			'question' => array()
		);

		array_push($item['question'], array(
			'title' => 'Use PHP-FPM for the Froxlor Panel?',
			'form' => \Froxlor\UI\HTML::makeyesno('update_phpfpm_enabled_ownvhost', '1', '0', '1')
		));

		array_push($item['question'], array(
			'title' => 'Info',
			'form' => '<div class="alert alert-secondary" role="alert">If \'yes\', please specify local user/group (have to exist, Froxlor does not add them automatically)</div>'
		));

		array_push($item['question'], array(
			'title' => 'Local user',
			'form' => '<input type="text" class="form-control" name="update_phpfpm_httpuser" value="' . Settings::Get('system.mod_fcgid_httpuser') . '" />'
		));
		array_push($item['question'], array(
			'title' => 'Local group',
			'form' => '<input type="text" class="form-control" name="update_phpfpm_httpgroup" value="' . Settings::Get('system.mod_fcgid_httpgroup') . '" />'
		));
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.17-svn1')) {
	$item = array(
		'description' => 'Select if you want to enable the web- and traffic-reports',
		'question' => array()
	);

	array_push($item['question'], array(
		'title' => 'Enable web- and traffic-reports?',
		'form' => \Froxlor\UI\HTML::makeyesno('update_system_report_enable', '1', '0', '1')
	));

	array_push($item['question'], array(
		'title' => 'Info',
		'form' => '<div class="alert alert-secondary" role="alert">If \'yes\', please specify a percentage value for web- and traffic when reports are to be sent</div>'
	));

	array_push($item['question'], array(
		'title' => 'Webusage warning level',
		'form' => '<input type="number" class="form-control" name="update_system_report_webmax" value="90" />'
	));

	array_push($item['question'], array(
		'title' => 'Traffic warning level',
		'form' => '<input type="number" class="form-control" name="update_system_report_trafficmax" value="90" />'
	));
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.28-svn6')) {

	if (Settings::Get('system.webserver') == 'apache2') {
		$item = array(
			'description' => 'Froxlor now supports the new Apache 2.4. Please be aware that you need to load additional apache-modules in ordner to use it.',
			'question' => array(
				0 => array(
					'title' => 'Module to load',
					'form' => '<div class="alert alert-dark" role="alert"><code>LoadModule authz_core_module modules/mod_authz_core.so<br>LoadModule authz_host_module modules/mod_authz_host.so</code></div>'
				),
				1 => array(
					'title' => 'Do you want to enable the Apache-2.4 modification?',
					'form' => \Froxlor\UI\HTML::makeyesno('update_system_apache24', '1', '0', '1')
				)
			)
		);
		array_push($preconfig_items, $item);
	} elseif (Settings::Get('system.webserver') == 'nginx') {
		$item = array(
			'description' => 'The path to nginx\'s fastcgi_params file is now customizable.',
			'question' => array(
				0 => array(
					'title' => 'Please enter full path to you nginx/fastcgi_params file (including filename)',
					'form' => '<input type="text" class="form-control" name="nginx_fastcgi_params" value="/etc/nginx/fastcgi_params" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.28-rc2')) {

	$item = array(
		'description' => 'This version adds an option to append the domain-name to the document-root for domains and subdomains.<br />You can enable or disable this feature anytime from settings -> system settings.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to automatically append the domain-name to the documentroot of newly created domains?',
				'form' => \Froxlor\UI\HTML::makeyesno('update_system_documentroot_use_default_value', '1', '0', '1')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.29-dev1')) {
	// we only need to ask if fcgid|php-fpm is enabled
	if (Settings::Get('system.mod_fcgid') == '1' || Settings::Get('phpfpm.enabled') == '1') {
		$item = array(
			'description' => 'Standard-subdomains can now be hidden from the php-configuration overview.',
			'question' => array(
				0 => array(
					'title' => 'Do you want to hide the standard-subdomains (this can be changed in the settings any time)?',
					'form' => \Froxlor\UI\HTML::makeyesno('hide_stdsubdomains', '1', '0', '1')
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.29-dev3')) {
	$item = array(
		'description' => 'There is now a possibility to specify AXFR servers for your bind zone-configuration',
		'question' => array(
			0 => array(
				'title' => 'Enter a comma-separated list of AXFR servers or leave empty (default)',
				'form' => '<input type="text" class="form-control" name="system_afxrservers" value="" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.29-dev4')) {
	$item = array(
		'description' => 'As customers can now specify ssl-certificate data for their domains, you need to specify where the generated files are stored',
		'question' => array(
			0 => array(
				'title' => 'Specify the directory for customer ssl-certificates',
				'form' => '<input type="text" class="form-control" name="system_customersslpath" value="/etc/ssl/froxlor-custom/" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.29.1-dev3')) {
	$item = array(
		'description' => '<div class="alert alert-warning" role="alert">The build in logrotation-feature has been removed. Please follow the configuration-instructions for your system to enable logrotating again.</div>',
		'question' => array()
	);
	array_push($preconfig_items, $item);
}

// let the apache+fpm users know that they MUST change their config
// for the domains / webserver to work after the update
if (PreConfig::versionInUpdate($current_version, '0.9.30-dev1')) {
	if (Settings::Get('system.webserver') == 'apache2' && Settings::Get('phpfpm.enabled') == '1') {
		$item = array(
			'description' => 'The PHP-FPM implementation for apache2 has changed. Please look for the "<b>fastcgi.conf</b>" (Debian/Ubuntu) or "<b>70_fastcgi.conf</b>" (Gentoo) within /etc/apache2/ and change it as shown below',
			'question' => array(
				0 => array(
					'title' => 'File-content',
					'form' => '<div class="alert alert-dark" role="alert"><code>&lt;IfModule mod_fastcgi.c&gt;<br>
    FastCgiIpcDir /var/lib/apache2/fastcgi/<br>
    &lt;Location "/fastcgiphp"&gt;<br>
        Order Deny,Allow<br>
        Deny from All<br>
        # Prevent accessing this path directly<br>
        Allow from env=REDIRECT_STATUS<br>
    &lt;/Location&gt;<br>
&lt;/IfModule&gt;</code></div>'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.31-dev2')) {
	if (Settings::Get('system.webserver') == 'apache2' && Settings::Get('phpfpm.enabled') == '1') {
		$item = array(
			'description' => '<div class="alert alert-warning" role="alert">The FPM socket directory is now a setting in froxlor. Its default is <b>/var/lib/apache2/fastcgi/</b>.<br/>If you are using <b>/var/run/apache2</b> in the "<b>fastcgi.conf</b>" (Debian/Ubuntu) or "<b>70_fastcgi.conf</b>" (Gentoo) please correct this path accordingly</div>',
			'question' => array()
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.31-dev4')) {
	$item = array(
		'description' => '<div class="alert alert-warning" role="alert">The template-variable {PASSWORD} has been replaced with {LINK}. Please update your password reset templates!</div>',
		'question' => array()
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.31-dev5')) {
	$item = array(
		'description' => 'You can enable/disable error-reporting for admins and customers!',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable error-reporting for admins? (default: yes)',
				'form' => \Froxlor\UI\HTML::makeyesno('update_error_report_admin', '1', '0', '1')
			),
			1 => array(
				'title' => 'Do you want to enable error-reporting for customers? (default: no)',
				'form' => \Froxlor\UI\HTML::makeyesno('update_error_report_customer', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.31-rc2')) {
	$item = array(
		'description' => 'You can enable/disable the display/usage of the news-feed for admins',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable the news-feed for admins? (default: yes)',
				'form' => \Froxlor\UI\HTML::makeyesno('update_admin_news_feed', '1', '0', '1')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.32-dev2')) {
	$item = array(
		'description' => 'To enable logging of the mail-traffic, you need to set the following settings accordingly',
		'question' => array()
	);

	array_push($item['question'], array(
		'title' => 'Do you want to enable the traffic collection for mail? (default: yes)',
		'form' => \Froxlor\UI\HTML::makeyesno('mailtraffic_enabled', '1', '0', '1')
	));

	array_push($item['question'], array(
		'title' => 'Info',
		'form' => '<div class="alert alert-secondary" role="alert">Mail Transfer Agent</div>'
	));

	$mta_sel = '<select name="mtaserver" class="form-control">';
	$mta_sel .= \Froxlor\UI\HTML::makeoption('Postfix', 'postfix', 'postfix');
	$mta_sel .= \Froxlor\UI\HTML::makeoption('Exim4', 'exim4', 'postfix');
	$mta_sel .= '</select>';
	array_push($item['question'], array(
		'title' => 'Type of your MTA',
		'form' => $mta_sel
	));

	array_push($item['question'], array(
		'title' => 'Logfile for your MTA',
		'form' => '<input type="text" class="form-control" name="mtalog" value="/var/log/mail.log" />'
	));

	array_push($item['question'], array(
		'title' => 'Info',
		'form' => '<div class="alert alert-secondary" role="alert">Mail Delivery Agent</div>'
	));

	$mda_sel = '<select name="mdaserver" class="form-control">';
	$mda_sel .= \Froxlor\UI\HTML::makeoption('Dovecot', 'dovecot', 'dovecot');
	$mda_sel .= \Froxlor\UI\HTML::makeoption('Courier', 'courier', 'dovecot');
	$mda_sel .= '</select>';
	array_push($item['question'], array(
		'title' => 'Type of your MDA',
		'form' => $mda_sel
	));

	array_push($item['question'], array(
		'title' => 'Logfile for your MDA',
		'form' => '<input type="text" class="form-control" name="mdalog" value="/var/log/mail.log" />'
	));

	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.32-dev5')) {
	$item = array(
		'description' => 'Froxlor now generates a cron-configuration file for the cron-daemon. Please set a filename which will be included automatically by your crond (e.g. files in /etc/cron.d/)',
		'question' => array(
			0 => array(
				'title' => 'Path to the cron-service configuration-file.</strong> This file will be updated regularly and automatically by froxlor.<br />Note: please <b>be sure</b> to use the same filename as for the main froxlor cronjob (default: /etc/cron.d/froxlor)!',
				'form' => '<input type="text" class="form-control" name="crondfile" value="/etc/cron.d/froxlor" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.32-dev6')) {
	$item = array(
		'description' => 'In order for the new cron.d file to work properly, we need to know about the cron-service reload command.',
		'question' => array(
			0 => array(
				'title' => 'Please specify the reload-command of your cron-daemon</strong> (default: /etc/init.d/cron reload)',
				'form' => '<input type="text" class="form-control" name="crondreload" value="/etc/init.d/cron reload" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.32-rc2')) {
	$item = array(
		'description' => 'To customize the command which executes the cronjob (php - basically) change the path below according to your system.',
		'question' => array(
			0 => array(
				'title' => 'Please specify the command to execute cronscripts</strong> (default: "/usr/bin/nice -n 5 /usr/bin/php -q")',
				'form' => '<input type="text" class="form-control" name="croncmdline" value="/usr/bin/nice -n 5 /usr/bin/php -q" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.33-dev1')) {
	$item = array(
		'description' => 'You can enable/disable the display/usage of the custom newsfeed for customers.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable the custom newsfeed for customer? (default: no)',
				'form' => \Froxlor\UI\HTML::makeyesno('customer_show_news_feed', '1', '0', '0')
			),
			1 => array(
				'title' => 'You have to set the URL for your RSS-feed here, if you have chosen to enable the custom newsfeed on the customer-dashboard',
				'form' => '<input type="text" class="form-control" name="customer_news_feed_url" value="" />'
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.33-dev2')) {
	// only if bind is used - if not the default will be set, which is '0' (off)
	if (Settings::get('system.bind_enable') == 1) {
		$item = array(
			'description' => 'You can enable/disable the generation of the bind-zone / config for the system hostname.',
			'question' => array(
				0 => array(
					'title' => 'Do you want to generate a bind-zone for the system-hostname? (default: no)',
					'form' => \Froxlor\UI\HTML::makeyesno('dns_createhostnameentry', '1', '0', '0')
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_version, '0.9.33-rc2')) {
	$item = array(
		'description' => 'You can chose whether you want to receive an e-mail on cronjob errors. Keep in mind that this can lead to an e-mail being sent every 5 minutes.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to receive cron-errors via mail? (default: no)',
				'form' => \Froxlor\UI\HTML::makeyesno('system_send_cron_errors', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_version, '0.9.34-dev3')) {
	$item = array(
		'description' => 'Froxlor now requires the PHP mbstring-extension as we need to be multibyte-character safe in some cases',
		'question' => array(
			'title' => 'PHP mbstring is currently',
			'form' => ""
		)
	);

	if (! extension_loaded('mbstring')) {
		$item['question']['form'] = '<div class="alert alert-warning" role="alert">not installed/loaded<br><br>Please install the PHP mbstring extension in order to finish the update</div>';
	} else {
		$item['question']['form'] = '<div class="alert alert-success" role="alert">installed/loaded</div>';
	}
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201603070')) {
	$item = array(
		'description' => 'You can chose whether you want to enable or disable our Let\'s Encrypt implementation.<br />Please remember that you need to go through the webserver-configuration when enabled because this feature needs a special configuration.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable Let\'s Encrypt? (default: yes)',
				'form' => \Froxlor\UI\HTML::makeyesno('enable_letsencrypt', '1', '0', '1')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201604270')) {
	$item = array(
		'description' => 'You can chose whether you want to enable or disable our backup function.',
		'question' => array(
			0 => array(
				'title' => 'Do you want to enable Backup? (default: no)',
				'form' => \Froxlor\UI\HTML::makeyesno('enable_backup', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201605090')) {
	if (Settings::get('system.bind_enable') == 1) {
		$item = array(
			'description' => 'You can chose whether you want to enable or disable our DNS editor',
			'question' => array(
				0 => array(
					'title' => 'Do you want to enable the DNS editor? (default: no)',
					'form' => \Froxlor\UI\HTML::makeyesno('enable_dns', '1', '0', '0')
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_db_version, '201605170')) {
	if (Settings::get('system.bind_enable') == 1) {
		$dns_sel = '<select name="new_dns_daemon" class="form-control>';
		$dns_sel .= \Froxlor\UI\HTML::makeoption('Bind9', 'Bind', 'Bind');
		$dns_sel .= \Froxlor\UI\HTML::makeoption('PowerDNS', 'PowerDNS', 'Bind');
		$dns_sel .= '</select>';
		$item = array(
			'description' => 'Froxlor now supports the dns-daemon Power-DNS, you can chose between bind and powerdns now.',
			'question' => array(
				0 => array(
					'title' => 'Select dns-daemon you want to use',
					'form' => $dns_sel
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_db_version, '201609120')) {
	if (Settings::Get('system.leenabled') == 1) {
		$item = array(
			'description' => 'You can now customize the path to your acme.conf file (global alias for Let\'s Encrypt). If you already set up Let\'s Encrypt and the acme.conf file, please set this to the complete path to the file!',
			'question' => array(
				0 => array(
					'title' => 'Path to the acme.conf alias-file.',
					'form' => '<input type="text" class="form-control" name="acmeconffile" value="/etc/apache2/conf-enabled/acme.conf" />'
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}

if (PreConfig::versionInUpdate($current_db_version, '201609200')) {
	$item = array(
		'description' => 'Specify SMTP settings which froxlor should use to send mail (optional)',
		'question' => array()
	);

	array_push($item['question'], array(
		'title' => 'Enable sending mails via SMTP?',
		'form' => \Froxlor\UI\HTML::makeyesno('smtp_enable', '1', '0', '0')
	));

	array_push($item['question'], array(
		'title' => 'SMTP Server',
		'form' => '<input type="text" class="form-control" name="smtp_host" value="localhost" />'
	));
	array_push($item['question'], array(
		'title' => 'TCP port to connect to?',
		'form' => '<input type="number" class="form-control" name="smtp_port" value="25" />'
	));
	array_push($item['question'], array(
		'title' => 'Enable TLS encryption?',
		'form' => \Froxlor\UI\HTML::makeyesno('smtp_usetls', '1', '0', '1')
	));
	array_push($item['question'], array(
		'title' => 'Enable SMTP authentication?',
		'form' => \Froxlor\UI\HTML::makeyesno('smtp_auth', '1', '0', '1')
	));
	array_push($item['question'], array(
		'title' => 'SMTP user?',
		'form' => '<input type="text" class="form-control" name="smtp_user" value="" />'
	));
	array_push($item['question'], array(
		'title' => 'SMTP password?',
		'form' => '<input type="password" class="form-control" name="smtp_passwd" value="" />'
	));
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201705050')) {
	$item = array(
		'description' => 'DEBIAN/UBUNTU ONLY: Enable usage of libnss-extrausers as alternative to libnss-mysql (NOTE: if enabled, go through the configuration steps right after the update!!!)',
		'question' => array(
			0 => array(
				'title' => 'Enable usage of libnss-extrausers?',
				'form' => \Froxlor\UI\HTML::makeyesno('system_nssextrausers', '1', '0', '0')
			)
		)
	);
	array_push($preconfig_items, $item);
}

if (PreConfig::versionInUpdate($current_db_version, '201712310')) {
	if (Settings::Get('system.leenabled') == 1) {
		$item = array(
			'description' => 'Chose whether you want to disable the Let\'s Encrypt selfcheck as it causes false positives for some configurations.',
			'question' => array(
				0 => array(
					'title' => 'Disable Let\'s Encrypt self-check?',
					'form' => \Froxlor\UI\HTML::makeyesno('system_disable_le_selfcheck', '1', '0', '0')
				)
			)
		);
		array_push($preconfig_items, $item);
	}
}
