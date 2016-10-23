<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Orgs;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Orgs Hooks class for the Joomla Framework.
 *
 * All actions against organization webhooks require the authenticated user to be an admin of the organization being managed.
 * Additionally, OAuth tokens require the "admin:org_hook" scope.
 *
 * @documentation http://developer.github.com/v3/orgs/hooks/
 *
 * @since  1.4.0
 */
class Hooks extends AbstractPackage
{
	/**
	 * List hooks.
	 *
	 * @param   string  $org  The name of the organization.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getList($org)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks";

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Get single hook.
	 *
	 * @param   string   $org  The name of the organization.
	 * @param   integer  $id   The hook id.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function get($org, $id)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks/" . (int) $id;

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Create a hook.
	 *
	 * @param   string   $org          The name of the organization.
	 * @param   string   $url          The URL to which the payloads will be delivered.
	 * @param   string   $contentType  The media type used to serialize the payloads. Supported values include "json" and "form".
	 * @param   string   $secret       If provided, payloads will be delivered with an X-Hub-Signature header.
	 *                                 The value of this header is computed as the
	 *                                 [HMAC hex digest of the body, using the secret as the key][hub-signature].
	 * @param   boolean  $insecureSsl  Determines whether the SSL certificate of the host for url will be verified when delivering payloads.
	 *                                 If false, verification is performed.  If true, verification is not performed.
	 * @param   array    $events       Determines what events the hook is triggered for.
	 * @param   boolean  $active       Determines whether the hook is actually triggered on pushes.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \UnexpectedValueException
	 */
	public function create($org, $url, $contentType = 'form', $secret = null, $insecureSsl = false, array $events = array('push'), $active = true)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks";

		if (false == in_array($contentType, array('form', 'json')))
		{
			throw new \UnexpectedValueException('Content type must be either "form" or "json".');
		}

		$config = array(
			'url'          => $url,
			'content_type' => $contentType,
			'insecure_ssl' => (int) $insecureSsl,
		);

		if ($secret)
		{
			$config['secret'] = $secret;
		}

		$data = array(
			'name'   => 'web',
			'active' => $active,
			'config' => (object) $config,
		);

		if (!empty($events))
		{
			// Check to ensure all events are in the allowed list
			foreach ($events as $event)
			{
				if (!in_array($event, $this->hookEvents))
				{
					throw new \RuntimeException('Your events array contains an unauthorized event.');
				}
			}

			$data['events'] = $events;
		}

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), $data),
			201
		);
	}

	/**
	 * Edit a hook.
	 *
	 * @param   string   $org          The name of the organization.
	 * @param   string   $url          The URL to which the payloads will be delivered.
	 * @param   string   $contentType  The media type used to serialize the payloads. Supported values include "json" and "form".
	 * @param   string   $secret       If provided, payloads will be delivered with an X-Hub-Signature header.
	 *                                 The value of this header is computed as the
	 *                                 [HMAC hex digest of the body, using the secret as the key][hub-signature].
	 * @param   boolean  $insecureSsl  Determines whether the SSL certificate of the host for url will be verified when delivering payloads.
	 *                                 If false, verification is performed.  If true, verification is not performed.
	 * @param   array    $events       Determines what events the hook is triggered for.
	 * @param   boolean  $active       Determines whether the hook is actually triggered on pushes.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \UnexpectedValueException
	 */
	public function edit($org, $url, $contentType = null, $secret = null, $insecureSsl = null, array $events = array(), $active = null)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks";

		$config = array(
			'url' => $url,
		);

		if ($contentType)
		{
			if (false == in_array($contentType, array('form', 'json')))
			{
				throw new \UnexpectedValueException('Content type must be either "form" or "json".');
			}

			$config['content_type'] = $contentType;
		}

		if ($insecureSsl !== null)
		{
			$config['insecure_ssl'] = (int) $insecureSsl;
		}

		if ($secret)
		{
			$config['secret'] = $secret;
		}

		$data = array(
			'config' => (object) $config,
		);

		if ($active !== null)
		{
			$data['active'] = (bool) $active;
		}

		if (!empty($events))
		{
			// Check to ensure all events are in the allowed list
			foreach ($events as $event)
			{
				if (!in_array($event, $this->hookEvents))
				{
					throw new \RuntimeException('Your events array contains an unauthorized event.');
				}
			}

			$data['events'] = $events;
		}

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), $data),
			201
		);
	}

	/**
	 * Ping a hook.
	 *
	 * @param   string   $org  The name of the organization
	 * @param   integer  $id   ID of the hook to ping
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function ping($org, $id)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks/$id/pings";

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), json_encode('')),
			204
		);
	}

	/**
	 * Delete a hook.
	 *
	 * @param   string   $org  The name of the organization
	 * @param   integer  $id   ID of the hook to delete
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function delete($org, $id)
	{
		// Build the request path.
		$path = "/orgs/$org/hooks/$id";

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}
}
