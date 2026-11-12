<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;

use AcyChecker\Libraries\AcycObject;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Date;
use AcyCheckerCmsServices\Language;

class DateService extends AcycObject
{
    public static function getDate($time = 0, $format = '%d %B %Y %H:%M')
    {
        if (empty($time)) return '';

        if (is_numeric($format)) {
            $format = Language::translation('ACYC_DATE_FORMAT_LC'.$format);
        }

        $format = str_replace(
            ['%A', '%d', '%B', '%m', '%Y', '%y', '%H', '%M', '%S', '%a', '%I', '%p', '%w'],
            ['l', 'd', 'F', 'm', 'Y', 'y', 'H', 'i', 's', 'D', 'h', 'a', 'w'],
            $format
        );

        //Not sure why but sometimes it fails... so lets try to catch the error...
        try {
            return DateService::date($time, $format, false);
        } catch (\Exception $e) {
            return date($format, $time);
        }
    }

    public static function date($time = 'now', $format = null, $useTz = true, $translate = true)
    {
        if ($time == 'now') {
            $time = time();
        }

        if (is_numeric($time)) {
            $time = Date::dateTimeCMS((int)$time);
        }

        if (empty($format)) {
            $format = Language::translation('ACYC_DATE_FORMAT_LC1');
        }

        //Don't use timezone
        if ($useTz === false) {
            $date = new \DateTime($time);

            if ($translate) {
                return DateService::translateDate($date->format($format));
            } else {
                return $date->format($format);
            }
        } else {
            //use timezone
            $cmsOffset = Database::getCMSConfig('offset');

            $timezone = new \DateTimeZone($cmsOffset);

            if (!is_numeric($cmsOffset)) {
                $cmsOffset = $timezone->getOffset(new \DateTime);
            }

            if ($translate) {
                return DateService::translateDate(date($format, strtotime($time) + $cmsOffset));
            } else {
                return date($format, strtotime($time) + $cmsOffset);
            }
        }
    }

    public static function translateDate($date)
    {
        $map = [
            'January' => Language::translation('ACYC_JANUARY'),
            'February' => Language::translation('ACYC_FEBRUARY'),
            'March' => Language::translation('ACYC_MARCH'),
            'April' => Language::translation('ACYC_APRIL'),
            'May' => Language::translation('ACYC_MAY'),
            'June' => Language::translation('ACYC_JUNE'),
            'July' => Language::translation('ACYC_JULY'),
            'August' => Language::translation('ACYC_AUGUST'),
            'September' => Language::translation('ACYC_SEPTEMBER'),
            'October' => Language::translation('ACYC_OCTOBER'),
            'November' => Language::translation('ACYC_NOVEMBER'),
            'December' => Language::translation('ACYC_DECEMBER'),
            'Monday' => Language::translation('ACYC_MONDAY'),
            'Tuesday' => Language::translation('ACYC_TUESDAY'),
            'Wednesday' => Language::translation('ACYC_WEDNESDAY'),
            'Thursday' => Language::translation('ACYC_THURSDAY'),
            'Friday' => Language::translation('ACYC_FRIDAY'),
            'Saturday' => Language::translation('ACYC_SATURDAY'),
            'Sunday' => Language::translation('ACYC_SUNDAY'),
        ];

        foreach ($map as $english => $translation) {
            if ($translation === $english) {
                continue;
            }

            $date = preg_replace('#'.preg_quote($english).'( |,|$)#i', $translation.'$1', $date);
            $date = preg_replace('#'.preg_quote(substr($english, 0, 3)).'( |,|$)#i', mb_substr($translation, 0, 3).'$1', $date);
        }

        return $date;
    }
}
