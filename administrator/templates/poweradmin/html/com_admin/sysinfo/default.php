<?php
/**
 * @package     Poweradmin
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var \Joomla\Component\Admin\Administrator\View\Sysinfo\HtmlView $this */

HTMLHelper::_('script', 'legacy/js/tabs-state.js', ['version' => 'auto', 'relative' => true, 'detectDebug' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}
?>
<div class="row">
	<?php // Begin Content ?>
	<div class="col-md-12">
		<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', ['active' => 'site']); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'site', Text::_('COM_ADMIN_SYSTEM_INFORMATION')); ?>
		<?php echo $this->loadTemplate('system'); ?>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'phpsettings', Text::_('COM_ADMIN_PHP_SETTINGS')); ?>
		<?php echo $this->loadTemplate('phpsettings'); ?>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'config', Text::_('COM_ADMIN_CONFIGURATION_FILE')); ?>
		<?php echo $this->loadTemplate('config'); ?>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'directory', Text::_('COM_ADMIN_DIRECTORY_PERMISSIONS')); ?>
		<?php echo $this->loadTemplate('directory'); ?>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'phpinfo', Text::_('COM_ADMIN_PHP_INFORMATION')); ?>
		<?php echo $this->loadTemplate('phpinfo'); ?>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
	</div>
	<?php // End Content ?>
</div>

