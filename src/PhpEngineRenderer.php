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
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * PhpEngine template renderer
 *
 * @since  1.0
 */
class PhpEngineRenderer extends AbstractRenderer implements RendererInterface
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
	private $renderer;

	/**
	 * Constructor
	 *
	 * @param   TemplateNameParserInterface  $parser  Object to parese template names
	 * @param   LoaderInterface              $loader  Object to direct the engine where to search for templates
	 * @param   PhpEngine|null               $engine  Optional PhpEngine instance to inject or null for a new object to be created
	 *
	 * @since   1.0
	 */
	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, PhpEngine $engine = null)
	{
		$this->renderer = is_null($engine) ? new PhpEngine($parser, $loader) : $engine;
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
		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  PhpEngine
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
		return $this->getRenderer()->exists($path);
	}

	/**
	 * Render and return compiled data.
	 *
	 * @param   string|TemplateReferenceInterface  $template  A template name or a TemplateReferenceInterface instance
	 * @param   array                              $data      The data to pass to the template
	 *
	 * @return  string  Compiled data
	 *
	 * @since   1.0
	 */
	public function render($template, array $data = array())
	{
		$data = array_merge($this->data, $data);

		return $this->getRenderer()->render($template, $data);
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
		return $this;
	}
}
