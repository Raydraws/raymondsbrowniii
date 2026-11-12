<?php

/**
 * @package     Poweradmin
 * @subpackage  System.Poweradmin
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Plugin\System\Poweradmin\Extension;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Poweradmin Plugin
 * 
 * This plugin overrides module classes (mod_quickicon, mod_logged, mod_popular, mod_latest, mod_stats_admin)
 * to use custom implementations from the template directory.
 *
 * @since  1.0.0
 */
final class Poweradmin extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * Tracks whether the plugin should run for the current template.
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
        $this->registerToolbarOverrides();
        $this->registerFormFieldOverrides();
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
        return [
            'onAfterInitialise' => 'onAfterInitialise',
            'onContentPrepareForm' => 'onContentPrepareForm',
            'onAjaxCache' => 'onAjaxCache',
        ];
    }

    /**
     * Register class overrides for mod_quickicon module
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function registerClassOverrides()
    {
        $classesToOverride = [
            'Joomla\\CMS\\Dispatcher\\ModuleDispatcher',
            'Joomla\\CMS\\Helper\\ModuleHelper',
            'Joomla\\Module\\Quickicon\\Administrator\\Dispatcher\\Dispatcher',
            'Joomla\\Module\\Quickicon\\Administrator\\Helper\\QuickIconHelper',
            'Joomla\\Module\\Quickicon\\Administrator\\Event\\QuickIconsEvent',
            'Joomla\\Module\\Logged\\Administrator\\Helper\\LoggedHelper',
            'Joomla\\Module\\Popular\\Administrator\\Helper\\PopularHelper',
            'Joomla\\Module\\Latest\\Administrator\\Helper\\LatestHelper',
            'Joomla\\Module\\Latest\\Administrator\\Helper\\ModLatestHelper',
            'Joomla\\Module\\StatsAdmin\\Administrator\\Helper\\StatsAdminHelper',
            'Joomla\\Component\\Modules\\Administrator\\View\\Modules\\HtmlView',
            'Joomla\\Component\\Modules\\Administrator\\View\\Module\\HtmlView',
            'Joomla\\Component\\Modules\\Administrator\\View\\Select\\HtmlView',
            'Joomla\\Component\\Content\\Administrator\\View\\Articles\\HtmlView',
            'Joomla\\Component\\Content\\Administrator\\View\\Article\\HtmlView',
            'Joomla\\Component\\Content\\Administrator\\View\\Featured\\HtmlView',
            'Joomla\\Component\\Content\\Administrator\\Model\\ArticlesModel',
            'Joomla\\Component\\Categories\\Administrator\\View\\Categories\\HtmlView',
            'Joomla\\Component\\Categories\\Administrator\\View\\Category\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Workflows\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Workflow\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Transitions\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Transition\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Stages\\HtmlView',
            'Joomla\\Component\\Workflow\\Administrator\\View\\Stage\\HtmlView',
            'Joomla\\Component\\Fields\\Administrator\\View\\Field\\HtmlView',
            'Joomla\\Component\\Fields\\Administrator\\View\\Fields\\HtmlView',
            'Joomla\\Component\\Fields\\Administrator\\View\\Group\\HtmlView',
            'Joomla\\Component\\Fields\\Administrator\\View\\Groups\\HtmlView',
            'Joomla\\Component\\Media\\Administrator\\View\\File\\HtmlView',
            'Joomla\\Component\\Media\\Administrator\\View\\Media\\HtmlView',
            'Joomla\\Component\\Menus\\Administrator\\View\\Menus\\HtmlView',
            'Joomla\\Component\\Menus\\Administrator\\View\\Items\\HtmlView',
            'Joomla\\Component\\Menus\\Administrator\\View\\Item\\HtmlView',
            'Joomla\\Component\\Menus\\Administrator\\View\\Menu\\HtmlView',
            'Joomla\\Component\\Menus\\Administrator\\View\\Menu\\XmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Users\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\User\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Notes\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Note\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Mail\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Levels\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Level\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Groups\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Group\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Debuguser\\HtmlView',
            'Joomla\\Component\\Users\\Administrator\\View\\Debuggroup\\HtmlView',
            'Joomla\\Component\\Actionlogs\\Administrator\\View\\Actionlogs\\HtmlView',
            'Joomla\\Component\\Templates\\Administrator\\View\\Templates\\HtmlView',
            'Joomla\\Component\\Templates\\Administrator\\View\\Template\\HtmlView',
            'Joomla\\Component\\Templates\\Administrator\\View\\Styles\\HtmlView',
            'Joomla\\Component\\Templates\\Administrator\\View\\Style\\HtmlView',
            'Joomla\\Component\\Plugins\\Administrator\\View\\Plugins\\HtmlView',
            'Joomla\\Component\\Plugins\\Administrator\\View\\Plugin\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Database\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Discover\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Install\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Installer\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Languages\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Manage\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Update\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Updatesite\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Updatesites\\HtmlView',
            'Joomla\\Component\\Installer\\Administrator\\View\\Warnings\\HtmlView',
            'Joomla\\Component\\Cache\\Administrator\\View\\Cache\\HtmlView',
            'Joomla\\Component\\Config\\Administrator\\View\\Application\\HtmlView',
            'Joomla\\Component\\Config\\Administrator\\View\\Component\\HtmlView',
            'Joomla\\Component\\Checkin\\Administrator\\View\\Checkin\\HtmlView',
            'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Joomlaupdate\\HtmlView',
            'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Update\\HtmlView',
            'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Upload\\HtmlView',
            'Joomla\\Component\\Redirect\\Administrator\\View\\Links\\HtmlView',
            'Joomla\\Component\\Redirect\\Administrator\\View\\Link\\HtmlView',
            'Joomla\\Component\\Admin\\Administrator\\View\\Help\\HtmlView',
            'Joomla\\Component\\Admin\\Administrator\\View\\Sysinfo\\HtmlView',
            'Joomla\\Component\\Admin\\Administrator\\Service\\HTML\\Directory',
            'Joomla\\Component\\Users\\Administrator\\Service\\HTML\\Users',
            'Joomla\\Component\\Messages\\Administrator\\View\\Messages\\HtmlView',
            'Joomla\\Component\\Messages\\Administrator\\View\\Message\\HtmlView',
            'Joomla\\Component\\Postinstall\\Administrator\\View\\Messages\\HtmlView',
            'Joomla\\Component\\Associations\\Administrator\\View\\Association\\HtmlView',
            'Joomla\\Component\\Associations\\Administrator\\View\\Associations\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Banner\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Banners\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Client\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Clients\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Download\\HtmlView',
            'Joomla\\Component\\Banners\\Administrator\\View\\Tracks\\HtmlView',
            'Joomla\\Component\\Contact\\Administrator\\View\\Contact\\HtmlView',
            'Joomla\\Component\\Contact\\Administrator\\View\\Contacts\\HtmlView',
            'Joomla\\Component\\Tags\\Administrator\\View\\Tag\\HtmlView',
            'Joomla\\Component\\Tags\\Administrator\\View\\Tags\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Filter\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Filters\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Index\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Maps\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView',
            'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView',
            'Joomla\\Component\\Newsfeeds\\Administrator\\View\\Newsfeed\\HtmlView',
            'Joomla\\Component\\Newsfeeds\\Administrator\\View\\Newsfeeds\\HtmlView',
            'Joomla\\Component\\Cpanel\\Administrator\\View\\Cpanel\\HtmlView',
            'Joomla\\Component\\Modules\\Administrator\\Controller\\ModuleController',
            'Joomla\\Component\\Modules\\Administrator\\Model\\ModuleModel',
            'Joomla\\Component\\Checkin\\Administrator\\Model\\CheckinModel',
            'Joomla\\Plugin\\Editors\\TinyMCE\\Extension\\TinyMCE',
            'Joomla\\Plugin\\Editors\\TinyMCE\\PluginTraits\\DisplayTrait',
            'Joomla\\Component\\Contenthistory\\Administrator\\View\\History\\HtmlView',
            'Joomla\\Component\\Contenthistory\\Administrator\\View\\Preview\\HtmlView',
            'Joomla\\Component\\Contenthistory\\Administrator\\View\\Compare\\HtmlView',
            'Joomla\\Component\\Messages\\Administrator\\Controller\\DisplayController',
            'Joomla\\Component\\Privacy\\Administrator\\View\\Consents\\HtmlView',
            'Joomla\\Component\\Privacy\\Administrator\\View\\Capabilities\\HtmlView',
            'Joomla\\Component\\Privacy\\Administrator\\View\\Requests\\HtmlView',
            'Joomla\\Component\\Privacy\\Administrator\\View\\Request\\HtmlView',
            'Joomla\\Component\\Privacy\\Administrator\\View\\Export\\XmlView',
            'Joomla\\Component\\Content\\Administrator\\Field\\Modal\\ArticleField',
        ];
        
        // Register autoloader to override mod_quickicon classes
        $pluginPath = __DIR__ . '/../../';
        
        spl_autoload_register(function ($className) use ($pluginPath) {
            // Override ModuleDispatcher
            if ($className === 'Joomla\\CMS\\Dispatcher\\ModuleDispatcher') {
                $file = $pluginPath . 'src/Dispatcher/ModuleDispatcher.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override Dispatcher
            if ($className === 'Joomla\\Module\\Quickicon\\Administrator\\Dispatcher\\Dispatcher') {
                $file = $pluginPath . 'src/Module/Quickicon/Administrator/Dispatcher/Dispatcher.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override QuickIconHelper
            if ($className === 'Joomla\\Module\\Quickicon\\Administrator\\Helper\\QuickIconHelper') {
                $file = $pluginPath . 'src/Module/Quickicon/Administrator/Helper/QuickIconHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override QuickIconsEvent
            if ($className === 'Joomla\\Module\\Quickicon\\Administrator\\Event\\QuickIconsEvent') {
                $file = $pluginPath . 'src/Module/Quickicon/Administrator/Event/QuickIconsEvent.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override LoggedHelper
            if ($className === 'Joomla\\Module\\Logged\\Administrator\\Helper\\LoggedHelper') {
                $file = $pluginPath . 'src/Module/Logged/Administrator/Helper/LoggedHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override PopularHelper
            if ($className === 'Joomla\\Module\\Popular\\Administrator\\Helper\\PopularHelper') {
                $file = $pluginPath . 'src/Module/Popular/Administrator/Helper/PopularHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override LatestHelper (alias to ModLatestHelper for compatibility)
            if ($className === 'Joomla\\Module\\Latest\\Administrator\\Helper\\LatestHelper') {
                $file = $pluginPath . 'src/Module/Latest/Administrator/Helper/ModLatestHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    // Create an alias for LatestHelper
                    class_alias('Joomla\\Module\\Latest\\Administrator\\Helper\\ModLatestHelper', 'Joomla\\Module\\Latest\\Administrator\\Helper\\LatestHelper');
                    return true;
                }
            }
            
            // Override ModLatestHelper
            if ($className === 'Joomla\\Module\\Latest\\Administrator\\Helper\\ModLatestHelper') {
                $file = $pluginPath . 'src/Module/Latest/Administrator/Helper/ModLatestHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            // Override StatsAdminHelper
            if ($className === 'Joomla\\Module\\StatsAdmin\\Administrator\\Helper\\StatsAdminHelper') {
                $file = $pluginPath . 'src/Module/StatsAdmin/Administrator/Helper/StatsAdminHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Modules\\Administrator\\View\\Modules\\HtmlView') {
                $file = $pluginPath . 'src/Component/Modules/Administrator/View/Modules/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Modules\\Administrator\\View\\Module\\HtmlView') {
                $file = $pluginPath . 'src/Component/Modules/Administrator/View/Module/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Modules\\Administrator\\View\\Select\\HtmlView') {
                $file = $pluginPath . 'src/Component/Modules/Administrator/View/Select/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Content\\Administrator\\View\\Articles\\HtmlView') {
                $file = $pluginPath . 'src/Component/Content/Administrator/View/Articles/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Content\\Administrator\\View\\Article\\HtmlView') {
                $file = $pluginPath . 'src/Component/Content/Administrator/View/Article/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Content\\Administrator\\View\\Featured\\HtmlView') {
                $file = $pluginPath . 'src/Component/Content/Administrator/View/Featured/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Categories\\Administrator\\View\\Categories\\HtmlView') {
                $file = $pluginPath . 'src/Component/Categories/Administrator/View/Categories/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Categories\\Administrator\\View\\Category\\HtmlView') {
                $file = $pluginPath . 'src/Component/Categories/Administrator/View/Category/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Categories\\Administrator\\Field\\Modal\\CategoryField') {
                $file = $pluginPath . 'src/Component/Categories/Administrator/Field/Modal/CategoryField.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Content\\Administrator\\Field\\Modal\\ArticleField') {
                $file = $pluginPath . 'src/Component/Content/Administrator/Field/Modal/ArticleField.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Workflows\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Workflows/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Workflow\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Workflow/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Transitions\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Transitions/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Transition\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Transition/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Stages\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Stages/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Workflow\\Administrator\\View\\Stage\\HtmlView') {
                $file = $pluginPath . 'src/Component/Workflow/Administrator/View/Stage/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Fields\\Administrator\\View\\Field\\HtmlView') {
                $file = $pluginPath . 'src/Component/Fields/Administrator/View/Field/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Fields\\Administrator\\View\\Fields\\HtmlView') {
                $file = $pluginPath . 'src/Component/Fields/Administrator/View/Fields/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Fields\\Administrator\\View\\Group\\HtmlView') {
                $file = $pluginPath . 'src/Component/Fields/Administrator/View/Group/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Fields\\Administrator\\View\\Groups\\HtmlView') {
                $file = $pluginPath . 'src/Component/Fields/Administrator/View/Groups/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Media\\Administrator\\View\\File\\HtmlView') {
                $file = $pluginPath . 'src/Component/Media/Administrator/View/File/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Media\\Administrator\\View\\Media\\HtmlView') {
                $file = $pluginPath . 'src/Component/Media/Administrator/View/Media/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\View\\Menus\\HtmlView') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/View/Menus/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\View\\Items\\HtmlView') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/View/Items/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\View\\Item\\HtmlView') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/View/Item/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\View\\Menu\\HtmlView') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/View/Menu/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\View\\Menu\\XmlView') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/View/Menu/XmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Menus\\Administrator\\Field\\MenutypeField') {
                $file = $pluginPath . 'src/Component/Menus/Administrator/Field/MenutypeField.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Users\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Users/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\User\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/User/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Notes\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Notes/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Note\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Note/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Mail\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Mail/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Levels\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Levels/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Level\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Level/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Groups\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Groups/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Group\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Group/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Debuguser\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Debuguser/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Users\\Administrator\\View\\Debuggroup\\HtmlView') {
                $file = $pluginPath . 'src/Component/Users/Administrator/View/Debuggroup/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Actionlogs\\Administrator\\View\\Actionlogs\\HtmlView') {
                $file = $pluginPath . 'src/Component/Actionlogs/Administrator/View/Actionlogs/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Templates\\Administrator\\View\\Templates\\HtmlView') {
                $file = $pluginPath . 'src/Component/Templates/Administrator/View/Templates/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Templates\\Administrator\\View\\Template\\HtmlView') {
                $file = $pluginPath . 'src/Component/Templates/Administrator/View/Template/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Templates\\Administrator\\View\\Styles\\HtmlView') {
                $file = $pluginPath . 'src/Component/Templates/Administrator/View/Styles/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Templates\\Administrator\\View\\Style\\HtmlView') {
                $file = $pluginPath . 'src/Component/Templates/Administrator/View/Style/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Plugins\\Administrator\\View\\Plugins\\HtmlView') {
                $file = $pluginPath . 'src/Component/Plugins/Administrator/View/Plugins/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Plugins\\Administrator\\View\\Plugin\\HtmlView') {
                $file = $pluginPath . 'src/Component/Plugins/Administrator/View/Plugin/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Database\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Database/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Discover\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Discover/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Install\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Install/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Installer\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Installer/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Languages\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Languages/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Manage\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Manage/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Update\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Update/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Updatesite\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Updatesite/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Updatesites\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Updatesites/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Installer\\Administrator\\View\\Warnings\\HtmlView') {
                $file = $pluginPath . 'src/Component/Installer/Administrator/View/Warnings/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Cache\\Administrator\\View\\Cache\\HtmlView') {
                $file = $pluginPath . 'src/Component/Cache/Administrator/View/Cache/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Config\\Administrator\\View\\Application\\HtmlView') {
                $file = $pluginPath . 'src/Component/Config/Administrator/View/Application/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Config\\Administrator\\View\\Component\\HtmlView') {
                $file = $pluginPath . 'src/Component/Config/Administrator/View/Component/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Checkin\\Administrator\\View\\Checkin\\HtmlView') {
                $file = $pluginPath . 'src/Component/Checkin/Administrator/View/Checkin/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Joomlaupdate\\HtmlView') {
                $file = $pluginPath . 'src/Component/Joomlaupdate/Administrator/View/Joomlaupdate/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Update\\HtmlView') {
                $file = $pluginPath . 'src/Component/Joomlaupdate/Administrator/View/Update/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Joomlaupdate\\Administrator\\View\\Upload\\HtmlView') {
                $file = $pluginPath . 'src/Component/Joomlaupdate/Administrator/View/Upload/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Redirect\\Administrator\\View\\Links\\HtmlView') {
                $file = $pluginPath . 'src/Component/Redirect/Administrator/View/Links/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Redirect\\Administrator\\View\\Link\\HtmlView') {
                $file = $pluginPath . 'src/Component/Redirect/Administrator/View/Link/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Admin\\Administrator\\View\\Help\\HtmlView') {
                $file = $pluginPath . 'src/Component/Admin/Administrator/View/Help/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Admin\\Administrator\\View\\Sysinfo\\HtmlView') {
                $file = $pluginPath . 'src/Component/Admin/Administrator/View/Sysinfo/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Admin\\Administrator\\Service\\HTML\\Directory') {
                $file = $pluginPath . 'src/Component/Admin/Administrator/Service/HTML/Directory.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Users\\Administrator\\Service\\HTML\\Users') {
                $file = $pluginPath . 'src/Component/Users/Administrator/Service/HTML/Users.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Messages\\Administrator\\View\\Messages\\HtmlView') {
                $file = $pluginPath . 'src/Component/Messages/Administrator/View/Messages/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Messages\\Administrator\\View\\Message\\HtmlView') {
                $file = $pluginPath . 'src/Component/Messages/Administrator/View/Message/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Postinstall\\Administrator\\View\\Messages\\HtmlView') {
                $file = $pluginPath . 'src/Component/Postinstall/Administrator/View/Messages/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Associations\\Administrator\\View\\Association\\HtmlView') {
                $file = $pluginPath . 'src/Component/Associations/Administrator/View/Association/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Associations\\Administrator\\View\\Associations\\HtmlView') {
                $file = $pluginPath . 'src/Component/Associations/Administrator/View/Associations/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Banner\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Banner/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Banners\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Banners/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Client\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Client/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Clients\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Clients/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Download\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Download/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Banners\\Administrator\\View\\Tracks\\HtmlView') {
                $file = $pluginPath . 'src/Component/Banners/Administrator/View/Tracks/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Contact\\Administrator\\View\\Contact\\HtmlView') {
                $file = $pluginPath . 'src/Component/Contact/Administrator/View/Contact/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Contact\\Administrator\\View\\Contacts\\HtmlView') {
                $file = $pluginPath . 'src/Component/Contact/Administrator/View/Contacts/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Tags\\Administrator\\View\\Tag\\HtmlView') {
                $file = $pluginPath . 'src/Component/Tags/Administrator/View/Tag/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Tags\\Administrator\\View\\Tags\\HtmlView') {
                $file = $pluginPath . 'src/Component/Tags/Administrator/View/Tags/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Filter\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Filter/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Filters\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Filters/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Index\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Index/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Maps\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Maps/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Searches/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Searches/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Finder\\Administrator\\View\\Searches\\HtmlView') {
                $file = $pluginPath . 'src/Component/Finder/Administrator/View/Searches/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Newsfeeds\\Administrator\\View\\Newsfeed\\HtmlView') {
                $file = $pluginPath . 'src/Component/Newsfeeds/Administrator/View/Newsfeed/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Newsfeeds\\Administrator\\View\\Newsfeeds\\HtmlView') {
                $file = $pluginPath . 'src/Component/Newsfeeds/Administrator/View/Newsfeeds/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Cpanel\\Administrator\\View\\Cpanel\\HtmlView') {
                $file = $pluginPath . 'src/Component/Cpanel/Administrator/View/Cpanel/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\CMS\\Helper\\ModuleHelper') {
                $file = $pluginPath . 'src/Helper/ModuleHelper.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Modules\\Administrator\\Controller\\ModuleController') {
                $file = $pluginPath . 'src/Component/Modules/Administrator/Controller/ModuleController.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Messages\\Administrator\\Controller\\DisplayController') {
                $file = $pluginPath . 'src/Component/Messages/Administrator/Controller/DisplayController.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Modules\\Administrator\\Model\\ModuleModel') {
                $file = $pluginPath . 'src/Component/Modules/Administrator/Model/ModuleModel.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Checkin\\Administrator\\Model\\CheckinModel') {
                $file = $pluginPath . 'src/Component/Checkin/Administrator/Model/CheckinModel.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Plugin\\Editors\\TinyMCE\\Extension\\TinyMCE') {
                $file = $pluginPath . 'src/Plugin/Editors/TinyMCE/Extension/TinyMCE.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Plugin\\Editors\\TinyMCE\\PluginTraits\\DisplayTrait') {
                $file = $pluginPath . 'src/Plugin/Editors/TinyMCE/PluginTraits/DisplayTrait.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Content\\Administrator\\Model\\ArticlesModel') {
                $file = $pluginPath . 'src/Component/Content/Administrator/Model/ArticlesModel.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Contenthistory\\Administrator\\View\\History\\HtmlView') {
                $file = $pluginPath . 'src/Component/Contenthistory/Administrator/View/History/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Contenthistory\\Administrator\\View\\Preview\\HtmlView') {
                $file = $pluginPath . 'src/Component/Contenthistory/Administrator/View/Preview/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Contenthistory\\Administrator\\View\\Compare\\HtmlView') {
                $file = $pluginPath . 'src/Component/Contenthistory/Administrator/View/Compare/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            if ($className === 'Joomla\\Component\\Privacy\\Administrator\\View\\Consents\\HtmlView') {
                $file = $pluginPath . 'src/Component/Privacy/Administrator/View/Consents/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Privacy\\Administrator\\View\\Capabilities\\HtmlView') {
                $file = $pluginPath . 'src/Component/Privacy/Administrator/View/Capabilities/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Privacy\\Administrator\\View\\Requests\\HtmlView') {
                $file = $pluginPath . 'src/Component/Privacy/Administrator/View/Requests/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Privacy\\Administrator\\View\\Request\\HtmlView') {
                $file = $pluginPath . 'src/Component/Privacy/Administrator/View/Request/HtmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            if ($className === 'Joomla\\Component\\Privacy\\Administrator\\View\\Export\\XmlView') {
                $file = $pluginPath . 'src/Component/Privacy/Administrator/View/Export/XmlView.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            return false;
        }, true, true); // prepend=true means this autoloader runs FIRST
    }

    /**
     * Overrides Joomla toolbar classes with Poweradmin template equivalents.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function registerToolbarOverrides(): void
    {
        $toolbarPath   = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar';
        $autoloadFile  = $toolbarPath . '/autoload.php';

        if (!is_dir($toolbarPath)) {
            return;
        }

        if (is_file($autoloadFile)) {
            require_once $autoloadFile;
        }

        $classMap = [
            'Joomla\\CMS\\Toolbar\\ToolbarButton'                 => 'Poweradmin\\Admin\\Toolbar\\ToolbarButton',
            'Joomla\\CMS\\Toolbar\\ToolbarFactoryInterface'       => 'Poweradmin\\Admin\\Toolbar\\ToolbarFactoryInterface',
            'Joomla\\CMS\\Toolbar\\ContainerAwareToolbarFactory'  => 'Poweradmin\\Admin\\Toolbar\\ContainerAwareToolbarFactory',
            'Joomla\\CMS\\Toolbar\\CoreButtonsTrait'              => 'Poweradmin\\Admin\\Toolbar\\CoreButtonsTrait',
            'Joomla\\CMS\\Toolbar\\Button\\AbstractGroupButton'   => 'Poweradmin\\Admin\\Toolbar\\Button\\AbstractGroupButton',
            'Joomla\\CMS\\Toolbar\\Button\\BasicButton'           => 'Poweradmin\\Admin\\Toolbar\\Button\\BasicButton',
            'Joomla\\CMS\\Toolbar\\Button\\ConfirmButton'         => 'Poweradmin\\Admin\\Toolbar\\Button\\ConfirmButton',
            'Joomla\\CMS\\Toolbar\\Button\\CustomButton'          => 'Poweradmin\\Admin\\Toolbar\\Button\\CustomButton',
            'Joomla\\CMS\\Toolbar\\Button\\DropdownButton'        => 'Poweradmin\\Admin\\Toolbar\\Button\\DropdownButton',
            'Joomla\\CMS\\Toolbar\\Button\\HelpButton'            => 'Poweradmin\\Admin\\Toolbar\\Button\\HelpButton',
            'Joomla\\CMS\\Toolbar\\Button\\InlinehelpButton'      => 'Poweradmin\\Admin\\Toolbar\\Button\\InlinehelpButton',
            'Joomla\\CMS\\Toolbar\\Button\\LinkButton'            => 'Poweradmin\\Admin\\Toolbar\\Button\\LinkButton',
            'Joomla\\CMS\\Toolbar\\Button\\PopupButton'           => 'Poweradmin\\Admin\\Toolbar\\Button\\PopupButton',
            'Joomla\\CMS\\Toolbar\\Button\\SeparatorButton'       => 'Poweradmin\\Admin\\Toolbar\\Button\\SeparatorButton',
            'Joomla\\CMS\\Toolbar\\Button\\StandardButton'        => 'Poweradmin\\Admin\\Toolbar\\Button\\StandardButton',
        ];

        spl_autoload_register(function ($class) use ($classMap) {
            if (!isset($classMap[$class])) {
                return false;
            }

            $poweradminClass = $classMap[$class];
            $exists = class_exists($poweradminClass, true)
                || interface_exists($poweradminClass, true)
                || trait_exists($poweradminClass, true);

            if (!$exists) {
                return false;
            }

            if (!class_exists($class, false) && !interface_exists($class, false) && !trait_exists($class, false)) {
                class_alias($poweradminClass, $class);
            }

            return true;
        }, true, true);
    }

    /**
     * Overrides Joomla form field classes with Poweradmin template equivalents.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function registerFormFieldOverrides(): void
    {
        $formPath   = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminForm';
        $autoloadFile  = $formPath . '/autoload.php';

        if (!is_dir($formPath)) {
            return;
        }

        if (is_file($autoloadFile)) {
            require_once $autoloadFile;
        }

        $classMap = [
            'Joomla\\CMS\\Form\\Field\\RadioField' => 'Poweradmin\\Admin\\Form\\Field\\RadioField',
        ];

        spl_autoload_register(function ($class) use ($classMap) {
            if (!isset($classMap[$class])) {
                return false;
            }

            $poweradminClass = $classMap[$class];
            $exists = class_exists($poweradminClass, true)
                || interface_exists($poweradminClass, true)
                || trait_exists($poweradminClass, true);

            if (!$exists) {
                return false;
            }

            if (!class_exists($class, false) && !interface_exists($class, false) && !trait_exists($class, false)) {
                class_alias($poweradminClass, $class);
            }

            return true;
        }, true, true);
    }

    /**
     * Adds template form paths and loads template form overrides
     *
     * @param   Event  $event  The onContentPrepareForm event.
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function onContentPrepareForm(Event $event)
    {
        if (!$this->isTemplateActive) {
            return true;
        }

        /**
         * @var   Form  $form The form to be altered.
         * @var   mixed $data The associated data for the form.
         */
        [$form, $data] = array_values($event->getArguments());

        $formName = $form->getName();

        // Only process com_templates forms
        if (strpos($formName, 'com_templates') !== 0) {
            return true;
        }

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        if (!method_exists($app, 'getTemplate')) {
            return false;
        }

        $template = $app->getTemplate(true);
        $templatePath = JPATH_ADMINISTRATOR . '/templates/' . $template->template . '/html/com_templates/forms';

        // Check if template form directory exists
        if (!is_dir($templatePath)) {
            return true;
        }

        // Map form names to XML file names
        $formFileMap = [
            'com_templates.style' => 'style.xml',
            'com_templates.source' => 'source.xml',
        ];

        // Determine which form file to load based on form name
        $formFile = null;
        if (isset($formFileMap[$formName])) {
            $formFile = $templatePath . '/' . $formFileMap[$formName];
        }

        // Load template form file if it exists
        // Using reset=true will replace existing fields with the same name
        if ($formFile && file_exists($formFile)) {
            // Load the template form file - this will override matching fields in the component form
            $form->loadFile($formFile, true);
        }

        // Also handle filter forms and style_administrator/style_site forms
        // These are loaded via loadFile() with just the filename, so we need to add the path
        Form::addFormPath($templatePath);

        return true;
    }

    /**
     * Event triggered after Joomla initialises.
     * Used to load JGrid override after classmap is processed.
     *
     * @param   Event  $event  The event object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function onAfterInitialise(Event $event): void
    {
        if (!$this->isTemplateActive) {
            return;
        }

        $app = Factory::getApplication();

        // Handle dark mode toggle
        $this->handleDarkModeToggle($app);

        // Normalise media adapter aliases before Joomla processes the request
        $this->normaliseMediaPathAlias($app);

        // Register autoloader for JGrid override (namespaced class)
        // This will intercept when Joomla\CMS\HTML\Helpers\JGrid is requested
        $this->registerJGridAutoloader();
    }

    /**
     * Handle dark mode toggle request
     *
     * @param   CMSApplication  $app  The application object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function handleDarkModeToggle(CMSApplication $app): void
    {
        if (!$app->isClient('administrator')) {
            return;
        }

        $input = $app->input;
        $option = $input->getCmd('option');
        $task = $input->getCmd('task');

        // Check if this is a dark mode toggle request
        if ($option === 'com_users' && $task === 'poweradmin.toggleDarkMode') {
            // Check token
            if (!\Joomla\CMS\Session\Session::checkToken('get')) {
                $app->enqueueMessage(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'), 'error');
                $app->redirect(\Joomla\CMS\Uri\Uri::root());
                return;
            }

            $user = $app->getIdentity();
            
            if (!$user->id) {
                $app->redirect(\Joomla\CMS\Uri\Uri::root());
                return;
            }

            // Toggle global dark mode on the active administrator template style (not per-user).
            // The value "id" is not reliably present on the template object returned by $app->getTemplate(true),
            // so we must fetch it from #__template_styles.
            $template    = $app->getTemplate(true);
            $templateKey = isset($template->template) && $template->template ? (string) $template->template : 'poweradmin';

            $db = $this->getDatabase();
            $styleId = 0;

            // Active style: client_id=1 (administrator), home=1.
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__template_styles'))
                ->where(
                    [
                        $db->quoteName('client_id') . ' = ' . (int) 1,
                        $db->quoteName('home') . ' = ' . $db->quote('1'),
                        $db->quoteName('template') . ' = ' . $db->quote($templateKey),
                    ]
                );

            $db->setQuery($query);
            $styleId = (int) ($db->loadResult() ?: 0);

            // Fallback: if home=1 record isn't found, use latest for this template.
            if ($styleId < 1) {
                $query = $db->getQuery(true)
                    ->select($db->quoteName('id'))
                    ->from($db->quoteName('#__template_styles'))
                    ->where(
                        [
                            $db->quoteName('client_id') . ' = ' . (int) 1,
                            $db->quoteName('template') . ' = ' . $db->quote($templateKey),
                        ]
                    )
                    ->order($db->quoteName('id') . ' DESC')
                    ->setLimit(1);

                $db->setQuery($query);
                $styleId = (int) ($db->loadResult() ?: 0);
            }

            if ($styleId < 1) {
                $app->enqueueMessage('Failed to save Poweradmin dark mode (template style not found).', 'error');
                $app->redirect(\Joomla\CMS\Uri\Uri::root());
                return;
            }
            $query = $db->getQuery(true)
                ->select($db->quoteName('params'))
                ->from($db->quoteName('#__template_styles'))
                ->where($db->quoteName('id') . ' = ' . $styleId);

            $db->setQuery($query);
            $paramsJson = $db->loadResult() ?: '{}';

            $params = new Registry($paramsJson);
            if (!class_exists('JHtmlPoweradmin')) {
                require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';   
            }

            $currentDarkMode = \JHtmlPoweradmin::isDarkMode();
            $newDarkMode = !$currentDarkMode;
            $params->set('poweradmin_dark_mode', $newDarkMode ? '1' : '0');

            $updateQuery = $db->getQuery(true)
                ->update($db->quoteName('#__template_styles'))
                ->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
                ->where($db->quoteName('id') . ' = ' . $styleId);

            $db->setQuery($updateQuery);

            try {
                $db->execute();
            } catch (\RuntimeException $e) {
                $app->enqueueMessage('Failed to save Poweradmin dark mode (database error).', 'error');
                $app->redirect(\Joomla\CMS\Uri\Uri::root());
                return;
            }

            // Redirect back to current page or home
            $return = $input->getBase64('return');
            if ($return) {
                $redirectUrl = base64_decode($return);
            } else {
                $redirectUrl = \Joomla\CMS\Uri\Uri::getInstance()->toString(['scheme', 'host', 'port', 'path', 'query']);
                // Remove the toggle parameters
                $uri = \Joomla\CMS\Uri\Uri::getInstance($redirectUrl);
                $uri->delVar('option');
                $uri->delVar('task');
                $uri->delVar(\Joomla\CMS\Session\Session::getFormToken());
                $redirectUrl = $uri->toString();
            }

            $app->redirect($redirectUrl);
        }
    }

    /**
     * Register autoloader for JGrid override.
     * This intercepts the namespaced class loading to provide our override.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function registerJGridAutoloader(): void
    {
        $jgridOverride = __DIR__ . '/../HTML/Helpers/JGrid.php';
        
        spl_autoload_register(function ($className) use ($jgridOverride) {
            // Intercept Joomla\CMS\HTML\Helpers\JGrid class loading
            if ($className === 'Joomla\\CMS\\HTML\\Helpers\\JGrid' && file_exists($jgridOverride)) {
                require_once $jgridOverride;
                return true;
            }
            
            return false;
        }, true, true); // Prepend to autoloader stack to take precedence
    }

    /**
     * Ensures legacy media adapter aliases are converted to the expected identifiers.
     *
     * @param   CMSApplication  $app  The current application object.
     *
     * @return  void
     */
    private function normaliseMediaPathAlias(CMSApplication $app): void
    {
        if (!$app->isClient('administrator')) {
            return;
        }

        $input  = $app->input;
        $option = $input->getCmd('option');

        if ($option !== 'com_media') {
            return;
        }

        $path = $input->getString('path', '');

        // For standard HTML requests, redirect so the URL reflects the corrected adapter
        $format = $input->getCmd('format', 'html');
        $method = $input->getMethod();

        if ($method !== 'GET' || !in_array($format, ['', 'html'], true)) {
            return;
        }

        // If no path is provided, default to local-images:/
        if ($path === '') {
            $normalisedPath = 'local-images:/';
        } else {
            $normalisedPath = $this->mapMediaPathAlias($path);
        }

        // Only redirect if the path needs to be changed
        if ($normalisedPath === $path) {
            return;
        }

        // Update the request so the rest of the application uses the normalised value
        $input->set('path', $normalisedPath);

        $uri = Uri::getInstance();
        $uri->setVar('path', $normalisedPath);

        $app->redirect($uri->toString());
    }

    /**
     * Maps known adapter aliases to their canonical identifiers.
     *
     * @param   string  $path  The original adapter-prefixed path.
     *
     * @return  string  The normalised adapter-prefixed path.
     */
    private function mapMediaPathAlias(string $path): string
    {
        $aliases = [
            'local-0:' => 'local-images:',
        ];

        foreach ($aliases as $alias => $target) {
            if (strpos($path, $alias) === 0) {
                return $target . substr($path, strlen($alias));
            }
        }

        return $path;
    }

    /**
     * Determines if the active administrator template is Poweradmin.
     *
     * @return bool
     */
    private function isPoweradminTemplate(): bool
    {
        $app = Factory::getApplication();

        if (!$app->isClient('administrator')) {
            return false;
        }

        $template = $app->getTemplate(true);

        return isset($template->template) && $template->template === 'poweradmin';
    }

    /**
     * Handle AJAX request for mod_cache module
     * This intercepts the cache module AJAX request to load the helper from the correct path
     *
     * @param   Event  $event  The event object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function onAjaxCache(Event $event): void
    {
        if (!$this->isTemplateActive) {
            return;
        }

        $app = Factory::getApplication();
        $input = $app->getInput();

        // Only handle clearCache method
        $method = $input->get('method', 'clearCache', 'cmd');
        
        if ($method !== 'clearCache') {
            return;
        }

        // Load the helper from administrator path
        $helperFile = JPATH_ADMINISTRATOR . '/modules/mod_cache/helper.php';

        if (!is_file($helperFile)) {
            return;
        }

        require_once $helperFile;

        // Check if class exists and method exists
        if (!class_exists('ModCacheHelper') || !method_exists('ModCacheHelper', 'clearCacheAjax')) {
            return;
        }

        // Call the method - it will handle the response and close the app
        // Using global namespace prefix since ModCacheHelper is loaded from helper.php
        \ModCacheHelper::clearCacheAjax();
    }
}

