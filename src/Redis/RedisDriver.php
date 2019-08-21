<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Redis;

use Joomla\CMS\Factory;
use Psr\Log;
use Redis;


/**
 * Redis Database Driver
 *
 * @since  1.5.0
 */
class RedisDriver implements Log\LoggerAwareInterface
{
	/**
	 * The minimum supported database version.
	 *
	 * @var    string
	 * @since  1.5.0
	 */
	protected static $dbMinimum = '3.4.0';

	/**
	 * A logger.
	 *
	 * @var    Log\LoggerInterface
	 * @since  1.0
	 */
	private $logger;

	/**
	 * Redis connection object
	 *
	 * @var    \Redis
	 * @since  3.4
	 */
	protected static $instances = null;
	protected static $connected = false;


	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 *
	 * @since   3.4
	 */

	public function __construct($options = array())
	{
		if (!\extension_loaded('redis') || !class_exists('\Redis'))
		{
			throw new \RuntimeException('Redis not supported.');
		}
		parent::__construct($options);
		
	}


	
	


	/**
	 * Test to see if the storage handler is available.
	 *
	 * @return  boolean
	 *
	 * @since   3.4
	 */
	public static function isSupported()
	{
		return (\extension_loaded('redis') && class_exists('\Redis'));
	}

	/**
	 * Test to see if the Redis connection is available.
	 *
	 * @return  boolean
	 *
	 * @since   3.4
	 */
	public static function isConnected()
	{
		//return this instanceof \Redis;
		return self::$connected;
	}

	/**
	 * Method to return a RedisDriver instance based on the given options.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   array  $options  Parameters to be passed to the database driver.
	 *
	 * @return  RedisDriver  A redis database object.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function getInstance($options = array())
	{

		$app = Factory::getApplication();

		// Sanitize the redis connector options.
		$options['driver'] = 'redis';
		$options['host']   = isset($options['host']) ? $options['host'] : $app->get('redis_server_host', 'localhost');
		$options['port']   = isset($options['port']) ? $options['host'] : $app->get('redis_server_port', 6379);
		$options['auth']   = isset($options['auth']) ? $options['auth'] : $app->get('redis_server_auth', null);
		$options['db']     = isset($options['db']) ? $options['db'] : (int) $app->get('redis_server_db', null);

		$options['persistent']   = isset($options['persistent']) ? $options['persistent'] : true;

		// If you are trying to connect to a socket file, ignore the supplied port  ???
		if ($options['host'][0] === '/')
		{
					$options['port'] = 0;
		}

		// Get the options signature for the redis connector.
		$signature = md5(serialize($options));
		// If we already have a redis connector instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{

			$instance = new \Redis;

			// Create our new RedisDriver connector based on the options given.
			try
			{
				if ($options['persistent'])
				{
					$instance->pconnect($options['host'],$options['port']);
				}
				else
				{
					$instance->connect($options['host'],$options['port']);
				}
			}
			catch (\RedisException $e)
			{
				$instance = null;
				throw new \RuntimeException(sprintf('Unable to connect to Redis: %s', $e->getMessage()));
			}
		
			try
			{
				$options['auth'] ? $instance->auth($server['auth']) : true;
			}
			catch (\RedisException $e)
			{
				$instance = null;
				throw new \RuntimeException(sprintf('Redis authentication failed: %s', $e->getMessage()));
			}

			try
			{
				$instance->ping();
			}
			catch (\RedisException $e)
			{
				$instance = null;
				throw new \RuntimeException('Redis ping failed', 500);
			}
			// Set the new connector to the global instances based on signature.
			self::$instances[$signature] = $instance;
			self::$connected = true;
		}
		return self::$instances[$signature];
	}


	/**
	 * Logs a message.
	 *
	 * @param   string  $level    The level for the log. Use constants belonging to Psr\Log\LogLevel.
	 * @param   string  $message  The message.
	 * @param   array   $context  Additional context.
	 *
	 * @return  DatabaseDriver  Returns itself to allow chaining.
	 *
	 * @since   1.0
	 */
	public function log($level, $message, array $context = array())
	{
		if ($this->logger)
		{
			$this->logger->log($level, $message, $context);
		}
		return $this;
	}

	/**
	 * Sets a logger instance on the object
	 *
	 * @param   Log\LoggerInterface  $logger  A PSR-3 compliant logger.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setLogger(Log\LoggerInterface $logger)
	{
		$this->logger = $logger;
	}
}
