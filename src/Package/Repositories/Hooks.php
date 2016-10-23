<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Repositories;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Hooks class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/repos/hooks
 *
 * @since  1.0
 */
class Hooks extends AbstractPackage
{
	/**
	 * Create a hook.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   string   $name    The name of the service being called.
	 * @param   array    $config  Array containing the config for the service.
	 * @param   array    $events  The events the hook will be triggered for.
	 * @param   boolean  $active  Flag to determine if the hook is active
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 * @throws  \RuntimeException
	 */
	public function create($user, $repo, $name, $config, array $events = array('push'), $active = true)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks';

		// Check to ensure all events are in the allowed list
		foreach ($events as $event)
		{
			if (!in_array($event, $this->hookEvents))
			{
				throw new \RuntimeException('Your events array contains an unauthorized event.');
			}
		}

		$data = json_encode(
			array('name' => $name, 'config' => $config, 'events' => $events, 'active' => $active)
		);

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), $data),
			201
		);
	}

	/**
	 * Delete a hook
	 *
	 * @param   string   $user  The name of the owner of the GitHub repository.
	 * @param   string   $repo  The name of the GitHub repository.
	 * @param   integer  $id    ID of the hook to delete.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function delete($user, $repo, $id)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks/' . $id;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * Edit a hook.
	 *
	 * @param   string   $user          The name of the owner of the GitHub repository.
	 * @param   string   $repo          The name of the GitHub repository.
	 * @param   integer  $id            ID of the hook to edit.
	 * @param   string   $name          The name of the service being called.
	 * @param   array    $config        Array containing the config for the service.
	 * @param   array    $events        The events the hook will be triggered for.  This resets the currently set list
	 * @param   array    $addEvents     Events to add to the hook.
	 * @param   array    $removeEvents  Events to remove from the hook.
	 * @param   boolean  $active        Flag to determine if the hook is active
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 * @throws  \RuntimeException
	 */
	public function edit($user, $repo, $id, $name, $config, array $events = array('push'), array $addEvents = array(),
		array $removeEvents = array(), $active = true)
	{
		// Check to ensure all events are in the allowed list
		foreach ($events as $event)
		{
			if (!in_array($event, $this->hookEvents))
			{
				throw new \RuntimeException('Your events array contains an unauthorized event.');
			}
		}

		foreach ($addEvents as $event)
		{
			if (!in_array($event, $this->hookEvents))
			{
				throw new \RuntimeException('Your active_events array contains an unauthorized event.');
			}
		}

		foreach ($removeEvents as $event)
		{
			if (!in_array($event, $this->hookEvents))
			{
				throw new \RuntimeException('Your remove_events array contains an unauthorized event.');
			}
		}

		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks/' . $id;

		$data = json_encode(
			array(
				'name'          => $name,
				'config'        => $config,
				'events'        => $events,
				'add_events'    => $addEvents,
				'remove_events' => $removeEvents,
				'active'        => $active)
		);

		return $this->processResponse(
			$this->client->patch($this->fetchUrl($path), $data)
		);
	}

	/**
	 * Get single hook.
	 *
	 * @param   string   $user  The name of the owner of the GitHub repository.
	 * @param   string   $repo  The name of the GitHub repository.
	 * @param   integer  $id    ID of the hook to retrieve
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($user, $repo, $id)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks/' . $id;

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * List hooks.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($user, $repo)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Ping a hook.
	 *
	 * @param   string   $user  The name of the owner of the GitHub repository.
	 * @param   string   $repo  The name of the GitHub repository.
	 * @param   integer  $id    ID of the hook to ping
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function ping($user, $repo, $id)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks/' . $id . '/pings';

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), json_encode('')),
			204
		);
	}

	/**
	 * Test a `push` hook.
	 *
	 * @param   string   $user  The name of the owner of the GitHub repository.
	 * @param   string   $repo  The name of the GitHub repository.
	 * @param   integer  $id    ID of the hook to test
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function test($user, $repo, $id)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/hooks/' . $id . '/test';

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), json_encode('')),
			204
		);
	}
}
