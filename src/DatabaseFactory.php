<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Joomla Framework Database Factory class
 *
 * @since  1.0
 */
class DatabaseFactory
{
	/**
	 * Contains the current Factory instance
	 *
	 * @var    DatabaseFactory
	 * @since  1.0
	 * @deprecated  1.4.0  Instantiate a new factory object as needed
	 */
	private static $instance = null;

	/**
	 * Method to return a DatabaseDriver instance based on the given options.
	 *
	 * There are three global options and then the rest are specific to the database driver. The 'database' option determines which database is to
	 * be used for the connection. The 'select' option determines whether the connector should automatically select the chosen database.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver.
	 *
	 * @return  DatabaseDriver
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getDriver($name = 'mysqli', $options = array())
	{
		// Sanitize the database connector options.
		$options['driver']   = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$options['database'] = isset($options['database']) ? $options['database'] : null;
		$options['select']   = isset($options['select']) ? $options['select'] : true;

		// Derive the class name from the driver.
		$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($options['driver'])) . '\\' . ucfirst(strtolower($options['driver'])) . 'Driver';

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new Exception\UnsupportedAdapterException(sprintf('Unable to load Database Driver: %s', $options['driver']));
		}

		// Create our new Driver connector based on the options given.
		try
		{
			return new $class($options);
		}
		catch (\RuntimeException $e)
		{
			throw new Exception\ConnectionFailureException(sprintf('Unable to connect to the Database: %s', $e->getMessage()), $e->getCode(), $e);
		}
	}

	/**
	 * Gets an exporter class object.
	 *
	 * @param   string          $name  Name of the driver you want an exporter for.
	 * @param   DatabaseDriver  $db    Optional DatabaseDriver instance to inject into the exporter.
	 *
	 * @return  DatabaseExporter
	 *
	 * @since   1.0
	 * @throws  Exception\UnsupportedAdapterException
	 */
	public function getExporter($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($name)) . '\\' . ucfirst(strtolower($name)) . 'Exporter';

		// Make sure we have an exporter class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new Exception\UnsupportedAdapterException('Database Exporter not found.');
		}

		/** @var $o DatabaseExporter */
		$o = new $class;

		if ($db instanceof DatabaseDriver)
		{
			$o->setDbo($db);
		}

		return $o;
	}

	/**
	 * Gets an importer class object.
	 *
	 * @param   string          $name  Name of the driver you want an importer for.
	 * @param   DatabaseDriver  $db    Optional DatabaseDriver instance to inject into the importer.
	 *
	 * @return  DatabaseImporter
	 *
	 * @since   1.0
	 * @throws  Exception\UnsupportedAdapterException
	 */
	public function getImporter($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($name)) . '\\' . ucfirst(strtolower($name)) . 'Importer';

		// Make sure we have an importer class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new Exception\UnsupportedAdapterException('Database importer not found.');
		}

		/** @var $o DatabaseImporter */
		$o = new $class;

		if ($db instanceof DatabaseDriver)
		{
			$o->setDbo($db);
		}

		return $o;
	}

	/**
	 * Gets an instance of the factory object.
	 *
	 * @return  DatabaseFactory
	 *
	 * @since   1.0
	 * @deprecated  1.4.0  Instantiate a new factory object as needed
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::setInstance(new static);
		}

		return self::$instance;
	}

	/**
	 * Get the current query object or a new Query object.
	 *
	 * @param   string          $name  Name of the driver you want an query object for.
	 * @param   DatabaseDriver  $db    Optional Driver instance
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0
	 * @throws  Exception\UnsupportedAdapterException
	 */
	public function getQuery($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($name)) . '\\' . ucfirst(strtolower($name)) . 'Query';

		// Make sure we have a query class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new Exception\UnsupportedAdapterException('Database Query class not found');
		}

		return new $class($db);
	}

	/**
	 * Gets an instance of a factory object to return on subsequent calls of getInstance.
	 *
	 * @param   DatabaseFactory  $instance  A Factory object.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @deprecated  1.4.0  Instantiate a new factory object as needed
	 */
	public static function setInstance(DatabaseFactory $instance = null)
	{
		self::$instance = $instance;
	}
}
