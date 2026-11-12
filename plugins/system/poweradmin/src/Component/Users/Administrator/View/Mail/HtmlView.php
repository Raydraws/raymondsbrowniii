<?php
/**
 * @package     Poweradmin
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Users\Administrator\View\Mail;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Users mail view.
 *
 * @since  1.6
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
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 */
	public function display($tpl = null)
	{
		// Redirect to admin index if mass mailer disabled in conf
		if (Factory::getApplication()->get('massmailoff', 0) == 1)
		{
			Factory::getApplication()->redirect(Route::_('index.php', false));
		}

		// Get data from the model
		$this->form = $this->get('Form');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_USERS_MASS_MAIL'), 'users massmail');
		ToolbarHelper::cancel('mail.cancel');
		ToolbarHelper::help('JHELP_USERS_MASS_MAIL_USERS');
		ToolbarHelper::preferences('com_users');
		ToolbarHelper::divider();
		ToolbarHelper::custom('mail.send', 'envelope.png', 'send_f2.png', 'COM_USERS_TOOLBAR_MAIL_SEND_MAIL', false);
	}
}
