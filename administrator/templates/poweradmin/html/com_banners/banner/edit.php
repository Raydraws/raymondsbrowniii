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

/** @var \Joomla\Component\Banners\Administrator\View\Banner\HtmlView $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

HTMLHelper::_('script', 'com_banners/admin-banner-edit.min.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}
?>

<form action="<?php echo Route::_('index.php?option=com_banners&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="banner-form" class="form-validate">
	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
			<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_BANNERS_BANNER_DETAILS')); ?>
				<div class="j-card">
					<div class="j-card-body">
						<?php echo $this->form->renderField('type'); ?>
						<div id="image">
							<?php echo $this->form->renderFieldset('image'); ?>
						</div>
						<div id="custom">
							<?php echo $this->form->renderField('custombannercode'); ?>
						</div>
						<?php
						echo $this->form->renderField('clickurl');
						echo $this->form->renderField('description');
						?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'otherparams', Text::_('COM_BANNERS_GROUP_LABEL_BANNER_DETAILS')); ?>
				<div id="fieldset-otherparams" class="j-card form-no-margin options-grid-form">
					<!-- <div class="j-card-header">
						<?php echo Text::_('COM_BANNERS_GROUP_LABEL_BANNER_DETAILS'); ?>
					</div> -->
					<div class="j-card-body">
						<?php echo $this->form->renderFieldset('otherparams'); ?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
				<div id="fieldset-publishingdata" class="j-card mb-4">
					<div class="j-card-body">
						<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
					</div>
				</div>
				<div id="fieldset-metadata" class="j-card">
					<div class="j-card-header">
						<?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?>
					</div>
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
