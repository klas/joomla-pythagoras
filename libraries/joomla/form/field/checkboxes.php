<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Layout\Helper as JLayoutHelper;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @see    JFormFieldCheckbox
 * @since  11.1
 */
class JFormFieldCheckboxes extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Checkboxes';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;

	/**
	 * The comma seprated list of checked checkboxes value.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	public $checkedOptions;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'forceMultiple':
			case 'checkedOptions':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'checkedOptions':
				$this->checkedOptions = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->checkedOptions = (string) $this->element['checked'];
		}

		return $return;
	}

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$displayData = $this->getInputLayoutData();

		return JLayoutHelper::render('joomla.fields.checkboxes', $displayData);
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since 3.5
	 */
	protected function getInputLayoutData()
	{
		$displayData = parent::getInputLayoutData();

		// True if the field has 'value' set. In other words, it has been stored, don't use the default values.
		$hasValue = (isset($this->value) && !empty($this->value));

		// If a value has been stored, use it. Otherwise, use the defaults.
		$checkedOptions = $hasValue ? $this->value : $this->checkedOptions;

		$displayData['checkedOptions'] = is_array($checkedOptions) ? $checkedOptions : explode(',', (string) $checkedOptions);
		$displayData['hasValue'] = $hasValue;
		$displayData['options'] = $this->getOptions();

		return $displayData;
	}
}