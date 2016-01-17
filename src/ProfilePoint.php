<?php
/**
 * Part of the Joomla Framework Profiler Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Profiler;

/**
 * Implementation of ProfilePointInterface.
 *
 * @since  1.0
 */
class ProfilePoint implements ProfilePointInterface
{
	/**
	 * The profile point name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name;

	/**
	 * The elapsed time in seconds since the first point in the profiler it belongs to was marked.
	 *
	 * @var    float
	 * @since  1.0
	 */
	protected $time;

	/**
	 * The allocated amount of memory in bytes since the first point in the profiler it belongs to was marked.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $memoryBytes;

	/**
	 * Constructor.
	 *
	 * @param   string   $name         The point name.
	 * @param   float    $time         The time in seconds.
	 * @param   integer  $memoryBytes  The allocated amount of memory in bytes
	 *
	 * @since   1.0
	 */
	public function __construct($name, $time = 0.0, $memoryBytes = 0)
	{
		$this->name = $name;
		$this->time = (float) $time;
		$this->memoryBytes = (int) $memoryBytes;
	}

	/**
	 * Get the name of this profile point.
	 *
	 * @return  string  The name of this profile point.
	 *
	 * @since   1.0
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the elapsed time in seconds since the first point in the profiler it belongs to was marked.
	 *
	 * @return  float  The time in seconds.
	 *
	 * @since   1.0
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * Get the allocated amount of memory in bytes since the first point in the profiler it belongs to was marked.
	 *
	 * @return  integer  The amount of allocated memory in B.
	 *
	 * @since   1.0
	 */
	public function getMemoryBytes()
	{
		return $this->memoryBytes;
	}

	/**
	 * Get the allocated amount of memory in mega bytes
	 * since the first point in the profiler it belongs to was marked.
	 *
	 * @return  integer  The amount of allocated memory in MB.
	 *
	 * @since   1.0
	 */
	public function getMemoryMegaBytes()
	{
		return $this->memoryBytes / 1048576;
	}
}
