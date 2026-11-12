<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Installer\Administrator\View\Updatesites;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\Model\UpdatesitesModel;
use Joomla\Component\Installer\Administrator\View\Installer\HtmlView as InstallerViewDefault;

/**
 * Extension Manager Update Sites View
 *
 * @since  3.4
 */
class HtmlView extends InstallerViewDefault
{
	/**
	 * The search tools form
	 *
	 * @var    Form
	 * @since  3.4
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  3.4
	 */
	public $activeFilters = [];

	/**
	 * List of updatesites
	 *
	 * @var    \stdClass[]
	 * @since 3.4
	 */
	protected $items;

	/**
	 * Pagination object
	 *
	 * @var    Pagination
	 * @since 3.4
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
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  mixed|void
	 *
	 * @since   3.4
	 *
	 * @throws  \Exception on errors
	 */
	public function display($tpl = null): void
	{
		/** @var UpdatesitesModel $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_installer');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::custom('updatesites.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		}

		ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_UPDATESITES');

		parent::addToolbar();

		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::checkin('updatesites.checkin');
		}

		if ($canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'updatesites.delete');
		}

		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::unpublish('updatesites.unpublish', 'JTOOLBAR_DISABLE', true);
			ToolbarHelper::publish('updatesites.publish', 'JTOOLBAR_ENABLE', true);
		}

		if ($canDo->get('core.edit'))
		{
			ToolbarHelper::editList('updatesite.edit');
		}
	}
}
