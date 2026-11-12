<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;

$name = $displayData;

?>
<div class="toggle-editor btn-toolbar float-right clearfix mt-3">
	<div class="btn-group">
		<button type="button" class="btn btn-secondary" href="#"
			onclick="tinyMCE.execCommand('mceToggleEditor', false, '<?php echo $name; ?>');return false;"
		>
			<span class="icon-eye" aria-hidden="true"></span>
			<?php echo Text::_('PLG_TINY_BUTTON_TOGGLE_EDITOR'); ?>
		</button>
	</div>
</div>