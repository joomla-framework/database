<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * PhpEngine template renderer
 *
 * @since  __DEPLOY_VERSION__
 */
class PhpEngineRenderer extends AbstractRenderer
{
	/**
	 * Rendering engine
	 *
	 * @var    PhpEngine
	 * @since  __DEPLOY_VERSION__
	 */
	private $renderer;

	/**
	 * Constructor
	 *
	 * @param   TemplateNameParserInterface  $parser  Object to parse template names
	 * @param   LoaderInterface              $loader  Object to direct the engine where to search for templates
	 * @param   PhpEngine|null               $engine  Optional PhpEngine instance to inject or null for a new object to be created
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, PhpEngine $engine = null)
	{
		$this->renderer = is_null($engine) ? new PhpEngine($parser, $loader) : $engine;
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
		// TODO: Implement addFolder() method.
		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  PhpEngine
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
	 * @since   __DEPLOY_VERSION__
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
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setFileExtension($extension)
	{
		// TODO: Implement setFileExtension() method.
		return $this;
	}
}
