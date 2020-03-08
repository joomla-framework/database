<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use Joomla\Application\Tests\Stubs\ConcreteDaemon;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Application\AbstractDaemonApplication.
 *
 * @requires extension pcntl
 */
class AbstractDaemonApplicationTest extends TestCase
{
	/**
	 * An instance of a Daemon inspector.
	 *
	 * @var  ConcreteDaemon
	 */
	protected $inspector;

	/**
	 * Setup for testing.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		// Get a new ConcreteDaemon instance.
		$this->inspector = new ConcreteDaemon;
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		// Reset some daemon inspector static settings.
		ConcreteDaemon::$pcntlChildExitStatus = 0;
		ConcreteDaemon::$pcntlFork = 0;
		ConcreteDaemon::$pcntlSignal = true;
		ConcreteDaemon::$pcntlWait = 0;

		$pidPath = JPATH_ROOT . '/japplicationdaemontest.pid';

		if (file_exists($pidPath))
		{
			unlink($pidPath);
		}

		parent::tearDown();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		ini_restore('memory_limit');

		parent::tearDownAfterClass();
	}

	/**
	 * Tests the Joomla\Application\Daemon::setupSignalHandlers method.
	 */
	public function testSetupSignalHandlers()
	{
		$this->inspector->setClassSignals(array('SIGTERM', 'SIGHUP', 'SIGFOOBAR123'));

		$this->assertTrue(
			$this->inspector->setupSignalHandlers(),
			'Check that only setupSignalHandlers return is true.'
		);

		$this->assertCount(
			2,
			$this->inspector->setupSignalHandlers,
			'Check that only the two valid signals are setup.'
		);
	}

	/**
	 * Tests the Joomla\Application\Daemon::setupSignalHandlers method.
	 */
	public function testSetupSignalHandlersFailure()
	{
		ConcreteDaemon::$pcntlSignal = false;

		$this->inspector->setClassSignals(array('SIGTERM', 'SIGHUP', 'SIGFOOBAR123'));

		$this->assertFalse($this->inspector->setupSignalHandlers());

		$this->assertCount(
			0,
			$this->inspector->setupSignalHandlers,
			'Check that no signals are setup.'
		);
	}

	/**
	 * Tests the Joomla\Application\Daemon::writeProcessIdFile method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testWriteProcessIdFile()
	{
		$pidPath = JPATH_ROOT . '/japplicationdaemontest.pid';

		// We set a custom process id file path so that we don't interfere
		// with other tests that are running on a system
		$this->inspector->set('application_pid_file', $pidPath);

		// Get the current process id and set it to the daemon instance.
		$pid = (int) posix_getpid();
		$this->inspector->setClassProperty('processId', $pid);

		// Execute the writeProcessIdFile method.
		$this->inspector->writeProcessIdFile();

		// Check the value of the file.
		$this->assertSame(
			$pid,
			(int) file_get_contents($this->inspector->getClassProperty('config')->get('application_pid_file'))
		);

		// Check the permissions on the file.
		$this->assertEquals(
			'0644',
			substr(decoct(fileperms($this->inspector->getClassProperty('config')->get('application_pid_file'))), 1)
		);
	}
}
