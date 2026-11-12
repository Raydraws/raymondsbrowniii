<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Installer\Administrator\View\Install;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\View\Installer\HtmlView as InstallerViewDefault;

/**
 * Extension Manager Install View
 *
 * @since  1.5
 */
class HtmlView extends InstallerViewDefault
{
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
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function display($tpl = null)
	{
		$paths        = new \stdClass;
		$paths->first = '';

		$this->paths  = &$paths;

		PluginHelper::importPlugin('installer');

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
		// Help button
		ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_INSTALL');

		// Options and others
		parent::addToolbar();
	}
}
