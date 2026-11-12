<?php
/**
 * @package     Poweradmin
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

Text::script('ERROR');
?>
<form action="<?php echo Route::_('index.php?option=com_menus&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_MENUS_MENU_DETAILS')); ?>

			<div id="fieldset-details" class="j-card">
				<div class="j-card-header">
					<?php echo Text::_('COM_MENUS_MENU_DETAILS'); ?>
				</div>

				<div class="j-card-body">
					<?php
					echo $this->form->renderField('menutype');

					echo $this->form->renderField('description');

					echo $this->form->renderField('client_id');

					echo $this->form->renderField('preset');
					?>
				</div>
			</div>

			<?php echo JHtmlPoweradminUiTab::endTab(); ?>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'permissions', Text::_('COM_MENUS_FIELDSET_RULES')); ?>
				<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
					<legend><?php echo Text::_('COM_MENUS_FIELDSET_RULES'); ?></legend>
					<div>
					<?php echo $this->form->getInput('rules'); ?>
					</div>
				</fieldset>
				<?php echo JHtmlPoweradminUiTab::endTab(); ?>
			<?php endif; ?>

		<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
		<input type="hidden" name="task" value="">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
