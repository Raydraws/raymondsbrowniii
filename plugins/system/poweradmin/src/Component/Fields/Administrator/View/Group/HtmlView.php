<?php
/**
 * @package     Poweradmin
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Fields\Administrator\View\Group;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Group View
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
	protected $form;

	/**
	 * @var  \JObject
	 *
	 * @since  3.7.0
	 */
	protected $item;

	/**
	 * @var  \JObject
	 *
	 * @since  3.7.0
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  \JObject
	 *
	 * @since  3.7.0
	 */
	protected $canDo;


	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   3.7.0
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		$component = '';
		$parts     = FieldsHelper::extract($this->state->get('filter.context'));

		if ($parts)
		{
			$component = $parts[0];
		}

		$this->canDo = ContentHelper::getActions($component, 'fieldgroup', $this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		Factory::getApplication()->getInput()->set('hidemainmenu', true);

		$this->addToolbar();

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
		$component = '';
		$parts     = FieldsHelper::extract($this->state->get('filter.context'));

		if ($parts)
		{
			$component = $parts[0];
		}

		$userId    = Factory::getUser()->get('id');
		$canDo     = $this->canDo;
		$toolbar   = Toolbar::getInstance('toolbar');

		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Avoid nonsense situation.
		if ($component == 'com_fields')
		{
			return;
		}

		// Load component language file
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_ADMINISTRATOR)
		|| $lang->load($component, Path::clean(JPATH_ADMINISTRATOR . '/components/' . $component));

		$title = Text::sprintf('COM_FIELDS_VIEW_GROUP_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE', Text::_(strtoupper($component)));

		// Prepare the toolbar.
		ToolbarHelper::title(
			$title,
			'puzzle field-' . ($isNew ? 'add' : 'edit') . ' ' . substr($component, 4) . '-group-' .
			($isNew ? 'add' : 'edit')
		);

		// Help button
		ToolbarHelper::help('Field_Groups:_Edit');

		$toolbarButtons = [];

		// For new records, check the create permission.
		if ($isNew)
		{
			// Cancel button
			ToolbarHelper::cancel('group.cancel');

			// Save button's group
			ToolbarHelper::saveGroup(
				[
					['apply', 'group.apply'],
					['save', 'group.save'],
					['save2new', 'group.save2new']
				],
				'btn-success'
			);
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

			// Cancel button
			ToolbarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');

			$toolbarButtons = [];

			// Can't save the record if it's checked out and editable
			if (!$checkedOut && $itemEditable)
			{
				// Save button's group
				$toolbarButtons[] = ['apply', 'group.apply'];
				$toolbarButtons[] = ['save', 'group.save'];

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					$toolbarButtons[] = ['save2new', 'group.save2new'];
				}
			}

			// If an existing item, can save to a copy.
			if ($canDo->get('core.create'))
			{
				$toolbarButtons[] = ['save2copy', 'group.save2copy'];
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);
		}
	}
}
