<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\IO;

use Joomla\Console\IO\ColorStyle;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Console\IO\ColorStyle
 */
class ColorStyleTest extends TestCase
{
	/**
	 * @var  ColorStyle
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->object = new ColorStyle('red', 'white', ['blink']);
	}

	/**
	 * Data provider for constructor test cases
	 *
	 * @return  array
	 */
	public function dataConstructor(): array
	{
		return [
			'red background with white foreground' => [false, 'white', 'red', ['blink', 'bold']],
			'invalid foreground color' => [true, 'INVALID'],
			'invalid background color' => [true, '', 'INVALID'],
			'invalid options' => [true, '', '', ['INVALID']],
		];
	}

	/**
	 * Data provider for fromString test cases
	 *
	 * @return  array
	 */
	public function dataFromString(): array
	{
		return [
			'red background with white foreground' => [false, 'fg=white;bg=red;options=blink,bold', '37;41;5;1'],
			'invalid string' => [true, 'XXX;XX=YY', ''],
		];
	}

	/**
	 * @covers  Joomla\Console\IO\ColorStyle::getStyle
	 */
	public function testAStringRepresentationOfTheStyleIsReturned()
	{
		$this->assertEquals(
			'31;47;5',
			$this->object->getStyle()
		);
	}

	/**
	 * @covers  Joomla\Console\IO\ColorStyle::__toString
	 * @uses    Joomla\Console\IO\ColorStyle::getStyle
	 */
	public function testTheObjectCanBeCastToAString()
	{
		$this->assertEquals(
			'31;47;5',
			$this->object->getStyle()
		);
	}

	/**
	 * @param   boolean  $expectException  Flag indicating an exception should be thrown by the method
	 * @param   string   $fg               Foreground color.
	 * @param   string   $bg               Background color.
	 * @param   array    $options          Style options.
	 *
	 * @dataProvider  dataConstructor
	 *
	 * @covers  Joomla\Console\IO\ColorStyle::__construct
	 */
	public function testTheObjectIsCreatedCorrectly(bool $expectException, string $fg = '', string $bg = '', array $options = [])
	{
		if ($expectException)
		{
			$this->expectException('InvalidArgumentException');
		}

		$object = new ColorStyle($fg, $bg, $options);

		// TODO - Test other values
		$this->assertAttributeEquals($options, 'options', $object);
	}

	/**
	 * @param   boolean  $expectException  Flag indicating an exception should be thrown by the method
	 * @param   string   $fg               The parameter string.
	 * @param   string   $expected         The expected format value.
	 *
	 * @dataProvider  dataFromString
	 *
	 * @covers  Joomla\Console\IO\ColorStyle::fromString
	 * @uses    Joomla\Console\IO\ColorStyle::__construct
	 * @uses    Joomla\Console\IO\ColorStyle::getStyle
	 */
	public function testTheObjectIsCreatedFromAString(bool $expectException, string $string, string $expected)
	{
		if ($expectException)
		{
			$this->expectException('RuntimeException');
		}

		$object = ColorStyle::fromString($string);

		$this->assertSame($expected, $object->getStyle());
	}
}
