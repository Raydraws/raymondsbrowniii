<?php
/**
 * @package     Poweradmin
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var JoomlaupdateViewDefault $this */
?>

<fieldset class="options-grid-form options-grid-form-full">
	<legend>
		<?php echo Text::_('COM_JOOMLAUPDATE_LIVE_UPDATE'); ?>
	</legend>
	<p class="j-alert j-alert-warning">
		<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_NO_LIVE_UPDATE'); ?>
	</p>
	<p>
		<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_NO_LIVE_UPDATE_DESC'); ?>
	</p>
</fieldset>
