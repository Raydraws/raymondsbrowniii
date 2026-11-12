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
 * Renders a button separator
 *
 * @since  3.0
 */
class SeparatorButton extends ToolbarButton
{
	/**
	 * Property layout.
	 *
	 * @var  string
	 *
	 * @since  4.0.0
	 */
	protected $layout = 'joomla.toolbar.separator';

	/**
	 * Empty implementation (not required for separator)
	 *
	 * @return  string  Empty string for separator
	 *
	 * @since   3.0
	 *
	 * @deprecated  5.0 Use render() instead.
	 */
	public function fetchButton()
	{
		return '';
	}
}
