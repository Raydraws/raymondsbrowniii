<?php
/**
 * @package     Poweradmin
 * @subpackage  HTML
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

/**
 * Utility class for Webcomponent elements.
 *
 * @since  4.0.0
 */
abstract class JHtmlWebcomponent
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  4.0.0
	 */
	protected static $loaded = array();

	/**
	 * Method to render modal web-component
	 *
	 * @component   joomla-modal
	 *
	 * @param   string  $selector  The ID selector for the modal.
	 *
	 * @since  4.0.0
	 */
	public static function renderModal($selector = 'modal', $params = array(), $body = '')
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		// Include joomla-modal webcomponent assets (js)
		static::assets('joomla-modal');

		$layoutData = array(
			'selector' => $selector,
			'params'   => $params,
			'body'     => $body,
		);

		$output = LayoutHelper::render('joomla.webcomponent.modal.main', $layoutData);

		static::$loaded[__METHOD__][$selector] = true;

		if (!empty($params['appendToBody']))
		{
			$encodedOutput = json_encode($output, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

			Factory::getDocument()->addScriptDeclaration(
				<<<JS
document.addEventListener('DOMContentLoaded', function () {
	if (!document.getElementById('$selector')) {
		document.body.insertAdjacentHTML('beforeend', $encodedOutput);
	}
});
JS
			);

			return '';
		}

		return $output;
	}

	/**
	 * Method to load the webcomponent JavaScript library into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public static function assets($filename, $debug = null)
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__][$filename]))
		{
			return;
		}

		$debug = (isset($debug) && $debug != JDEBUG) ? $debug : JDEBUG;

		// Load the needed scripts
		HTMLHelper::_('script', 'system/' . $filename . '.js', array('version' => 'auto', 'relative' => true, 'detectDebug' => $debug));

		static::$loaded[__METHOD__][$filename] = true;
	}
}
