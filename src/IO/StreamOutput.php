<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\IO;

/**
 * Output handler for writing command line output to a stream
 *
 * @since  __DEPLOY_VERSION__
 */
final class StreamOutput extends AbstractOutput
{
	/**
	 * The stream resource.
	 *
	 * @var    resource
	 * @since  __DEPLOY_VERSION__
	 */
	private $stream;

	/**
	 * StreamOutput constructor.
	 *
	 * @param   resource|null             $stream     The stream resource.
	 * @param   OutputProcessorInterface  $processor  The output processor.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($stream = null, OutputProcessorInterface $processor = null)
	{
		parent::__construct($processor);

		$stream = $stream ?: STDOUT;

		if (!is_resource($stream))
		{
			throw new \InvalidArgumentException(sprintf('The $stream argument for %s must be a resource.', get_class($this)));
		}

		$this->stream = $stream;
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
	public function out(string $text = '', bool $nl = true)
	{
		fwrite($this->stream, $this->getProcessor()->process($text) . ($nl ? "\n" : null));

		return $this;
	}
}
