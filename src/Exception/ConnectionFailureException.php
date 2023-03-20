<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Exception;

/**
 * Exception class defining an error connecting to the database platform
 *
 * @since  1.5.0
 */
class ConnectionFailureException extends \RuntimeException
{
    /**
     * Construct the exception
     *
     * @param   string       $message   The Exception message to throw. [optional]
     * @param   integer      $code      The Exception code. [optional]
     * @param   ?\Exception  $previous  The previous exception used for the exception chaining. [optional]
     *
     * @since   2.0.0
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        // PDO uses strings for exception codes, PHP forces numeric codes, so "force" the string code to be used
        parent::__construct($message, 0, $previous);

        $this->code = $code;
    }
}
