<?php
/**
 * @package     Poweradmin
 * @subpackage  com_redirect
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById('batch_urls').value='';">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<button type="submit" class="btn btn-success" onclick="Joomla.submitbutton('links.batch');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
