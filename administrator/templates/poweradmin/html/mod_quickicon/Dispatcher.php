<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_quickicon
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Module\Quickicon\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\Module\Quickicon\Administrator\Helper\QuickIconHelper;

/**
 * Dispatcher class for mod_quickicon
 *
 * @since  4.0.0
 */
class Dispatcher extends AbstractModuleDispatcher
{
	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$data['buttons'] = QuickIconHelper::getButtons($data['params'], $this->getApplication());

		return $data;
	}
}
