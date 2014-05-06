<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Emojis class for the Joomla Framework.
 *
 * @since  1.0
 *
 * @documentation  http://developer.github.com/v3/emojis/
 */
class Emojis extends AbstractPackage
{
	/**
	 * Lists all the emojis available to use on GitHub.
	 *
	 * @return array
	 *
	 * @since  1.1
	 * @throws \DomainException
	 */
	public function getList()
	{
		// Build the request path.
		$path = '/emojis';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new \DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}
}
