<?php
/**
 * @package     Poweradmin
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Finder\Administrator\View\Maps;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Finder\Administrator\Helper\FinderHelper;
use Joomla\Component\Finder\Administrator\Helper\LanguageHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;

/**
 * Groups view class for Finder.
 *
 * @since  2.5
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 *
	 * @since  3.6.1
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \Joomla\CMS\Pagination\Pagination
	 *
	 * @since  3.6.1
	 */
	protected $pagination;

	/**
	 * The HTML markup for the sidebar
	 *
	 * @var  string
	 *
	 * @since  3.6.1
	 */
	protected $sidebar;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 *
	 * @since  3.6.1
	 */
	protected $state;

	/**
	 * The total number of items
	 *
	 * @var  integer
	 *
	 * @since  3.6.1
	 */
	protected $total;

	/**
	 * Form object for search filters
	 *
	 * @var    \JForm
	 * @since  4.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	public $activeFilters;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.5
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
	 * Add the submenu items to the sidebar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		Sidebar::addEntry(
			Text::_('COM_FINDER_SUBMENU_INDEX'),
			'index.php?option=com_finder&view=index',
			$vName === 'index'
		);
		Sidebar::addEntry(
			Text::_('COM_FINDER_SUBMENU_MAPS'),
			'index.php?option=com_finder&view=maps',
			$vName === 'maps'
		);
		Sidebar::addEntry(
			Text::_('COM_FINDER_SUBMENU_FILTERS'),
			'index.php?option=com_finder&view=filters',
			$vName === 'filters'
		);
		Sidebar::addEntry(
			Text::_('COM_FINDER_SUBMENU_SEARCHES'),
			'index.php?option=com_finder&view=searches',
			$vName === 'searches'
		);
	}

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise an \Exception object.
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		// Load plugin language files.
		LanguageHelper::loadPluginLanguage();

		// Load the view data.
		$this->items         = $this->get('Items');
		$this->total         = $this->get('Total');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// self::addSubmenu('maps');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Prepare the view.
		$this->addToolbar();
		$this->sidebar = Sidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Method to configure the toolbar for this view.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_finder');

		// Set title
		ToolbarHelper::title(Text::_('COM_FINDER_MAPS_TOOLBAR_TITLE'), 'zoom-in finder');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Action button
		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('COM_CONTENT_SELECT_AN_ACTION')
				->toggleSplit(false)
				->icon('icon-select')
				->buttonClass('btn btn-white')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			$childBar->publish('maps.publish')->listCheck(true);
			$childBar->unpublish('maps.unpublish')->listCheck(true);
		}

		// Delete button
		ToolbarHelper::divider();

		if ($canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('', 'maps.delete');
			ToolbarHelper::divider();
		}

		// Help button
		ToolbarHelper::help('JHELP_COMPONENTS_FINDER_MANAGE_CONTENT_MAPS');

		// Option button
		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_finder');
		}

		// Statistics button
		ToolbarHelper::divider();
		Toolbar::getInstance('toolbar')->appendButton(
			'Popup',
			'bars',
			'COM_FINDER_STATISTICS',
			'index.php?option=com_finder&view=statistics&tmpl=component',
			550,
			350
		);
	}
}
