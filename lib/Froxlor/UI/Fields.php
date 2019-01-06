<?php
namespace Froxlor\UI;

class Fields
{

	private static function getFormTpl($form_element = '')
	{
		$tpl = \Froxlor\Frontend\UI::getTheme() . '/misc/form/elements/' . $form_element . '.html.twig';
		if (! file_exists(\Froxlor\Froxlor::getInstallDir() . '/templates/' . $tpl)) {
			$tpl = 'Sparkle2/misc/form/elements/' . $form_element . '.html.twig';
		}
		return $tpl;
	}

	public static function getFormFieldOutputText($fieldname, $fielddata, $do_show = true)
	{
		$tpl = self::getFormTpl('textarea');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'value' => $fielddata['value']
		));
	}

	public static function getFormFieldOutputString($fieldname, $fielddata, $do_show = true)
	{
		$tpl = self::getFormTpl('input');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'type' => 'input',
			'value' => $fielddata['value']
		));
	}

	public static function getFormFieldOutputOption($fieldname, $fielddata, $do_show = true)
	{
		$returnvalue = '';

		if (isset($fielddata['option_options']) && is_array($fielddata['option_options']) && ! empty($fielddata['option_options'])) {
			if (isset($fielddata['option_mode']) && $fielddata['option_mode'] == 'multiple') {
				$multiple = true;
				$fielddata['value'] = explode(',', $fielddata['value']);
			} else {
				$multiple = false;
			}

			$options_array = $fielddata['option_options'];
			$options = '';
			foreach ($options_array as $value => $title) {
				$options .= \Froxlor\UI\HTML::makeoption($title, $value, $fielddata['value']);
			}

			$extras = '';
			if ($multiple) {
				$extras .= ' multiple';
			}

			$tpl = self::getFormTpl('select');
			return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
				'fieldname' => $fieldname,
				'select_var' => $options,
				'extras' => $extras
			));
		}

		return $returnvalue;
	}

	public static function prefetchFormFieldDataOption($fieldname, $fielddata)
	{
		$returnvalue = array();

		if ((! isset($fielddata['option_options']) || ! is_array($fielddata['option_options']) || empty($fielddata['option_options'])) && (isset($fielddata['option_options_method']))) {
			$returnvalue['options'] = call_user_func($fielddata['option_options_method']);
		}

		return $returnvalue;
	}

	public static function getFormFieldOutputInt($fieldname, $fielddata, $do_show = true)
	{
		$tpl = self::getFormTpl('input');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'type' => 'number',
			'value' => $fielddata['value']
		));
	}

	public static function getFormFieldOutputHiddenString($fieldname, $fielddata, $do_show = true)
	{
		return self::getFormFieldOutputHidden($fieldname, $fielddata, $do_show);
	}

	public static function getFormFieldOutputHidden($fieldname, $fielddata)
	{
		$tpl = self::getFormTpl('input');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'type' => 'hidden',
			'value' => $fielddata['value']
		));
	}

	public static function getFormFieldOutputFile($fieldname, $fielddata, $do_show = true)
	{
		$label = $fielddata['label'];
		$value = htmlentities($fielddata['value']);
		eval("\$returnvalue = \"" . \Froxlor\UI\Template::getTemplate("formfields/text", true) . "\";");
		return $returnvalue;
	}

	public static function getFormFieldOutputDate($fieldname, $fielddata, $do_show = true)
	{
		if (isset($fielddata['date_timestamp']) && $fielddata['date_timestamp'] === true) {
			$fielddata['value'] = date('Y-m-d', $fielddata['value']);
		}

		return self::getFormFieldOutputString($fieldname, $fielddata, $do_show);
	}

	public static function getFormFieldOutputBool($fieldname, $fielddata, $do_show = true)
	{
		return \Froxlor\UI\HTML::makeyesno($fieldname, '1', '0', $fielddata['value']);
	}
}
