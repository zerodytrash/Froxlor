<?php
namespace Froxlor\UI;

class Response
{

	/**
	 * Sends an header ( 'Location ...' ) to the browser.
	 *
	 * @param string $destination
	 *        	Destination
	 * @param array $get_variables
	 *        	Get-Variables
	 * @param boolean $isRelative
	 *        	if the target we are creating for a redirect
	 *        	should be a relative or an absolute url
	 *        	
	 * @return boolean false if params is not an array
	 */
	public static function redirectTo($destination, $get_variables = null, $isRelative = true)
	{
		if (is_array($get_variables)) {
			$linker = new Linker($destination);

			foreach ($get_variables as $key => $value) {
				$linker->add($key, $value);
			}

			if ($isRelative) {
				$linker->protocol = '';
				$linker->hostname = '';
				$path = './';
			} else {
				if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
					$linker->protocol = 'https';
				} else {
					$linker->protocol = 'http';
				}

				$linker->hostname = $_SERVER['HTTP_HOST'];

				if (dirname($_SERVER['PHP_SELF']) == '/') {
					$path = '/';
				} else {
					$path = dirname($_SERVER['PHP_SELF']) . '/';
				}
				$linker->filename = $path . $destination;
			}
			header('Location: ' . $linker->getLink());
			exit();
		} elseif ($get_variables == null) {
			$linker = new Linker($destination);
			header('Location: ' . $linker->getLink());
			exit();
		}

		return false;
	}

	/**
	 * Prints one ore more errormessages on screen
	 *
	 * @param array $errors
	 *        	Errormessages
	 * @param string $replacer
	 *        	A %s in the errormessage will be replaced by this string.
	 * @param bool $throw_exception
	 *
	 * @author Florian Lippert <flo@syscp.org>
	 * @author Ron Brand <ron.brand@web.de>
	 */
	public static function standard_error($errors = '', $replacer = '', $throw_exception = false)
	{
		$replacer = htmlentities($replacer);

		if (! is_array($errors)) {
			$errors = array(
				$errors
			);
		}

		$error = '';
		foreach ($errors as $single_error) {
			if (! empty(\Froxlor\Frontend\UI::getLng('error.' . $single_error))) {
				$single_error = \Froxlor\Frontend\UI::getLng('error.' . $single_error);
				$single_error = strtr($single_error, array(
					'%s' => $replacer
				));
			} else {
				$error = 'Unknown Error (' . $single_error . '): ' . $replacer;
				break;
			}

			if (empty($error)) {
				$error = $single_error;
			} else {
				$error .= ' ' . $single_error;
			}
		}

		if ($throw_exception) {
			throw new \Exception(strip_tags($error), 400);
		}

		$link = '';
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
			$link = '<a href="' . htmlentities($_SERVER['HTTP_REFERER']) . '" class="btn btn-danger">' . \Froxlor\Frontend\UI::getLng('panel.back') . '</a>';
		}

		self::outputAlert($error, \Froxlor\Frontend\UI::getLng('error.error'), 'danger', $link);
	}

	/**
	 * output dynamic contet error-message
	 *
	 * @param string $message
	 * @param string $title
	 */
	public static function dynamic_error($message, $title = null)
	{
		if (empty($title)) {
			$title = \Froxlor\Frontend\UI::getLng('error.error');
		}
		$link = '';
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
			$link = '<a href="' . htmlentities($_SERVER['HTTP_REFERER']) . '" class="btn btn-danger">' . \Froxlor\Frontend\UI::getLng('panel.back') . '</a>';
		}
		self::outputAlert($message, $title, 'danger', $link);
	}

	/**
	 * Prints one ore more successmessages on screen
	 *
	 * @param array $success_message
	 *        	successmessages
	 * @param string $replacer
	 *        	A %s in the successmessage will be replaced by this string.
	 * @param array $params
	 * @param bool $throw_exception
	 *
	 * @author Florian Lippert <flo@syscp.org>
	 */
	public static function standard_success($success_message = '', $replacer = '', $params = array(), $throw_exception = false)
	{
		if (! empty(\Froxlor\Frontend\UI::getLng('success.' . $success_message))) {
			$success_message = strtr(\Froxlor\Frontend\UI::getLng('success.' . $success_message), array(
				'%s' => htmlentities($replacer)
			));
		}

		if ($throw_exception) {
			throw new \Exception(strip_tags($success_message), 200);
		}

		if (is_array($params) && isset($params['filename'])) {
			$redirect_url = $params['filename'];
			unset($params['filename']);

			foreach ($params as $varname => $value) {
				if ($value != '') {
					$redirect_url .= '&amp;' . $varname . '=' . $value;
				}
			}
		} else {
			$redirect_url = '';
		}

		self::outputAlert($success_message, \Froxlor\Frontend\UI::getLng('success.success'), 'success', "", $redirect_url);
	}

	public static function dynamic_success($message, $title = null)
	{
		if (empty($title)) {
			$title = \Froxlor\Frontend\UI::getLng('success.success');
		}
		self::outputAlert($message, $title, 'success');
	}

	private static function outputAlert($message, $title = null, $type = "danger", $extra = "", $redirect = "")
	{
		if (\Froxlor\CurrentUser::hasSession()) {
			$alerttpl = 'misc/alert.html.twig';
		} else {
			$alerttpl = 'misc/alert_nosession.html.twig';
		}
		\Froxlor\Frontend\UI::TwigBuffer($alerttpl, array(
			'page_title' => $title,
			'type' => $type,
			'heading' => $title,
			'alert_msg' => $message,
			'alert_info' => $extra,
			'redirect_link' => $redirect
		));
		\Froxlor\Frontend\UI::TwigOutputBuffer();
		exit();
	}
}
