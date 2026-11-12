<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

class Date
{
    public static function getTimeOffsetCMS()
    {
        static $timeoffset = null;
        if ($timeoffset === null) {

            $dateC = Factory::getDate(
                'now',
                Database::getCMSConfig('offset')
            );
            $timeoffset = $dateC->getOffsetFromGMT(true) * 3600;
        }

        return $timeoffset;
    }

    public static function dateTimeCMS($time)
    {
        return HTMLHelper::_('date', $time, 'Y-m-d H:i:s', null);
    }
}
