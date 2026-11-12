<?php
/**
 * @package     Poweradmin
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$input = Factory::getApplication()->input;
?>
<form action="<?php echo Route::_('index.php?option=com_templates&view=template&id=' . $input->getInt('id') . '&file=' . $this->file); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'description')); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('COM_TEMPLATES_TAB_DESCRIPTION')); ?>
		<div class="j-card">
			<div class="j-card-body">
				<?php echo $this->loadTemplate('description'); ?>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
