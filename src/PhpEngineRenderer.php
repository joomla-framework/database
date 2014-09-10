<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * PhpEngine template renderer
 *
 * @since  1.0
 */
class PhpEngineRenderer implements RendererInterface
{
	/**
	 * Data for output by the renderer
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $data = array();

	/**
	 * Rendering engine
	 *
	 * @var    PhpEngine
	 * @since  1.0
	 */
	private $engine;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader)
	{
		$this->engine = new PhpEngine($parser, $loader);
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
		$data = array_merge($this->data, $data);

		$this->engine->render($template, $data);
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $alias      The folder alias
	 * @param   string  $directory  The folder path
	 *
	 * @return  PhpEngineRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function addFolder($alias, $directory)
	{
		// TODO: Implement addFolder() method.
	}

	/**
	 * Sets file extension for template loader
	 *
	 * @param   string  $extension  Template files extension
	 *
	 * @return  PhpEngineRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function setFileExtension($extension)
	{
		// TODO: Implement setFileExtension() method.
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
		return $this->engine->exists($path);
	}

	/**
	 * Loads data from array into the renderer
	 *
	 * @param   array  $data  Array of variables
	 *
	 * @return  PhpEngineRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Unloads data from renderer
	 *
	 * @return  PhpEngineRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function unsetData()
	{
		$this->data = array();

		return $this;
	}

	/**
	 * Sets a piece of data
	 *
	 * @param   string  $key    Name of variable
	 * @param   string  $value  Value of variable
	 *
	 * @return  PhpEngineRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function set($key, $value)
	{
		// TODO: Implement set() method.
	}
}
