<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

use Twig\Environment;
use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * Twig class for rendering output.
 *
 * @since  __DEPLOY_VERSION__
 */
class TwigRenderer extends AbstractRenderer implements AddTemplateFolderInterface
{
	/**
	 * Rendering engine
	 *
	 * @var    Environment
	 * @since  __DEPLOY_VERSION__
	 */
	private $renderer;

	/**
	 * Constructor.
	 *
	 * @param   Environment  $renderer  Rendering engine
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Environment $renderer = null)
	{
		$this->renderer = $renderer ?: new Environment(new FilesystemLoader);
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $directory  The folder path
	 * @param   string  $alias      The folder alias
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addFolder(string $directory, string $alias = '')
	{
		$loader = $this->getRenderer()->getLoader();

		// This can only be reliably tested with a loader using the filesystem loader's API
		if (method_exists($loader, 'addPath'))
		{
			if ($alias === '')
			{
				$alias = FilesystemLoader::MAIN_NAMESPACE;
			}

			$loader->addPath($directory, $alias);
		}

		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  Environment
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * Checks if folder, folder alias, template or template path exists
	 *
	 * @param   string  $path  Full path or part of a path
	 *
	 * @return  boolean  True if the path exists
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function pathExists(string $path): bool
	{
		$loader = $this->getRenderer()->getLoader();

		/*
		 * For Twig 1.x compatibility, check if the loader implements ExistsLoaderInterface
		 * As of Twig 2.0, the `exists()` method is part of LoaderInterface
		 * This conditional may be removed when dropping Twig 1.x support
		 */
		if ($loader instanceof ExistsLoaderInterface || method_exists(LoaderInterface::class, 'exists'))
		{
			return $loader->exists($path);
		}

		// For all other cases we'll assume the path exists
		return true;
	}

	/**
	 * Render and return compiled data.
	 *
	 * @param   string  $template  The template file name
	 * @param   array   $data      The data to pass to the template
	 *
	 * @return  string  Compiled data
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function render(string $template, array $data = []): string
	{
		$data = array_merge($this->data, $data);

		return $this->getRenderer()->render($template, $data);
	}
}
