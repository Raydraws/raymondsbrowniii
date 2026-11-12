<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.Poweradmin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 *
 * Module chrome for rendering the module in a submenu
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];

if ($module->content) : ?>
	<div class="header-item header-<?php echo str_replace('_', '-', $module->module); ?>">
		<div class="header-item-content">
			<?php echo $module->content; ?>
		</div>
	</div>
<?php endif; ?>
