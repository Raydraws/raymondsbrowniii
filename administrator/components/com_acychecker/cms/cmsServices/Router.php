<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\HTML\HTMLHelper;

class Router
{
    public static function initView()
    {
        self::addScript(
            true,
            '
            var ACYC_AJAX_URL = "'.(Security::isAdmin() ? '' : Url::rootURI()).'index.php?option='.ACYC_COMPONENT.'&'.Form::noTemplate().'&'.Form::getFormToken().'&nocache='.time(
            ).'";
            var ACYC_IS_ADMIN = '.(Security::isAdmin() ? 'true' : 'false').';'
        );
        HTMLHelper::_('jquery.framework');
    }

    public static function addScript($raw, $script, $type = 'text/javascript', $defer = true, $async = false, $needTagScript = false, $deps = ['jquery'])
    {
        $acyDocument = Miscellaneous::getGlobal('doc');

        if ($raw) {
            $acyDocument->addScriptDeclaration($script, $type);
        } else {
            if (ACYC_J37) {
                $attributes = [];
                $attributes['type'] = $type;
                if ($defer) $attributes['defer'] = 'defer';
                if ($async) $attributes['async'] = 'async';
                $acyDocument->addScript($script, [], $attributes);
            } else {
                $acyDocument->addScript($script, $type, $defer, $async);
            }
        }

        return 'script';
    }

    public static function addStyle($raw, $style, $type = 'text/css', $media = null, $attribs = [])
    {
        $acyDocument = Miscellaneous::getGlobal('doc');

        if ($raw) {
            $acyDocument->addStyleDeclaration($style, $type);
        } else {
            if (ACYC_J37) {
                $attributes = [];
                $attributes['type'] = $type;
                if ($media) $attributes['media'] = $media;
                if (!empty($attribs)) {
                    $attributes = array_merge($attributes, $attribs);
                }
                $acyDocument->addStyleSheet($style, [], $attributes);
            } else {
                $acyDocument->addStyleSheet($style, $type, $media, $attribs);
            }
        }
    }

    public static function redirect($url, $msg = '', $msgType = 'message', $safe = false)
    {
        $msg = Language::translation($msg);
        $acyapp = Miscellaneous::getGlobal('app');

        if ($safe && !Router::checkRedirect($url)) {
            $url = Url::rootURI();
            Message::enqueueMessage(Language::translation('ACYC_REDIRECT_NOT_ALLOWED'), 'warning');
        }

        if (ACYC_J40) {
            if (!empty($msg)) {
                Message::enqueueMessage($msg, $msgType);
            }

            return $acyapp->redirect($url);
        } else {
            return $acyapp->redirect($url, $msg, $msgType);
        }
    }
}
