<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Poweradmin\Admin\Toolbar\Button;

\defined('_JEXEC') or die;

use Poweradmin\Admin\Toolbar\ToolbarButton;

/**
 * Renders a basic button.
 *
 * @since  4.0.0
 */
class BasicButton extends ToolbarButton
{
	/**
	 * Property layout.
	 *
	 * @var  string
	 *
	 * @since  4.0.0
	 */
	protected $layout = 'joomla.toolbar.basic';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Unused string.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 *
	 * @deprecated  5.0 Use render() instead.
	 * @throws  \LogicException
	 */
	public function fetchButton($type = 'Basic')
	{
		throw new \LogicException('This is a new button in 4.0, please use render() instead.');
	}
}
