<?php
/**
 * @package     Poweradmin
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Plugins\Administrator\View\Plugins;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of plugins.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
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
	 * @var    \JObject
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
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

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
		$canDo = ContentHelper::getActions('com_plugins');

		ToolbarHelper::title(Text::_('COM_PLUGINS_MANAGER_PLUGINS'), 'plugins plugin');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Action button
		$dropdown = $toolbar->dropdownButton('status-group')
			->text('COM_CONTENT_SELECT_AN_ACTION')
			->toggleSplit(false)
			->icon('icon-select')
			->buttonClass('btn btn-white')
			->listCheck(true);

		$childBar = $dropdown->getChildToolbar();

		if ($canDo->get('core.edit.state'))
		{
			$childBar->publish('plugins.publish', 'JTOOLBAR_ENABLE')->listCheck(true);
			$childBar->unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE')->listCheck(true);
			$childBar->checkin('plugins.checkin');
		}

		// Help button
		$toolbar->help('JHELP_EXTENSIONS_PLUGIN_MANAGER');

		// Option button
		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_plugins');
		}

	}

	/**
	 * Returns an array of fields the table can be sorted by.
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value.
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'ordering'     => Text::_('JGRID_HEADING_ORDERING'),
			'enabled'      => Text::_('JSTATUS'),
			'name'         => Text::_('JGLOBAL_TITLE'),
			'folder'       => Text::_('COM_PLUGINS_FOLDER_HEADING'),
			'element'      => Text::_('COM_PLUGINS_ELEMENT_HEADING'),
			'access'       => Text::_('JGRID_HEADING_ACCESS'),
			'extension_id' => Text::_('JGRID_HEADING_ID'),
		);
	}
}
