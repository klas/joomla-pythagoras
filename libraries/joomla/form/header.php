<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract Header Field class for the Joomla Platform.
 *
 * JHeaderFields are strongly encouraged to use JLayout layout files to render themselves. For this reason you are
 * strongly encouraged to change the $headerLayout property to specify your layout file and extend the
 * getHeaderLayoutData method to supply your layout template with the necessary data to render itself. This allows
 * template developers and front-end developers to customise the look and feel of header fields to match the design
 * language of their template without having to mess with the PHP code of header fields.
 */
abstract class JFormHeader
{
	/**
	 * The description text for the form field.  Usually used in tooltips.
	 *
	 * @var    string
	 */
	protected $description;

	/**
	 * The SimpleXMLElement object of the <field /> XML element that describes the header field.
	 *
	 * @var    SimpleXMLElement
	 */
	protected $element;

	/**
	 * The Form object of the form attached to the header field.
	 *
	 * @var    JForm
	 */
	protected $form;

	/**
	 * The label for the header field.
	 *
	 * @var    string
	 */
	protected $label;

	/**
	 * The header HTML.
	 *
	 * @var    string|null
	 */
	protected $header;

	/**
	 * The filter HTML.
	 *
	 * @var    string|null
	 */
	protected $filter;

	/**
	 * The buttons HTML.
	 *
	 * @var    string|null
	 */
	protected $buttons;

	/**
	 * The options for a drop-down filter.
	 *
	 * @var    array|null
	 */
	protected $options;

	/**
	 * The name of the form field.
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * The name of the field.
	 *
	 * @var    string
	 */
	protected $fieldname;

	/**
	 * The group of the field.
	 *
	 * @var    string
	 */
	protected $group;

	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type;

	/**
	 * The value of the filter.
	 *
	 * @var    mixed
	 */
	protected $value;

	/**
	 * The intended table data width (in pixels or percent).
	 *
	 * @var    mixed
	 */
	protected $tdwidth;

	/**
	 * The hint text for the form field used to display hint inside the field.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $hint;

	/**
	 * The key of the filter value in the model state.
	 *
	 * @var    mixed
	 */
	protected $filterSource;

	/**
	 * The name of the filter field
	 *
	 * @var    mixed
	 */
	protected $filterFieldName;

	/**
	 * Is this a sortable column?
	 *
	 * @var    bool
	 */
	protected $sortable = false;

	/**
	 * Should we ignore the header (have the field act as just a filter)?
	 *
	 * @var    bool
	 */
	protected $onlyFilter = false;

	/**
	 * Layout to render the header in getHeader() e.g. joomla.form.header.changeme
	 *
	 * @var  string
	 */
	protected $renderHeaderLayout = '';

	/**
	 * Layout to render the filter input field in getFilter() e.g. joomla.form.header.changeme.filter
	 *
	 * @var  string
	 */
	protected $renderFilterLayout = '';

	/**
	 * True to translate the field label string.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $translateLabel = true;

	/**
	 * True to translate the field description string.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $translateDescription = true;

	/**
	 * True to translate the field hint string.
	 *
	 * @var    boolean
	 * @since  3.2
	 */
	protected $translateHint = true;

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   2.0
	 */
	public function __construct(JForm $form = null)
	{
		// If there is a form passed into the constructor set the form and form control properties.
		if ($form instanceof JForm)
		{
			$this->form = $form;
		}
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   2.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'description':
			case 'name':
			case 'type':
			case 'fieldname':
			case 'group':
			case 'tdwidth':
			case 'filterSource':
			case 'filterFieldName':
				return $this->$name;
				break;

			case 'label':
			case 'value':
			case 'filter':
			case 'buttons':
			case 'options':
			case 'sortable':
				if (empty($this->$name))
				{
					$methodName = 'get' . ucfirst($name);
					$this->$name = $this->{$methodName}();
				}

				return $this->$name;
				break;

			case 'header':
				if (empty($this->header))
				{
					$this->header = $this->onlyFilter ? '' : $this->getHeader();
				}

				return $this->header;
				break;

		}

