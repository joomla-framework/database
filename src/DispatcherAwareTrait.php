<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * Defines the trait for a Dispatcher Aware Class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait DispatcherAwareTrait
{
	/**
	 * Event Dispatcher
	 *
	 * @var    DispatcherInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $dispatcher;

	/**
	 * Get the event dispatcher.
	 *
	 * @return  DispatcherInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \UnexpectedValueException May be thrown if the dispatcher has not been set.
	 */
	public function getDispatcher()
	{
		if ($this->dispatcher)
		{
			return $this->dispatcher;
		}

		throw new \UnexpectedValueException('Dispatcher not set in ' . __CLASS__);
	}

	/**
	 * Set the dispatcher to use.
	 *
	 * @param   DispatcherInterface  $dispatcher  The dispatcher to use.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setDispatcher(DispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;

		return $this;
	}
}
