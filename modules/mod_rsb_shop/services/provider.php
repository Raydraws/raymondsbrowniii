<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Raydraws\\Module\\RsbShop'));
        $container->registerServiceProvider(new HelperFactory('\\Raydraws\\Module\\RsbShop\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
