<?php
/**
 * Part of the Joomla Framework Profiler Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Profiler;

/**
 * Interface for profiler renderers.
 *
 * @since  1.0
 */
interface ProfilerRendererInterface
{
	/**
	 * Render the profiler.
	 *
	 * @param   ProfilerInterface  $profiler  The profiler to render.
	 *
	 * @return  string  The rendered profiler.
	 *
	 * @since   1.0
	 */
	public function render(ProfilerInterface $profiler);
}
