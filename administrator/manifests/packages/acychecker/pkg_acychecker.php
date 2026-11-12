<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class Pkg_AcycheckerInstallerScript extends \Joomla\CMS\Installer\InstallerScript
{
    public function preflight($type, $parent)
    {
        if (!parent::preflight($type, $parent)) {
            return false;
        }

        return true;
    }

    public function postflight($type, $parent)
    {
        // Perform some post install tasks
        if ($type === 'install') {
            $jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
            $method = version_compare($jversion, '4.0.0', '>=') ? 'execute' : 'query';

            $db = Factory::getDbo();
            $db->setQuery('UPDATE `#__extensions` SET enabled = 1 WHERE type = "plugin" AND element = "acyctriggers"');
            $db->$method();
        }

        return true;
    }
}
