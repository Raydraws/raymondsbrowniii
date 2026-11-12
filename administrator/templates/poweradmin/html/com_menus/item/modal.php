<?php
/**
 * @package     Poweradmin
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

HTMLHelper::_('stylesheet', 'form-select-bridge.css', ['version' => 'auto', 'relative' => true]);

?>
<button id="applyBtn" type="button" class="sr-only" onclick="Joomla.submitbutton('item.apply'); jEditMenuModal();"></button>
<button id="saveBtn" type="button" class="sr-only" onclick="Joomla.submitbutton('item.save'); jEditMenuModal();"></button>
<button id="closeBtn" type="button" class="sr-only" onclick="Joomla.submitbutton('item.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('edit'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
