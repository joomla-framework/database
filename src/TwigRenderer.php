<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

use Joomla\Renderer\Twig\FilesystemLoader;

/**
 * Twig class for rendering output.
 *
 * @since  __DEPLOY_VERSION__
 */
class TwigRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Configuration array
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	private $config = array(
		'path'      => null,
		'debug'     => false,
		'extension' => '.twig'
	);

	/**
	 * Rendering engine
	 *
	 * @var    \Twig_Environment
	 * @since  __DEPLOY_VERSION__
	 */
	private $renderer;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		$this->config = array_merge($this->config, (array) $config);

		$loader = new FilesystemLoader($this->config['path']);
		$loader->setExtension($this->config['extension']);

		$this->renderer = new \Twig_Environment($loader, $this->config);
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
	public function addFolder($directory, $alias = null)
	{
		if ($alias === null)
		{
			$alias = \Twig_Loader_Filesystem::MAIN_NAMESPACE;
		}

		$this->getRenderer()->getLoader()->addPath($directory, $alias);
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  \Twig_Environment
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
	public function pathExists($path)
	{
		return $this->getRenderer()->getLoader()->exists($path);
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
	public function render($template, array $data = array())
	{
		$data = array_merge($this->data, $data);

		// TODO Process template name

		return $this->getRenderer()->render($template, $data);
	}

	/**
	 * Sets file extension for template loader
	 *
	 * @param   string  $extension  Template files extension
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setFileExtension($extension)
	{
		$this->config['extension'] = $extension;

		return $this;
	}

	/**
	 * Unloads data from renderer
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function unsetData()
	{
		$this->data = array();

		return $this;
	}
}
