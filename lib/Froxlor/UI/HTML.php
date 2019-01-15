<?php
namespace Froxlor\UI;

class HTML
{

	/**
	 * Return HTML Code for a checkbox
	 *
	 * @param string $name
	 *        	The fieldname
	 * @param string $title
	 *        	The captions
	 * @param string $value
	 *        	The Value which will be returned
	 * @param bool $break
	 *        	Add a <br /> at the end of the checkbox
	 * @param string $selvalue
	 *        	Values which will be selected by default
	 * @param bool $title_trusted
	 *        	Whether the title may contain html or not
	 * @param bool $value_trusted
	 *        	Whether the value may contain html or not
	 *        	
	 * @return string HTML Code
	 */
	public static function makecheckbox($name, $title, $value, $break = false, $selvalue = null, $title_trusted = false, $value_trusted = false)
	{
		if ($selvalue !== null && $value == $selvalue) {
			$checked = 'checked="checked"';
		} elseif (isset($_SESSION['requestData'][$name])) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		if (! $title_trusted) {
			$title = htmlspecialchars($title);
		}

		if (! $value_trusted) {
			$value = htmlspecialchars($value);
		}

		$checkbox = '<input type="checkbox" name="' . $name . '" value="' . $value . '" ' . $checked . ' />&nbsp;' . $title;

		if ($break) {
			$checkbox .= '<br />';
		}

		return $checkbox;
	}

	/**
	 * Return HTML Code for an option within a <select>
	 *
	 * @param string $title
	 *        	The caption
	 * @param string $value
	 *        	The Value which will be returned
	 * @param string $selvalue
	 *        	Values which will be selected by default.
	 * @param bool $title_trusted
	 *        	Whether the title may contain html or not
	 * @param bool $value_trusted
	 *        	Whether the value may contain html or not
	 * @param int $id
	 * @param bool $disabled
	 *
	 * @return string HTML Code
	 */
	public static function makeoption($title, $value, $selvalue = null, $title_trusted = false, $value_trusted = false, $id = null, $disabled = false)
	{
		if ($selvalue !== null && ((is_array($selvalue) && in_array($value, $selvalue)) || $value == $selvalue)) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}

		if ($disabled) {
			$selected .= ' disabled="disabled"';
		}

		if (! $title_trusted) {
			$title = htmlspecialchars($title);
		}

		if (! $value_trusted) {
			$value = htmlspecialchars($value);
		}

		$id_str = ' ';
		if ($id !== null) {
			$id_str = 'id="' . $id . '"';
		}

		$option = '<option value="' . $value . '" ' . $id_str . $selected . ' >' . $title . '</option>';
		return $option;
	}

	/**
	 * Returns HTML Code for two radio buttons with two choices: yes and no
	 *
	 * @param string $name
	 *        	Name of HTML-Variable
	 * @param string $yesvalue
	 *        	Value which will be returned if user chooses yes
	 * @param string $novalue
	 *        	Value which will be returned if user chooses no
	 * @param string $yesselected
	 *        	Value which is chosen by default
	 * @param bool $disabled
	 *        	Whether this element is disabled or not (default: false)
	 * @param string $extra_css
	 *        	optional, wether to apply more css classes
	 *        	
	 * @return string HTML Code
	 * @author Florian Lippert <flo@syscp.org> (2003-2009)
	 * @author Froxlor team <team@froxlor.org> (2010-)
	 */
	public static function makeyesno($name, $yesvalue, $novalue = '', $yesselected = '', $disabled = false, $extra_css = '')
	{
		$d = '';
		if ($disabled) {
			$d = ' disabled="disabled"';
		}

		if (isset($_SESSION['requestData'])) {
			$yesselected = $yesselected & $_SESSION['requestData'][$name];
		}

		$s = '';
		if ($yesselected == $yesvalue) {
			$s = ' checked="checked"';
		}

		return '<label class="switch switch-left-right mb-0">
			<input type="hidden" name="' . $name . '" value="' . $novalue . '">
			<input class="switch-input' . $extra_css . '" type="checkbox" id="' . $name . '" name="' . $name . '"' . $d . ' value="' . $yesvalue . '"' . $s . '>
			<span class="switch-label" data-on="' . \Froxlor\Frontend\UI::getLng('panel.yes') . '" data-off="' . \Froxlor\Frontend\UI::getLng('panel.no') . '"></span> <span class="switch-handle"></span>
			</label>';
	}

	/**
	 * Prints Question on screen
	 *
	 * @param string $text
	 *        	The question
	 * @param string $yesfile
	 *        	File which will be called with POST if user clicks yes
	 * @param array $params
	 *        	Values which will be given to $yesfile. Format: array(variable1=>value1, variable2=>value2, variable3=>value3)
	 * @param string $targetname
	 *        	Name of the target eg Domain or eMail address etc.
	 *        	
	 * @author Florian Lippert <flo@syscp.org>
	 * @author Froxlor team <team@froxlor.org> (2010-)
	 *        
	 * @return string outputs parsed question_yesno template
	 */
	public static function askYesNo($text, $yesfile, $params = array(), $targetname = '', $extraparams = "")
	{
		$hiddenparams = '';

		if (is_array($params)) {
			foreach ($params as $field => $value) {
				$hiddenparams .= '<input type="hidden" name="' . htmlspecialchars($field) . '" value="' . htmlspecialchars($value) . '" />' . "\n";
			}
		}

		if (\Froxlor\Frontend\UI::getLng('question.' . $text) != null) {
			$text = \Froxlor\Frontend\UI::getLng('question.' . $text);
		}

		$text = strtr($text, array(
			'%s' => $targetname
		));

		\Froxlor\Frontend\UI::TwigBuffer('misc/yesno.html.twig', array(
			'page_title' => \Froxlor\Frontend\UI::getLng('question.question'),
			'yesno_msg' => $text,
			'hiddenparams' => $hiddenparams,
			'extraparams' => $extraparams,
			'yesfile' => $yesfile
		));
	}

	public static function askYesNoWithCheckbox($text, $chk_text, $yesfile, $params = array(), $targetname = '', $show_checkbox = true)
	{
		if (\Froxlor\Frontend\UI::getLng('question.' . $chk_text) != null) {
			$chk_text = \Froxlor\Frontend\UI::getLng('question.' . $chk_text);
		}

		if ($show_checkbox) {
			$checkbox = $chk_text . ':&nbsp;' . self::makeyesno('delete_userfiles', '1', '0', '0')."<br /><br />";
		} else {
			$params['delete_userfiles'] = "0";
			$checkbox = '';
		}

		self::askYesNo($text, $yesfile, $params, $targetname, $checkbox);
	}
}
