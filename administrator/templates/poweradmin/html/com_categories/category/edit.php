<?php
/**
 * @package     Poweradmin
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

$app = Factory::getApplication();
$input = $app->input;

$assoc = Associations::isEnabled();
// Are associations implemented for this extension?
$extensionassoc = array_key_exists('item_associations', $this->form->getFieldsets());
$hasAssoc = ($this->form->getValue('language', null, '*') !== '*');

// Fieldsets to not automatically render by /layouts/joomla/edit/params.php
$this->ignore_fieldsets = ['jmetadata', 'item_associations'];
$this->useCoreUI = true;

// In case of modal
$isModal = $input->get('layout') === 'modal';
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>

<form action="<?php echo Route::_('index.php?option=com_categories&extension=' . $input->getCmd('extension', 'com_content') . '&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
			
			<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'general')); ?>
			
			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'general', Text::_('JCATEGORY')); ?>
			<?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
				<div id="fieldset-publishingdata" class="j-card options-grid-form options-grid-form-full mb-4">
					<div class="j-card-header">
						<?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?>
					</div>
					<div class="j-card-body">
						<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
					</div>
				</div>
				<div id="fieldset-metadata" class="j-card options-grid-form options-grid-form-full">
					<div class="j-card-header">
						<?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?>
					</div>
					<div class="j-card-body">
						<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php if (!$isModal && $assoc && $extensionassoc) : ?>
				<?php if ($hasAssoc) : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS')); ?>
					<div id="fieldset-associations" class="j-card options-grid-form options-grid-form-full">
						<div class="j-card-header">
							<?php echo Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS'); ?>
						</div>
						<div class="j-card-body">
							<?php echo LayoutHelper::render('joomla.edit.associations', $this); ?>
						</div>
					</div>
					<?php echo JHtmlPoweradminUiTab::endTab(); ?>
				<?php endif; ?>
			<?php elseif ($isModal && $assoc && $extensionassoc) : ?>
				<div class="hidden"><?php echo LayoutHelper::render('joomla.edit.associations', $this); ?></div>
			<?php endif; ?>

			<?php if ($this->canDo->get('core.admin')) : ?>

				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'rules', Text::_('COM_CATEGORIES_FIELDSET_RULES')); ?>
				<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
					<!-- <legend><?php echo Text::_('COM_CATEGORIES_FIELDSET_RULES'); ?></legend> -->
					<div>
					<?php echo $this->form->getInput('rules'); ?>
					</div>
				</fieldset>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>

			<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
			<?php echo $this->form->getInput('extension'); ?>
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
	<input type="hidden" name="forcedLanguage" value="<?php echo $input->get('forcedLanguage', '', 'cmd'); ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
