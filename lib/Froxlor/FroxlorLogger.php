<?php
namespace Froxlor;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Froxlor\System\MysqlHandler;

/**
 * Class FroxlorLogger
 */
class FroxlorLogger
{

	/**
	 * current \Monolog\Logger object
	 *
	 * @var \Monolog\Logger
	 */
	private static $ml = null;

	/**
	 * LogTypes Array
	 *
	 * @var array
	 */
	private static $logtypes = null;

	/**
	 * whether to output log-messages to STDOUT (cron)
	 *
	 * @var bool
	 */
	private static $crondebug_flag = false;

	/**
	 * user info of logged in user
	 *
	 * @var array
	 */
	private static $userinfo = array();

	const USR_ACTION = '10';

	const ADM_ACTION = '30';

	const CRON_ACTION = '40';

	const LOGIN_ACTION = '50';

	/**
	 * return FroxlorLogger instance
	 *
	 * @param array $userinfo
	 *
	 * @return \Monolog\Logger
	 */
	public static function getLog($userinfo = array())
	{
		if (empty($userinfo)) {
			if (\Froxlor\CurrentUser::hasSession()) {
				$userinfo = \Froxlor\CurrentUser::getData();
			} else {
				$userinfo = array(
					'loginname' => 'system',
					'adminsession' => - 1
				);
			}
		}
		return self::initMonolog($userinfo);
	}

	/**
	 * initiate monolog object
	 *
	 * @return \Monolog\Logger
	 */
	private static function initMonolog($userinfo)
	{
		if (empty(self::$ml)) {
			// get Theme object
			self::$ml = new Logger('froxlor');

			self::$userinfo = $userinfo;
			self::$logtypes = array();

			if ((Settings::Get('logger.logtypes') == null || Settings::Get('logger.logtypes') == '') && (Settings::Get('logger.enabled') !== null && Settings::Get('logger.enabled'))) {
				self::$logtypes[0] = 'syslog';
				self::$logtypes[1] = 'mysql';
			} else {
				if (Settings::Get('logger.logtypes') !== null && Settings::Get('logger.logtypes') != '') {
					self::$logtypes = explode(',', Settings::Get('logger.logtypes'));
				} else {
					self::$logtypes = null;
				}
			}

			$level = Logger::DEBUG;
			if (Settings::Get('logger.severity') == '1') {
				$level = Logger::NOTICE;
			}

			foreach (self::$logtypes as $logger) {

				switch ($logger) {
					case 'syslog':
						self::$ml->pushHandler(new SyslogHandler('froxlor', LOG_USER, $level));
						break;
					case 'file':
						self::$ml->pushHandler(new StreamHandler(Settings::Get('logger.logfile'), $level));
						break;
					case 'mysql':
						self::$ml->pushHandler(new MysqlHandler($level));
						break;
				}
			}

			if (self::$crondebug_flag) {
				self::$ml->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
			}

			self::$ml->pushProcessor(function ($entry) {
				$action = self::LOGIN_ACTION;
				if (self::$userinfo['adminsession']) {
					if (self::$userinfo['adminsession'] == 1) {
						$action = self::ADM_ACTION;
					} elseif (self::$userinfo['adminsession'] == 0) {
						$action = self::USR_ACTION;
					} else {
						$action = self::CRON_ACTION;
					}
				}
				$entry['extra']['user'] = self::$userinfo['loginname'];
				$entry['extra']['action'] = $action;
				return $entry;
			});
		}
		return self::$ml;
	}

	/**
	 * Set whether to log cron-runs
	 *
	 * @param bool $_cronlog
	 *
	 * @return boolean
	 */
	public static function setCronLog($_cronlog = 0)
	{
		$_cronlog = (int) $_cronlog;

		if ($_cronlog < 0 || $_cronlog > 2) {
			$_cronlog = 0;
		}
		Settings::Set('logger.log_cron', $_cronlog);
		return $_cronlog;
	}

	/**
	 * setter for crondebug-flag
	 *
	 * @param bool $_flag
	 *
	 * @return void
	 */
	public static function setCronDebugFlag($_flag = false)
	{
		self::$crondebug_flag = (bool) $_flag;
		// force re-init
		self::$ml = null;
	}
}
