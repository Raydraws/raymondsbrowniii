<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
Text::script('PLG_INSTALLER_PACKAGEINSTALLER_NO_PACKAGE');
Text::script('PLG_INSTALLER_FOLDERINSTALLER_NO_INSTALL_PATH');
Text::script('PLG_INSTALLER_URLINSTALLER_NO_URL');
Text::script('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL');

HTMLHelper::_('script', 'legacy/js/tabs-state.js', ['version' => 'auto', 'relative' => true, 'detectDebug' => true]);
HTMLHelper::_('stylesheet', 'com_installer/installer.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'com_installer/installer.js', ['version' => 'auto', 'relative' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

$app = Factory::getApplication();
?>

<div id="installer-install" class="clearfix">

	<form enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_installer&view=install'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php // Render messages set by extension install scripts here ?>
					<?php if ($this->showMessage) : ?>
						<?php echo $this->loadTemplate('message'); ?>
					<?php endif; ?>
					<?php $tabs = $app->triggerEvent('onInstallerAddInstallationTab', []); ?>
					<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', ['active' => $tabs[0]['name'] ?? '']); ?>
					<?php // Show installation tabs ?>
					<?php foreach ($tabs as $tab) : ?>
						<?php echo JHtmlPoweradminUiTab::addTab('myTab', $tab['name'], $tab['label']); ?>
						<fieldset class="uploadform option-fieldset options-grid-form-full">
							<?php echo $tab['content']; ?>
						</fieldset>
						<?php echo JHtmlPoweradminUiTab::endTab(); ?>
					<?php endforeach; ?>
					<?php if (!$tabs) : ?>
						<?php $app->enqueueMessage(Text::_('COM_INSTALLER_NO_INSTALLATION_PLUGINS_FOUND'), 'warning'); ?>
					<?php endif; ?>

					<?php if (!empty($this->ftp)) : ?>
						<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'ftp', Text::_('COM_INSTALLER_MSG_DESCFTPTITLE')); ?>
						<?php echo $this->loadTemplate('ftp'); ?>
						<?php echo JHtmlPoweradminUiTab::endTab(); ?>
					<?php endif; ?>

					<input type="hidden" name="installtype" value="">
					<input type="hidden" name="task" value="install.install">
					<?php echo HTMLHelper::_('form.token'); ?>

					<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
				</div>
			</div>
		</div>
	</form>
</div>
<div id="loading"></div>
