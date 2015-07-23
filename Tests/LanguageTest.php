<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/data/language/en-GB/en-GB.localise.php';

use Joomla\Language\Language;
use Joomla\Filesystem\Folder;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Language\Language.
 *
 * @since  1.0
 */
class LanguageTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test language object
	 *
	 * @var    Joomla\Language\Language
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$path = JPATH_ROOT . '/language';

		if (is_dir($path))
		{
			Folder::delete($path);
		}

		Folder::copy(__DIR__ . '/data/language', $path);

		$this->object = new Language;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		Folder::delete(JPATH_ROOT . '/language');

		parent::tearDown();
	}

	/**
	 * Tests retrieving an instance of the Language object
	 *
	 * @covers  Joomla\Language\Language::getInstance
	 * @covers  Joomla\Language\Language::getLanguage
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetInstanceAndLanguage()
	{
		$instance = Language::getInstance(null);
		$this->assertInstanceOf('Joomla\Language\Language', $instance);

		$this->assertEquals(
			TestHelper::getValue($instance, 'default'),
			$instance->getLanguage(),
			'Asserts that getInstance when called with a null language returns the default language.  Line: ' . __LINE__
		);

		$instance = Language::getInstance('es-ES');

		$this->assertEquals(
			'es-ES',
			$instance->getLanguage(),
			'Asserts that getInstance when called with a specific language returns that language.  Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::__construct
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testConstruct()
	{
		// @codingStandardsIgnoreStart
		// @todo check the instanciating new classes without brackets sniff
		$instance = new Language(null, true);
		// @codingStandardsIgnoreEnd

		$this->assertInstanceOf('Joomla\Language\Language', $instance);
		$this->assertTrue($instance->getDebug());

		// @codingStandardsIgnoreStart
		// @todo check the instanciating new classes without brackets sniff
		$instance = new Language(null, false);
		// @codingStandardsIgnoreEnd

		$this->assertInstanceOf('Joomla\Language\Language', $instance);
		$this->assertFalse($instance->getDebug());

		$override = TestHelper::getValue($instance, 'override');
		$this->assertArrayHasKey('OVER', $override);
		$this->assertEquals('Ride', $override['OVER']);
	}

	/**
	 * Tests the _ method
	 *
	 * @covers  Joomla\Language\Language::_
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test_()
	{
		$string1 = 'delete';
		$string2 = "delete's";

		$this->assertEquals(
			'',
			$this->object->_('', false),
			'Line: ' . __LINE__ . ' Empty string should return as it is when javascript safe is false '
		);

		$this->assertEquals(
			'',
			$this->object->_('', true),
			'Line: ' . __LINE__ . ' Empty string should return as it is when javascript safe is true '
		);

		$this->assertEquals(
			'delete',
			$this->object->_($string1, false),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is false '
		);

		$this->assertNotEquals(
			'Delete',
			$this->object->_($string1, false),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is false'
		);

		$this->assertEquals(
			'delete',
			$this->object->_($string1, true),
			'Line: ' . __LINE__ . ' Exact case match should work when javascript safe is true'
		);

		$this->assertNotEquals(
			'Delete',
			$this->object->_($string1, true),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is true'
		);

		$this->assertEquals(
			'delete\'s',
			$this->object->_($string2, false),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is false '
		);

		$this->assertNotEquals(
			'Delete\'s',
			$this->object->_($string2, false),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is false'
		);

		$this->assertEquals(
			"delete\'s",
			$this->object->_($string2, true),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is true, also it calls addslashes (\' => \\\') '
		);

		$this->assertNotEquals(
			"Delete\'s",
			$this->object->_($string2, true),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is true,, also it calls addslashes (\' => \\\') '
		);
	}

	/**
	 * Tests the _ method with strings loaded and debug enabled
	 *
	 * @covers  Joomla\Language\Language::_
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function test_WithLoadedStringsAndDebug()
	{
		// Loading some strings.
		TestHelper::setValue($this->object, 'strings', array('DEL' => 'Delete'));

		$this->assertEquals(
			"Delete",
			$this->object->_('del', true)
		);

		$this->assertEquals(
			"Delete",
			$this->object->_('DEL', true)
		);

		// Debug true tests
		TestHelper::setValue($this->object, 'debug', true);

		$this->assertArrayNotHasKey(
			'DEL',
			TestHelper::getValue($this->object, 'used')
		);
		$this->assertEquals(
			"**Delete**",
			$this->object->_('del', true)
		);
		$this->assertArrayHasKey(
			'DEL',
			TestHelper::getValue($this->object, 'used')
		);

		$this->assertArrayNotHasKey(
			'DELET\\ED',
			TestHelper::getValue($this->object, 'orphans')
		);
		$this->assertEquals(
			"??Delet\\\\ed??",
			$this->object->_('Delet\\ed', true)
		);
		$this->assertArrayHasKey(
			'DELET\\ED',
			TestHelper::getValue($this->object, 'orphans')
		);
	}

	/**
	 * Tests the transliterate function
	 *
	 * @covers  Joomla\Language\Language::transliterate
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTransliterate()
	{
		$string1 = 'Así';
		$string2 = 'EÑE';

		// Don't use loaded transliterator for this test.
		TestHelper::setValue($this->object, 'transliterator', null);

		$this->assertEquals(
			'asi',
			$this->object->transliterate($string1),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			'Asi',
			$this->object->transliterate($string1),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			'Así',
			$this->object->transliterate($string1),
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			'ene',
			$this->object->transliterate($string2),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			'ENE',
			$this->object->transliterate($string2),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			'EÑE',
			$this->object->transliterate($string2),
			'Line: ' . __LINE__
		);

		TestHelper::setValue(
			$this->object,
			'transliterator',
			function ($string)
			{
				return str_replace(
					array('a', 'c', 'e', 'g'),
					array('b', 'd', 'f', 'h'),
					$string
				);
			}
		);

		$this->assertEquals(
			'bbddffhh',
			$this->object->transliterate('abcdefgh'),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Tests the getTransliterator function
	 *
	 * @covers  Joomla\Language\Language::getTransliterator
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTransliterator()
	{
		$lang = new Language('');

		$this->assertEquals(
			array('en_GBLocalise', 'transliterate'),
			$lang->getTransliterator()
		);
	}

	/**
	 * Tests the setTransliterator function
	 *
	 * @covers  Joomla\Language\Language::setTransliterator
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetTransliterator()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		// Set sets new function and return old.
		$this->assertEquals(
			array('en_GBLocalise', 'transliterate'),
			$lang->setTransliterator($function1)
		);

		$get = $lang->getTransliterator();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setTransliterator($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getTransliterator(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getTransliterator(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Tests the getPluralSuffixes method
	 *
	 * @covers  Joomla\Language\Language::getPluralSuffixes
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPluralSuffixes()
	{
		$this->assertEquals(
			array('0'),
			$this->object->getPluralSuffixes(0),
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			array('1'),
			$this->object->getPluralSuffixes(1),
			'Line: ' . __LINE__
		);

		TestHelper::setValue($this->object, 'pluralSuffixesCallback', null);
		$this->assertEquals(
			array(100),
			$this->object->getPluralSuffixes(100),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getPluralSuffixesCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPluralSuffixesCallback()
	{
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getPluralSuffixesCallback())
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setPluralSuffixesCallback
	 * @covers  Joomla\Language\Language::getPluralSuffixesCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetPluralSuffixesCallback()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getPluralSuffixesCallback())
		);

		$this->assertTrue(
			is_callable($lang->setPluralSuffixesCallback($function1))
		);

		$get = $lang->getPluralSuffixesCallback();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setPluralSuffixesCallback($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getPluralSuffixesCallback(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getPluralSuffixesCallback(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getIgnoredSearchWords
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetIgnoredSearchWords()
	{
		$lang = new Language('');

		$this->assertEquals(
			array('and', 'in', 'on'),
			$lang->getIgnoredSearchWords(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue($lang, 'ignoredSearchWordsCallback', null);
		$this->assertEmpty(
			$lang->getIgnoredSearchWords(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getIgnoredSearchWordsCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetIgnoredSearchWordsCallback()
	{
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getIgnoredSearchWordsCallback())
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setIgnoredSearchWordsCallback
	 * @covers  Joomla\Language\Language::getIgnoredSearchWordsCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetIgnoredSearchWordsCallback()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getIgnoredSearchWordsCallback())
		);

		// Note: set -> $funtion1: set returns NULL and get returns $function1
		$this->assertTrue(
			is_callable($lang->setIgnoredSearchWordsCallback($function1))
		);

		$get = $lang->getIgnoredSearchWordsCallback();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setIgnoredSearchWordsCallback($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getIgnoredSearchWordsCallback(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getIgnoredSearchWordsCallback(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getLowerLimitSearchWord
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetLowerLimitSearchWord()
	{
		$lang = new Language('');

		$this->assertEquals(
			3,
			$lang->getLowerLimitSearchWord(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue($lang, 'lowerLimitSearchWordCallback', null);
		$this->assertEquals(
			3,
			$lang->getLowerLimitSearchWord(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getLowerLimitSearchWordCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetLowerLimitSearchWordCallback()
	{
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getLowerLimitSearchWordCallback())
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setLowerLimitSearchWordCallback
	 * @covers  Joomla\Language\Language::getLowerLimitSearchWordCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetLowerLimitSearchWordCallback()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getLowerLimitSearchWordCallback())
		);

		// Note: set -> $funtion1: set returns NULL and get returns $function1
		$this->assertTrue(
			is_callable($lang->setLowerLimitSearchWordCallback($function1))
		);

		$get = $lang->getLowerLimitSearchWordCallback();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setLowerLimitSearchWordCallback($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getLowerLimitSearchWordCallback(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getLowerLimitSearchWordCallback(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getUpperLimitSearchWord
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUpperLimitSearchWord()
	{
		$lang = new Language('');

		$this->assertEquals(
			20,
			$lang->getUpperLimitSearchWord(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue($lang, 'upperLimitSearchWordCallback', null);
		$this->assertEquals(
			20,
			$lang->getUpperLimitSearchWord(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getUpperLimitSearchWordCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUpperLimitSearchWordCallback()
	{
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getUpperLimitSearchWordCallback())
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setUpperLimitSearchWordCallback
	 * @covers  Joomla\Language\Language::getUpperLimitSearchWordCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetUpperLimitSearchWordCallback()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getUpperLimitSearchWordCallback())
		);

		// Note: set -> $funtion1: set returns NULL and get returns $function1
		$this->assertTrue(
			is_callable($lang->setUpperLimitSearchWordCallback($function1))
		);

		$get = $lang->getUpperLimitSearchWordCallback();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setUpperLimitSearchWordCallback($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getUpperLimitSearchWordCallback(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getUpperLimitSearchWordCallback(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getSearchDisplayedCharactersNumber
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetSearchDisplayedCharactersNumber()
	{
		$lang = new Language('');

		$this->assertEquals(
			200,
			$lang->getSearchDisplayedCharactersNumber(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue($lang, 'searchDisplayedCharactersNumberCallback', null);
		$this->assertEquals(
			200,
			$lang->getSearchDisplayedCharactersNumber(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getSearchDisplayedCharactersNumberCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetSearchDisplayedCharactersNumberCallback()
	{
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getSearchDisplayedCharactersNumberCallback())
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setSearchDisplayedCharactersNumberCallback
	 * @covers  Joomla\Language\Language::getSearchDisplayedCharactersNumberCallback
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetSearchDisplayedCharactersNumberCallback()
	{
		$function1 = 'phpinfo';
		$function2 = 'print';
		$lang = new Language('');

		$this->assertTrue(
			is_callable($lang->getSearchDisplayedCharactersNumberCallback())
		);

		// Note: set -> $funtion1: set returns NULL and get returns $function1
		$this->assertTrue(
			is_callable($lang->setSearchDisplayedCharactersNumberCallback($function1))
		);

		$get = $lang->getSearchDisplayedCharactersNumberCallback();
		$this->assertEquals(
			$function1,
			$get,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$get,
			'Line: ' . __LINE__
		);

		// Note: set -> $function2: set returns $function1 and get retuns $function2
		$set = $lang->setSearchDisplayedCharactersNumberCallback($function2);
		$this->assertEquals(
			$function1,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function2,
			$set,
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			$function2,
			$lang->getSearchDisplayedCharactersNumberCallback(),
			'Line: ' . __LINE__
		);

		$this->assertNotEquals(
			$function1,
			$lang->getSearchDisplayedCharactersNumberCallback(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::exists
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExists()
	{
		$this->assertFalse(
			$this->object->exists(null)
		);

		$basePath = __DIR__ . '/data';

		$this->assertTrue(
			$this->object->exists('en-GB', $basePath)
		);

		$this->assertFalse(
			$this->object->exists('es-ES', $basePath)
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::load
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoad()
	{
		TestHelper::setValue($this->object, 'paths', array());

		$this->assertTrue($this->object->load());

		$filename = JPATH_ROOT . '/language/en-GB/en-GB.ini';
		$paths = TestHelper::getValue($this->object, 'paths');
		$this->assertArrayHasKey('joomla', $paths);
		$this->assertArrayHasKey(
			$filename,
			$paths['joomla']
		);
		$this->assertTrue($paths['joomla'][$filename]);

		// Loading non-existent language should load default language.
		TestHelper::setValue($this->object, 'paths', array());

		$this->assertTrue($this->object->load('joomla', JPATH_ROOT, 'es-ES'));

		$paths = TestHelper::getValue($this->object, 'paths');
		$this->assertArrayHasKey('joomla', $paths);
		$this->assertArrayHasKey(
			$filename,
			$paths['joomla']
		);
		$this->assertTrue($paths['joomla'][$filename]);

		// Don't reload if language file is already laoded.
		$this->assertTrue($this->object->load());
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::loadLanguage
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testLoadLanguage()
	{
		$ob = $this->object;

		TestHelper::setValue($ob, 'counter', 1);
		TestHelper::setValue($ob, 'strings', array('bar' => 'foo'));
		TestHelper::setValue($ob, 'override', array('FOO' => 'OOF'));

		$filename = __DIR__ . '/data/good.ini';
		$result = TestHelper::invoke($ob, 'loadLanguage', $filename);

		$this->assertTrue($result);
		$this->assertEquals(
			2,
			TestHelper::getValue($ob, 'counter')
		);

		$strings = TestHelper::getValue($ob, 'strings');
		$this->assertArrayHasKey('bar', $strings);
		$this->assertEquals('foo', $strings['bar']);
		$this->assertEquals('OOF', $strings['FOO']);

		$paths = TestHelper::getValue($ob, 'paths');
		$this->assertArrayHasKey($filename, $paths['unknown']);
		$this->assertTrue($paths['unknown'][$filename]);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::parse
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testParse()
	{
		$strings = TestHelper::invoke($this->object, 'parse', __DIR__ . '/data/good.ini');

		$this->assertNotEmpty(
			$strings,
			'Line: ' . __LINE__ . ' good ini file should load properly.'
		);

		$this->assertEquals(
			$strings,
			array('FOO' => 'Bar'),
			'Line: ' . __LINE__ . ' test that the strings were parsed correctly.'
		);

		$strings = TestHelper::invoke($this->object, 'parse', __DIR__ . '/data/bad.ini');

		$this->assertEmpty(
			$strings,
			'Line: ' . __LINE__ . ' bad ini file should not load properly.'
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::get
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGet()
	{
		$this->assertNull(
			$this->object->get('noExist')
		);

		$this->assertEquals(
			'abc',
			$this->object->get('noExist', 'abc')
		);

		// Note: property = tag, returns en-GB (default language)
		$this->assertEquals(
			'en-GB',
			$this->object->get('tag')
		);

		// Note: property = name, returns English (United Kingdom) (default language)
		$this->assertEquals(
			'English (United Kingdom)',
			$this->object->get('name')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getCallerInfo
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testGetCallerInfo()
	{
		$info = TestHelper::invoke($this->object, 'getCallerInfo');

		$this->assertArrayHasKey('function', $info);
		$this->assertArrayHasKey('class', $info);
		$this->assertArrayHasKey('step', $info);
		$this->assertArrayHasKey('file', $info);
		$this->assertArrayHasKey('line', $info);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getName
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetName()
	{
		$this->assertEquals(
			'English (United Kingdom)',
			$this->object->getName()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getPaths
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPaths()
	{
		// Non-existent extension, retuns NULL
		$this->assertNull(
			$this->object->getPaths('')
		);

		$paths = array('f' => 'foo', 'bar');
		TestHelper::setValue($this->object, 'paths', $paths);

		$this->assertEquals(
			$paths,
			$this->object->getPaths()
		);

		$this->assertEquals(
			'foo',
			$this->object->getPaths('f')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getErrorFiles
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetErrorFiles()
	{
		TestHelper::setValue($this->object, 'errorfiles', array('foo', 'bar'));
		$this->assertEquals(
			array('foo', 'bar'),
			$this->object->getErrorFiles()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getTag
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTag()
	{
		$this->assertEquals(
			'en-GB',
			$this->object->getTag()
		);

		TestHelper::setValue($this->object, 'metadata', array('tag' => 'foobar'));
		$this->assertEquals(
			'foobar',
			$this->object->getTag()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::isRtl
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testisRtl()
	{
		$this->assertFalse(
			$this->object->isRtl()
		);

		TestHelper::setValue($this->object, 'metadata', array('rtl' => true));
		$this->assertTrue(
			$this->object->isRtl()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setDebug
	 * @covers  Joomla\Language\Language::getDebug
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetSetDebug()
	{
		$current = $this->object->getDebug();
		$this->assertEquals(
			$current,
			$this->object->setDebug(true),
			'Line: ' . __LINE__
		);

		$this->object->setDebug(false);
		$this->assertFalse(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug(true);
		$this->assertTrue(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug(0);
		$this->assertFalse(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug(1);
		$this->assertTrue(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug('');
		$this->assertFalse(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug('test');
		$this->assertTrue(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);

		$this->object->setDebug('0');
		$this->assertFalse(
			$this->object->getDebug(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getDefault
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetDefault()
	{
		$this->assertEquals(
			'en-GB',
			$this->object->getDefault(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setDefault
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetDefault()
	{
		$this->object->setDefault('de-DE');
		$this->assertEquals(
			'de-DE',
			$this->object->getDefault(),
			'Line: ' . __LINE__
		);
		$this->object->setDefault('en-GB');
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getOrphans
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetOrphans()
	{
		$this->assertEmpty(
			$this->object->getOrphans(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue(
			$this->object,
			'orphans',
			array('COM_ADMIN.KEY' => array('caller info'))
		);
		$this->assertEquals(
			array('COM_ADMIN.KEY' => array('caller info')),
			$this->object->getOrphans(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getUsed
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUsed()
	{
		$this->assertEmpty(
			$this->object->getUsed(),
			'Line: ' . __LINE__
		);

		TestHelper::setValue(
			$this->object,
			'used',
			array('COM_ADMIN.KEY' => array('caller info'))
		);
		$this->assertEquals(
			array('COM_ADMIN.KEY' => array('caller info')),
			$this->object->getUsed(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::hasKey
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testHasKey()
	{
		// Key doesn't exist, returns false
		$this->assertFalse(
			$this->object->hasKey('com_admin.key')
		);

		TestHelper::setValue($this->object, 'strings', array('COM_ADMIN.KEY' => 'A key'));
		$this->assertTrue(
			$this->object->hasKey('com_admin.key')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getMetadata
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetMetadata()
	{
		// Language doesn't exist, retun NULL
		$this->assertNull(
		     TestHelper::invoke($this->object, 'getMetadata', 'es-ES')
		);

		$localeString = 'en_GB.utf8, en_GB.UTF-8, en_GB, eng_GB, en, english, english-uk, uk, gbr, britain, england, great britain, ' .
			'uk, united kingdom, united-kingdom';

		// In this case, returns array with default language
		// - same operation of get method with metadata property
		$options = array(
			'name' => 'English (United Kingdom)',
			'tag' => 'en-GB',
			'rtl' => '0',
			'locale' => $localeString,
			'firstDay' => '0'
		);

		// Language exists, returns array with values
		$this->assertEquals(
			$options,
			TestHelper::invoke($this->object, 'getMetadata', 'en-GB')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getKnownLanguages
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetKnownLanguages()
	{
		// This method returns a list of known languages
		$basePath = __DIR__ . '/data';

		$localeString = 'en_GB.utf8, en_GB.UTF-8, en_GB, eng_GB, en, english, english-uk, uk, gbr, britain, england, great britain,' .
			' uk, united kingdom, united-kingdom';

		$option1 = array(
			'name' => 'English (United Kingdom)',
			'tag' => 'en-GB',
			'rtl' => '0',
			'locale' => $localeString,
			'firstDay' => '0'
		);
		$listCompareEqual1 = array(
			'en-GB' => $option1,
		);

		$list = Language::getKnownLanguages($basePath);
		$this->assertEquals(
			$listCompareEqual1,
			$list,
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getLanguagePath
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetLanguagePath()
	{
		$basePath = 'test';

		// $language = null, returns language directory
		$this->assertEquals(
			'test/language',
			Language::getLanguagePath($basePath, null),
			'Line: ' . __LINE__
		);

		// $language = value (en-GB, for example), returns en-GB language directory
		$this->assertEquals(
			'test/language/en-GB',
			Language::getLanguagePath($basePath, 'en-GB'),
			'Line: ' . __LINE__
		);

		// With no argument JPATH_ROOT should be returned
		$this->assertEquals(
			JPATH_ROOT . '/language',
			Language::getLanguagePath(),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::setLanguage
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetLanguage()
	{
		$this->assertEquals(
			'en-GB',
			$this->object->setLanguage('es-ES'),
			'Line: ' . __LINE__
		);

		$this->assertEquals(
			'es-ES',
			$this->object->setLanguage('en-GB'),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getLocale
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetLocale()
	{
		TestHelper::setValue($this->object, 'metadata', array('locale' => null));
		$this->assertFalse($this->object->getLocale());

		TestHelper::setValue($this->object, 'locale', null);
		TestHelper::setValue($this->object, 'metadata', array('locale' => 'en_GB, en, english'));
		$this->assertEquals(
			array('en_GB', 'en', 'english'),
			$this->object->getLocale()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::getFirstDay
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetFirstDay()
	{
		TestHelper::setValue($this->object, 'metadata', array('firstDay' => null));
		$this->assertEquals(0, $this->object->getFirstDay());

		TestHelper::setValue($this->object, 'metadata', array('firstDay' => 1));
		$this->assertEquals(1, $this->object->getFirstDay());
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::parseLanguageFiles
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testParseLanguageFiles()
	{
		$dir = __DIR__ . '/data/language';
		$option = array(
			'name' => 'English (United Kingdom)',
			'tag' => 'en-GB',
			'rtl' => '0',
			'locale' => 'en_GB.utf8, en_GB.UTF-8, en_GB, eng_GB, en, english, english-uk, uk, gbr, britain, england,' .
				' great britain, uk, united kingdom, united-kingdom',
			'firstDay' => '0'
		);
		$expected = array(
			'en-GB' => $option
		);

		$result = Joomla\Language\Language::parseLanguageFiles($dir);

		$this->assertEquals(
			$expected,
			$result,
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::parseXmlLanguageFile
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testParseXmlLanguageFile()
	{
		$option = array(
			'name' => 'English (United Kingdom)',
			'tag' => 'en-GB',
			'rtl' => '0',
			'locale' => 'en_GB.utf8, en_GB.UTF-8, en_GB, eng_GB, en, english, english-uk, uk, gbr, britain, england, great britain,' .
				' uk, united kingdom, united-kingdom',
			'firstDay' => '0'
		);
		$path = __DIR__ . '/data/language/en-GB/en-GB.xml';

		$this->assertEquals(
			$option,
			Language::parseXmlLanguageFile($path),
			'Line: ' . __LINE__
		);

		$path2 = __DIR__ . '/data/language/es-ES/es-ES.xml';
		$this->assertEquals(
			$option,
			Language::parseXmlLanguageFile($path),
			'Line: ' . __LINE__
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Language::parseXmlLanguageFile
	 * @expectedException  RuntimeException
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testParseXmlLanguageFileException()
	{
		$path = __DIR__ . '/data/language/es-ES/es-ES.xml';

		Language::parseXmlLanguageFile($path);
	}
}
