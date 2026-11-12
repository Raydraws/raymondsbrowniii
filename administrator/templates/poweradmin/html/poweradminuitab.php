<?php

/**
 * @package     Poweradmin
 * @subpackage  HTML
 *
 * @copyright  (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Utility class for the Joomla core UI Tab element.
 *
 * @since  4.0.0
 */
abstract class JHtmlPoweradminUiTab
{
    /**
     * @var    array  Array containing information for loaded files
     * @since  4.0.0
     */
    protected static $loaded = [];

    /**
     * Creates a core UI tab pane
     *
     * @param   string  $selector  The pane identifier.
     * @param   array   $params    The parameters for the pane
     *
     * @return  string
     *
     * @since   4.0.0
     */
	public static function startTabSet($selector = 'myTab', $params = array())
	{
		$sig = md5(serialize(array($selector, $params)));

		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			// Include the custom element
			HTMLHelper::_('script', 'system/joomla-tab.min.js', ['version' => 'auto', 'relative' => true]);

			// Setup options object
			$opt['active'] = (isset($params['active']) && ($params['active'])) ? (string) $params['active'] : '';

			// Set static array
			static::$loaded[__METHOD__][$sig] = true;
			static::$loaded[__METHOD__][$selector]['active'] = $opt['active'];
		}

		// @TODO echo the recall attribute correctly, now it's hardcoded!!!
		$recall = !isset($params['recall']) ? '' : 'recall';

		return '<joomla-tab id="' . $selector . '" recall>';
	}

    /**
     * Close the current tab pane
     *
     * @return  string  HTML to close the pane
     *
     * @since   4.0.0
     */
	public static function endTabSet()
	{
		return '</joomla-tab>';
	}
    /**
     * Begins the display of a new tab content panel.
     *
     * @param   string  $selector  Identifier of the panel.
     * @param   string  $id        The ID of the div element
     * @param   string  $title     The title text for the button
     *
     * @return  string  HTML to start a new panel
     *
     * @since   4.0.0
     */
	public static function addTab($selector, $id, $title)
	{
		$active = (static::$loaded[__CLASS__ . '::startTabSet'][$selector]['active'] == $id) ? ' active' : '';

		return '<section id="' . $id . '"' . $active . ' name="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '">';

	}

    /**
     * Close the current tab content panel
     *
     * @return  string  HTML to close the pane
     *
     * @since   4.0.0
     */
	public static function endTab()
	{
		return '</section>';
	}
}
