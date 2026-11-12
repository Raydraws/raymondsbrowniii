<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_quickicon
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Module\Quickicon\Administrator\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Event\AbstractEvent;

/**
 * Event object for retrieving pluggable quick icons
 *
 * @since  4.0.0
 */
class QuickIconsEvent extends AbstractEvent
{
	/**
	 * The event context
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	private $context;

	/**
	 * Get the event context
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Set the event context
	 *
	 * @param   string  $context  The event context
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	public function setContext($context)
	{
		$this->context = $context;

		return $context;
	}
}
