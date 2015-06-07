<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer;

/**
 * Mustache class for rendering output.
 *
 * @since  1.0
 */
class MustacheRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Rendering engine
	 *
	 * @var    \Mustache_Engine
	 * @since  1.0
	 */
	private $renderer;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->renderer = new \Mustache_Engine;
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $directory  The folder path
	 * @param   string  $alias      The folder alias
	 *
	 * @return  MustacheRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function addFolder($directory, $alias = null)
	{
		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  \Mustache_Engine
	 *
	 * @since   1.0
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
	 * @since   1.0
	 */
	public function pathExists($path)
	{
		try
		{
			$this->getRenderer()->getLoader()->load($name);

			return true;
		}
		catch (Mustache_Exception_UnknownTemplateException $e)
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
	 * @since   1.0
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
	 * @return  MustacheRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function setFileExtension($extension)
	{
		return $this;
	}
}
