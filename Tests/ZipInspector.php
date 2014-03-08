<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Zip as ArchiveZip;

/**
 * Inspector for the ArchiveZip class.
 *
 * @since  1.0
 */
class ZipInspector extends ArchiveZip
{
	/**
	 * Inspects the extractCustom Method.
	 *
	 * @param   string  $archive      Path to zip archive to extract
	 * @param   string  $destination  Path to extract archive into
	 * @param   array   $options      An array of options
	 *
	 * @return mixed
	 */
	public function accessExtractCustom($archive, $destination, array $options = array())
	{
		return parent::extractCustom($archive, $destination, $options);
	}

	/**
	 * Inspects the extractNative Method.
	 *
	 * @param   string  $archive      Path to zip archive to extract
	 * @param   string  $destination  Path to extract archive into
	 * @param   array   $options      An array of options
	 *
	 * @return bool
	 */
	public function accessExtractNative($archive, $destination, array $options = array())
	{
		return parent::extractNative($archive, $destination, $options);
	}
}
