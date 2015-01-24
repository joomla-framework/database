<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014-2015 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Service;

use BabDev\Renderer\PhpEngineRenderer;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * PhpEngine renderer service provider
 *
 * @since  1.0
 */
class PHPRendererProvider implements ServiceProviderInterface
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
			'BabDev\Renderer\RendererInterface',
			function (Container $container) {
				/* @type  \Joomla\Registry\Registry  $config */
				$config = $container->get('config');

				$loader = new FilesystemLoader(array($config->get('template.path')));

				return new PhpEngineRenderer(new TemplateNameParser, $loader);
			},
			true,
			true
		);

		$container->alias('renderer', 'BabDev\Renderer\RendererInterface');

		return;
	}
}
