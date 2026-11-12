<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Poweradmin\Admin\Toolbar;

\defined('_JEXEC') or die;

/**
 * Interface for creating toolbar objects
 *
 * @since  4.0.0
 */
interface ToolbarFactoryInterface
{
	/**
	 * Creates a new toolbar button.
	 *
	 * @param   Toolbar  $toolbar  The Toolbar instance to attach to the button
	 * @param   string   $type     Button Type
	 *
	 * @return  ToolbarButton
	 *
	 * @since   4.0.0
	 * @throws  \InvalidArgumentException
	 */
	public function createButton(Toolbar $toolbar, string $type): ToolbarButton;

	/**
	 * Creates a new Toolbar object.
	 *
	 * @param   string  $name  The toolbar name.
	 *
	 * @return  Toolbar
	 *
	 * @since   4.0.0
	 */
	public function createToolbar(string $name = 'toolbar'): Toolbar;
}
