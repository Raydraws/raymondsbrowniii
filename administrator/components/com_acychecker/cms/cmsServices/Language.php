<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;

class Language
{
    public static function translation($key)
    {
        return Text::_($key, false, true);
    }

    public static function translationSprintf()
    {
        $args = func_get_args();

        return call_user_func_array(['Joomla\CMS\Language\Text', 'sprintf'], $args);
    }

    public static function getLanguageTag($simple = false)
    {
        if (ACYC_J40) {
            $acylanguage = Factory::getLanguage();
        } else {
            $acylanguage = Factory::getApplication()->getLanguage();
        }

        $langCode = $acylanguage->getTag();

        return $simple ? substr($langCode, 0, 2) : $langCode;
    }

    public static function loadLanguageFile($extension = 'joomla', $basePath = JPATH_SITE, $lang = null, $reload = false, $default = true)
    {
        if (ACYC_J40) {
            $acylanguage = Factory::getLanguage();
        } else {
            $acylanguage = Factory::getApplication()->getLanguage();
        }

        $acylanguage->load($extension, $basePath, $lang, $reload, $default);
    }

    public static function getLanguagePath($basePath = ACYC_BASE, $language = null)
    {
        return LanguageHelper::getLanguagePath(rtrim($basePath, DS), $language);
    }

    public static function getLanguages($installed = false, $uppercase = false)
    {
        $result = [];

        $path = self::getLanguagePath(ACYC_ROOT);
        $dirs = File::getFolders($path);

        $languages = Database::loadObjectList('SELECT * FROM #__languages', 'lang_code');

        foreach ($dirs as $dir) {
            if (strlen($dir) != 5 || $dir == "xx-XX") {
                continue;
            }
            if ($installed && (empty($languages[$dir]) || $languages[$dir]->published != 1)) {
                continue;
            }

            $xmlFiles = File::getFiles($path.DS.$dir, '^([-_A-Za-z]*)\.xml$');
            $xmlFile = reset($xmlFiles);
            if (empty($xmlFile)) {
                $data = [];
            } else {
                $data = Installer::parseXMLInstallFile(ACYC_LANGUAGE.$dir.DS.$xmlFile);
            }

            $lang = new \stdClass();
            $lang->sef = empty($languages[$dir]) ? null : $languages[$dir]->sef;
            $lang->language = $uppercase ? $dir : strtolower($dir);
            $lang->name = empty($data['name']) ? (empty($languages[$dir]) ? $dir : $languages[$dir]->title_native) : $data['name'];
            $lang->exists = file_exists(ACYC_LANGUAGE.$dir.DS.$dir.'.'.ACYC_COMPONENT.'.ini');
            $lang->content = empty($languages[$dir]) ? false : $languages[$dir]->published == 1;

            $result[$dir] = $lang;
        }

        return $result;
    }
}
