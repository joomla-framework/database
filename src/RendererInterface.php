<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Rendering interface.
 *
 * @since  2.0.0
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
	 * @since   2.0.0
	 */
	public function pathExists(string $path): bool;

	/**
	 * Get the rendering engine
	 *
	 * @return  mixed
	 *
	 * @since   2.0.0
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
	 * @since   2.0.0
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
	 * @since   2.0.0
	 */
	public function set(string $key, $value);

	/**
	 * Loads data from array into the renderer
	 *
	 * @param   array  $data  Array of variables
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function setData(array $data);

	/**
	 * Unloads data from renderer
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function unsetData();
}
