<?php
/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'form-select-bridge.css', ['version' => 'auto', 'relative' => true]);

?>
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('module.apply');"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('module.save');"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('module.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('edit'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
