<?php
/**
 * Part of the Joomla Framework Http Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Exception;

use Joomla\Http\Response;

/**
 * Exception representing an unexpected response
 *
 * @since  __DEPLOY_VERSION__
 */
class UnexpectedResponseException extends \DomainException
{
	/**
	 * The Response object.
	 *
	 * @var    Response
	 * @since  __DEPLOY_VERSION__
	 */
	private $response;

	/**
	 * Constructor
	 *
	 * @param   Response    $response  The Response object.
	 * @param   string      $message   The Exception message to throw.
	 * @param   integer     $code      The Exception code.
	 * @param   \Exception  $previous  The previous exception used for the exception chaining.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Response $response, $message = '', $code = 0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->response = $response;
	}

	/**
	 * Get the Response object.
	 *
	 * @return  Response
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getResponse()
	{
		return $this->response;
	}
}
