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

$params = $displayData['params'];
$legacy = $displayData['legacy'];

?>
<?php if ($params->get('show_icons')) : ?>
	<?php if ($legacy) : ?>
		<?php echo HTMLHelper::_('image', 'system/new.png', Text::_('JNEW'), null, true); ?>
	<?php else : ?>
		<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('plus', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span>
		<?php echo Text::_('JNEW'); ?>
	<?php endif; ?>
<?php else : ?>
	<?php echo Text::_('JNEW') . '&#160;'; ?>
<?php endif; ?>
