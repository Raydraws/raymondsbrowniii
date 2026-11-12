<?php
/**
 * @package     Poweradmin
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

?>
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('plugin.apply');"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('plugin.save');"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('plugin.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('edit'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>

