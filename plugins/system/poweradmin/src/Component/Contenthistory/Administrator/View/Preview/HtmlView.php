<?php
/**
 * @package     Poweradmin
 * @subpackage  com_contenthistory
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Component\Contenthistory\Administrator\View\Preview;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View class for a list of contenthistory.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  \stdClass|false
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  \Exception on failure, void on success.
	 *
	 * @since   3.2
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');

		if (false === $this->item)
		{
			Factory::getLanguage()->load('com_content', JPATH_SITE, null, true);

			throw new \Exception(Text::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'), 404);

			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		return parent::display($tpl);
	}
}
