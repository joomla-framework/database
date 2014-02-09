<?php
/**
 * Created by PhpStorm.
 * User: imaclennan
 * Date: 2/8/14
 * Time: 5:24 PM
 */

namespace Joomla\Authentication\Strategies;

use Joomla\Authentication\AuthenticationStrategyInterface;
use Joomla\Input\Input;

class LocalStrategy implements AuthenticationStrategyInterface
{
	/**
	 * The Input object
	 *
	 * @var    Joomla\Input\Input  $input  The input object from which to retrieve the username and password.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $input;

	/**
	 * The credential store.
	 *
	 * @var    array  $credentialStore  An array of username/hash pairs.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $credentialStore;

	/**
	 * Strategy Constructor
	 *
	 * @param   Joomla\Input\Input  $input            The input object from which to retrieve the request credentials.
	 * @param   array               $credentialStore  Hash of username and hash pairs.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Input $input, $credentialStore)
	{
		$this->input = $input;

		$this->credentialStore = $credentialStore;
	}

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function authenticate()
	{
		$username = $input->get('username');
		$password = $input->get('password');

		if (!$username || !$password)
		{
			return false;
		}

		if (isset($credentialStore[$username]))
		{
			$hash = $this->credentialStore[$username];
		}
		else
		{
			return false;
		}

		return password_verify($password, $hash);
	}

	/**
	 * Get strategy name
	 *
	 * @return  string  A string containing the strategy name.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getName()
	{
		return 'local';
	}
}