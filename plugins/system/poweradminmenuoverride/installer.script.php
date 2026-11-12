<?php

/**
 * @package     Poweradmin
 * @subpackage  System.PoweradminMenuOverride
 *
 * @copyright   (C) 2010 - 2026 JoomShaper.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Installer script to ensure the Poweradmin menu override system plugin stays enabled.
 *
 * @since  1.0.0
 */
final class PlgSystemPoweradminMenuOverrideInstallerScript
{
    /**
     * Ensure the plugin is enabled after install/update/discover install.
     *
     * @param   string  $type    The type of change being performed.
     * @param   mixed   $parent  The adapter instance invoking the script.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function postflight($type, $parent): void
    {
        $this->enablePlugin();
    }

    /**
     * Enable the Poweradmin menu override plugin.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function enablePlugin(): void
    {
        $container = Factory::getContainer();

        if (!$container->has(DatabaseInterface::class)) {
            return;
        }

        /** @var DatabaseInterface $db */
        $db    = $container->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('type') . ' = :type')
            ->where($db->quoteName('element') . ' = :element')
            ->where($db->quoteName('folder') . ' = :folder');

        $type    = 'plugin';
        $element = 'poweradminmenuoverride';
        $folder  = 'system';

        $query
            ->bind(':type', $type)
            ->bind(':element', $element)
            ->bind(':folder', $folder);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\Throwable $exception) {
            // Do not block installation if enabling fails.
        }
    }
}

