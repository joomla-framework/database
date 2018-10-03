<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Rendering interface.
 *
 * @since  __DEPLOY_VERSION__
 */
interface RendererInterface
{
	/**
	 * Checks if folder, folder alias, template or template path exists
	 *
	 * @param   string  $path  Full path or part of a path
	 *
	 * @return  boolean  True if the path exists
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function pathExists(string $path): bool;

	/**
	 * Get the rendering engine
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getRenderer();

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
	public function render(string $template, array $data = []): string;

	/**
	 * Sets a piece of data
	 *
	 * @param   string  $key    Name of variable
	 * @param   string  $value  Value of variable
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function set(string $key, $value);

	/**
	 * Loads data from array into the renderer
	 *
	 * @param   array  $data  Array of variables
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setData(array $data);

	/**
	 * Unloads data from renderer
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function unsetData();
}
