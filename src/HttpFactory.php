<?php
/**
 * Part of the Joomla Framework Http Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http;

/**
 * HTTP factory class.
 *
 * @since  1.0
 */
class HttpFactory
{
	/**
	 * Method to create an Http instance.
	 *
	 * @param   array|\ArrayAccess  $options   Client options array.
	 * @param   array|string        $adapters  Adapter (string) or queue of adapters (array) to use for communication.
	 *
	 * @return  Http
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 */
	public static function getHttp($options = array(), $adapters = null)
	{
		if (!is_array($options) && !($options instanceof \ArrayAccess))
		{
			throw new \InvalidArgumentException(
				'The options param must be an array or implement the ArrayAccess interface.'
			);
		}

		if (!$driver = self::getAvailableDriver($options, $adapters))
		{
			throw new \RuntimeException('No transport driver available.');
		}

		return new Http($options, $driver);
	}

	/**
	 * Finds an available TransportInterface object for communication
	 *
	 * @param   array|\ArrayAccess  $options  Options for creating TransportInterface object
	 * @param   array|string        $default  Adapter (string) or queue of adapters (array) to use
	 *
	 * @return  TransportInterface|boolean  Interface sub-class or boolean false if no adapters are available
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public static function getAvailableDriver($options = array(), $default = null)
	{
		if (!is_array($options) && !($options instanceof \ArrayAccess))
		{
			throw new \InvalidArgumentException(
				'The options param must be an array or implement the ArrayAccess interface.'
			);
		}

		if (is_null($default))
		{
			$availableAdapters = self::getHttpTransports();
		}
		else
		{
			settype($default, 'array');
			$availableAdapters = $default;
		}

		// Check if there is at least one available http transport adapter
		if (!count($availableAdapters))
		{
			return false;
		}

		foreach ($availableAdapters as $adapter)
		{
			/* @var  $class  TransportInterface */
			$class = 'Joomla\\Http\\Transport\\' . ucfirst($adapter);

			if (class_exists($class))
			{
				if ($class::isSupported())
				{
					return new $class($options);
				}
			}
		}

		return false;
	}

	/**
	 * Get the HTTP transport handlers
	 *
	 * @return  array  An array of available transport handlers
	 *
	 * @since   1.0
	 */
	public static function getHttpTransports()
	{
		$names = array();
		$iterator = new \DirectoryIterator(__DIR__ . '/Transport');

		/*  @var  $file  \DirectoryIterator */
		foreach ($iterator as $file)
		{
			$fileName = $file->getFilename();

			// Only load for php files.
			if ($file->isFile() && $file->getExtension() == 'php')
			{
				$names[] = substr($fileName, 0, strrpos($fileName, '.'));
			}
		}

		// Keep alphabetical order across all environments
		sort($names);

		// If curl is available set it to the first position
		$key = array_search('Curl', $names);

		if ($key)
		{
			unset($names[$key]);
			array_unshift($names, 'Curl');
		}

		return $names;
	}
}
