<?php
/**
 * Part of the Joomla Framework MediaWiki Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki;

use Joomla\Registry\Registry;

/**
 * Class for interacting with a MediaWiki server instance.
 *
 * @property-read  Joomla\Mediawiki\Sites          $sites          MediaWiki API object for sites.
 * @property-read  Joomla\Mediawiki\Pages          $pages          MediaWiki API object for pages.
 * @property-read  Joomla\Mediawiki\Users          $users          MediaWiki API object for users.
 * @property-read  Joomla\Mediawiki\Links          $links          MediaWiki API object for links.
 * @property-read  Joomla\Mediawiki\Categories     $categories     MediaWiki API object for categories.
 * @property-read  Joomla\Mediawiki\Images         $images         MediaWiki API object for images.
 * @property-read  Joomla\Mediawiki\Search         $search         MediaWiki API object for search.
 *
 * @since  1.0
 */
class Mediawiki
{
	/**
	 * @var    Registry  Options for the MediaWiki object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    Http  The HTTP client object to use in sending HTTP requests.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    Sites  MediaWiki API object for Site.
	 * @since  1.0
	 */
	protected $sites;

	/**
	 * @var    Pages  MediaWiki API object for pages.
	 * @since  1.0
	 */
	protected $pages;

	/**
	 * @var    Users  MediaWiki API object for users.
	 * @since  1.0
	 */
	protected $users;

	/**
	 * @var    Links  MediaWiki API object for links.
	 * @since  1.0
	 */
	protected $links;

	/**
	 * @var    Categories  MediaWiki API object for categories.
	 * @since  1.0
	 */
	protected $categories;

	/**
	 * @var    Images  MediaWiki API object for images.
	 * @since  1.0
	 */
	protected $images;

	/**
	 * @var    Search  MediaWiki API object for search.
	 * @since  1.0
	 */
	protected $search;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  MediaWiki options array.
	 * @param   Http   $client   The HTTP client object.
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $options = null, Http $client = null)
	{
		$this->options = isset($options) ? $options : new Registry;
		$this->client = isset($client) ? $client : new Http($this->options);
	}

	/**
	 * Magic method to lazily create API objects
	 *
	 * @param   string  $name  Name of property to retrieve
	 *
	 * @return  AbstractMediawikiObject  MediaWiki API object (users, reviews, etc).
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function __get($name)
	{
		$name = strtolower($name);
		$class = 'Joomla\\Mediawiki\\' . ucfirst($name);
		$accessible = array(
			'categories',
			'images',
			'links',
			'pages',
			'search',
			'sites',
			'users'
		);

		if (class_exists($class) && in_array($name, $accessible))
		{
			if (!isset($this->$name))
			{
				$this->$name = new $class($this->options, $this->client);
			}

			return $this->$name;
		}

		throw new \InvalidArgumentException(sprintf('Property %s is not accessible.', $name));
	}

	/**
	 * Get an option from the Mediawiki instance.
	 *
	 * @param   string  $key  The name of the option to get.
	 *
	 * @return  mixed  The option value.
	 *
	 * @since   1.0
	 */
	public function getOption($key)
	{
		return $this->options->get($key);
	}

	/**
	 * Set an option for the Mediawiki instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  Mediawiki  This object for method chaining.
	 *
	 * @since   1.0
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);

		return $this;
	}
}
