<?php
/**
 * @package     Poweradmin
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<button type="button" class="btn btn-secondary" onclick="document.getElementById('batch-access').value='';document.getElementById('batch-language-id').value=''" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<button type="submit" class="btn btn-success" onclick="Joomla.submitbutton('tag.batch');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
