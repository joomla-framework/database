<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Cli\Tests;

use Joomla\Application\Cli\ColorStyle;
use Joomla\Application\Tests\CompatTestCase;

/**
 * Test class for Joomla\Application\Cli\ColorStyle.
 */
class ColorStyleTest extends CompatTestCase
{
	/**
	 * @var ColorStyle
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function doSetUp()
	{
		$this->object = new ColorStyle('red', 'white', array('blink'));

		parent::doSetUp();
	}

	/**
	 * Test the getStyle method.
	 */
	public function testGetStyle()
	{
		$this->assertSame(
			'31;47;5',
			$this->object->getStyle()
		);
	}

	/**
	 * Test the object can be converted to a string
	 *
	 * @return void
	 */
	public function testToString()
	{
		$this->assertSame(
			'31;47;5',
			(string) $this->object
		);
	}

	/**
	 * Tests a ColorStyle can be created from a string
	 */
	public function fromString()
	{
		$this->assertEquals(
			new ColorStyle('white', 'red', array('blink', 'bold')),
			ColorStyle::fromString('fg=white;bg=red;options=blink,bold')
		);
	}

	/**
	 * Tests a ColorStyle cannot be created from an invalid string
	 */
	public function testFromStringInvalid()
	{
		if (method_exists($this, 'expectException'))
		{
			$this->expectException('\\RuntimeException');
		}
		else
		{
			$this->setExpectedException('\\RuntimeException');
		}

		ColorStyle::fromString('XXX;XX=YY');
	}

	/**
	 * Tests a ColorStyle cannot be created from an invalid string
	 */
	public function testConstructInvalid1()
	{
		if (method_exists($this, 'expectException'))
		{
			$this->expectException('\\InvalidArgumentException');
		}
		else
		{
			$this->setExpectedException('\\InvalidArgumentException');
		}

		new ColorStyle('INVALID');
	}

	/**
	 * Tests a ColorStyle cannot be created from an invalid string
	 */
	public function testConstructInvalid2()
	{
		if (method_exists($this, 'expectException'))
		{
			$this->expectException('\\InvalidArgumentException');
		}
		else
		{
			$this->setExpectedException('\\InvalidArgumentException');
		}

		new ColorStyle('', 'INVALID');
	}

	/**
	 * Tests a ColorStyle cannot be created from an invalid options array
	 */
	public function testConstructInvalid3()
	{
		if (method_exists($this, 'expectException'))
		{
			$this->expectException('\\InvalidArgumentException');
		}
		else
		{
			$this->setExpectedException('\\InvalidArgumentException');
		}

		new ColorStyle('', '', array('INVALID'));
	}
}
