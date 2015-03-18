<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Abstract class for templates renderer
 *
 * @since  1.0
 */
abstract class AbstractRenderer implements RendererInterface
{
	/**
	 * Data for output by the renderer
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $data = array();

	/**
	 * Sets a piece of data
	 *
	 * @param   string  $key    Name of variable
	 * @param   string  $value  Value of variable
	 *
	 * @return  AbstractRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function set($key, $value)
	{
		// TODO Make use of Joomla\Registry to provide paths
		$this->data[$key] = $value;

		return $this;
	}

	/**
	 * Loads data from array into the renderer
	 *
	 * @param   array  $data  Array of variables
	 *
	 * @return  AbstractRenderer  Returns self for chaining
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
	 * @return  AbstractRenderer  Returns self for chaining
	 *
	 * @since   1.0
	 */
	public function unsetData()
	{
		$this->data = array();

		return $this;
	}
}
