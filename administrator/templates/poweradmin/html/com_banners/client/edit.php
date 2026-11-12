<?php
/**
 * @package     Poweradmin
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Banners\Administrator\View\Client\HtmlView $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}
?>

<form action="<?php echo Route::_('index.php?option=com_banners&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="client-form" class="form-validate">
	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

			<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', ['active' => 'general']); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'general', empty($this->item->id) ? Text::_('COM_BANNERS_NEW_CLIENT') : Text::_('COM_BANNERS_EDIT_CLIENT')); ?>
				<div class="j-card">
					<div class="j-card-body">
						<?php
						echo $this->form->renderField('contact');
						echo $this->form->renderField('email');
						echo $this->form->renderField('purchase_type');
						echo $this->form->renderField('track_impressions');
						echo $this->form->renderField('track_clicks');
						echo $this->form->renderFieldset('extra');
						?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'metadata', Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>
				<div id="fieldset-metadata" class="j-card">
					<div class="j-card-body">
						<?php echo $this->form->renderFieldset('metadata'); ?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
		</div>
		<div class="col-lg-3">
			<div class="j-card">
				<div class="j-card-body">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
