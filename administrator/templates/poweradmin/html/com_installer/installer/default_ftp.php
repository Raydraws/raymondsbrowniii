<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<fieldset class="option-fieldset options-grid-form-full">
	<legend><?php echo Text::_('COM_INSTALLER_MSG_DESCFTPTITLE'); ?></legend>

	<?php echo Text::_('COM_INSTALLER_MSG_DESCFTP'); ?>

	<?php if ($this->ftp instanceof Exception) : ?>
		<p><?php echo Text::_($this->ftp->getMessage()); ?></p>
	<?php endif; ?>

	<table class="adminform">
		<tbody>
			<tr>
				<td style="width:120">
					<label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
				</td>
				<td>
					<input type="text" id="username" name="username" class="form-control" size="70" value="">
				</td>
			</tr>
			<tr>
				<td style="width:120">
					<label for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				</td>
				<td>
					<input type="password" id="password" name="password" class="form-control" size="70" value="">
				</td>
			</tr>
		</tbody>
	</table>

</fieldset>
