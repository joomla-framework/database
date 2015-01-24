<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014-2015 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Service;

use BabDev\Renderer\TwigRenderer;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Twig renderer service provider
 *
 * @since  1.0
 */
class TwigRendererProvider implements ServiceProviderInterface
{
	/**
	 * Configuration instance
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $config;

	/**
	 * Constructor.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct(array $config = array())
	{
		$this->config = $config;
	}

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
		$options = $this->config;

		$container->set(
			'BabDev\Renderer\RendererInterface',
			function () use ($options) {
				$renderer = new TwigRenderer($options);

				// Set the Lexer object
				$renderer->setLexer(
					new \Twig_Lexer($renderer, array('delimiters' => array(
						'tag_comment'  => array('{#', '#}'),
						'tag_block'    => array('{%', '%}'),
						'tag_variable' => array('{{', '}}')
					)))
				);

				return $renderer;
			},
			true,
			true
		);

		$container->alias('renderer', 'BabDev\Renderer\RendererInterface');

		return;
	}
}
