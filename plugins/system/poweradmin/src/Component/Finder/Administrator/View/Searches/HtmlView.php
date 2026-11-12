<?php
/**
 * @package     Poweradmin
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Finder\Administrator\View\Searches;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Finder\Administrator\Helper\FinderHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;

/**
 * View class for a list of search terms.
 *
 * @since  4.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * True if gathering search statistics is enabled
	 *
	 * @var  boolean
	 */
	protected $enabled;

	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

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
	 * The actions the user is authorised to perform
	 *
	 * @var    \JObject
	 * @since  4.0.0
	 */
	protected $canDo;

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
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$app                 = Factory::getApplication();
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->enabled       = $this->state->params->get('logging_enabled');
		$this->canDo         = ContentHelper::getActions('com_finder');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// self::addSubmenu('searches');

		// Check if plugin is enabled
		if (!$this->enabled)
		{
			$app->enqueueMessage(Text::_('TPL_FINDER_LOGGING_DISABLED'), 'warning');
		}

		// Prepare the view.
		$this->addToolbar();
		$this->sidebar = Sidebar::render();

		return parent::display($tpl);
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
		$canDo = $this->canDo;

		// Set title
		ToolbarHelper::title(Text::_('COM_FINDER_MANAGER_SEARCHES'), 'search');

		// Help button
		ToolbarHelper::help('JHELP_COMPONENTS_FINDER_MANAGE_SEARCHES');

		// Options button
		ToolbarHelper::divider();

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_finder');
		}

		// Reset button
		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::custom('searches.reset', 'refresh.png', 'refresh_f2.png', 'JSEARCH_RESET', false);
		}
	}
}
