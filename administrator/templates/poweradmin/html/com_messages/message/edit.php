<?php
/**
 * @package     Poweradmin
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<form action="<?php echo Route::_('index.php?option=com_messages'); ?>" method="post" name="adminForm" id="message-form" class="form-validate">
	<div class="adminform">
		<div class="j-card">
			<div class="j-card-body clearfix">
				<div class="form-group">
					<?php echo $this->form->getLabel('user_id_to'); ?>
					<?php echo $this->form->getInput('user_id_to'); ?>
				</div>
				<div class="form-group">
					<?php echo $this->form->getLabel('subject'); ?>
					<?php echo $this->form->getInput('subject'); ?>
				</div>
				<div class="form-group">
					<?php echo $this->form->getLabel('message'); ?>
					<?php echo $this->form->getInput('message'); ?>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
