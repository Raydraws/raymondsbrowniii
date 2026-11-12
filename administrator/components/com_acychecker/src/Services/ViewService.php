<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;


use AcyCheckerCmsServices\Security;

class ViewService
{
    public static function getView($ctrl, $view)
    {
        return ACYC_VIEW.$ctrl.DS.$view.'.php';
    }
}
