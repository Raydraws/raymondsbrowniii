<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Libraries;

use AcyChecker\Classes\ConfigurationClass;
use AcyCheckerCmsServices\User;

class AcycObject
{
    public $config;
    public $cmsUserVars;

    public function __construct()
    {
        $this->cmsUserVars = User::getCmsUserDbStructure();
        $this->config = ConfigurationClass::class === get_class($this) ? $this : ConfigurationClass::getConfiguration();
    }
}
