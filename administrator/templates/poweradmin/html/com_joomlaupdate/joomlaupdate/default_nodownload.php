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

/** @var \Joomla\Component\Joomlaupdate\Administrator\View\Joomlaupdate\Html $this */
?>

<fieldset class="options-grid-form options-grid-form-full">
	<legend>
		<?php echo Text::_('COM_JOOMLAUPDATE_SYSTEM_CHECK'); ?>
	</legend>
	<div>
	<?php if ( !$this->getModel()->isDatabaseTypeSupported()) : ?>
		<p class="j-alert j-alert-warning">
			<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_DB_NOT_SUPPORTED'); ?>
		</p>
		<p>
			<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_DB_NOT_SUPPORTED_DESC', $this->updateInfo['latest']); ?>
		</p>
	<?php endif; ?>
	<?php if (!$this->getModel()->isPhpVersionSupported()) : ?>
		<p class="j-alert j-alert-warning">
			<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_PHP_VERSION_NOT_SUPPORTED'); ?>
		</p>
		<p>
			<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_PHP_VERSION_NOT_SUPPORTED_DESC', $this->updateInfo['latest']); ?>
		</p>
	<?php endif; ?>
	<?php if (!isset($this->updateInfo['object']->downloadurl->_data) && $this->updateInfo['installed'] < $this->updateInfo['latest'] && $this->getModel()->isPhpVersionSupported() && $this->getModel()->isDatabaseTypeSupported()) : ?>
		<p class="j-alert j-alert-warning">
			<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_NO_DOWNLOAD_URL'); ?>
		</p>
		<p>
			<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_NO_DOWNLOAD_URL_DESC', $this->updateInfo['latest']); ?>
		</p>
	<?php endif; ?>
	</div>
</fieldset>
