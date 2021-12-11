<?php

namespace Joomla\Filter\Tests\Stubs;

class ArbitraryObject
{
	public    $publicVar;
	protected $protectedVar;
	private   $privateVar;

	public function __construct($publicVar, $protectedVar, $privateVar)
	{
		$this->publicVar    = $publicVar;
		$this->protectedVar = $protectedVar;
		$this->privateVar   = $privateVar;
	}
}
