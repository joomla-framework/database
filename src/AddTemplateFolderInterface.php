<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

/**
 * Interface for a renderer which can have template paths added during runtime.
 *
 * @since  __DEPLOY_VERSION__
 */
interface AddTemplateFolderInterface
{
	/**
	 * Add a folder with alias to the renderer
	 *
	 * @param   string  $directory  The folder path
	 * @param   string  $alias      The folder alias
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addFolder($directory, $alias = null);
}
