<?php

namespace Joomla\Github\Tests\Stub;

class TransportMock implements \Joomla\Http\TransportInterface
{
	public function request($method, \Joomla\Uri\UriInterface $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null)
	{
		// TODO: Implement request() method.
	}

	public static function isSupported()
	{
		// TODO: Implement isSupported() method.
	}
}