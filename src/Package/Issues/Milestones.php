<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Issues;

use Joomla\Github\AbstractPackage;
use Joomla\Uri\Uri;

/**
 * GitHub API Milestones class for the Joomla Framework.
 *
 * @link   https://developer.github.com/v3/issues/milestones/
 *
 * @since  1.0
 */
class Milestones extends AbstractPackage
{
	/**
	 * List milestones for a repository.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   string   $state      The milestone state to retrieved.  Open (default) or closed.
	 * @param   string   $sort       Sort can be due_date (default) or completeness.
	 * @param   string   $direction  Direction is asc or desc (default).
	 * @param   integer  $page       The page number from which to get items.
	 * @param   integer  $limit      The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($user, $repo, $state = 'open', $sort = 'due_date', $direction = 'desc', $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/milestones';

		$uri = new Uri($this->fetchUrl($path, $page, $limit));
		$uri->setVar('state', $state);
		$uri->setVar('sort', $sort);
		$uri->setVar('direction', $direction);

		// Send the request.
		return $this->processResponse($this->client->get($uri));
	}

	/**
	 * Get a single milestone.
	 *
	 * @param   string   $user         The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   integer  $milestoneId  The milestone id to get.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($user, $repo, $milestoneId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/milestones/' . (int) $milestoneId;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Create a milestone.
	 *
	 * @param   string   $user         The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   integer  $title        The title of the milestone.
	 * @param   string   $state        Can be open (default) or closed.
	 * @param   string   $description  Optional description for milestone.
	 * @param   string   $dueOn        The milestone due date. This is a timestamp in ISO 8601 format.
	 *
	 * @return  object
	 *
	 * @note    As of 2.0 the $dueOn parameter will be typehinted to a \DateTime object
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function create($user, $repo, $title, $state = null, $description = null, $dueOn = null)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/milestones';

		// Build the request data.
		$data = array(
			'title' => $title,
		);

		if ($state !== null)
		{
			$data['state'] = $state;
		}

		if ($description !== null)
		{
			$data['description'] = $description;
		}

		if ($dueOn !== null)
		{
			$data['due_on'] = $dueOn;
		}

		$data = json_encode($data);

		// Send the request.
		return $this->processResponse($this->client->post($this->fetchUrl($path), $data), 201);
	}

	/**
	 * Update a milestone.
	 *
	 * @param   string   $user         The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   integer  $milestoneId  The id of the comment to update.
	 * @param   integer  $title        Optional title of the milestone.
	 * @param   string   $state        Can be open (default) or closed.
	 * @param   string   $description  Optional description for milestone.
	 * @param   string   $dueOn        The milestone due date. This is a timestamp in ISO 8601 format.
	 *
	 * @return  object
	 *
	 * @note    As of 2.0 the $dueOn parameter will be typehinted to a \DateTime object
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function edit($user, $repo, $milestoneId, $title = null, $state = null, $description = null, $dueOn = null)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/milestones/' . (int) $milestoneId;

		// Build the request data.
		$data = array();

		if ($title !== null)
		{
			$data['title'] = $title;
		}

		if ($state !== null)
		{
			$data['state'] = $state;
		}

		if ($description !== null)
		{
			$data['description'] = $description;
		}

		if ($dueOn !== null)
		{
			$data['due_on'] = $dueOn;
		}

		$data = json_encode($data);

		// Send the request.
		return $this->processResponse($this->client->patch($this->fetchUrl($path), $data));
	}

	/**
	 * Delete a milestone.
	 *
	 * @param   string   $user         The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   integer  $milestoneId  The id of the milestone to delete.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function delete($user, $repo, $milestoneId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/milestones/' . (int) $milestoneId;

		// Send the request.
		$this->processResponse($this->client->delete($this->fetchUrl($path)), 204);
	}
}
