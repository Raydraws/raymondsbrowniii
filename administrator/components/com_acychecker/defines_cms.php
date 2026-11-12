<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyCheckerCmsServices\Url;

define('ACYC_CMS', 'joomla');
define('ACYC_CMS_TITLE', 'Joomla!');
define('ACYC_COMPONENT', 'com_acychecker');
define('ACYC_ACYMAILING_COMPONENT', 'com_acym');
define('ACYC_ACYMAILING5_COMPONENT', 'com_acymailing');
define('ACYC_DEFAULT_LANGUAGE', 'en-GB');
define('ACYC_ADMIN_GROUP', 8);

define('ACYC_BASE', rtrim(JPATH_BASE, DS).DS);
define('ACYC_ROOT', rtrim(JPATH_ROOT, DS).DS);
define('ACYC_BACK', rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS.ACYC_COMPONENT.DS);
define('ACYC_VIEW', ACYC_BACK.'src'.DS.'Views'.DS);
define('ACYC_MEDIA', ACYC_ROOT.'media'.DS.ACYC_COMPONENT.DS);
define('ACYC_LANGUAGE', ACYC_ROOT.'language'.DS);
define('ACYC_VAR', ACYC_BACK.'var'.DS);

define('ACYC_MEDIA_RELATIVE', 'media/'.ACYC_COMPONENT.'/');
define('ACYC_MEDIA_URL', Url::rootURI().ACYC_MEDIA_RELATIVE);
define('ACYC_IMAGES', ACYC_MEDIA_URL.'images/');
define('ACYC_CSS', ACYC_MEDIA_URL.'css/');
define('ACYC_JS', ACYC_MEDIA_URL.'js/');

define('ACYC_MEDIA_FOLDER', 'media/'.ACYC_COMPONENT);
define('ACYC_LOGS_FOLDER', ACYC_MEDIA_FOLDER.DS.'logs'.DS);

$jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
define('ACYC_CMSV', $jversion);
define('ACYC_J37', version_compare($jversion, '3.7.0', '>='));
define('ACYC_J39', version_compare($jversion, '3.9.0', '>='));
define('ACYC_J40', version_compare($jversion, '4.0.0', '>='));
