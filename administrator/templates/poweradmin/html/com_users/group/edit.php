<?php
/**
 * @package     Poweradmin
 * @subpackage  com_users
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

$this->useCoreUI = true;
?>

<form action="<?php echo Route::_('index.php?option=com_users&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="group-form" class="form-validate">
	<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>
	<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_USERS_USERGROUP_DETAILS')); ?>
	<div class="j-card">
		<div class="j-card-body">
			<div class="row">
				<div class="col-lg-8 col-xl-6">
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('parent_id'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php echo JHtmlPoweradminUiTab::endTab(); ?>
	<?php $this->ignore_fieldsets = array('group_details'); ?>
	<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
	<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
