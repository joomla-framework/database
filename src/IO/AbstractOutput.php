<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\IO;

/**
 * Base class defining an output handler
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractOutput
{
	/**
	 * Output processor
	 *
	 * @var    OutputProcessorInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $processor;

	/**
	 * Constructor
	 *
	 * @param   OutputProcessorInterface  $processor  The output processor.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(OutputProcessorInterface $processor = null)
	{
		$this->setProcessor($processor ?: new ColorProcessor);
	}

	/**
	 * Get a processor
	 *
	 * @return  OutputProcessorInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function getProcessor(): OutputProcessorInterface
	{
		return $this->processor;
	}

	/**
	 * Write a string to the output handler.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	abstract public function out(string $text = '', bool $nl = true);

	/**
	 * Set a processor
	 *
	 * @param   OutputProcessorInterface  $processor  The output processor.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setProcessor(OutputProcessorInterface $processor)
	{
		$this->processor = $processor;
	}
}
