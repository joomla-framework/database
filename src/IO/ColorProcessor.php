<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\IO;

/**
 * Command line output processor supporting ANSI-colored output
 *
 * @since  __DEPLOY_VERSION__
 */
class ColorProcessor implements OutputProcessorInterface
{
	/**
	 * Flag to remove color codes from the output
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	private $colorsSupported = false;

	/**
	 * Regex to match tags
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $tagFilter = '/<([a-z=;]+)>(.*?)<\/\\1>/s';

	/**
	 * Regex used for removing color codes
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $stripFilter = '/<[\/]?[a-z=;]+>/';

	/**
	 * Supported color styles
	 *
	 * @var    ColorStyle[]
	 * @since  __DEPLOY_VERSION__
	 */
	protected $styles = [];

	/**
	 * Class constructor
	 *
	 * @param   boolean|null  $colorsSupported  Defines non-colored mode on construct or null to auto detect based on the environment
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($colorsSupported = null)
	{
		if ($colorsSupported === null)
		{
			/*
			 * By default windows cmd.exe and PowerShell does not support ANSI-colored output
			 * if the variable is not set explicitly colors should be disabled on Windows
			 */
			$colorsSupported = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		}

		$this->setColorSupport($colorsSupported);

		$this->addPredefinedStyles();
	}

	/**
	 * Add a style.
	 *
	 * @param   string      $name   The style name.
	 * @param   ColorStyle  $style  The color style.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addStyle(string $name, ColorStyle $style)
	{
		$this->styles[$name] = $style;

		return $this;
	}

	/**
	 * Check if color output is supported.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasColorSupport(): bool
	{
		return $this->colorsSupported;
	}

	/**
	 * Process a string.
	 *
	 * @param   string  $string  The string to process.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process(string $string): string
	{
		preg_match_all($this->tagFilter, $string, $matches);

		if (!$matches)
		{
			return $string;
		}

		foreach ($matches[0] as $i => $m)
		{
			if (array_key_exists($matches[1][$i], $this->styles))
			{
				$string = $this->replaceColors($string, $matches[1][$i], $matches[2][$i], $this->styles[$matches[1][$i]]);
			}
			// Custom format
			elseif (strpos($matches[1][$i], '='))
			{
				$string = $this->replaceColors($string, $matches[1][$i], $matches[2][$i], ColorStyle::fromString($matches[1][$i]));
			}
		}

		return $string;
	}

	/**
	 * Set whether color output is supported.
	 *
	 * @param   boolean  $colorsSupported  Flag if color output is supported
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setColorSupport(bool $colorsSupported)
	{
		$this->colorsSupported = $colorsSupported;

		return $this;
	}

	/**
	 * Strip color tags from a string.
	 *
	 * @param   string  $string  The string.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function stripColors(string $string): string
	{
		return preg_replace(static::$stripFilter, '', $string);
	}

	/**
	 * Adds predefined color styles to the ColorProcessor object.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function addPredefinedStyles()
	{
		$this->addStyle(
			'info',
			new ColorStyle('green', '', ['bold'])
		);

		$this->addStyle(
			'comment',
			new ColorStyle('yellow', '', ['bold'])
		);

		$this->addStyle(
			'question',
			new ColorStyle('black', 'cyan')
		);

		$this->addStyle(
			'error',
			new ColorStyle('white', 'red')
		);

		return $this;
	}

	/**
	 * Replace color tags in a string.
	 *
	 * @param   string      $text   The original text.
	 * @param   string      $tag    The matched tag.
	 * @param   string      $match  The match.
	 * @param   ColorStyle  $style  The color style to apply.
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function replaceColors(string $text, string $tag, string $match, ColorStyle $style)
	{
		$replace = $this->hasColorSupport()
			? $match
			: "\033[" . $style . "m" . $match . "\033[0m";

		return str_replace('<' . $tag . '>' . $match . '</' . $tag . '>', $replace, $text);
	}
}
