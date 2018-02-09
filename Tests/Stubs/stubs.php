<?php
/**
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

// @codingStandardsIgnoreStart

use Joomla\DI\Exception\KeyNotFoundException;
use Psr\Container\ContainerInterface;

interface StubInterface {}

class Stub1 implements StubInterface {}

class Stub2 implements StubInterface
{
	protected $stub;

	public function __construct(StubInterface $stub)
	{
		$this->stub = $stub;
	}
}

class Stub3
{
	protected $stub;
	protected $stub2;

	public function __construct(StubInterface $stub, StubInterface $stub2)
	{
		$this->stub = $stub;
		$this->stub2 = $stub2;
	}
}

class Stub4 implements StubInterface {}

class Stub5
{
	protected $stub;

	public function __construct(Stub4 $stub)
	{
		$this->stub = $stub;
	}
}

class Stub6
{
	protected $stub;

	public function __construct($stub = 'foo')
	{
		$this->stub = $stub;
	}
}

class Stub7
{
	protected $stub;

	public function __construct($stub)
	{
		$this->stub = $stub;
	}
}

class Stub8
{
	protected $stub;

	public function __construct(DoesntExist $stub)
	{
		$this->stub = $stub;
	}
}

class Stub9
{
}

class StubPsrContainer implements ContainerInterface
{
	private $services = array(
		'foo' => 'bar',
	);

	public function get($id)
	{
		if (!$this->has($id))
		{
			throw new KeyNotFoundException;
		}

		return $this->services[$id];
	}

	public function has($id)
	{
		return isset($this->services[$id]);
	}
}
