<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use Joomla\CMS\Factory;
use AcyChecker\Services\RouterService;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    $app = Factory::getApplication();
    $app->enqueueMessage('This version of AcyChecker requires at least PHP 7.4.0, it is time to update the PHP version of your server!', 'error');
} else {
    include_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    include_once __DIR__.DIRECTORY_SEPARATOR.'defines.php';

    new RouterService();
}
