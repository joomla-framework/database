<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

use RuntimeException;

/**
 * Stemmer base class.
 *
 * @since  1.0
 */
abstract class Stemmer
{
	/**
	 * An internal cache of stemmed tokens.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $cache = array();

	/**
	 * Stemmer instances.
	 *
	 * @var    Stemmer[]
	 * @since  1.0
	 * @deprecated  2.0
	 */
	protected static $instances = array();

	/**
	 * Method to get a stemmer, creating it if necessary.
	 *
	 * @param   string  $adapter  The type of stemmer to load.
	 *
	 * @return  Stemmer
	 *
	 * @since   1.0
	 * @deprecated  2.0  Use LanguageFactory::getStemmer() instead
	 * @throws  RuntimeException on invalid stemmer.
	 */
	public static function getInstance($adapter)
	{
		$factory = new LanguageFactory;

		return $factory->getStemmer($adapter);
	}

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 *
	 * @return  string  The root token.
	 *
	 * @since   1.0
	 */
	abstract public function stem($token, $lang);
}
