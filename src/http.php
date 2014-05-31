<?php
/**
 * Part of the Joomla Framework MediaWiki Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki;

use Joomla\Http\Http as BaseHttp;
use Joomla\Http\TransportInterface;
use Joomla\Http\Transport\Stream;
use Joomla\Registry;

/**
 * HTTP client class for connecting to a MediaWiki instance.
 *
 * @since  1.0
 */
class Http extends BaseHttp
{
	/**
	 * Constructor.
	 *
	 * @param   array               $options    Client options object.
	 * @param   TransportInterface  $transport  The HTTP transport object.
	 *
	 * @since   12.3
	 */
	public function __construct($options = array(), TransportInterface $transport = null)
	{
		// Override the Http constructor to use Joomla\Http\Transport\Stream.
		$transport = isset($transport) ? $transport : new Stream($this->options);
		parent::__construct($options, $transport);

		// Make sure the user agent string is defined.
		if (!isset($this->options['userAgent']))
		{
			$this->options['userAgent'] = 'JMediawiki/1.0';
		}

		// Set the default timeout to 120 seconds.
		if (!isset($this->options['timeout']))
		{
			$this->options['timeout'] = 120;
		}
	}
}
