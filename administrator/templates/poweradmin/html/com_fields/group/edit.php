<?php
/**
 * @package     Poweradmin
 * @subpackage  com_fields
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
HTMLHelper::_('script', 'legacy/js/tabs-state.js', ['version' => 'auto', 'relative' => true, 'detectDebug' => true]);

$app = Factory::getApplication();
$input = $app->input;

$this->useCoreUI = true;

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

?>

<form action="<?php echo Route::_('index.php?option=com_fields&context=' . $this->state->get('filter.context') . '&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
			
			<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'general')); ?>
			
			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'general', Text::_('COM_FIELDS_VIEW_FIELD_FIELDSET_GENERAL', true)); ?>
			<div class="j-card">
				<div class="j-card-body">
					<?php echo $this->form->renderField('label'); ?>
					<?php echo $this->form->renderField('description'); ?>
				</div>
			</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
			<div id="fieldset-rules" class="j-card options-grid-form options-grid-form-full">
				<div class="j-card-header">
					<?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?>
				</div>
				<div class="j-card-body">
					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
			</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php $this->set('ignore_fieldsets', array('fieldparams')); ?>
			<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'rules', Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
				<div id="fieldset-rules" class="options-grid-form options-grid-form-full">
					<!-- <legend><?php echo Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></legend> -->
					<div>
						<?php echo $this->form->getInput('rules'); ?>
					</div>
				</div>
				<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>
			<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
			<?php echo $this->form->getInput('context'); ?>
		</div>

		<div class="col-lg-3">
			<div class="j-card">
				<div class="j-card-body">
				<?php $this->set('fields',
						array(
							array(
								'published',
								'state',
								'enabled',
							),
							'access',
							'language',
							'note',
						)
				); ?>
				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				<?php $this->set('fields', null); ?>
				</div>
			</div>
		</div>

	</div>
		
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
