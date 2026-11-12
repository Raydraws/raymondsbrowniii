<?php
/**
 * @package     Poweradmin
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\Language\Text;
use Joomla\CMS\Updater\Update;

/** @var JoomlaupdateViewDefault $this */
?>
<fieldset class="options-grid-form options-grid-form-full">
	<legend>
		<?php echo Text::_('COM_JOOMLAUPDATE_UPDATE_CHECK'); ?>
	</legend>
	<p class="j-alert j-alert-info">
		<span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span>
		<span class="sr-only"><?php echo Text::_('NOTICE'); ?></span>
		<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_NOUPDATES'); ?>
	</p>
	<p><?php echo Text::sprintf($this->langKey, $this->updateSourceKey); ?></p>
	<p class="j-alert j-alert-success">
		<span class="icon-checkinmark" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('NOTICE'); ?></span>
		<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_NOUPDATESNOTICE', '&#x200E;' . JVERSION); ?>
	</p>
	<?php if (is_object($this->updateInfo['object']) && ($this->updateInfo['object'] instanceof Update)) : ?>
	<div>
		<table class="table">
			<tbody>
			<tr>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_PACKAGE_REINSTALL'); ?>
				</td>
				<td>
					<a href="<?php echo $this->updateInfo['object']->downloadurl->_data; ?>">
						<?php echo $this->updateInfo['object']->downloadurl->_data; ?>
					</a>
				</td>
			</tr>
			<?php if (isset($this->updateInfo['object']->get('infourl')->_data)
				&& isset($this->updateInfo['object']->get('infourl')->title)) : ?>
				<tr>
					<td>
						<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_INFOURL'); ?>
					</td>
					<td>
						<a href="<?php echo $this->updateInfo['object']->get('infourl')->_data; ?>">
							<?php echo $this->updateInfo['object']->get('infourl')->title; ?>
						</a>
					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->methodSelect) : ?>
			<tr>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_METHOD'); ?>
				</td>
				<td>
					<?php echo $this->methodSelect; ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php
			// FTP support has been removed from Joomla 4+, so hide FTP fields
			$ftpFieldsDisplay = isset($this->ftpFieldsDisplay) ? $this->ftpFieldsDisplay : 'style="display: none"';
			$ftp = isset($this->ftp) ? $this->ftp : [
				'host' => '',
				'port' => '',
				'username' => '',
				'password' => '',
				'directory' => '',
			];
			?>
			<tr id="row_ftp_hostname" <?php echo $ftpFieldsDisplay; ?>>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_FTP_HOSTNAME'); ?>
				</td>
				<td>
					<input class="form-control" type="text" name="ftp_host" value="<?php echo htmlspecialchars($ftp['host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</td>
			</tr>
			<tr id="row_ftp_port" <?php echo $ftpFieldsDisplay; ?>>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_FTP_PORT'); ?>
				</td>
				<td>
					<input class="form-control" type="text" name="ftp_port" value="<?php echo htmlspecialchars($ftp['port'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</td>
			</tr>
			<tr id="row_ftp_username" <?php echo $ftpFieldsDisplay; ?>>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_FTP_USERNAME'); ?>
				</td>
				<td>
					<input class="form-control" type="text" name="ftp_user" value="<?php echo htmlspecialchars($ftp['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</td>
			</tr>
			<tr id="row_ftp_password" <?php echo $ftpFieldsDisplay; ?>>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_FTP_PASSWORD'); ?>
				</td>
				<td>
					<input class="form-control" type="password" name="ftp_pass" value="<?php echo htmlspecialchars($ftp['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</td>
			</tr>
			<tr id="row_ftp_directory" <?php echo $ftpFieldsDisplay; ?>>
				<td>
					<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_FTP_DIRECTORY'); ?>
				</td>
				<td>
					<input class="form-control" type="text" name="ftp_root" value="<?php echo htmlspecialchars($ftp['directory'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td>
					<button class="btn btn-warning" type="submit">
						<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_INSTALLAGAIN'); ?>
					</button>
				</td>
			</tr>
			</tfoot>
		</table>
	</div>
	<?php endif; ?>

</fieldset>
