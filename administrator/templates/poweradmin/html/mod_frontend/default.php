<?php

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

/**
 * @package     Poweradmin
 * @subpackage  mod_frontend
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
?>
<a class="header-item-link" href="<?php echo Uri::root(); ?>"
    title="<?php echo Text::sprintf('MOD_FRONTEND_PREVIEW', $sitename); ?>"
    target="_blank">
    <span class="header-item-text">
        <?php echo Text::_('MOD_FRONTEND_VIEW_SITE'); ?>
    </span>
    <span class="header-item-icon" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('link', 20, 'currentColor'); ?></span>
</a>
