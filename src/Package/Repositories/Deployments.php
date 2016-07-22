<?php
/**
 * Part of the Joomla Framework GitHub Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Repositories;

use Joomla\Github\AbstractPackage;
use Joomla\Uri\Uri;

/**
 * GitHub API Deployments class for the Joomla Framework.
 *
 * @documentation https://developer.github.com/v3/repos/deployments
 *
 * @since  1.4.0
 */
class Deployments extends AbstractPackage
{
	/**
	 * List Deployments.
	 *
	 * @param   string   $owner        The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   string   $sha          The SHA that was recorded at creation time.
	 * @param   string   $ref          The name of the ref. This can be a branch, tag, or SHA.
	 * @param   string   $task         The name of the task for the deployment.
	 * @param   string   $environment  The name of the environment that was deployed to.
	 * @param   integer  $page         The page number from which to get items.
	 * @param   integer  $limit        The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getList($owner, $repo, $sha = '', $ref = '', $task = '', $environment = '', $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/deployments";

		$uri = new Uri($this->fetchUrl($path, $page, $limit));

		if ($sha)
		{
			$uri->setVar('sha', $sha);
		}

		if ($ref)
		{
			$uri->setVar('ref', $ref);
		}

		if ($task)
		{
			$uri->setVar('task', $task);
		}

		if ($environment)
		{
			$uri->setVar('environment', $environment);
		}

		return $this->processResponse(
			$this->client->get((string) $uri)
		);
	}

	/**
	 * Create a Deployment.
	 *
	 * @param   string      $owner             The name of the owner of the GitHub repository.
	 * @param   string      $repo              The name of the GitHub repository.
	 * @param   string      $ref               The ref to deploy. This can be a branch, tag, or SHA.
	 * @param   string      $task              Optional parameter to specify a task to execute.
	 * @param   boolean     $autoMerge         Optional parameter to merge the default branch into the requested ref if it is behind the default branch.
	 * @param   array|null  $requiredContexts  Optional array of status contexts verified against commit status checks. If this parameter is omitted
	 *                                         from the parameters then all unique contexts will be verified before a deployment is created. To bypass
	 *                                         checking entirely pass an empty array. Defaults to all unique contexts.
	 * @param   string      $payload           Optional JSON payload with extra information about the deployment.
	 * @param   string      $environment       Optional name for the target deployment environment.
	 * @param   string      $description       Optional short description.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \RuntimeException
	 */
	public function create($owner, $repo, $ref, $task = '', $autoMerge = true, $requiredContexts = null, $payload = '', $environment = '',
		$description = '')
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/deployments";

		$data = array(
			'ref'        => $ref,
			'auto_merge' => $autoMerge,
		);

		if ($task)
		{
			$data['task'] = $task;
		}

		if (is_array($requiredContexts))
		{
			$data['required_contexts'] = $requiredContexts;
		}

		if ($payload)
		{
			$data['payload'] = $payload;
		}

		if ($environment)
		{
			$data['environment'] = $environment;
		}

		if ($description)
		{
			$data['description'] = $description;
		}

		$response = $this->client->post($this->fetchUrl($path), json_encode($data));

		switch ($response->code)
		{
			case 201 :
				// The deployment was successful
				return json_decode($response->body);

			case 409 :
				// There was a merge conflict or a status check failed.
				$body    = json_decode($response->body);
				$message = isset($body->message) ? $body->message : 'Invalid response received from GitHub.';

				throw new \RuntimeException($message, $response->code);

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
		}
	}

	/**
	 * List Deployment Statuses.
	 *
	 * @param   string   $owner  The name of the owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 * @param   integer  $id     The Deployment ID to list the statuses from.
	 * @param   integer  $page   The page number from which to get items.
	 * @param   integer  $limit  The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getDeploymentStatuses($owner, $repo, $id, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/deployments/" . (int) $id . '/statuses';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path, $page, $limit))
		);
	}

	/**
	 * Create a Deployment Status.
	 *
	 * @param   string   $owner        The name of the owner of the GitHub repository.
	 * @param   string   $repo         The name of the GitHub repository.
	 * @param   integer  $id           The Deployment ID to list the statuses from.
	 * @param   string   $state        The state of the status.
	 * @param   string   $targetUrl    The target URL to associate with this status. This URL should contain output to keep the user updated while
	 *                                 the task is running or serve as historical information for what happened in the deployment.
	 * @param   string   $description  A short description of the status. Maximum length of 140 characters.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \InvalidArgumentException
	 */
	public function createStatus($owner, $repo, $id, $state, $targetUrl = '', $description = '')
	{
		$allowedStates = array('pending', 'success', 'error', 'failure');

		// Build the request path.
		$path = "/repos/$owner/$repo/deployments/" . (int) $id . '/statuses';

		if (!in_array($state, $allowedStates))
		{
			throw new \InvalidArgumentException(sprintf('The deployment state must be: %s', implode(', ', $allowedStates)));
		}

		$data = array(
			'state' => $state,
		);

		if ($targetUrl)
		{
			$data['target_url'] = $targetUrl;
		}

		if ($description)
		{
			$data['description'] = $description;
		}

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), json_encode($data)),
			201
		);
	}
}
