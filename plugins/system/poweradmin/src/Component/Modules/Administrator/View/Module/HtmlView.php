<?php
/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Modules\Administrator\View\Module;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View to edit a module.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Ensure custom toolbar wrappers are available.
	 *
	 * @since  1.0.0
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
	 * The \JForm object.
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The active item.
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state.
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform.
	 *
	 * @var    \JObject
	 * @since  4.0.0
	 */
	protected $canDo;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = ContentHelper::getActions('com_modules', 'module', $this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();
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
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user       = Factory::getUser();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo      = $this->canDo;

		ToolbarHelper::title(Text::sprintf('COM_MODULES_MANAGER_MODULE', Text::_($this->item->module)), 'modules module');

		// Get the help information for the menu item.
		$lang = Factory::getLanguage();

		$help = $this->get('Help');

		if ($lang->hasKey($help->url))
		{
			$debug = $lang->setDebug(false);
			$url   = Text::_($help->url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}

		ToolbarHelper::help($help->key, false, $url);

		// For new records, check the create permission.
		if ($isNew && $canDo->get('core.create'))
		{
			// Cancel button.
			ToolbarHelper::cancel('module.cancel');

			// Save group.
			ToolbarHelper::saveGroup(
				[
					['apply', 'module.apply'],
					['save', 'module.save'],
					['save2new', 'module.save2new'],
				]
			);
		}
		else
		{
			// Close button.
			ToolbarHelper::cancel('module.cancel', 'JTOOLBAR_CLOSE');

			$toolbarButtons = [];

			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission.
				if ($canDo->get('core.edit'))
				{
					$toolbarButtons[] = ['apply', 'module.apply'];
					$toolbarButtons[] = ['save', 'module.save'];

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						$toolbarButtons[] = ['save2new', 'module.save2new'];
					}
				}
			}

			// If checked out, we can still save.
			if ($canDo->get('core.create'))
			{
				$toolbarButtons[] = ['save2copy', 'module.save2copy'];
			}

			// echo '<pre>';
			// print_r($toolbarButtons);
			// exit;

			ToolbarHelper::saveGroup($toolbarButtons);
		}
	}
}


