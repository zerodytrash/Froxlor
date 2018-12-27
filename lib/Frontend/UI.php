<?php
namespace Froxlor\Frontend;

class UI
{

	/**
	 * twig object
	 *
	 * @var \Twig_Environment
	 */
	private static $twig = null;

	/**
	 * twig buffer
	 *
	 * @var array
	 */
	private static $twigbuf = array();

	/**
	 * language strigs array
	 *
	 * @var array
	 */
	private static $lng = array();

	/**
	 * send various security related headers
	 */
	public static function sendHeaders()
	{
		header("Content-Type: text/html; charset=UTF-8");

		// prevent Froxlor pages from being cached
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));

		// Prevent inline - JS to be executed (i.e. XSS) in browsers which support this,
		// Inline-JS is no longer allowed and used
		// See: http://people.mozilla.org/~bsterne/content-security-policy/index.html
		// New stuff see: https://www.owasp.org/index.php/List_of_useful_HTTP_headers and https://www.owasp.org/index.php/Content_Security_Policy
		$csp_content = "default-src 'self'; script-src 'self'; connect-src 'self'; img-src 'self' data:; style-src 'self';";
		header("Content-Security-Policy: " . $csp_content);
		header("X-Content-Security-Policy: " . $csp_content);
		header("X-WebKit-CSP: " . $csp_content);

		header("X-XSS-Protection: 1; mode=block");

		// Don't allow to load Froxlor in an iframe to prevent i.e. clickjacking
		header("X-Frame-Options: DENY");

		// Internet Explorer shall not guess the Content-Type, see:
		// http://blogs.msdn.com/ie/archive/2008/07/02/ie8-security-part-v-comprehensive-protection.aspx
		header("X-Content-Type-Options: nosniff");

		// ensure that default timezone is set
		if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get")) {
			@date_default_timezone_set(@date_default_timezone_get());
		}

		self::sendSslHeaders();
	}

	private static function sendSslHeaders()
	{
		/**
		 * If Froxlor was called via HTTPS -> enforce it for the next time by settings HSTS header according to settings
		 */
		if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			$maxage = \Froxlor\Settings::Get('system.hsts_maxage');
			if (empty($maxage)) {
				$maxage = 0;
			}
			$hsts_header = "Strict-Transport-Security: max-age=" . $maxage;
			if (\Froxlor\Settings::Get('system.hsts_incsub') == '1') {
				$hsts_header .= "; includeSubDomains";
			}
			if (\Froxlor\Settings::Get('system.hsts_preload') == '1') {
				$hsts_header .= "; preload";
			}
			header($hsts_header);
		}
	}

	/**
	 * initialize Twig template engine
	 */
	public static function initTwig()
	{
		// init twig template engine
		$loader = new \Twig_Loader_Filesystem(\Froxlor\Froxlor::getInstallDir() . '/templates/Sparkle2/');
		self::$twig = new \Twig_Environment($loader, array(
			'debug' => true,
			'cache' => '../cache',
			'auto_reload' => true
		));
		self::$twig->addExtension(new \Twig_Extension_Debug());
		self::$twig->addExtension(new \Twig_Extensions_Extension_I18n());
		self::$twig->addExtension(new CustomReflection());
		self::$twig->addExtension(new FroxlorTwig());
		// default wert für number_format
		self::$twig->getExtension('Twig_Extension_Core')->setNumberFormat(2, ',', '.');
		// empty buffer
		self::$twigbuf = [];
	}

	/**
	 * twig wrapper
	 *
	 * @return \Twig_Environment
	 */
	public static function Twig()
	{
		return self::$twig;
	}

	/**
	 * wrapper for twig's "render" function to buffer the output
	 *
	 * @see \Twig_Environment::render()
	 */
	public static function TwigBuffer($name, array $context = [])
	{
		self::$twigbuf[] = [
			$name => $context
		];
	}

	/**
	 * echo output buffer and empty buffer-content
	 */
	public static function TwigOutputBuffer()
	{
		$output = "";
		foreach (self::$twigbuf as $buf) {
			foreach ($buf as $name => $context) {
				$output .= self::$twig->render($name, $context);
			}
		}
		echo $output;
		// empty buffer
		self::$twigbuf = [];
	}

	public static function setLng($lng = array())
	{
		self::$lng = $lng;
	}

	public static function getLng($identifier, $context = null)
	{
		$id = explode(".", $identifier);
		if (is_null($context)) {
			$id_first = array_shift($id);
			if (empty($id)) {
				return self::$lng[$id_first];
			} else {
				return self::getLng(implode(".", $id), self::$lng[$id_first]);
			}
		} else {
			$id_first = array_shift($id);
			if (empty($id)) {
				return $context[$id_first];
			} else {
				return self::getLng(implode(".", $id), $context[$id_first]);
			}
		}
	}
}
