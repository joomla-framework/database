<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Service;

use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Database service provider
 *
 * @since  __DEPLOY_VERSION__
 */
class DatabaseProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function register(Container $container)
	{
		$container->share(
			'Joomla\\Database\\DatabaseDriver',
			function () use ($container)
			{
				$config = $container->get('config');

				$options = array(
					'driver' => $config->get('database.driver'),
					'host' => $config->get('database.host'),
					'user' => $config->get('database.user'),
					'password' => $config->get('database.password'),
					'database' => $config->get('database.name'),
					'prefix' => $config->get('database.prefix')
				);

				$db = DatabaseDriver::getInstance($options);
				$db->setDebug($config->get('database.debug', false));

				return $db;
			}, true
		);
	}
}
