<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class Security
{
    public static function getVar($type, $name, $default = null, $source = 'default', $mask = 0)
    {
        if (ACYC_J40) {
            if ($mask & 2) {
                $type = 'RAW';
            } elseif ($mask & 4) {
                $type = 'HTML';
            }

            if (empty($source) || $source === 'default') $source = 'REQUEST';
            $input = Factory::getApplication()->input;
            $sourceInput = $input->__get($source);
            if (Security::isAdmin()) {
                $result = $sourceInput->get($name, $default, $type);
            } else {
                // When the SEF is active, $_REQUEST is empty as Joomla doesn't populate it anymore
                $result = $sourceInput->get($name, $input->get($name, $default, $type), $type);
            }
        } else {
            $result = \JRequest::getVar($name, $default, $source, $type, $mask);
        }

        if (is_string($result) && !($mask & 2)) {
            return ComponentHelper::filterText($result);
        }

        return $result;
    }

    public static function setVar($name, $value = null, $hash = 'method', $overwrite = true)
    {
        if (ACYC_J40) {
            if (empty($hash) || $hash === 'method') $hash = 'REQUEST';
            $input = Factory::getApplication()->input;
            $hashInput = $input->__get($hash);
            $hashInput->set($name, $value);

            return $input->set($name, $value);
        }

        return \JRequest::setVar($name, $value, $hash, $overwrite);
    }

    public static function raiseError($level, $code, $msg, $info = null)
    {
        Message::display($code.': '.$msg, 'error');
        exit;
    }

    public static function isAdmin()
    {
        $acyapp = Miscellaneous::getGlobal('app');

        if (ACYC_J40) {
            return $acyapp->isClient('administrator');
        } else {
            return $acyapp->isAdmin();
        }
    }

    public static function cmsLoaded()
    {
        defined('_JEXEC') || die('Restricted access');
    }

    public static function isDebug()
    {
        return defined('JDEBUG') && JDEBUG;
    }

    public static function askLog($current = true, $message = 'ACYC_NOTALLOWED', $type = 'error')
    {
        //If the user is not logged in, we just redirect him to the login page....
        $url = 'index.php?option=com_users&view=login';
        if ($current) {
            $url .= '&return='.base64_encode(Url::currentURL());
        }
        Router::redirect($url, $message, $type);
    }

    public static function triggerCmsHook($method, $args = [])
    {
        if (ACYC_J40) {
            return Factory::getApplication()->triggerEvent($method, $args);
        }

        global $acydispatcher;
        if ($acydispatcher === null) {
            $acydispatcher = \JEventDispatcher::getInstance();
        }

        return @$acydispatcher->trigger($method, $args);
    }

    public static function arrayToInteger(&$array)
    {
        if (is_array($array)) {
            $array = array_map('intval', $array);
        } else {
            $array = [];
        }
    }

    public static function escape($text)
    {
        if (is_array($text) || is_object($text)) {
            $text = json_encode($text);
        }

        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
