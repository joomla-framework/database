<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Mustache class for rendering output.
 *
 * @since  __DEPLOY_VERSION__
 */
class MustacheRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Rendering engine
	 *
	 * @var    \Mustache_Engine
	 * @since  __DEPLOY_VERSION__
	 */
	private $renderer;

	/**
	 * Constructor
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$this->renderer = new \Mustache_Engine;
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $alias      The folder alias
	 * @param   string  $directory  The folder path
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addFolder($alias, $directory)
	{
		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  \Mustache_Engine
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
		try
		{
			$this->getRenderer()->getLoader()->load($name);

			return true;
		}
		catch (\Mustache_Exception_UnknownTemplateException $e)
		{
			return false;
		}
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
		return $this;
	}
}
