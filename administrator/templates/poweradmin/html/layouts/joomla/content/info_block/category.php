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
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;

?>
<dd class="category-name">
	<?php $title = $this->escape($displayData['item']->category_title); ?>
	<?php if ($displayData['params']->get('link_category') && !empty($displayData['item']->catid)) : ?>
		<?php $url = '<a href="' . Route::_(
			ContentRouteHelper::getCategoryRoute($displayData['item']->catid, $displayData['item']->category_language)
			)
			. '" itemprop="genre">' . $title . '</a>'; ?>
		<?php echo Text::sprintf('COM_CONTENT_CATEGORY', $url); ?>
	<?php else : ?>
		<?php echo Text::sprintf('COM_CONTENT_CATEGORY', '<span itemprop="genre">' . $title . '</span>'); ?>
	<?php endif; ?>
</dd>