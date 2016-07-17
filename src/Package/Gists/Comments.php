<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Gists;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Gists Comments class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/gists/comments/
 *
 * @since  1.0
 */
class Comments extends AbstractPackage
{
	/**
	 * Create a comment.
	 *
	 * @param   integer  $gistId  The gist number.
	 * @param   string   $body    The comment body text.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function create($gistId, $body)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/comments';

		// Build the request data.
		$data = json_encode(
			array(
				'body' => $body,
			)
		);

		// Send the request.
		return $this->processResponse($this->client->post($this->fetchUrl($path), $data), 201);
	}

	/**
	 * Delete a comment.
	 *
	 * @param   integer  $commentId  The id of the comment to delete.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function delete($commentId)
	{
		// Build the request path.
		$path = '/gists/comments/' . (int) $commentId;

		// Send the request.
		$this->processResponse($this->client->delete($this->fetchUrl($path)), 204);
	}

	/**
	 * Edit a comment.
	 *
	 * @param   integer  $commentId  The id of the comment to update.
	 * @param   string   $body       The new body text for the comment.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function edit($commentId, $body)
	{
		// Build the request path.
		$path = '/gists/comments/' . (int) $commentId;

		// Build the request data.
		$data = json_encode(
			array(
				'body' => $body
			)
		);

		// Send the request.
		return $this->processResponse($this->client->patch($this->fetchUrl($path), $data));
	}

	/**
	 * Get a single comment.
	 *
	 * @param   integer  $commentId  The comment id to get.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($commentId)
	{
		// Build the request path.
		$path = '/gists/comments/' . (int) $commentId;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * List comments on a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($gistId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/comments';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}
}
