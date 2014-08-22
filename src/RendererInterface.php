<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer;

/**
 * Rendering interface.
 *
 * @since  1.0
 */
interface RendererInterface
{
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
	public function render($template, array $data = array());
	
	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param  string  $alias      The folder alias
	 * @param  string  $directory  The folder path
	 * 
	 * @return  boolean  TRUE if the folder is loaded
	 *
	 * @since 1.0
	 */
	public function addFolder($alias, $directory);
	
	/**
	 * Sets file extension for template loader
	 *
	 * @param  string  $extension  Template files extension
	 *
	 * @return  void
	 *
	 * @since 1.0
	 */
	public function setFileExtension($extension);
	
	/**
	 * Checks if folder, folder alias, template or template path exists
	 *
	 * @param  string  $path  Full path or part of a path
	 *
	 * @return  boolean  TRUE of the path exists
	 *
	 * @since  1.0
	 */
	public function pathExists($path);
	
	/**
	 * Loads data from array into the renderer
	 *
	 * @param  array  $data  Array of variables
	 *
	 * @return boolean  TRUE if data loaded successfully
	 *
	 * @since  1.0
	 */
	public function setData($data);
	
	/**
	 * Unloads data from renderer
	 *
	 * @return void
	 *
   * @since  1.0
	 */
	public function unsetData();
	
	/**
	 * Sets a piece of data
	 *
	 * @param  string  $key    Name of variable
	 * @param  string  $value  Value of variable
	 *
	 * @return RendererInterface  Returns self for chaining
   *
   * @since  1.0
	 */
	public function set($key, $value);
	
}
