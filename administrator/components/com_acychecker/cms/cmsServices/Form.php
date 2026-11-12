<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

class Form
{
    public static function formToken()
    {
        return HTMLHelper::_('form.token');
    }

    /**
     * Check token with all the possibilities
     */
    public static function checkToken()
    {
        Session::checkToken() || Session::checkToken('get') || die('Invalid Token');
    }

    public static function getFormToken()
    {
        return Session::getFormToken().'=1';
    }

    public static function noTemplate($component = true)
    {
        return 'tmpl='.($component ? 'component' : 'raw');
    }

    public static function isNoTemplate()
    {
        $tmpl = Security::getVar('cmd', 'tmpl');

        return in_array($tmpl, ['component', 'raw']);
    }

    public static function setNoTemplate($status = true)
    {
        if ($status) {
            Security::setVar('tmpl', 'component');
        } else {
            Security::setVar('tmpl', '');
        }
    }

    /**
     * @param bool   $token
     * @param string $task
     * @param string $currentStep
     * @param string $currentCtrl
     */
    public static function formOptions($token = true, $task = '', $currentStep = null, $currentCtrl = '', $addPage = true)
    {
        if (!empty($currentStep)) {
            echo '<input type="hidden" name="step" value="'.$currentStep.'"/>';
        }
        echo '<input type="hidden" name="nextstep" value=""/>';
        echo '<input type="hidden" name="option" value="com_acychecker"/>';
        echo '<input type="hidden" name="task" value="'.$task.'"/>';
        echo '<input type="hidden" name="ctrl" value="'.(empty($currentCtrl) ? Security::getVar('cmd', 'ctrl', '') : $currentCtrl).'"/>';
        if ($token) {
            echo Form::formToken();
        }
        echo '<button type="submit" class="is-hidden" id="formSubmit"></button>';
    }

    public static function includeHeaders()
    {
    }
}
