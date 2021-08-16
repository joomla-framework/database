<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Mediawiki\AbstractMediawikiObject;
use Joomla\Http\Response;

/**
 * AbstractMediawikiObjectMock class.
 *
 * @since  1.0
 */
class AbstractMediawikiObjectMock extends AbstractMediawikiObject
{
	/**
	 * Method to build and return a full request URL for the request.  This method will
	 * add appropriate pagination details if necessary and also prepend the API url
	 * to have a complete URL for the request.
	 *
	 * @param   string  $path  URL to inflect
	 *
	 * @return  string   The request URL.
	 *
	 * @since   1.0
	 */
	public function fetchUrl($path)
	{
		return parent::fetchUrl($path);
	}

	/**
	 * Method to build request parameters from a string array.
	 *
	 * @param   array  $params  string array that contains the parameters
	 *
	 * @return  string   request parameter
	 *
	 * @since   1.0
	 */
	public function buildParameter(array $params)
	{
		return parent::buildParameter($params);
	}

	/**
	 * Method to validate response for errors
	 *
	 * @param   Response  $response  The response from the mediawiki server.
	 *
	 * @return  Object
	 *
	 * @since   1.0
	 */
	public function validateResponse(Response $response)
	{
		return parent::validateResponse($response);
	}
}
