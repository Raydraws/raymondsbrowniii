<?php
/**
 * @package     Poweradmin
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('script', 'com_users/admin-users-user.min.js', array('version' => 'auto', 'relative' => true));

$input = Factory::getApplication()->input;

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
$settings  = array();

$this->useCoreUI = true;

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}
?>

<form action="<?php echo Route::_('index.php?option=com_users&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="user-form" enctype="multipart/form-data" class="form-validate">

	<h2><?php echo $this->form->getValue('name'); ?></h2>

	<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'details')); ?>

		<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'details', Text::_('COM_USERS_USER_ACCOUNT_DETAILS')); ?>
			<div class="j-card">
				<div class="j-card-body">
					<div class="row">
						<div class="col-lg-8 col-xl-6">
						<?php echo $this->form->renderFieldset('user_details'); ?>
						</div>
					</div>
				</div>
			</div>
		<?php echo JHtmlPoweradminUiTab::endTab(); ?>
		<?php if ($this->grouplist) : ?>
			<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'groups', Text::_('COM_USERS_ASSIGNED_GROUPS')); ?>
				<div id="fieldset-groups" class="j-card options-grid-form options-grid-form-full">
					<div class="j-card-header">
						<?php echo Text::_('COM_USERS_ASSIGNED_GROUPS'); ?>
					</div>
					<div class="j-card-body">
						<?php echo $this->loadTemplate('groups'); ?>
					</div>
				</div>
			<?php echo JHtmlPoweradminUiTab::endTab(); ?>
		<?php endif; ?>

		<?php
		$this->ignore_fieldsets = array('user_details');
		echo LayoutHelper::render('joomla.edit.params', $this);
		?>

	<?php if (!empty($this->tfaform) && $this->item->id) : ?>
	<?php echo JHtmlPoweradminUiTab::addTab('myTab', 'twofactorauth', Text::_('COM_USERS_USER_TWO_FACTOR_AUTH')); ?>
	<div class="control-group">
		<div class="control-label">
			<label id="jform_twofactor_method-lbl" for="jform_twofactor_method">
				<?php echo Text::_('COM_USERS_USER_FIELD_TWOFACTOR_LABEL'); ?>
			</label>
		</div>
		<div class="controls">
			<?php echo HTMLHelper::_('select.genericlist', Usershelper::getTwoFactorMethods(), 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()', 'class' => 'custom-select'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
		</div>
	</div>
	<div id="com_users_twofactor_forms_container">
		<?php foreach ($this->tfaform as $form) : ?>
		<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
		<div id="com_users_twofactor_<?php echo $form['method'] ?>" style="<?php echo $style; ?>">
			<?php echo $form['form'] ?>
		</div>
		<?php endforeach; ?>
	</div>

	<fieldset>
		<legend>
			<?php echo Text::_('COM_USERS_USER_OTEPS'); ?>
		</legend>
		<div class="j-alert j-alert-info">
			<span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('COM_USERS_USER_OTEPS_DESC'); ?>
		</div>
		<?php if (empty($this->otpConfig->otep)) : ?>
			<div class="j-alert j-alert-warning">
				<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('warning', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
				<?php echo Text::_('COM_USERS_USER_OTEPS_WAIT_DESC'); ?>
			</div>
		<?php else : ?>
		<?php foreach ($this->otpConfig->otep as $otep) : ?>
		<span class="col-lg-3">
			<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
		</span>
		<?php endforeach; ?>
		<?php endif; ?>
	</fieldset>

	<?php echo JHtmlPoweradminUiTab::endTab(); ?>
	<?php endif; ?>

	<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
