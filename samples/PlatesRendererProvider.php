<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Renderer\PlatesRenderer;
use League\Plates\Engine;

/**
 * Plates renderer service provider
 *
 * @since  1.0
 */
class PlatesRendererProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
	{
		$container->set(
			'Joomla\Renderer\RendererInterface',
			function (Container $container) {
				/* @type  \Joomla\Registry\Registry  $config */
				$config = $container->get('config');

				$engine = new Engine($config->get('template.path'), $config->get('template.extension', 'php'));
				$engine->addFolder('partials', $config->get('template.partials'));

				return new PlatesRenderer($engine);
			},
			true,
			true
		);

		$container->alias('renderer', 'Joomla\Renderer\RendererInterface');

		return;
	}
}
