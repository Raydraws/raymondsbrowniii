<?php
/**
 * @package     Poweradmin
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Joomlaupdate\Administrator\View\Upload;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Joomla! Update's Update View
 *
 * @since  3.6.0
 */
class HtmlView extends BaseHtmlView
{
	public function __construct($config = [])
	{
		$toolbarAutoload = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar/autoload.php';
		if (is_file($toolbarAutoload)) {
			require_once $toolbarAutoload;
		}
		parent::__construct($config);
	}
	/**
	 * Renders the view.
	 *
	 * @param   string  $tpl  Template name.
	 *
	 * @return  void
	 *
	 * @since   3.6.0
	 */
	public function display($tpl = null)
	{
		// Set the toolbar information.
		ToolbarHelper::title(Text::_('COM_JOOMLAUPDATE_OVERVIEW'), 'loop install');
		ToolbarHelper::divider();
		ToolbarHelper::help('JHELP_COMPONENTS_JOOMLA_UPDATE');

		// Load com_installer's language
		$language = Factory::getLanguage();
		$language->load('com_installer', JPATH_ADMINISTRATOR, 'en-GB', false, true);
		$language->load('com_installer', JPATH_ADMINISTRATOR, null, true);

		// Render the view.
		parent::display($tpl);
	}
}
