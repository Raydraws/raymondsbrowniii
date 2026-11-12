<?php
/**
 * @package     Poweradmin
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Templates\Administrator\View\Styles;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Templates\Administrator\Helper\TemplatesHelper;

/**
 * View class for a list of template styles.
 *
 * @since  1.6
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
	 * @var  \Joomla\CMS\Pagination\Pagination
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
	 * Is the parameter enabled to show template positions in the frontend?
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	public $preview;

	public function __construct($config = [])
	{
		$toolbarAutoload = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar/autoload.php';
		if (is_file($toolbarAutoload)) {
			require_once $toolbarAutoload;
		}
		parent::__construct($config);
	}

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->total         = $this->get('Total');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->preview       = ComponentHelper::getParams('com_templates')->get('template_positions_display');

		// Remove the menu item filter for administrator styles.
		if ((int) $this->state->get('client_id') !== 0)
		{
			unset($this->activeFilters['menuitem']);
			$this->filterForm->removeField('menuitem', 'filter');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Populate xmldata and thumbnail for each item
		$this->populateItemData();

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Populate xmldata and thumbnail for each template style item
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateItemData()
	{
		if (empty($this->items))
		{
			return;
		}

		foreach ($this->items as &$item)
		{
			// Get client info
			$client = ApplicationHelper::getClientInfo($item->client_id);
			
			// Populate xmldata if not already set
			if (!isset($item->xmldata))
			{
				$templateBaseDir = $client->id === 0 ? JPATH_ROOT : JPATH_ROOT . '/administrator';
				$item->xmldata = TemplatesHelper::parseXMLTemplateFile($templateBaseDir, $item->template);
			}

			// Populate thumbnail if not already set
			if (!isset($item->thumbnail))
			{
				$item->thumbnail = $this->getThumbnailPath($item, $client);
			}
		}
	}

	/**
	 * Get thumbnail path for a template style
	 *
	 * @param   object  $item    The template style item
	 * @param   object  $client  The client info object
	 *
	 * @return  string|null  The thumbnail path or null if not found
	 *
	 * @since   1.0.0
	 */
	protected function getThumbnailPath($item, $client)
	{
		$templateDir = $item->template;
		$baseUri = Uri::root(true);

		// Check if xmldata has parent or inheritable
		if (isset($item->xmldata) && $item->xmldata)
		{
			$xmldata = $item->xmldata;
			
			// Check for inheritable templates with parent
			// Use property access for Joomla 6 compatibility (CMSObject removed, now stdClass)
			$inheritable = is_object($xmldata) && method_exists($xmldata, 'get') 
				? $xmldata->get('inheritable') 
				: (isset($xmldata->inheritable) ? $xmldata->inheritable : null);
			$parent = is_object($xmldata) && method_exists($xmldata, 'get') 
				? $xmldata->get('parent') 
				: (isset($xmldata->parent) ? $xmldata->parent : null);
			
			if ($inheritable || $parent)
			{
				
				// Check media/templates path first (for child templates)
				if ($parent && file_exists(JPATH_ROOT . '/media/templates/' . $client->name . '/' . $templateDir . '/images/template_thumbnail.png'))
				{
					return $baseUri . '/media/templates/' . $client->name . '/' . $templateDir . '/images/template_thumbnail.png';
				}
				
				// Check parent template in media
				if ($parent && file_exists(JPATH_ROOT . '/media/templates/' . $client->name . '/' . $parent . '/images/template_thumbnail.png'))
				{
					return $baseUri . '/media/templates/' . $client->name . '/' . $parent . '/images/template_thumbnail.png';
				}
			}
			
			// Check media/templates path
			if (file_exists(JPATH_ROOT . '/media/templates/' . $client->name . '/' . $templateDir . '/images/template_thumbnail.png'))
			{
				return $baseUri . '/media/templates/' . $client->name . '/' . $templateDir . '/images/template_thumbnail.png';
			}
		}

		// Check template directory
		if (file_exists($client->path . '/templates/' . $templateDir . '/template_thumbnail.png'))
		{
			if ($client->id === 0) {
				return $baseUri . '/templates/' . $templateDir . '/template_thumbnail.png';
			} else {
				return $baseUri . '/administrator/templates/' . $templateDir . '/template_thumbnail.png';
			}
		}

		return null;
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
		$canDo = ContentHelper::getActions('com_templates');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Set the title.
		if ((int) $this->get('State')->get('client_id') === 1)
		{
			ToolbarHelper::title(Text::_('COM_TEMPLATES_MANAGER_STYLES_ADMIN'), 'brush thememanager');
		}
		else
		{
			ToolbarHelper::title(Text::_('COM_TEMPLATES_MANAGER_STYLES_SITE'), 'brush thememanager');
		}

		// batch actions
		if ($canDo->get('core.create') || $canDo->get('core.delete') || $canDo->get('core.edit.state'))
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
				$childBar->standardButton('star')
					->text('COM_TEMPLATES_TOOLBAR_SET_HOME')
					->task('styles.setDefault')
					->listCheck(true);
			}

			if ($canDo->get('core.create'))
			{
				$childBar->standardButton('copy')
					->text('JTOOLBAR_DUPLICATE')
					->task('styles.duplicate')
					->listCheck(true);
			}

			if ($canDo->get('core.delete'))
			{
				$childBar->delete('styles.delete')->message('JGLOBAL_CONFIRM_DELETE')->listCheck(true);
			}
		}

		// Install new template
		// ToolbarHelper::modal('ModalInstallTemplate', 'icon-arrow-down-2', 'COM_TEMPLATES_BUTTON_INSTALL_TEMPLATE');

		ToolbarHelper::help('JHELP_EXTENSIONS_TEMPLATE_MANAGER_STYLES');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_templates');
		}
	}
}
