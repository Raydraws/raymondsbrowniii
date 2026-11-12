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

?>
<dd class="create">
	<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('calendar', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span>
	<time datetime="<?php echo HTMLHelper::_('date', $displayData['item']->created, 'c'); ?>" itemprop="dateCreated">
		<?php echo Text::sprintf('COM_CONTENT_CREATED_DATE_ON', HTMLHelper::_('date', $displayData['item']->created, Text::_('DATE_FORMAT_LC3'))); ?>
	</time>
</dd>
