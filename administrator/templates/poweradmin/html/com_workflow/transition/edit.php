<?php
/**
 * @package     Poweradmin
 * @subpackage  com_workflow
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

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

// In case of modal
$isModal = $this->input->get('layout') === 'modal';
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $this->input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

?>

<form action="<?php echo Route::_('index.php?option=com_workflow&view=transition&workflow_id=' . $this->workflowID . '&extension=' . $this->input->getCmd('extension') . '&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="workflow-form" class="form-validate">
	<div>
		<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

		<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_WORKFLOW_DESCRIPTION')); ?>
		<div class="row">
			<div class="col-lg-9">
				<div class="card card-block">
					<div class="card-body">
						<?php echo $this->form->renderField('from_stage_id'); ?>
						<?php echo $this->form->renderField('to_stage_id'); ?>
						<?php echo $this->form->renderField('description'); ?>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="card card-block">
					<div class="card-body">
						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'permissions', Text::_('COM_WORKFLOW_RULES_TAB')); ?>
			<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
				<legend><?php echo Text::_('COM_WORKFLOW_RULES_TAB'); ?></legend>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>

		<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
	</div>
	<?php echo $this->form->getInput('workflow_id'); ?>
	<input type="hidden" name="task" value="transition.edit" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
