<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Component\ComponentHelper;

class Extension
{
    public static function isExtensionActive($extension)
    {
        return ComponentHelper::isEnabled($extension, true);
    }

    public static function getPluginsPath($file, $dir)
    {
        return rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS;
    }
}
