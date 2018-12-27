<?php
namespace Froxlor\Frontend;

class FroxlorTwig extends \Twig_Extension
{

	/**
	 *
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('get_setting', [
				$this,
				'getSetting'
			]),
			new \Twig_SimpleFunction('lng', [
				$this,
				'getLang'
			])
		);
	}

	public function getSetting($setting = null)
	{
		return \Froxlor\Settings::Get($setting);
	}

	public function getLang($identifier = null)
	{
		return \Froxlor\Frontend\UI::getLng($identifier);
	}

	/**
	 *
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'froxlortwig';
	}
}
