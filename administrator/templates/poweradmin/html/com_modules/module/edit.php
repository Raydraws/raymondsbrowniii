<?php
/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\StringHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$hasContent = isset($this->item->xml->customContent);
$hasContentFieldName = 'content';

// For a later improvement
if ($hasContent)
{
	$hasContentFieldName = 'content';
}

// Get Params Fieldsets
$this->fieldsets = $this->form->getFieldsets('params');
$this->useCoreUI = true;

Text::script('JYES');
Text::script('JNO');
Text::script('JALL');
Text::script('JTRASHED');

$this->document->addScriptOptions('module-edit', ['itemId' => $this->item->id, 'state' => (int) $this->item->id == 0 ? 'Add' : 'Edit']);
$wa->registerAndUseScript('com_modules.admin-module-edit', 'com_modules/admin-module-edit.min.js', ['version' => 'auto', 'relative' => true]);

$input = Factory::getApplication()->input;

// In case of modal
$isModal = $input->get('layout') === 'modal';
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') == 'component' ? '&tmpl=component' : '';

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

?>

<form action="<?php echo Route::_('index.php?option=com_modules&layout=' . $layout . $tmpl . '&client_id=' . $this->form->getValue('client_id') . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="module-form" class="form-validate">

	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
			
			<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'general')); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'general', Text::_('COM_MODULES_MODULE')); ?>
			<div class="j-card">
				<?php if ($this->item->xml) : ?>
					<?php if ($this->item->xml->description) : ?>
						<div class="j-card-header">
							<h2 class="j-card-title">
								<?php
								if ($this->item->xml)
								{
									echo ($text = (string) $this->item->xml->name) ? Text::_($text) : $this->item->module;
								}
								else
								{
									echo Text::_('COM_MODULES_ERR_XML');
								}
								?>
								<span class="badge badge-success">
									<?php echo $this->item->client_id == 0 ? Text::_('JSITE') : Text::_('JADMINISTRATOR'); ?>
								</span>
								<small class="d-block mt-2">
									<?php
									$this->fieldset    = 'description';
									$short_description = Text::_($this->item->xml->description);
									$this->fieldset    = 'description';
									$long_description  = LayoutHelper::render('joomla.edit.fieldset', $this);

									if (!$long_description)
									{
										$truncated = StringHelper::truncate($short_description, 550, true, false);

										if (strlen($truncated) > 500)
										{
											$long_description  = $short_description;
											$short_description = StringHelper::truncate($truncated, 250);

											if ($short_description == $long_description)
											{
												$long_description = '';
											}
										}
									}
									?>
									<?php echo $short_description; ?>
									<?php if ($long_description) : ?>
										<span class="readmore d-block j-card-link">
											<a href="#" onclick="document.querySelector('#tab-description').click();">
												<?php echo Text::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
											</a>
										</span>
									<?php endif; ?>
								</small>
							</h2>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<div class="p-3">
						<div class="j-alert j-alert-danger">
							<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('warning', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('ERROR'); ?></span>
							<?php echo Text::_('COM_MODULES_ERR_XML'); ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="j-card-body">
					<?php
						if ($hasContent)
						{
							echo $this->form->getInput($hasContentFieldName);
						}
						$this->fieldset = 'basic';
						$html = LayoutHelper::render('joomla.edit.fieldset', $this);
						echo $html;
					?>
				</div>
			</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php if (isset($long_description) && $long_description != '') : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'description', Text::_('JGLOBAL_FIELDSET_DESCRIPTION')); ?>
					<div class="j-card">
						<div class="j-card-body">
							<?php echo $long_description; ?>
						</div>
					</div>
				<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>

			<?php if ($this->item->client_id == 0) : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'assignment', Text::_('COM_MODULES_MENU_ASSIGNMENT')); ?>
				<fieldset id="fieldset-assignment" class="options-grid-form options-grid-form-full">
					<?php echo $this->loadTemplate('assignment'); ?>
				</fieldset>
				<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>

			<?php
			$this->fieldsets        = array();
			$this->ignore_fieldsets = array('basic', 'description');
			echo LayoutHelper::render('joomla.edit.params', $this);
			?>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'permissions', Text::_('COM_MODULES_FIELDSET_RULES')); ?>
				<fieldset id="fieldset-permissions" class="options-grid-form options-grid-form-full">
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
				<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>

			<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
		</div>

		<div class="col-lg-3">
			<div class="j-card">
				<div class="j-card-body">
				<?php
				// Set main fields.
				$this->fields = array(
					'showtitle',
					'position',
					'published',
					'publish_up',
					'publish_down',
					'access',
					'ordering',
					'language',
					'note'
				);

				?>
				<?php if ($this->item->client_id == 0) : ?>
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				<?php else : ?>
					<?php echo LayoutHelper::render('joomla.edit.admin_modules', $this); ?>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
	<?php echo $this->form->getInput('module'); ?>
	<?php echo $this->form->getInput('client_id'); ?>
</form>
