<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Installer\Administrator\View\Update;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\View\Installer\HtmlView as InstallerViewDefault;

/**
 * Extension Manager Update View
 *
 * @since  1.6
 */
class HtmlView extends InstallerViewDefault
{
	/**
	 * List of update items.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * List pagination.
	 *
	 * @var \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct($config = [])
	{
		$toolbarAutoload = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar/autoload.php';
		if (is_file($toolbarAutoload)) {
			require_once $toolbarAutoload;
		}
		parent::__construct($config);
	}

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		// Get data from the model.
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$paths        = new \stdClass;
		$paths->first = '';

		$this->paths = &$paths;

		if (count($this->items) > 0)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_INSTALLER_MSG_WARNINGS_UPDATE_NOTICE'), 'warning');
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		ToolbarHelper::custom('update.purge', 'purge', 'purge', 'COM_INSTALLER_TOOLBAR_PURGE', false);
		ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_UPDATE');
		parent::addToolbar();
		ToolbarHelper::custom('update.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_UPDATES', false);
		ToolbarHelper::custom('update.update', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_UPDATE', true);
	}
}
