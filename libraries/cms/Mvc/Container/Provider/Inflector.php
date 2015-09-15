<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Mvc
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Mvc\Container\Provider;


use Joomla\Cms\Mvc\Inflector\Inflector as MvcInflector;
use Joomla\DI\Container as DIContainer;
use Joomla\DI\ServiceProviderInterface;

class Inflector implements ServiceProviderInterface
{
	/**
	 * Registers the service provider to the container
	 *
	 * @param   DIContainer  $container
	 */
	public function register(DIContainer $container)
	{
		$container->set('\\Joomla\\Cms\\Mvc\\Inflector\\Inflector', array($this, 'getInflector'));
	}

	/**
	 * Creates a new Inflector
	 *
	 * @param   DIContainer  $c  Our container
	 *
	 * @return  MvcInflector
	 */
	public function getInflector(DIContainer $c)
	{
		return new MvcInflector();
	}
}