<?php
namespace Froxlor\UI;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Froxlor team <team@froxlor.org> (2010-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Classes
 *         
 */
class HtmlForm
{

	/**
	 * internal tmp-variable to store form
	 *
	 * @var array
	 */
	private static $form = null;

	private static $filename = '';

	public static function genHTMLForm($data = array())
	{
		self::$form = array();

		foreach ($data as $fdata) {
			$sections = $fdata['sections'];

			foreach ($sections as $sindex => $section) {

				if (isset($section['visible']) && $section['visible'] === false) {
					continue;
				}

				if (! isset(self::$form['sections'])) {
					self::$form['sections'] = array();
				}
				self::$form['sections'][$sindex] = array(
					'title' => $section['title'],
					'nobuttons' => isset($section['nobuttons']) ? $section['nobuttons'] : false,
					'elements' => array()
				);

				$element = array();
				$nexto = false;
				foreach ($section['fields'] as $fieldname => $fielddata) {
					if (isset($fielddata['visible']) && $fielddata['visible'] === false) {
						continue;
					}

					if ($nexto === false || (isset($fielddata['next_to']) && $nexto['field'] != $fielddata['next_to'])) {
						$element[$fieldname] = array(
							'label' => $fielddata['label'],
							'desc' => (isset($fielddata['desc']) ? $fielddata['desc'] : ''),
							'style' => (isset($fielddata['style']) ? ' class="' . $fielddata['style'] . '"' : ''),
							'mandatory' => self::getMandatoryFlag($fielddata),
							'data_field' => self::parseDataField($fieldname, $fielddata),
							'type' => $fielddata['type'],
							'fieldname' => $fieldname
						);

						if (isset($fielddata['has_nextto'])) {
							$nexto = array(
								'field' => $fieldname
							);
							$element[$fieldname]['data_field'] .= '{NEXTTOFIELD_' . $fieldname . '}';
						} else {
							$nexto = false;
						}
						self::$form['sections'][$sindex]['elements'][$fieldname] = $element[$fieldname];
					} else {
						$data_field = self::parseDataField($fieldname, $fielddata);
						$data_field = str_replace("\t", "", $data_field);
						$data_field = $fielddata['next_to_prefix'] . $data_field;
						self::$form['sections'][$sindex]['elements'][$fielddata['next_to']] = str_replace('{NEXTTOFIELD_' . $fielddata['next_to'] . '}', $data_field, self::$form['sections'][$sindex]['elements'][$fielddata['next_to']]);
						$nexto = false;
					}
				}
			}
		}

		return self::$form;
	}

	private static function parseDataField($fieldname, $data = array())
	{
		switch ($data['type']) {
			case 'text':
				return self::textBox($fieldname, $data);
				break;
			case 'textul':
				return self::textBox($fieldname, $data, 'text', true);
				break;
			case 'password':
				return self::textBox($fieldname, $data, 'password');
				break;
			case 'hidden':
				return self::textBox($fieldname, $data, 'hidden');
				break;
			case 'yesno':
				return self::yesnoBox($data);
				break;
			case 'select':
				return self::selectBox($fieldname, $data);
				break;
			case 'label':
				return self::labelField($data);
				break;
			case 'textarea':
				return self::textArea($fieldname, $data);
				break;
			case 'checkbox':
				return self::checkbox($fieldname, $data);
				break;
			case 'file':
				return self::file($fieldname, $data);
				break;
			case 'int':
				return self::int($fieldname, $data);
				break;
		}
	}

	private static function getMandatoryFlag($data = array())
	{
		if (isset($data['mandatory'])) {
			return '&nbsp;<span class="text-danger">*</span>';
		} elseif (isset($data['mandatory_ex'])) {
			return '&nbsp;<span class="text-danger">**</span>';
		}
		return '';
	}

	private static function textBox($fieldname = '', $data = array(), $type = 'text', $unlimited = false)
	{
		// add support to save reloaded forms
		$value = '';
		if (isset($data['value'])) {
			$value = $data['value'];
		} elseif (isset($_SESSION['requestData'][$fieldname])) {
			$value = $_SESSION['requestData'][$fieldname];
		}
		unset($data['value']);

		$ulfield = ($unlimited == true ? $data['ul_field'] : '');
		unset($data['ul_field']);
		if (isset($data['display']) && $data['display'] != '') {
			$ulfield = $data['display'];
			unset($data['display']);
		}

		$extras = '';
		if (isset($data['maxlength'])) {
			$extras .= ' maxlength="' . $data['maxlength'] . '"';
		}
		if (isset($data['size'])) {
			$extras .= ' size="' . $data['size'] . '"';
		}
		if (isset($data['autocomplete'])) {
			$extras .= ' autocomplete="' . $data['autocomplete'] . '"';
		}
		if (isset($data['min'])) {
			$extras .= ' min="' . $data['min'] . '"';
		}
		if (isset($data['max'])) {
			$extras .= ' max="' . $data['max'] . '"';
		}
		if (isset($data['mandatory']) || isset($data['mandatory_ex'])) {
			$extras .= ' required';
		}
		if (isset($data['readonly'])) {
			$extras .= ' readonly';
		}

		$tpl = self::getFormTpl('input');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'type' => $type,
			'extras' => $extras,
			'value' => $value,
			'ulfield' => $ulfield
		));
	}

	private static function int($fieldname = '', $data = array())
	{
		return self::textBox($fieldname, $data, 'number');
	}

	private static function textArea($fieldname = '', $data = array())
	{
		// add support to save reloaded forms
		$value = '';
		if (isset($data['value'])) {
			$value = $data['value'];
		} elseif (isset($_SESSION['requestData'][$fieldname])) {
			$value = $_SESSION['requestData'][$fieldname];
		}
		trim($value);

		$extras = '';
		if (isset($data['cols'])) {
			$extras .= ' cols="' . $data['cols'] . '"';
		}
		if (isset($data['rows'])) {
			$extras .= ' rows="' . $data['rows'] . '"';
		}
		if (isset($data['mandatory']) || isset($data['mandatory_ex'])) {
			$extras .= ' required';
		}

		$tpl = self::getFormTpl('textarea');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'extras' => $extras,
			'value' => $value
		));
	}

	private static function yesnoBox($data = array())
	{
		return $data['yesno_var'];
	}

	private static function labelField($data = array())
	{
		return $data['value'];
	}

	private static function selectBox($fieldname = '', $data = array())
	{
		// add support to save reloaded forms
		if (isset($data['select_var'])) {
			$select_var = $data['select_var'];
		} elseif (isset($_SESSION['requestData'][$fieldname])) {
			$select_var = $_SESSION['requestData'][$fieldname];
		} else {
			$select_var = '';
		}

		$extras = '';
		if (isset($data['multiple'])) {
			$extras .= ' multiple';
		}

		$tpl = self::getFormTpl('select');
		return \Froxlor\Frontend\UI::Twig()->render($tpl, array(
			'fieldname' => $fieldname,
			'select_var' => $select_var,
			'extras' => $extras,
			'class' => isset($data['class']) ? $data['class'] : ""
		));
	}

	/**
	 * Function to generate checkboxes.
	 *
	 * <code>
	 * $data = array(
	 * 'label' => $lng['customer']['email_imap'],
	 * 'type' => 'checkbox',
	 * 'values' => array(
	 * array( 'label' => 'active',
	 * 'value' => '1'
	 * )
	 * ),
	 * 'value' => array('1'),
	 * 'mandatory' => true
	 * )
	 * </code>
	 *
	 * @param string $fieldname
	 *        	contains the fieldname
	 * @param array $data
	 *        	contains the data array
	 */
	private static function checkbox($fieldname = '', $data = array())
	{
		// $data['value'] contains checked items
		$checked = array();
		if (isset($data['value'])) {
			$checked = $data['value'];
		}

		if (isset($_SESSION['requestData'])) {
			if (isset($_SESSION['requestData'][$fieldname])) {
				$checked = array(
					$_SESSION['requestData'][$fieldname]
				);
			}
		}

		// default value is none, so the checkbox isn't an array
		$isArray = '';

		if (count($data['values']) > 1 || (isset($data['is_array']) && $data['is_array'] == 1)) {
			$isArray = '[]';
		}

		// will contain the output
		$output = "";
		$tpl = self::getFormTpl('checkbox');
		foreach ($data['values'] as $val) {
			// is this box checked?
			$isChecked = '';
			if (is_array($checked) && count($checked) > 0) {
				foreach ($checked as $tmp) {
					if ($tmp == $val['value']) {
						$isChecked = ' checked="checked" ';
						break;
					}
				}
			}

			$output .= \Froxlor\Frontend\UI::Twig()->render($tpl, array(
				'fieldname' => $fieldname . $isArray,
				'need_default' => empty($isArray),
				'value' => $val['value'],
				'checked' => $isChecked,
				'label' => $val['label']
			));
		}

		return $output;
	}

	private static function file($fieldname = '', $data = array())
	{
		$return = '';
		$extras = '';
		if (isset($data['maxlength'])) {
			$extras .= ' maxlength="' . $data['maxlength'] . '"';
		}

		// add support to save reloaded forms
		if (isset($data['value'])) {
			$value = $data['value'];
		} elseif (isset($_SESSION['requestData'][$fieldname])) {
			$value = $_SESSION['requestData'][$fieldname];
		} else {
			$value = '';
		}

		if (isset($data['display']) && $data['display'] != '') {
			$ulfield = '<strong>' . $data['display'] . '</strong>';
		}

		eval("\$return = \"" . Template::getTemplate("misc/form/input_file", "1") . "\";");
		return $return;
	}

	private static function getFormTpl($form_element = '')
	{
		$tpl = \Froxlor\Frontend\UI::getTheme() . '/misc/form/elements/' . $form_element . '.html.twig';
		if (! file_exists(\Froxlor\Froxlor::getInstallDir() . '/templates/' . $tpl)) {
			$tpl = 'Sparkle2/misc/form/elements/' . $form_element . '.html.twig';
		}
		return $tpl;
	}
}
