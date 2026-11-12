<?php
/**
 * @package     Poweradmin
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Tags\Administrator\View\Tag;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML View class for the Tags component
 *
 * @since  3.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * Flag if an association exists
	 *
	 * @var  boolean
	 */
	protected $assoc;

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
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

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
	 * @since  3.1
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user       = Factory::getUser();
		$userId     = $user->get('id');
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$canDo = ContentHelper::getActions('com_tags');

		ToolbarHelper::title($isNew ? Text::_('COM_TAGS_MANAGER_TAG_NEW') : Text::_('COM_TAGS_MANAGER_TAG_EDIT'), 'tag');

		if (!$isNew)
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId);

			if (ComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history', 0) && $itemEditable)
			{
				ToolbarHelper::versions('com_tags.tag', $this->item->id);
			}
		}

		ToolbarHelper::divider();
		ToolbarHelper::help('JHELP_COMPONENTS_TAGS_MANAGER_EDIT');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// Close button
			ToolbarHelper::cancel('tag.cancel');

			ToolbarHelper::saveGroup(
				[
					['apply', 'tag.apply'],
					['save', 'tag.save'],
					['save2new', 'tag.save2new']
				],
				'btn-success'
			);
		}
		else
		{
			// Close button
			ToolbarHelper::cancel('tag.cancel', 'JTOOLBAR_CLOSE');

			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId);

			$toolbarButtons = [];

			// Can't save the record if it's checked out and editable
			if (!$checkedOut && $itemEditable)
			{
				$toolbarButtons[] = ['apply', 'tag.apply'];
				$toolbarButtons[] = ['save', 'tag.save'];

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					$toolbarButtons[] = ['save2new', 'tag.save2new'];
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create'))
			{
				$toolbarButtons[] = ['save2copy', 'tag.save2copy'];
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);
		}
	}
}
