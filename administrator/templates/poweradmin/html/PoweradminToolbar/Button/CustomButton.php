<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Poweradmin\Admin\Toolbar\Button;

\defined('_JEXEC') or die;

use Poweradmin\Admin\Toolbar\ToolbarButton;

/**
 * Renders a custom button
 *
 * @method self html(string $value)
 * @method string getHtml()
 *
 * @since  3.0
 */
class CustomButton extends ToolbarButton
{
	/**
	 * Render button HTML.
	 *
	 * @param   array  $options  The button options.
	 *
	 * @return  string  The button HTML.
	 *
	 * @since  4.0.0
	 */
	protected function renderButton(array &$options): string
	{
		return (string) ($options['html'] ?? '');
	}

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Button type, unused string.
	 * @param   string  $html  HTML strng for the button
	 * @param   string  $id    CSS id for the button
	 *
	 * @return  string   HTML string for the button
	 *
	 * @since   3.0
	 *
	 * @deprecated  5.0 Use render() instead.
	 */
	public function fetchButton($type = 'Custom', $html = '', $id = 'custom')
	{
		return $html;
	}

	/**
	 * Method to configure available option accessors.
	 *
	 * @return  array
	 *
	 * @since  4.0.0
	 */
	protected static function getAccessors(): array
	{
		return array_merge(
			parent::getAccessors(),
			[
				'html',
			]
		);
	}
}
