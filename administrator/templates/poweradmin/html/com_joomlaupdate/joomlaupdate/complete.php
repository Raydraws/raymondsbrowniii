<?php
/**
 * @package     Poweradmin
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>

<fieldset class="options-grid-form options-grid-form-full">
	<legend>
		<?php echo Text::_('COM_JOOMLAUPDATE_VIEW_COMPLETE_HEADING'); ?>
	</legend>
	<div>
		<div class="j-alert j-alert-success">
			<span class="icon-checkinmark" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('NOTICE'); ?></span>
			<?php echo Text::sprintf('COM_JOOMLAUPDATE_VIEW_COMPLETE_MESSAGE', JVERSION); ?>
		</div>
	</div>
</fieldset>
<form action="<?php echo Route::_('index.php?option=com_joomlaupdate'); ?>" method="post" id="adminForm">
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
