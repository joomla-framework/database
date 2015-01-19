<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer;

use League\Plates\Engine;

/**
 * Plates class for rendering output.
 *
 * @since  1.0
 */
class PlatesRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Configuration array
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $config = array(
		'path'      => null,
		'debug'     => false,
		'extension' => '.tpl'
	);

	/**
	 * Rendering engine
	 *
	 * @var    Engine
	 * @since  1.0
	 */
	private $renderer;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration array
	 *
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		$this->config = array_merge($this->config, (array) $config);

		$this->renderer = new Engine($this->config['path'], ltrim($this->config['extension'], '.'));
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $alias      The folder alias
	 * @param   string  $directory  The folder path
	 *
	 * @return  PlatesRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function addFolder($alias, $directory)
	{
		$this->getRenderer()->addFolder($alias, $directory);

		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  Engine
	 *
	 * @since   1.0
	 */
	public function getRenderer()
	{
		return $this->renderer;
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
	 * @return  PlatesRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function setFileExtension($extension)
	{
		$this->getRenderer()->setFileExtension($extension);

		return $this;
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
		return $this->getRenderer()->pathExists($path);
	}
}
