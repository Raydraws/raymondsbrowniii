<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');

?>
<h1><?php echo $this->item->name; ?></h1>
<form action="<?php echo Route::_('index.php?option=com_installer&view=updatesite&layout=edit&update_site_id=' . (int) $this->item->update_site_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php echo $this->form->renderFieldset('updateSite'); ?>
	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
