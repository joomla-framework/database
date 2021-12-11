<?php
/**
 * Part of the Joomla Framework View Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View;

use Joomla\Model\ModelInterface;

/**
 * Joomla Framework Abstract View Class
 *
 * @since  1.0
 */
abstract class AbstractView implements ViewInterface
{
	/**
	 * The model object.
	 *
	 * @var    ModelInterface
	 * @since  1.0
	 * @deprecated  2.0  A view object will not require a ModelInterface implementation
	 */
	protected $model;

	/**
	 * Method to instantiate the view.
	 *
	 * @param   ModelInterface  $model  The model object.
	 *
	 * @since   1.0
	 * @deprecated  2.0  A view object will not require a ModelInterface implementation
	 */
	public function __construct(ModelInterface $model = null)
	{
		// Setup dependencies.
		$this->model = $model;
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     ViewInterface::escape()
	 * @since   1.0
	 * @deprecated  2.0  Interface method is deprecated without replacement.
	 */
	public function escape($output)
	{
		return $output;
	}
}
