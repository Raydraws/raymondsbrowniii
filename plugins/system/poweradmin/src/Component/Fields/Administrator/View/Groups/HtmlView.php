<?php
/**
 * @package     Poweradmin
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Fields\Administrator\View\Groups;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Groups View
 *
 * @since  3.7.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configuration array.
	 *
	 * @since   3.7.0
	 */
	public function __construct($config = [])
	{
		$toolbarAutoload = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar/autoload.php';

		if (is_file($toolbarAutoload))
		{
			require_once $toolbarAutoload;
		}

		parent::__construct($config);
	}
	/**
	 * @var  \JForm
	 *
	 * @since  3.7.0
	 */
	public $filterForm;

	/**
	 * @var  array
	 *
	 * @since  3.7.0
	 */
	public $activeFilters;

	/**
	 * @var  array
	 *
	 * @since  3.7.0
	 */
	protected $items;

	/**
	 * @var  \JPagination
	 *
	 * @since  3.7.0
	 */
	protected $pagination;

	/**
	 * @var  \JObject
	 *
	 * @since  3.7.0
	 */
	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     HtmlView::loadTemplate()
	 * @since   3.7.0
	 */
	public function display($tpl = null)
	{
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Display a warning if the fields system plugin is disabled
		if (!PluginHelper::isEnabled('system', 'fields'))
		{
			$link = Route::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . FieldsHelper::getFieldsPluginId());
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_FIELDS_SYSTEM_PLUGIN_NOT_ENABLED', $link), 'warning');
		}

		$this->addToolbar();

		// We do not need to filter by language when multilingual is disabled
		if (!Multilanguage::isEnabled())
		{
			unset($this->activeFilters['language']);
			$this->filterForm->removeField('language', 'filter');
		}

		return parent::display($tpl);
	}

	/**
	 * Adds the toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.7.0
	 */
	protected function addToolbar()
	{
		$groupId   = $this->state->get('filter.group_id');
		$component = '';
		$parts     = FieldsHelper::extract($this->state->get('filter.context'));

		if ($parts)
		{
			$component = $parts[0];
		}

		$canDo     = ContentHelper::getActions($component, 'fieldgroup', $groupId);
		$toolbar   = Toolbar::getInstance('toolbar');

		// Avoid nonsense situation.
		if ($component == 'com_fields')
		{
			return;
		}

		// Load component language file
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_ADMINISTRATOR)
		|| $lang->load($component, Path::clean(JPATH_ADMINISTRATOR . '/components/' . $component));

		$title = Text::sprintf('COM_FIELDS_VIEW_GROUPS_TITLE', Text::_(strtoupper($component)));

		// Prepare the toolbar.
		ToolbarHelper::title($title, 'puzzle-piece fields ' . substr($component, 4) . '-groups');

		if ($canDo->get('core.edit.state') || Factory::getUser()->authorise('core.admin'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('COM_CONTENT_SELECT_AN_ACTION')
				->toggleSplit(false)
				->icon('icon-select')
				->buttonClass('btn btn-white')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canDo->get('core.edit.state'))
			{
				$childBar->publish('groups.publish')->listCheck(true);

				$childBar->unpublish('groups.unpublish')->listCheck(true);

				$childBar->archive('groups.archive')->listCheck(true);
			}

			if (Factory::getUser()->authorise('core.admin'))
			{
				$childBar->checkin('groups.checkin')->listCheck(true);
			}

			if ($canDo->get('core.edit.state'))
			{
				$childBar->trash('groups.trash')->listCheck(true);
			}

			// Add a batch button
			if ($canDo->get('core.create') && $canDo->get('core.edit') && $canDo->get('core.edit.state'))
			{
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete', $component))
		{
			$toolbar->delete('groups.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		$toolbar->help('Field_Groups');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			$toolbar->preferences($component);
		}

		if ($canDo->get('core.create'))
		{
			$toolbar->addNew('group.add');
		}
	}

	/**
	 * Returns the sort fields.
	 *
	 * @return  array
	 *
	 * @since   3.7.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering'  => Text::_('JGRID_HEADING_ORDERING'),
			'a.state'     => Text::_('JSTATUS'),
			'a.title'     => Text::_('JGLOBAL_TITLE'),
			'a.access'    => Text::_('JGRID_HEADING_ACCESS'),
			'language'    => Text::_('JGRID_HEADING_LANGUAGE'),
			'a.context'   => Text::_('JGRID_HEADING_CONTEXT'),
			'a.id'        => Text::_('JGRID_HEADING_ID'),
		);
	}
}
