<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2022 Open Source Matters,
 *             Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Poweradmin\Admin\Toolbar\Button;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;

/**
 * Toolbar button to toggle inline help blocks.
 *
 * @since  4.3.0
 */
class InlinehelpButton extends StandardButton
{
	/**
	 * @inheritDoc
	 */
	protected $layout = 'joomla.toolbar.inlinehelp';

	/**
	 * Prepare options for this button.
	 *
	 * @param   array  $options  The options about this button.
	 *
	 * @return  void
	 *
	 * @since   4.3.0
	 */
	protected function prepareOptions(array &$options)
	{
		$this->listCheck(false);

		if (!$this->getText())
		{
			$this->text('JINLINEHELP');
		}

		$options['icon'] = $options['icon'] ?? 'icon-question-circle';
		$options['button_class'] = trim(($options['button_class'] ?? 'btn btn-link') . ' button-inlinehelp');
		$options['onclick'] = $options['onclick'] ?? '';

		$options['attributes'] = $options['attributes'] ?? [];
		$options['attributes']['data-class'] = $options['attributes']['data-class'] ?? 'hide-aware-inline-help';

		parent::prepareOptions($options);

		// Ensure the translated text is available to the JavaScript toggler
		Text::script('JINLINEHELP');
	}
}


