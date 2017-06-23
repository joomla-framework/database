<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Stemmer interface.
 *
 * @since  __DEPLOY_VERSION__
 */
interface StemmerInterface
{
	/**
	 * Method to stem a token and return the root.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 *
	 * @return  string  The root token.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function stem($token, $lang);
}
