<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Renderer;

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * PhpEngine template renderer
 *
 * @since  2.0.0
 */
class PhpEngineRenderer extends AbstractRenderer
{
	/**
	 * Rendering engine
	 *
	 * @var    PhpEngine
	 * @since  2.0.0
	 */
	private $renderer;

	/**
	 * Constructor
	 *
	 * @param   PhpEngine  $renderer  Rendering engine
	 *
	 * @since   2.0.0
	 */
	public function __construct(PhpEngine $renderer)
	{
		$this->renderer = $renderer;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  PhpEngine
	 *
	 * @since   2.0.0
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * Checks if folder, folder alias, template or template path exists
	 *
	 * @param   string  $path  Full path or part of a path
	 *
	 * @return  boolean  True if the path exists
	 *
	 * @since   2.0.0
	 */
	public function pathExists(string $path): bool
	{
		return $this->getRenderer()->exists($path);
	}

	/**
	 * Render and return compiled data.
	 *
	 * @param   string|TemplateReferenceInterface  $template  A template name or a TemplateReferenceInterface instance
	 * @param   array                              $data      The data to pass to the template
	 *
	 * @return  string  Compiled data
	 *
	 * @since   2.0.0
	 */
	public function render(string $template, array $data = []): string
	{
		$data = array_merge($this->data, $data);

		return $this->getRenderer()->render($template, $data);
	}
}
