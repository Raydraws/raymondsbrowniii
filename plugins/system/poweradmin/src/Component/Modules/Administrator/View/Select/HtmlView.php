<?php
/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Modules\Administrator\View\Select;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML View class for the Modules component.
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
	 * The model state.
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * An array of items.
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * A suffix for links for modal use.
	 *
	 * @var  string
	 */
	protected $modalLink;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state     = $this->get('State');
		$this->items     = $this->get('Items');
		$this->modalLink = '';

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
		$state    = $this->get('State');
		$clientId = (int) $state->get('client_id', 0);

		ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_SITE'), 'modules module');

		if ($clientId === 1)
		{
			ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_ADMIN'), 'modules module');
		}

		$bar = Toolbar::getInstance('toolbar');

		$layout = new FileLayout('toolbar.cancelselect');

		$bar->appendButton('Custom', $layout->render(['client_id' => $clientId]), 'new');
	}
}


