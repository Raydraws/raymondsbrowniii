<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_user
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$hideLinks = $app->input->getBool('hidemainmenu');

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

HTMLHelper::_('script', 'system/joomla-dropdown.min.js', array('version'=> 'auto', 'relative' => true));
HTMLHelper::_('script', 'vendor/punycode/punycode.min.js', array('version'=> 'auto', 'relative' => true));
?>

<div class="header-item-content header-profile">
    <div class="joomla-dropdown-container">
        <button data-target="header-user-dropdown" class="header-dropdown-button <?php echo ($hideLinks ? 'disabled' : ''); ?>" type="button" <?php echo ($hideLinks ? 'disabled' : ''); ?> title="<?php echo Text::_('MOD_USER_MENU'); ?>">
            <span class="header-user-icon" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('user', 22, 'currentColor'); ?></span>
            <span class="header-user-name"><?php echo $user->name; ?></span>
            <span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('down-arrow', 14, 'currentColor'); ?></span>
        </button>
        
        <joomla-dropdown for="header-user-dropdown">
            <?php
            // Light/Dark Mode toggle
            $darkMode = \JHtmlPoweradmin::isDarkMode();
            $currentUri = Uri::getInstance();
            $toggleRoute = 'index.php?option=com_users&task=poweradmin.toggleDarkMode&' . Session::getFormToken() . '=1&return=' . base64_encode($currentUri->toString());
            ?>
            <a href="<?php echo Route::_($toggleRoute); ?>" class="dropdown-item">
                <span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($darkMode ? 'light' : 'dark', $darkMode ? 14 : 12, 'currentColor'); ?></span>
                <?php echo $darkMode ? Text::_('TPL_POWERADMIN_LIGHT_MODE') : Text::_('TPL_POWERADMIN_DARK_MODE'); ?>
            </a>
            <?php $uri   = Uri::getInstance(); ?>
		    <?php $route = 'index.php?option=com_users&task=user.edit&id=' . $user->id; ?>
            <a href="<?php echo Route::_($route); ?>" class="dropdown-item">
                <span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('user', 14, 'currentColor'); ?></span>
				<?php echo Text::_('MOD_USER_EDIT_ACCOUNT'); ?>
            </a>
            <?php // TODO: route to accessibility settings ?>
            <!-- <a href="#" class="dropdown-item">
                <span class="icon-shield"></span>
				<?php echo Text::_('MOD_USER_ACCESSIBILITY_SETTINGS'); ?>
            </a> -->
            <?php $route = 'index.php?option=com_login&task=logout&amp;' . Session::getFormToken() . '=1'; ?>
            <a href="<?php echo Route::_($route); ?>" class="dropdown-item text-center">
                <?php echo Text::_('JLOGOUT'); ?>
                <span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('exit', 14, 'currentColor'); ?></span>
            </a>
        </joomla-dropdown>
    </div>
</div>
