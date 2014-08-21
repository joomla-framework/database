<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Language\Stemmer\Porteren;

/**
 * Test class for Porteren.
 *
 * @since  1.0
 */
class PorterenTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Porteren
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Porteren;
	}

	/**
	 * Data provider for testStem()
	 *
	 * @return array
	 */
	public function testData()
	{
		return array(
			array('Car', 'Car', 'en'),
			array('Cars', 'Car', 'en'),
			array('fishing', 'fish', 'en'),
			array('fished', 'fish', 'en'),
			array('fish', 'fish', 'en'),
			array('powerful', 'power', 'en'),
			array('Reflect', 'Reflect', 'en'),
			array('Reflects', 'Reflect', 'en'),
			array('Reflected', 'Reflect', 'en'),
			array('stemming', 'stem', 'en'),
			array('stemmed', 'stem', 'en'),
			array('walk', 'walk', 'en'),
			array('walking', 'walk', 'en'),
			array('walked', 'walk', 'en'),
			array('walks', 'walk', 'en'),
			array('allowance', 'allow', 'en'),
			array('us', 'us', 'en'),
			array('I', 'I', 'en'),
			array('Standardabweichung', 'Standardabweichung', 'de'),

			// Step 1a
			array('caresses', 'caress', 'en'),
			array('ponies', 'poni', 'en'),
			array('ties', 'ti', 'en'),
			array('caress', 'caress', 'en'),
			array('cats', 'cat', 'en'),

			// Step 1b
			array('feed', 'feed', 'en'),
			array('agreed', 'agre', 'en'),
			array('plastered', 'plaster', 'en'),
			array('bled', 'bled', 'en'),
			array('motoring', 'motor', 'en'),
			array('sing', 'sing', 'en'),
			array('conflated', 'conflat', 'en'),
			array('troubled', 'troubl', 'en'),
			array('sized', 'size', 'en'),
			array('hopping', 'hop', 'en'),
			array('tanned', 'tan', 'en'),
			array('falling', 'fall', 'en'),
			array('hissing', 'hiss', 'en'),
			array('fizzed', 'fizz', 'en'),
			array('failing', 'fail', 'en'),
			array('filing', 'file', 'en'),

			// Step 1c
			array('happy', 'happi', 'en'),
			array('sky', 'sky', 'en'),

			// Step 2
			array('relational', 'relat', 'en'),
			array('conditional', 'condit', 'en'),
			array('rational', 'ration', 'en'),
			array('valenci', 'valenc', 'en'),
			array('hesitanci', 'hesit', 'en'),
			array('digitizer', 'digit', 'en'),
			array('antropologi', 'antropolog', 'en'),
			array('conformabli', 'conform', 'en'),
			array('radicalli', 'radic', 'en'),
			array('differentli', 'differ', 'en'),
			array('vileli', 'vile', 'en'),
			array('analogousli', 'analog', 'en'),
			array('vietnamization', 'vietnam', 'en'),
			array('predication', 'predic', 'en'),
			array('operator', 'oper', 'en'),
			array('feudalism', 'feudal', 'en'),
			array('decisiveness', 'decis', 'en'),
			array('hopefulness', 'hope', 'en'),
			array('callousness', 'callous', 'en'),
			array('formaliti', 'formal', 'en'),
			array('sensitiviti', 'sensit', 'en'),
			array('sensibiliti', 'sensibl', 'en'),

			// Step 3
			array('triplicate', 'triplic', 'en'),
			array('formative', 'form', 'en'),
			array('formalize', 'formal', 'en'),
			array('electriciti', 'electr', 'en'),
			array('electrical', 'electr', 'en'),
			array('hopeful', 'hope', 'en'),
			array('goodness', 'good', 'en'),

			// Step 4
			array('revival', 'reviv', 'en'),
			array('allowance', 'allow', 'en'),
			array('inference', 'infer', 'en'),
			array('airliner', 'airlin', 'en'),
			array('gyroscopic', 'gyroscop', 'en'),
			array('adjustable', 'adjust', 'en'),
			array('defensible', 'defens', 'en'),
			array('irritant', 'irrit', 'en'),
			array('replacement', 'replac', 'en'),
			array('adjustment', 'adjust', 'en'),
			array('dependent', 'depend', 'en'),
			array('adoption', 'adopt', 'en'),
			array('homologou', 'homolog', 'en'),
			array('communism', 'commun', 'en'),
			array('activate', 'activ', 'en'),
			array('angulariti', 'angular', 'en'),
			array('homologous', 'homolog', 'en'),
			array('effective', 'effect', 'en'),
			array('bowdlerize', 'bowdler', 'en'),

			// Step 5a
			array('probate', 'probat', 'en'),
			array('rate', 'rate', 'en'),
			array('cease', 'ceas', 'en'),

			// Step 5b
			array('controll', 'control', 'en'),
			array('roll', 'roll', 'en'),
		);
	}

	/**
	 * Test...
	 *
	 * @param   string  $token   @todo
	 * @param   string  $result  @todo
	 * @param   string  $lang    @todo
	 *
	 * @covers  Joomla\Language\Stemmer\Porteren::stem
	 * @covers  Joomla\Language\Stemmer\Porteren::<!public>
	 * @dataProvider testData
	 *
	 * @return void
	 */
	public function testStem($token, $result, $lang)
	{
		$this->assertEquals($result, $this->object->stem($token, $lang));
	}
}
