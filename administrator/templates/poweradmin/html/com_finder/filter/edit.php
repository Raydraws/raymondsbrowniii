<?php
/**
 * @package     Poweradmin
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'legacy/js/tabs-state.js', ['version' => 'auto', 'relative' => true, 'detectDebug' => true]);

Text::script('COM_FINDER_FILTER_SHOW_ALL', true);
Text::script('COM_FINDER_FILTER_HIDE_ALL', true);

$this->ignore_fieldsets = ['jbasic'];

$this->useCoreUI = true;

HTMLHelper::_('script', 'com_finder/finder-edit.min.js', array('version' => 'auto', 'relative' => true));

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}
?>

<form action="<?php echo Route::_('index.php?option=com_finder&view=filter&layout=edit&filter_id=' . (int) $this->item->filter_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

	<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_FINDER_EDIT_FILTER')); ?>
	<div class="row">
		<div class="col-lg-9">
			<div class="card">
				<div class="card-body">
					<?php if ($this->total > 0) : ?>
						<div class="well">
							<?php echo $this->form->renderField('map_count'); ?>
						</div>
						<button class="btn btn-secondary filter-toggle-all" type="button">
							<span class="icon-square" aria-hidden="true"></span> <?php echo Text::_('JGLOBAL_SELECTION_INVERT'); ?></button>

						<button class="btn btn-secondary float-right" type="button" id="expandAccordion"><?php echo Text::_('COM_FINDER_FILTER_SHOW_ALL'); ?></button>
						<hr>
					<?php endif; ?>

					<?php echo HTMLHelper::_('filter.slider', array('selected_nodes' => $this->filter->data)); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card">
				<div class="card-body">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
		</div>
	</div>
	<?php echo JHtmlPoweradminUiTab::endTab(); ?>

	<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_OPTIONS')); ?>
	<div class="row">
		<div class="col-md-6">
			<fieldset id="fieldset-publishingdata" class="options-grid-form options-grid-form-full">
				<legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
				<div>
				<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset id="fieldset-filter" class="options-grid-form options-grid-form-full">
				<legend><?php echo Text::_('COM_FINDER_FILTER_FIELDSET_PARAMS'); ?></legend>
				<div>
				<?php echo $this->form->renderFieldset('jbasic'); ?>
				</div>
			</fieldset>
		</div>
	</div>
	<?php echo JHtmlPoweradminUiTab::endTab(); ?>

	<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

	<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="return" value="<?php echo Factory::getApplication()->input->get('return', '', 'cmd'); ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
