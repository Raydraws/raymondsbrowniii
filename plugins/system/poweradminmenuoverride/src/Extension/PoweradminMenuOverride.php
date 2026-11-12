<?php

/**
 * @package     Poweradmin Menu Override
 * @subpackage  System.PoweradminMenuOverride
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Plugin\System\PoweradminMenuOverride\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Poweradmin Menu Override Plugin
 * 
 * This plugin overrides menu-related classes (MenusHelper, Menu from mod_submenu)
 * to use custom implementations from the template directory.
 *
 * @since  1.0.0
 */
final class PoweradminMenuOverride extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * Tracks whether the plugin should run for Poweradmin template.
     *
     * @var bool
     */
    private $isTemplateActive = false;

    /**
     * Constructor
     *
     * @param   \Joomla\Event\DispatcherInterface  $dispatcher  The event dispatcher
     * @param   array                               $config      An optional associative array of configuration settings
     *
     * @since   1.0.0
     */
    public function __construct($dispatcher, array $config = [])
    {
        parent::__construct($dispatcher, $config);

        $this->isTemplateActive = $this->isPoweradminTemplate();

        if (!$this->isTemplateActive) {
            return;
        }
        
        // Register autoloader immediately in constructor to ensure it loads before modules
        $this->registerClassOverrides();
    }

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [];
    }

    /**
     * Register class overrides for menu-related classes
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function registerClassOverrides()
    {
        // Only register if classes haven't been loaded yet
        $classesToOverride = [
            'Joomla\\Component\\Menus\\Administrator\\Helper\\MenusHelper',
            'Joomla\\Module\\Submenu\\Administrator\\Menu\\Menu',
            'Joomla\\Module\\Menu\\Administrator\\Menu\\CssMenu'
        ];
        
        $alreadyLoaded = false;
        foreach ($classesToOverride as $className) {
            if (class_exists($className, false)) {
                $alreadyLoaded = true;
                break;
            }
        }
        
        // If classes are already loaded, we can't override them
        if ($alreadyLoaded) {
            return;
        }
        
        // Register autoloader to override menu classes
        $pluginPath = __DIR__ . '/../../';
        
        spl_autoload_register(function ($className) use ($pluginPath) {
            // Override MenusHelper
            if ($className === 'Joomla\\Component\\Menus\\Administrator\\Helper\\MenusHelper') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/Helper/MenusHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override Menu from mod_submenu
            if ($className === 'Joomla\\Module\\Submenu\\Administrator\\Menu\\Menu') {
                $file = $pluginPath . 'src/Module/Submenu/Administrator/Menu/Menu.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override CssMenu from mod_menu
            if ($className === 'Joomla\\Module\\Menu\\Administrator\\Menu\\CssMenu') {
                $file = $pluginPath . 'src/Module/Menu/Administrator/Menu/CssMenu.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            return false;
        }, true, true); // prepend=true means this autoloader runs FIRST
    }

    /**
     * Determines if the active administrator template is Poweradmin.
     *
     * @return bool
     */
    private function isPoweradminTemplate(): bool
    {
        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        if (!$app->isClient('administrator')) {
            return false;
        }

        if (!method_exists($app, 'getTemplate')) {
            return false;
        }

        $template = $app->getTemplate(true);

        return isset($template->template) && $template->template === 'poweradmin';
    }
}

