<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * Blade class for rendering output.
 *
 * @since  __DEPLOY_VERSION__
 */
class BladeRenderer extends AbstractRenderer
{
	/**
	 * Rendering engine
	 *
	 * @var    Factory
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
	public function __construct(Factory $renderer = null)
	{
		if (!$renderer)
		{
			$filesystem = new Filesystem;

			$resolver = new EngineResolver;
			$resolver->register(
				'blade',
				function () use ($filesystem)
				{
					return new CompilerEngine(new BladeCompiler($filesystem));
				}
			);

			$renderer = new Factory(
				$resolver,
				new FileViewFinder($filesystem, []),
				new Dispatcher
			);
		}

		$this->renderer = $renderer;
	}

	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $directory  The folder path
	 * @param   string  $alias      The folder alias (unused)
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addFolder($directory, $alias = null)
	{
		$this->getRenderer()->addLocation($directory);

		return $this;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  Factory
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

		return $this->getRenderer()->make($template, $data)->render();
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