		return null;
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   JForm  $form  The JForm object to attach to the form field.
	 *
	 * @return  JFormHeader  The form field object so that the method can be used in a chain.
	 *
	 * @since   2.0
	 */
	public function setForm(JForm $form)
	{
		$this->form = $form;

		return $this;
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.0
	 */
	public function setup(SimpleXMLElement $element, $group = null)
	{
		// Make sure there is a valid JFormField XML element.
		if ((string) $element->getName() != 'header')
		{
			return false;
		}

		// Reset the internal fields
		$this->label = null;
		$this->header = null;
		$this->filter = null;
		$this->buttons = null;
		$this->options = null;
		$this->value = null;
		$this->filterSource = null;
		$this->filterFieldName = null;

		// Set the XML element object.
		$this->element = $element;

		// Get some important attributes from the form field element.
		$id = (string) $element['id'];
		$name = (string) $element['name'];
		$filterSource = (string) $element['filter_source'];
		$filterFieldName = (string) $element['searchfieldname'];
		$tdwidth = (string) $element['tdwidth'];

		// Set the field description text.
		$this->description = (string) $element['description'];

		// Set the group of the field.
		$this->group = $group;

		// Set the td width of the field.
		$this->tdwidth = $tdwidth;

		// Set the field name and id.
		$this->fieldname = $this->getFieldName($name);
		$this->name = $this->getName($this->fieldname);
		$this->id = $this->getId($id, $this->fieldname);
		$this->filterSource = $this->getFilterSource($filterSource);
		$this->filterFieldName = $this->getFilterFieldName($filterFieldName);

		// Set the field default value.
		$this->value = $this->getValue();

		// Setup the onlyFilter property
		$onlyFilter = $this->element['onlyFilter'] ? (string) $this->element['onlyFilter'] : false;
		$this->onlyFilter = in_array($onlyFilter, array('yes', 'on', '1', 'true'));

		return true;
	}

	/**
	 * Method to get the id used for the field input tag.
	 *
	 * @param   string  $fieldId    The field element id.
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The id to be used for the field input tag.
	 *
	 * @since   2.0
	 */
	protected function getId($fieldId, $fieldName)
	{
		$id = '';

		// If the field is in a group add the group control to the field id.

		if ($this->group)
		{
			// If we already have an id segment add the group control as another level.

			if ($id)
			{
				$id .= '_' . str_replace('.', '_', $this->group);
			}
			else
			{
				$id .= str_replace('.', '_', $this->group);
			}
		}

		// If we already have an id segment add the field id/name as another level.

		if ($id)
		{
			$id .= '_' . ($fieldId ? $fieldId : $fieldName);
		}
		else
		{
			$id .= ($fieldId ? $fieldId : $fieldName);
		}

		// Clean up any invalid characters.
		$id = preg_replace('#\W#', '_', $id);

		return $id;
	}

	/**
	 * Method to get the name used for the field input tag.
	 *
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The name to be used for the field input tag.
	 *
	 * @since   2.0
	 */
	protected function getName($fieldName)
	{
		$name = '';

		// If the field is in a group add the group control to the field name.

		if ($this->group)
		{
			// If we already have a name segment add the group control as another level.
			$groups = explode('.', $this->group);

			if ($name)
			{
				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
			else
			{
				$name .= array_shift($groups);

				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
		}

		// If we already have a name segment add the field name as another level.

		if ($name)
		{
			$name .= '[' . $fieldName . ']';
		}
		else
		{
			$name .= $fieldName;
		}

		return $name;
	}

	/**
	 * Method to get the field name used.
	 *
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The field name
	 *
	 * @since   2.0
	 */
	protected function getFieldName($fieldName)
	{
		return $fieldName;
	}

	/**
	 * Method to get the field label.
	 *
	 * @return  string  The field label.
	 *
	 * @since   2.0
	 */
	protected function getLabel()
	{
		// Get the label text from the XML element, defaulting to the element name.
		$title = $this->element['label'] ? (string) $this->element['label'] : '';

		return $title;
	}

	/**
	 * Get the filter value for this header field
	 *
	 * @return  mixed  The filter value
	 */
	protected function getValue()
	{
		// TODO
		$model = $this->form->getModel();

		return $model->getState($this->filterSource);
	}

	/**
	 * Return the key of the filter value in the model state or, if it's not set,
	 * the name of the field.
	 *
	 * @param   string  $filterSource  The filter source value to return
	 *
	 * @return  string
	 */
	protected function getFilterSource($filterSource)
	{
		if ($filterSource)
		{
			return $filterSource;
		}
		else
		{
			return $this->name;
		}
	}

	/**
	 * Return the name of the filter field
	 *
	 * @param   string  $filterFieldName  The filter field name source value to return
	 *
	 * @return  string
	 */
	protected function getFilterFieldName($filterFieldName)
	{
		if ($filterFieldName)
		{
			return $filterFieldName;
		}
		else
		{
			return $this->filterSource;
		}
	}

	/**
	 * Is this a sortable field?
	 *
	 * @return  boolean  True if it's sortable
	 */
	protected function getSortable()
	{
		$sortable = ($this->element['sortable'] != 'false');

		if ($sortable)
		{
			if (empty($this->header))
			{
				$this->header = $this->onlyFilter ? '' : $this->getHeader();
			}

			$sortable = !empty($this->header);
		}

		return $sortable;
	}

	/**
	 * Returns the HTML for the header row, or null if this element should
	 * render no header element
	 *
	 * @return  string|null  HTML code or null if nothing is to be rendered
	 */
	protected function getHeader()
	{
		return null;
	}

	/**
	 * Returns the HTML for a text filter to be rendered in the filter row,
	 * or null if this element should render no text input filter.
	 *
	 * @return  string|null  HTML code or null if nothing is to be rendered
	 */
	protected function getFilter()
	{
		return null;
	}

	/**
	 * Returns the HTML for the buttons to be rendered in the filter row,
	 * next to the text input filter, or null if this element should render no
	 * text input filter buttons.
	 *
	 * @return  string|null  HTML code or null if nothing is to be rendered
	 */
	protected function getButtons()
	{
		return null;
	}

	/**
	 * Returns the JHtml options for rendering a drop-down filter in the sidebar. Do not include an empty option, it is
	 * added automatically.
	 *
	 * @return  array  The JHtml options for a drop-down filter
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Method to get an attribute of the field
	 *
	 * @param   string  $name     Name of the attribute to get
	 * @param   mixed   $default  Optional value to return if attribute not found
	 *
	 * @return  mixed             Value of the attribute / default
	 */
	public function getAttribute($name, $default = null)
	{
		if ($this->element instanceof SimpleXMLElement)
		{
			$attributes = $this->element->attributes();

			// Ensure that the attribute exists
			if (property_exists($attributes, $name))
			{
				$value = $attributes->$name;

				if ($value !== null)
				{
					return (string) $value;
				}
			}
		}

		return $default;
	}

	/**
	 * Get the data that is going to be passed to the header field layout.
	 *
	 * @return  array
	 */
	protected function getHeaderLayoutData()
	{
		// Label preprocess
		$label = $this->element['label'] ? (string)$this->element['label'] : (string)$this->element['name'];
		$label = $this->translateLabel ? JText::_($label) : $label;

		// Description preprocess
		$description = !empty($this->description) ? $this->description : null;
		$description = !empty($description) && $this->translateDescription ? JText::_($description) : $description;

		$hiddenLabel = $this->getAttribute('hiddenLabel');

		$alt         = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
		$hint        = $this->translateHint ? JText::alt($this->hint, $alt) : $this->hint;

		$debug       = !empty($this->element['debug']) ? ((string)$this->element['debug'] === 'true') : false;

		return array(
			'debug'           => $debug,
			'description'     => $description,
			'element'         => $this->element,
			'field'           => $this,
			'group'           => $this->group,
			'hiddenLabel'     => $hiddenLabel,
			'hint'            => $hint,
			'label'           => $label,
			'name'            => $this->name,
			'options'         => $this->getOptions(),
			'sortable'        => $this->getSortable(),
			'onlyFilter'      => $this->onlyFilter,
			'filterSource'    => $this->filterSource,
			'filterFieldName' => $this->filterFieldName,
			'tdwidth'         => $this->tdwidth,
			'value'           => $this->getValue()
		);
	}

	/**
	 * Get the data that is going to be passed to the filter input field layout.
	 *
	 * @return  array
	 */
	protected function getFilterLayoutData()
	{
		return $this->getHeaderLayoutData();
	}
}