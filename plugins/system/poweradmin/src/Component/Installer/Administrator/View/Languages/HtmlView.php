<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Installer\Administrator\View\Languages;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\View\Installer\HtmlView as InstallerViewDefault;

/**
 * Extension Manager Language Install View
 *
 * @since  2.5.7
 */
class HtmlView extends InstallerViewDefault
{
	/**
	 * @var object item list
	 */
	protected $items;

	/**
	 * @var object pagination information
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
	 * @param   null  $tpl  template to display
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		// Get data from the model.
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->installedLang = LanguageHelper::getInstalledLanguages();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_installer');
		ToolbarHelper::title(Text::_('COM_INSTALLER_HEADER_' . $this->getName()), 'puzzle install');

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_LANGUAGES');
			parent::addToolbar();
		}
	}
}
