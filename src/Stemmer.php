<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

use RuntimeException;

/**
 * Stemmer base class.
 *
 * @since       1.0
 * @deprecated  2.0  Stemmer objects should directly implement the StemmerInterface
 */
abstract class Stemmer implements StemmerInterface
{
	/**
	 * An internal cache of stemmed tokens.
	 *
	 * @var    array
	 * @since  1.0
	 * @deprecated  2.0  Subclasses should implement this property directly
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
}
