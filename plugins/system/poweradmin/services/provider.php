<?php

/**
 * @package     Poweradmin
 * @subpackage  System.Poweradmin
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Poweradmin\Extension\Poweradmin;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $config  = (array) PluginHelper::getPlugin('system', 'poweradmin');
                $subject = $container->get(DispatcherInterface::class);
                $app     = Factory::getApplication();
                $db      = $container->get(DatabaseInterface::class);

                $plugin = new \Joomla\Plugin\System\Poweradmin\Extension\Poweradmin($subject, $config);
                $plugin->setApplication($app);
                $plugin->setDatabase($db);

                return $plugin;
            }
        );
    }
};

