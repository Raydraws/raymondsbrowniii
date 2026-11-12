<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\Module\Content\Administrator\Helper\ModContentHelper;

require_once __DIR__ . '/Helper/ModContentHelper.php';

if (!class_exists('ModContentHelper', false)) {
    class_alias(ModContentHelper::class, 'ModContentHelper');
}

