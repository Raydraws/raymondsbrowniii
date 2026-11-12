<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Login\Administrator\Helper\LoginHelper;

$loginHelperPath = JPATH_ADMINISTRATOR . '/modules/mod_login/src/Helper/LoginHelper.php';
if (file_exists($loginHelperPath)) {
    require_once $loginHelperPath;
}

$langs            = LoginHelper::getLanguageList();
$twofactormethods = AuthenticationHelper::getTwoFactorMethods();
$return           = LoginHelper::getReturnUri();

require ModuleHelper::getLayoutPath('mod_login', $params->get('layout', 'default'));
