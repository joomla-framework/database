<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests\Cli\Output\Processor;

use Joomla\Application\Cli\Output\Processor\ProcessorInterface;

/**
 * Class TestProcessor.
 *
 * @since  
 */
class TestProcessor implements ProcessorInterface
{
	/**
	 * Process a string.
	 *
	 * @param   string  $string  The string to process.
	 *
	 * @return  string
	 *
	 * @since   
	 */
	public function process($string)
	{
		return $string;
	}
}
