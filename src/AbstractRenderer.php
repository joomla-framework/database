<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Abstract class for templates renderer
 *
 * @since  2.0.0
 */
abstract class AbstractRenderer implements RendererInterface
{
	/**
	 * Data for output by the renderer
	 *
	 * @var    array
	 * @since  2.0.0
	 */
	protected $data = [];

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
	public function set(string $key, $value)
	{
		$this->data[$key] = $value;

		return $this;
	}

	/**
	 * Loads data from array into the renderer
	 *
	 * @param   array  $data  Array of variables
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Unloads data from renderer
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function unsetData()
	{
		$this->data = [];

		return $this;
	}
}
