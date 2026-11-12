<?php
/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

$this->modalLink = '&tmpl=component&view=module&layout=modal';
?>
<div class="container-popup">
	<?php $this->setLayout('default'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
