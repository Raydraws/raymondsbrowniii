<?php
/**
 * @package     Poweradmin
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('script', 'legacy/js/tabs-state.js', ['version' => 'auto', 'relative' => true, 'detectDebug' => true]);

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

// Fieldsets to not automatically render by /layouts/joomla/edit/params.php
$this->ignore_fieldsets = ['jmetadata'];
$this->useCoreUI = true;

?>

<form action="<?php echo Route::_('index.php?option=com_tags&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<div>
		<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

		<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_TAGS_FIELDSET_DETAILS')); ?>
		<div class="row">
			<div class="col-lg-9">
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>
			</div>
			<div class="col-lg-3">
				<div class="j-card">
					<div class="j-card-body">
						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
		<div class="row">
			<div class="col-12 col-lg-6">
				<fieldset id="fieldset-publishingdata" class="options-grid-form options-grid-form-full">
					<legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
					<div>
					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
					</div>
				</fieldset>
			</div>
			<div class="col-md-6">
				<div class="sr-only"><?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?></div>
				<div>
					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
		</div>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
