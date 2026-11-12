<?php
/**
 * @package     Poweradmin
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<fieldset class="options-grid-form options-grid-form-half mb-4">
	<legend><?php echo Text::_('COM_CONFIG_FTP_DETAILS'); ?></legend>
	<p><?php echo Text::_('COM_CONFIG_FTP_DETAILS_TIP'); ?></p>
	<?php if ($this->ftp instanceof Exception) : ?>
	<?php /** @var Exception $this */ ?>
		<p><?php echo Text::_($this->ftp->message); ?></p>
	<?php endif; ?>
		<div>
			<div class="control-group">
				<div class="control-label">
					<label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
				</div>
				<div class="controls">
					<input type="text" id="username" name="username" class="form-control" size="70" value="">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				</div>
				<div class="controls">
					<input type="password" id="password" name="password" class="form-control" size="70" value="">
				</div>
			</div>
		</div>
</fieldset>
