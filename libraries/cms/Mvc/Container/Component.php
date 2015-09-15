<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Mvc
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Mvc\Container;

use Joomla\Cms\Mvc as Mvc;
use Joomla\DI\Container as DIContainer;

defined('JPATH_PLATFORM') or die;

/**
 * Dependency injection container for Components
 *
 * @property  string                                   $componentName      The name of the component (com_something)
 * @property  string                                   $bareComponentName  The name of the component without com_ (something)
 * @property  string                                   $componentNamespace The namespace of the component's classes (\Foobar)
 * @property  string                                   $frontEndPath       The absolute path to the front-end files
 * @property  string                                   $backEndPath        The absolute path to the back-end files
 * @property  string                                   $thisPath           The preferred path. Backend for Admin application, frontend otherwise
 *
 * @property-read   Mvc\Inflector\Inflector            $inflector          The English word inflector (pluralise / singularise words etc)
 */
class Component extends Container
{
	/**
	 * Cache of created container instances
	 *
	 * @var   array
	 */
	protected static $instances = array();

	/**
	 * Default services registered in the container
	 *
	 * @var   array
	 */
	protected static $defaultServices = [
		'\\Joomla\\Cms\\Mvc\\Container\\Provider\\Inflector'
	];

	/**
	 * Default aliases
	 *
	 * @var   array
	 */
	protected static $defaultAliases = [
		'inflector'		=> '\\Joomla\\Cms\\Mvc\\Inflector\\Inflector'
	];

	/**
	 * Returns the container instance for a specific component. If the instance exists it returns the one already
	 * constructed.
	 *
	 * @param   string     $component  The name of the component, e.g. com_example
	 * @param   array      $values     Configuration override values
	 * @param   Container  $parent     Optional. Parent container (default: application container)
	 *
	 * @return  Component
	 */
	public static function &getInstance($component, array $values = [], Container $parent = null)
	{
		if (!isset(self::$instances[$component]))
		{
			self::$instances[$component] = self::getInstance($component, $parent);
		}

		return self::$instances[$component];
	}

	/**
	 * Returns a temporary container instance for a specific component. A new instance is created with every call.
	 *
	 * @param   string     $component  The name of the component, e.g. com_example
	 * @param   array      $values     Configuration override values
	 * @param   Container  $parent     Optional. Parent container (default: application container)
	 *
	 * @return  Component
	 */
	public static function &getTempInstance($component, array $values = [], Container $parent = null)
	{
		// Get a temporary container. It allows us to get access to namespaces and register the component's autoloader.
		$tempConfig    = array_merge($values, ['componentName' => $component]);
		$tempContainer = new self($tempConfig, $parent);

		// Get the fully qualified class name for a specialised component container
		$isAdmin   = self::isBackend();
		$namespace = $tempContainer->componentNamespace . '\\' . ($isAdmin ? 'Admin' : 'Site');
		$className = $namespace . '\\Container';

		if (!class_exists($className, true))
		{
			$className = __CLASS__;
		}

		$container = new $className($values, $parent);

		return $container;
	}

	/**
	 * @param   array        $values
	 * @param   DIContainer  $parent  The parent DI container, optional
	 */
	public function __construct(array $values = [], DIContainer $parent = null)
	{
		if (!isset($values['componentName']))
		{
			if (!is_object($parent) || !$parent->exists('componentName'))
			{
				throw new Exception\NoComponent;
			}

			$values['componentName'] = $parent->get('componentName');
		}

		$defaultValues = [
			'bareComponentName'		=> substr($values['componentName'], 4),
			'componentNamespace'	=> self::getVendor($values['componentName']) . '\\' . ucfirst(substr($values['componentName'], 4)),
			'frontEndPath'			=> JPATH_SITE . '/components/' . $values['componentName'],
			'backEndPath'			=> JPATH_ADMINISTRATOR . '/components/' . $values['componentName'],
		];

		$values = array_merge($defaultValues, $values);

		// Register the component to the PSR-4 autoloader
		$feNamespace = $values['componentNamespace'] . '\\Site\\';
		$beNamespace = $values['componentNamespace'] . '\\Admin\\';

		/** @var \Composer\Autoload\ClassLoader $autoLoader */
		$autoLoader = include_once(JPATH_LIBRARIES . '/vendor/autoload.php');
		$autoLoader->setPsr4($feNamespace, $this->frontEndPath);
		$autoLoader->setPsr4($beNamespace, $this->backEndPath);

		// Get service definitions and aliases
		$services = self::$defaultServices;
		$aliases = self::$defaultAliases;

		if (isset($values['services']))
		{
			$services = array_merge($services, $values['services']);
			unset($values['services']);
		}

		if (isset($values['aliases']))
		{
			$aliases = array_merge($aliases, $values['aliases']);
			unset($values['aliases']);
		}

		parent::__construct($parent, $values);

		// Set up computed properties
		$isAdmin = self::isBackend();
		$this->thisPath = $isAdmin ? $this->backEndPath : $this->frontEndPath;

		// Set up services
		foreach ($services as $service)
		{
			$provider = $service;

			if (is_string($service))
			{
				$provider = new $service;
			}

			$this->registerServiceProvider($provider);
		}

		// Set up aliases
		foreach ($aliases as $alias => $originalKey)
		{
			$this->alias($alias, $originalKey);
		}
	}

	protected function getVendor($component)
	{
		$vendor = 'Joomla\\Component';

		try
		{
			$manifest = $this->getManifestCache($component);
		}
		catch(\RuntimeException $e)
		{
			// TODO Maybe try to load from the extension's XML manifest directly?

			return $vendor;
		}

		if (isset($manifest['vendor']))
		{
			// Remove  whitespace and ensure all characters are alphanumeric
			$vendor = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-_]/'), array('-', ''), $manifest['vendor']);
		}

		return $vendor;
	}

	/**
	 * Get the cached manifest of a component
	 *
	 * TODO Move this to the component helper?
	 *
	 * @param   string  $component
	 *
	 * @return  array
	 */
	private function getManifestCache($component)
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('manifest_cache')
					->from($db->qn('#__extensions'))
					->where($db->qn('element') . ' = ' . $db->q($component))
					->where($db->qn('type') . ' = ' . $db->q('component'));

		$manifestCacheText = $db->setQuery($query)->loadResult();

		if (empty($manifestCacheText))
		{
			throw new \RuntimeException("Component $component not found");
		}

		$manifestCache = json_decode($manifestCacheText, true);

		if (!is_array($manifestCache))
		{
			throw new \RuntimeException("Cached manifest for component $component does not exist");
		}

		return $manifestCache;
	}
}