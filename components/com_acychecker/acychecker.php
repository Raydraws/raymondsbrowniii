<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

$DS = DIRECTORY_SEPARATOR;

include_once JPATH_ADMINISTRATOR.$DS.'components'.$DS.'com_acychecker'.$DS.'vendor'.$DS.'autoload.php';
include_once JPATH_ADMINISTRATOR.$DS.'components'.$DS.'com_acychecker'.$DS.'defines.php';

$callbackController = new \AcyChecker\Controllers\CallbackController();
$callbackController->handleCallback();
