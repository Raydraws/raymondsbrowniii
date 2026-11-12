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

$input = Factory::getApplication()->input;
?>
<form method="post" action="">
	<input type="hidden" name="option" value="com_templates">
	<input type="hidden" name="task" value="template.delete">
	<input type="hidden" name="id" value="<?php echo $input->getInt('id'); ?>">
	<input type="hidden" name="file" value="<?php echo $this->file; ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Text::_('COM_TEMPLATES_TEMPLATE_CLOSE'); ?></button>
	<button type="submit" class="btn btn-danger"><?php echo Text::_('COM_TEMPLATES_BUTTON_DELETE'); ?></button>
</form>
