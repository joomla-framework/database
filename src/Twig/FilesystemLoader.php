<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 - 2015 Roman Kinyakin. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer\Twig;

/**
 * Twig class for rendering output.
 *
 * @since  1.0
 */
class FilesystemLoader extends \Twig_Loader_Filesystem
{
	/**
	 * Extension of template files
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $extension = '';

	/**
	 * Sets the file extension to use for template files
	 *
	 * @param   string  $extension  File extension to use for template files
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setExtension($extension)
	{
		// Remove dots in the beginning
		$extension = ltrim($extension, '.');

		// If the extension is not empty add dot again
		if (!empty($extension))
		{
			$extension = '.' . $extension;
		}

		$this->extension = $extension;
	}

	/**
	 * Attempts to find the specified template file
	 *
	 * @param   string  $name  Template file to locate
	 *
	 * @return  string
	 *
	 * @since   1.0
	 * @throws  \Twig_Error_Loader
	 */
	protected function findTemplate($name)
	{
		$parts = explode('.', $name);

		$extension = count($parts > 1) ? '.' . end($parts) : '';

		if ($extension != $this->extension)
		{
			$name .= $this->extension;
		}

		return parent::findTemplate($name);
	}
}
