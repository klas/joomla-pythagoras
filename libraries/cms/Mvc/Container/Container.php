<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Mvc
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Mvc\Container;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container as DIContainer;

class Container extends DIContainer
{
	/** @var null|bool Is this a CLI application? */
	protected static $isCLI = null;

	/** @var null|bool Is this an administrator application? */
	protected static $isAdmin = null;

	/**
	 * Creates a new DI container.
	 *
	 * @param   DIContainer  $parent  The parent DI container, optional
	 * @param   array        $values
	 */
	public function __construct(DIContainer $parent = null, array $values = [])
	{
		if (!is_object($parent))
		{
			// TODO Use the application container?
		}

		DIContainer::__construct($parent);

		if (!empty($values))
		{
			foreach ($values as $key => $value)
			{
				$this->set($key, $value);
			}
		}
	}

	/**
	 * Magic getter for alternative syntax, e.g. $container->foo instead of $container->get('foo'). Allows for type
	 * hinting using the property and property-read PHPdoc macros at the top of the container.
	 *
	 * @param   string  $name  The key of the data storage
	 *
	 * @return  mixed
	 */
	function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * Magic setter for alternative syntax, e.g. $container->foo = $value instead of $container->set('foo', $value).
	 * Allows for type hinting using the property and property-read PHPdoc macros at the top of the container.
	 *
	 * @param   string  $name   The key of the data storage
	 * @param   mixed   $value  The value to set in the data storage
	 */
	function __set($name, $value)
	{
		$this->set($name, $value);
	}

	// TODO Move these into a helper?

	/**
	 * Main function to detect if we're running in a CLI environment and we're admin
	 *
	 * @return  array  isCLI and isAdmin. It's not an associative array, so we can use list.
	 */
	protected static function isCliAdmin()
	{
		if (is_null(static::$isCLI) && is_null(static::$isAdmin))
		{
			try
			{
				if (is_null(\JFactory::$application))
				{
					static::$isCLI = true;
				}
				else
				{
					$app = \JFactory::getApplication();
					static::$isCLI = $app instanceof \Exception || $app instanceof \JApplicationCli;
				}
			}
			catch (\Exception $e)
			{
				static::$isCLI = true;
			}

			if (static::$isCLI)
			{
				static::$isAdmin = false;
			}
			else
			{
				static::$isAdmin = !\JFactory::$application ? false : \JFactory::getApplication()->isAdmin();
			}
		}

		return array(static::$isCLI, static::$isAdmin);
	}

	/**
	 * Is this the administrative section of the component?
	 *
	 * @see PlatformInterface::isBackend()
	 *
	 * @return  boolean
	 */
	public static function isBackend()
	{
		list ($isCli, $isAdmin) = self::isCliAdmin();

		return $isAdmin && !$isCli;
	}

	/**
	 * Is this the public section of the component?
	 *
	 * @see PlatformInterface::isFrontend()
	 *
	 * @return  boolean
	 */
	public static function isFrontend()
	{
		list ($isCli, $isAdmin) = self::isCliAdmin();

		return !$isAdmin && !$isCli;
	}

	/**
	 * Is this a component running in a CLI application?
	 *
	 * @see PlatformInterface::isCli()
	 *
	 * @return  boolean
	 */
	public static function isCli()
	{
		list ($isCli, $isAdmin) = self::isCliAdmin();

		return !$isAdmin && $isCli;
	}
}