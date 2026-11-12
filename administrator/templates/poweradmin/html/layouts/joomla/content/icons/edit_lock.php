<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$tooltip = $displayData['tooltip'];
$legacy  = $displayData['legacy'];

?>
<?php if ($legacy) : ?>
	<span class="hasTooltip" title="<?php echo HTMLHelper::tooltipText($tooltip . '', 0); ?>">
		<?php echo HTMLHelper::_('image', 'system/checked_out.png', null, null, true); ?>
	</span>
	<?php echo Text::_('JLIB_HTML_CHECKED_OUT'); ?>
<?php else : ?>
	<span class="hasTooltip icon-lock" title="<?php echo HTMLHelper::tooltipText($tooltip . '', 0); ?>"></span>
	<?php echo Text::_('JLIB_HTML_CHECKED_OUT'); ?>
<?php endif; ?>
