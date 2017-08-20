<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Application\AbstractApplication;
use Joomla\Input\Cli;
use Joomla\Registry\Registry;

/**
 * Base application class for a Joomla! command line application.
 *
 * @since  __DEPLOY_VERSION__
 */
class Application extends AbstractApplication
{
	/**
	 * The default command for the application.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $defaultCommand = '';

	/**
	 * Output handler.
	 *
	 * @var    IO\AbstractOutput
	 * @since  __DEPLOY_VERSION__
	 */
	private $output;

	/**
	 * Class constructor.
	 *
	 * @param   Cli       $input   An optional argument to provide dependency injection for the application's input object.  If the argument is an
	 *                             Input object that object will become the application's input object, otherwise a default input object is created.
	 * @param   Registry  $config  An optional argument to provide dependency injection for the application's config object.  If the argument
	 *                             is a Registry object that object will become the application's config object, otherwise a default config
	 *                             object is created.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Cli $input = null, Registry $config = null)
	{
		// Close the application if we are not executed from the command line.
		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$this->close();
		}

		$this->output = new IO\StreamOutput;

		// Call the constructor as late as possible (it runs `initialise`).
		parent::__construct($input ?: new Cli, $config);

		// Set the current directory.
		$this->set('cwd', getcwd());
	}

	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute()
	{
		$commandName = $this->getCommandName();

		if (!$commandName)
		{
			$this->out('<comment>Command name not given.</comment>');

			$this->close(1);
		}
	}

	/**
	 * Get the output handler.
	 *
	 * @return  IO\AbstractOutput
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOutputHandler(): IO\AbstractOutput
	{
		return $this->output;
	}

	/**
	 * Get the name of the command to run.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getCommandName(): string
	{
		$args = $this->input->args;

		return !empty($args[0]) ? $args[0] : $this->defaultCommand;
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
		$this->getOutputHandler()->out($text, $nl);

		return $this;
	}

	/**
	 * Set the output handler.
	 *
	 * @param   IO\AbstractOutput  $output  The new output handler.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setOutputHandler(IO\AbstractOutput $output)
	{
		$this->output = $output;

		return $this;
	}
}
