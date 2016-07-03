<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Users;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Emails class for the Joomla Framework.
 *
 * Management of email addresses via the API requires that you are authenticated
 * through basic auth or OAuth with the user scope.
 *
 * @documentation http://developer.github.com/v3/repos/users/emails
 *
 * @since  1.0
 */
class Emails extends AbstractPackage
{
	/**
	 * List email addresses for a user.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function getList()
	{
		// Build the request path.
		$path = '/user/emails';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Add email address(es).
	 *
	 * @param   string|array  $email  The email address(es).
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function add($email)
	{
		// Build the request path.
		$path = '/user/emails';

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), json_encode($email)),
			201
		);
	}

	/**
	 * Delete email address(es).
	 *
	 * @param   string|array  $email  The email address(es).
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function delete($email)
	{
		// Build the request path.
		$path = '/user/emails';

		$this->client->setOption('body', json_encode($email));

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}
}
