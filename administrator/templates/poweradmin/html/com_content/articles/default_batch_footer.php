<?php
/**
 * @package     Poweradmin
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('script', 'com_content/admin-articles-default-batch-footer.js', ['version' => 'auto', 'relative' => true]);
?>
<button type="button" class="btn btn-secondary" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<button type="submit" id='batch-submit-button-id' class="btn btn-success" data-submit-task='article.batch'>
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
