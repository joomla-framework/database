<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Inspector for the \Joomla\Language\Language class.
 *
 * @since  1.0
 */
class JLanguageInspector extends \Joomla\Language\Language
{
	/**
	 * Method for inspecting protected variables.
	 *
	 * @param   string  $name  Property name.
	 *
	 * @return  mixed  The value of the class variable.
	 *
	 * @since   1.0
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}
		else
		{
			trigger_error('Undefined or private property: ' . __CLASS__ . '::' . $name, E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Sets any property from the class.
	 *
	 * @param   string  $property  The name of the class property.
	 * @param   string  $value     The value of the class property.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Calls any inaccessible method from the class.
	 *
	 * @param   string      $name        Name of the method to invoke
	 * @param   array|bool  $parameters  Parameters to be handed over to the original method
	 *
	 * @return  mixed The return value of the method
	 *
	 * @since   1.0
	 */
	public function __call($name, $parameters = false)
	{
		return call_user_func_array(array($this, $name), $parameters);
	}

	/**
	 * Allows the internal singleton to be set and mocked.
	 *
	 * @param   \Joomla\Language\Language  $instance  A language object.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setInstance($instance)
	{
		self::$instance = $instance;
	}
}
