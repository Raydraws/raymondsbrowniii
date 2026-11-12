<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div id="installer-warnings" class="clearfix">
	<form action="<?php echo Route::_('index.php?option=com_installer&view=warnings'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php if (count($this->messages)) : ?>
						<?php foreach ($this->messages as $message) : ?>
							<div class="j-alert j-alert-warning d-flex mt-4">
								<div class="j-alert-icon-wrap"><span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('warning', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo $message['message']; ?></span></div>
								<div class="j-alert-info-wrap"><?php echo $message['description']; ?></div>
							</div>
						<?php endforeach; ?>
						<div class="j-alert j-alert-info d-flex mt-4">
							<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('COM_INSTALLER_MSG_WARNINGFURTHERINFO'); ?></span></div>
							<div class="j-alert-info-wrap"><?php echo Text::_('COM_INSTALLER_MSG_WARNINGFURTHERINFODESC'); ?></div>
						</div>
					<?php else: ?>
						<div class="j-alert j-alert-info d-flex mt-4">
							<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
							<div class="j-alert-info-wrap"><?php echo Text::_('COM_INSTALLER_MSG_WARNINGS_NONE'); ?></div>
						</div>
					<?php endif; ?>
					<div>
						<input type="hidden" name="boxchecked" value="0">
						<?php echo HTMLHelper::_('form.token'); ?>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
