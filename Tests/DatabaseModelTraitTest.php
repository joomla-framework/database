<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model\Tests;

/**
 * Tests for \Joomla\Model\DatabaseModelTrait.
 */
class DatabaseModelTraitTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  Calling getDb() without a DatabaseDriver set will throw an Exception
	 *
	 * @requires            PHP 5.4
	 * @covers              Joomla\Model\DatabaseModelTraitTest::getDb
	 * @expectedException   \UnexpectedValueException
	 */
	public function testGetDbException()
	{
		/** @var \Joomla\Model\DatabaseModelTrait $object */
		$object = $this->getObjectForTrait('\\Joomla\\Model\\DatabaseModelTrait');
		$object->getDb();
	}

	/**
	 * @testdox  A DatabaseDriver is set and retrieved
	 *
	 * @requires  PHP 5.4
	 * @covers    Joomla\Model\DatabaseModelTraitTest::getDb
	 * @covers    Joomla\Model\DatabaseModelTraitTest::setDb
	 */
	public function testSetAndGetDb()
	{
		/** @var \Joomla\Model\DatabaseModelTrait $object */
		$object = $this->getObjectForTrait('\\Joomla\\Model\\DatabaseModelTrait');

		/** @var \Joomla\Database\DatabaseDriver $db */
		$db = $this->getMockBuilder('\\Joomla\\Database\\DatabaseDriver')
			->disableOriginalConstructor()
			->getMock();

		$object->setDb($db);

		$this->assertSame($db, $object->getDb());
	}
}
