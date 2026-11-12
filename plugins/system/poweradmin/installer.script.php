<?php

/**
 * @package     Poweradmin
 * @subpackage  System.Poweradmin
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Installer script to ensure the Poweradmin system plugin is always enabled after installation.
 *
 * @since  1.0.0
 */
final class PlgSystemPoweradminInstallerScript
{
    /**
     * Ensure the plugin is published after every install/update/discover install.
     *
     * @param   string  $type    The type of change (install, update, discover_install).
     * @param   mixed   $parent  The adapter instance calling this method.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function postflight($type, $parent): void
    {
        $this->enablePlugin();
        
        // These methods should run for both install and update
        // Explicitly ensure they run during updates
        if ($type === 'install' || $type === 'discover_install') {
            $this->updateQuickIconModules();
            $this->updateStatusPositionModules();
            $this->handleAccessibilityModule();
            $this->handleCpanelModules();
        }
    }

    /**
     * Enable the system plugin by updating the extensions table directly.
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
        $element = 'poweradmin';
        $folder  = 'system';

        $query
            ->bind(':type', $type)
            ->bind(':element', $element)
            ->bind(':folder', $folder);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\Throwable $exception) {
            // Ignore failures to avoid blocking the installation/update process.
        }
    }

    /**
     * Update mod_quickicon modules based on context.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function updateQuickIconModules(): void
    {
        $container = Factory::getContainer();

        if (!$container->has(DatabaseInterface::class)) {
            return;
        }

        /** @var DatabaseInterface $db */
        $db = $container->get(DatabaseInterface::class);

        // Get all mod_quickicon modules
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'params', 'published']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = :module')
            ->where($db->quoteName('client_id') . ' = 1'); // Administrator modules only

        $module = 'mod_quickicon';
        $query->bind(':module', $module);

        $db->setQuery($query);

        try {
            $modules = $db->loadObjectList();
        } catch (\Throwable $exception) {
            // Ignore failures to avoid blocking the installation/update process.
            return;
        }

        foreach ($modules as $module) {
            $params = json_decode($module->params, true);

            if (!is_array($params)) {
                $params = [];
            }

            $context = $params['context'] ?? '';

            // Unpublish modules with context "update_quickicon"
            if ($context === 'update_quickicon') {
                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('published') . ' = 0')
                    ->where($db->quoteName('id') . ' = :id');

                $updateQuery->bind(':id', $module->id, ParameterType::INTEGER);
                $db->setQuery($updateQuery);

                try {
                    $db->execute();
                } catch (\Throwable $exception) {
                    // Ignore failures to avoid blocking the installation/update process.
                }
            }

            // Update modules with context "site_quickicon"
            if ($context === 'site_quickicon') {
                // Set the params as specified
                $newParams = [
                    'context' => 'site_quickicon',
                    'header_icon' => 'icon-desktop',
                    'show_global' => '0',
                    'show_checkin' => '0',
                    'show_cache' => '0',
                    'show_users' => '2',
                    'show_articles' => '2',
                    'show_featured' => '0',
                    'show_menuitems' => '2',
                    'show_workflow' => '0',
                    'show_banners' => '0',
                    'show_finder' => '0',
                    'show_newsfeeds' => '0',
                    'show_tags' => '0',
                    'show_redirect' => '0',
                    'show_associations' => '0',
                    'show_languages' => '0',
                    'show_modules' => '2',
                    'show_contact' => '0',
                    'show_categories' => '0',
                    'show_media' => '1',
                    'show_plugins' => '2',
                    'show_template_styles' => '1',
                    'show_template_code' => '0',
                    'layout' => '_:default',
                    'moduleclass_sfx' => '',
                    'cache' => 1,
                    'cache_time' => 900,
                    'module_tag' => 'div',
                    'bootstrap_size' => '12',
                    'header_tag' => 'h2',
                    'header_class' => '',
                    'style' => '0',
                ];

                // Merge with existing params to preserve any other settings
                $mergedParams = array_merge($params, $newParams);
                $paramsJson = json_encode($mergedParams);

                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('params') . ' = :params')
                    ->set($db->quoteName('title') . ' = :title')
                    ->where($db->quoteName('id') . ' = :id');

                $title = 'Quick Overview';
                $updateQuery->bind(':params', $paramsJson);
                $updateQuery->bind(':title', $title);
                $updateQuery->bind(':id', $module->id, ParameterType::INTEGER);

                $db->setQuery($updateQuery);

                try {
                    $db->execute();
                } catch (\Throwable $exception) {
                    // Ignore failures to avoid blocking the installation/update process.
                }
            }
        }
    }

    /**
     * Unpublish mod_multilangstatus and mod_post_installation_messages in status position.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function updateStatusPositionModules(): void
    {
        $container = Factory::getContainer();

        if (!$container->has(DatabaseInterface::class)) {
            return;
        }

        /** @var DatabaseInterface $db */
        $db = $container->get(DatabaseInterface::class);

        // Modules to unpublish in status position
        $modulesToUnpublish = ['mod_multilangstatus', 'mod_post_installation_messages'];
        $position = 'status';

        foreach ($modulesToUnpublish as $moduleType) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__modules'))
                ->set($db->quoteName('published') . ' = 0')
                ->where($db->quoteName('module') . ' = :module')
                ->where($db->quoteName('position') . ' = :position')
                ->where($db->quoteName('client_id') . ' = 1'); // Administrator modules only

            $query->bind(':module', $moduleType);
            $query->bind(':position', $position);

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (\Throwable $exception) {
                // Ignore failures to avoid blocking the installation/update process.
            }
        }
    }

    /**
     * Handle mod_accessibility module in status position.
     * Create it if it doesn't exist, or publish it if it exists but is unpublished.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function handleAccessibilityModule(): void
    {
        $container = Factory::getContainer();

        if (!$container->has(DatabaseInterface::class)) {
            return;
        }

        /** @var DatabaseInterface $db */
        $db = $container->get(DatabaseInterface::class);
        $moduleType = 'mod_accessibility';
        $position = 'status';

        // Check if mod_accessibility exists in status position
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'published']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = :module')
            ->where($db->quoteName('position') . ' = :position')
            ->where($db->quoteName('client_id') . ' = 1'); // Administrator modules only

        $query->bind(':module', $moduleType);
        $query->bind(':position', $position);

        $db->setQuery($query);

        try {
            $module = $db->loadObject();
        } catch (\Throwable $exception) {
            // Ignore failures to avoid blocking the installation/update process.
            return;
        }

        if ($module) {
            // Module exists - publish it if it's unpublished
            if ($module->published == 0) {
                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('published') . ' = 1')
                    ->where($db->quoteName('id') . ' = :id');

                $updateQuery->bind(':id', $module->id, ParameterType::INTEGER);
                $db->setQuery($updateQuery);

                try {
                    $db->execute();
                } catch (\Throwable $exception) {
                    // Ignore failures to avoid blocking the installation/update process.
                }
            }

            // Swap ordering with mod_user if it exists
            $this->swapOrderingWithUser($db, $module->id, $position);
        } else {
            // Module doesn't exist - create it using Joomla's Table class
            // First, verify the extension exists
            $extQuery = $db->getQuery(true)
                ->select($db->quoteName('extension_id'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = :type')
                ->where($db->quoteName('element') . ' = :element')
                ->where($db->quoteName('client_id') . ' = 1');

            $extType = 'module';
            $extElement = 'mod_accessibility';
            $extQuery->bind(':type', $extType);
            $extQuery->bind(':element', $extElement);

            $db->setQuery($extQuery);

            try {
                $extensionId = $db->loadResult();
            } catch (\Throwable $exception) {
                // Module extension doesn't exist, can't create module
                return;
            }

            if (!$extensionId) {
                // Extension not found, can't create module
                return;
            }

            // Get the highest ordering in status position
            $orderQuery = $db->getQuery(true)
                ->select('MAX(' . $db->quoteName('ordering') . ')')
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('position') . ' = :position')
                ->where($db->quoteName('client_id') . ' = 1');

            $orderQuery->bind(':position', $position);
            $db->setQuery($orderQuery);

            try {
                $maxOrdering = $db->loadResult() ?? 0;
                $newOrdering = (int) $maxOrdering + 1;
            } catch (\Throwable $exception) {
                $newOrdering = 1;
            }

            // Prepare module parameters
            $params = [
                'layout' => '_:default',
                'moduleclass_sfx' => '',
                'module_tag' => 'div',
                'bootstrap_size' => '0',
                'header_tag' => 'h3',
                'header_class' => '',
                'style' => '0',
            ];
            $paramsJson = json_encode($params);

            // Use Joomla's Table class to create the module (handles all columns and assets properly)
            try {
                /** @var \Joomla\CMS\Table\Module $moduleTable */
                $moduleTable = Table::getInstance('Module');
                
                // Set module properties
                $moduleTable->title      = 'Accessibility';
                $moduleTable->note       = '';
                $moduleTable->content   = '';
                $moduleTable->module    = $moduleType;
                $moduleTable->position  = $position;
                $moduleTable->ordering  = $newOrdering;
                $moduleTable->published = 1;
                $moduleTable->access    = 1;
                $moduleTable->showtitle = 0;
                $moduleTable->params    = $paramsJson;
                $moduleTable->client_id = 1;
                $moduleTable->language  = '*';

                // Store the module (this handles asset creation and all required fields)
                if (!$moduleTable->store()) {
                    // Failed to create module
                    return;
                }

                $newModuleId = $moduleTable->id;

                // Assign module to all menu items (menuid = 0 means all)
                if ($newModuleId) {
                    $menuQuery = $db->getQuery(true)
                        ->insert($db->quoteName('#__modules_menu'))
                        ->columns([$db->quoteName('moduleid'), $db->quoteName('menuid')])
                        ->values(':moduleid, :menuid');

                    $menuId = 0; // 0 means all menu items
                    $menuQuery->bind(':moduleid', $newModuleId, ParameterType::INTEGER);
                    $menuQuery->bind(':menuid', $menuId, ParameterType::INTEGER);

                    $db->setQuery($menuQuery);

                    try {
                        $db->execute();
                    } catch (\Throwable $exception) {
                        // Ignore failures to avoid blocking the installation/update process.
                    }

                    // Swap ordering with mod_user if it exists
                    $this->swapOrderingWithUser($db, $newModuleId, $position);
                }
            } catch (\Throwable $exception) {
                // Ignore failures to avoid blocking the installation/update process.
            }
        }
    }

    /**
     * Swap ordering between mod_accessibility and mod_user in status position.
     *
     * @param   DatabaseInterface  $db              Database instance.
     * @param   integer            $accessibilityId  The mod_accessibility module ID.
     * @param   string             $position         The position name.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function swapOrderingWithUser(DatabaseInterface $db, int $accessibilityId, string $position): void
    {
        try {
            // Get mod_accessibility ordering
            $accessibilityQuery = $db->getQuery(true)
                ->select($db->quoteName('ordering'))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('id') . ' = :id');

            $accessibilityQuery->bind(':id', $accessibilityId, ParameterType::INTEGER);
            $db->setQuery($accessibilityQuery);
            $accessibilityOrdering = $db->loadResult();

            if ($accessibilityOrdering === null) {
                return;
            }

            // Check if mod_user exists in status position
            $userQuery = $db->getQuery(true)
                ->select($db->quoteName(['id', 'ordering']))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' = :module')
                ->where($db->quoteName('position') . ' = :position')
                ->where($db->quoteName('client_id') . ' = 1');

            $userModule = 'mod_user';
            $userQuery->bind(':module', $userModule);
            $userQuery->bind(':position', $position);

            $db->setQuery($userQuery);
            $userModuleData = $db->loadObject();

            if ($userModuleData) {
                // Swap the ordering values
                // mod_accessibility gets mod_user's ordering
                // mod_user gets mod_accessibility's ordering
                $userOrdering = $userModuleData->ordering;

                if ($userOrdering >= $accessibilityOrdering) {
                    return;
                }

                // Update mod_accessibility ordering
                $updateAccessibilityQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('ordering') . ' = :ordering')
                    ->where($db->quoteName('id') . ' = :id');

                $updateAccessibilityQuery->bind(':ordering', $userOrdering, ParameterType::INTEGER);
                $updateAccessibilityQuery->bind(':id', $accessibilityId, ParameterType::INTEGER);
                $db->setQuery($updateAccessibilityQuery);
                $db->execute();

                // Update mod_user ordering
                $updateUserQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('ordering') . ' = :ordering')
                    ->where($db->quoteName('id') . ' = :id');

                $updateUserQuery->bind(':ordering', $accessibilityOrdering, ParameterType::INTEGER);
                $updateUserQuery->bind(':id', $userModuleData->id, ParameterType::INTEGER);
                $db->setQuery($updateUserQuery);
                $db->execute();
            }
        } catch (\Throwable $exception) {
            // Ignore failures to avoid blocking the installation/update process.
        }
    }

    /**
     * Handle cpanel modules - publish them if not already published in cpanel position.
     * These modules will be placed on the right side of the cpanel.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function handleCpanelModules(): void
    {
        $container = Factory::getContainer();

        if (!$container->has(DatabaseInterface::class)) {
            return;
        }

        /** @var DatabaseInterface $db */
        $db = $container->get(DatabaseInterface::class);

        // Modules to ensure are published in cpanel position
        // column_position: 0 = left column, 1 = right column
        $cpanelModules = [
            'mod_extension_updates' => [
                'title'  => 'Available Updates',
                'params' => [
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'automatic_title' => 0,
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 1,
                ],
            ],
            'mod_checkins' => [
                'title'  => 'Checkins',
                'params' => [
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'automatic_title' => 0,
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 1,
                ],
            ],
            'mod_quick_article' => [
                'title'  => 'Quick Article',
                'params' => [
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 1,
                ],
            ],
            'mod_cache' => [
                'title'  => 'Cache',
                'params' => [
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'automatic_title' => 0,
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 1,
                ],
            ],
            'mod_content' => [
                'title'  => 'Recent Articles',
                'params' => [
                    'count'           => 5,
                    'ordering'        => 'c_dsc',
                    'catid'           => '',
                    'user_id'         => '0',
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 0,
                ],
            ],
            'mod_resources' => [
                'title'  => 'Resources',
                'params' => [
                    'show_resources'  => 'jrd',
                    'layout'          => '_:default',
                    'moduleclass_sfx' => '',
                    'automatic_title' => 0,
                    'module_tag'      => 'div',
                    'bootstrap_size'  => '0',
                    'header_tag'      => 'h3',
                    'header_class'    => '',
                    'style'           => '0',
                    'column_position' => 0,
                ],
            ],
        ];

        $position = 'cpanel';

        // Get the current maximum ordering in cpanel position
        $orderQuery = $db->getQuery(true)
            ->select('MAX(' . $db->quoteName('ordering') . ')')
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('position') . ' = :position')
            ->where($db->quoteName('client_id') . ' = 1');

        $orderQuery->bind(':position', $position);
        $db->setQuery($orderQuery);

        try {
            $maxOrdering = (int) ($db->loadResult() ?? 0);
        } catch (\Throwable $exception) {
            $maxOrdering = 0;
        }

        // Calculate next even ordering (for right side)
        // Even orderings (2, 4, 6...) go to right column, odd (1, 3, 5...) go to left
        $nextOrdering = $maxOrdering + 1;
        if ($nextOrdering % 2 !== 0) {
            $nextOrdering++; // Make it even for right side
        }

        foreach ($cpanelModules as $moduleType => $moduleConfig) {
            $this->ensureCpanelModulePublished($db, $moduleType, $moduleConfig, $position, $nextOrdering);
            $nextOrdering += 2; // Keep ordering even for right side
        }
    }

    /**
     * Ensure a module is published in cpanel position.
     * If already published there, skip. Otherwise, create or update it.
     *
     * @param   DatabaseInterface  $db            Database instance.
     * @param   string             $moduleType    The module type (e.g., 'mod_checkins').
     * @param   array              $moduleConfig  Module configuration (title, params).
     * @param   string             $position      The position name.
     * @param   int                $ordering      The ordering value.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function ensureCpanelModulePublished(
        DatabaseInterface $db,
        string $moduleType,
        array $moduleConfig,
        string $position,
        int $ordering
    ): void {
        try {
            // Check if module is already published in cpanel position
            $query = $db->getQuery(true)
                ->select($db->quoteName(['id', 'published']))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' = :module')
                ->where($db->quoteName('position') . ' = :position')
                ->where($db->quoteName('published') . ' = 1')
                ->where($db->quoteName('client_id') . ' = 1');

            $query->bind(':module', $moduleType);
            $query->bind(':position', $position);

            $db->setQuery($query);
            $existingModule = $db->loadObject();

            // If already published in cpanel, ensure menu assignment and update params for column_position
            if ($existingModule) {
                $this->ensureModuleMenuAssignment($db, (int) $existingModule->id);
                $this->updateModuleColumnPosition($db, (int) $existingModule->id, $moduleConfig['params']);
                return;
            }

            // Check if module exists but is unpublished or in different position
            $query2 = $db->getQuery(true)
                ->select($db->quoteName(['id', 'published', 'position']))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' = :module')
                ->where($db->quoteName('client_id') . ' = 1');

            $query2->bind(':module', $moduleType);

            $db->setQuery($query2);
            $modules = $db->loadObjectList();

            if (!empty($modules)) {
                // Find a module to update - prefer unpublished ones, then any that's not in cpanel
                $moduleToUpdate = null;
                
                // First pass: look for unpublished modules
                foreach ($modules as $mod) {
                    if ((int) $mod->published === 0) {
                        $moduleToUpdate = $mod;
                        break;
                    }
                }

                // Second pass: if no unpublished, take the first one available
                // (it's published but in a different position than cpanel)
                if (!$moduleToUpdate && !empty($modules)) {
                    $moduleToUpdate = $modules[0];
                }

                if ($moduleToUpdate) {
                    // Update existing module to be published in cpanel position
                    $updateQuery = $db->getQuery(true)
                        ->update($db->quoteName('#__modules'))
                        ->set($db->quoteName('published') . ' = 1')
                        ->set($db->quoteName('position') . ' = :position')
                        ->set($db->quoteName('ordering') . ' = :ordering')
                        ->where($db->quoteName('id') . ' = :id');

                    $updateQuery->bind(':position', $position);
                    $updateQuery->bind(':ordering', $ordering, ParameterType::INTEGER);
                    $updateQuery->bind(':id', $moduleToUpdate->id, ParameterType::INTEGER);

                    $db->setQuery($updateQuery);
                    $db->execute();

                    // Ensure module has menu assignment (required for modules to show)
                    $this->ensureModuleMenuAssignment($db, (int) $moduleToUpdate->id);
                    
                    // Update params to include column_position
                    $this->updateModuleColumnPosition($db, (int) $moduleToUpdate->id, $moduleConfig['params']);
                    return;
                }
            }

            // Module doesn't exist - create it
            // First, verify the extension exists
            $extQuery = $db->getQuery(true)
                ->select($db->quoteName('extension_id'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = :type')
                ->where($db->quoteName('element') . ' = :element')
                ->where($db->quoteName('client_id') . ' = 1');

            $extType = 'module';
            $extQuery->bind(':type', $extType);
            $extQuery->bind(':element', $moduleType);

            $db->setQuery($extQuery);
            $extensionId = $db->loadResult();

            if (!$extensionId) {
                // Extension not found, can't create module
                return;
            }

            // Create the module using Joomla's Table class
            /** @var \Joomla\CMS\Table\Module $moduleTable */
            $moduleTable = Table::getInstance('Module');

            // Set module properties
            $moduleTable->title      = $moduleConfig['title'];
            $moduleTable->note       = '';
            $moduleTable->content    = '';
            $moduleTable->module     = $moduleType;
            $moduleTable->position   = $position;
            $moduleTable->ordering   = $ordering;
            $moduleTable->published  = 1;
            $moduleTable->access     = 1;
            $moduleTable->showtitle  = 1;
            $moduleTable->params     = json_encode($moduleConfig['params']);
            $moduleTable->client_id  = 1;
            $moduleTable->language   = '*';

            // Store the module
            if (!$moduleTable->store()) {
                return;
            }

            $newModuleId = $moduleTable->id;

            // Assign module to all menu items (menuid = 0 means all)
            if ($newModuleId) {
                $menuQuery = $db->getQuery(true)
                    ->insert($db->quoteName('#__modules_menu'))
                    ->columns([$db->quoteName('moduleid'), $db->quoteName('menuid')])
                    ->values(':moduleid, :menuid');

                $menuId = 0;
                $menuQuery->bind(':moduleid', $newModuleId, ParameterType::INTEGER);
                $menuQuery->bind(':menuid', $menuId, ParameterType::INTEGER);

                $db->setQuery($menuQuery);
                $db->execute();
            }
        } catch (\Throwable $exception) {
            // Ignore failures to avoid blocking the installation/update process.
        }
    }

    /**
     * Ensure a module has a menu assignment entry (required for modules to show).
     *
     * @param   DatabaseInterface  $db        Database instance.
     * @param   int                $moduleId  The module ID.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function ensureModuleMenuAssignment(DatabaseInterface $db, int $moduleId): void
    {
        try {
            // Check if module already has a menu assignment
            $checkQuery = $db->getQuery(true)
                ->select($db->quoteName('moduleid'))
                ->from($db->quoteName('#__modules_menu'))
                ->where($db->quoteName('moduleid') . ' = :moduleid');

            $checkQuery->bind(':moduleid', $moduleId, ParameterType::INTEGER);
            $db->setQuery($checkQuery);
            $exists = $db->loadResult();

            if (!$exists) {
                // Add menu assignment for all pages (menuid = 0)
                $insertQuery = $db->getQuery(true)
                    ->insert($db->quoteName('#__modules_menu'))
                    ->columns([$db->quoteName('moduleid'), $db->quoteName('menuid')])
                    ->values(':moduleid, :menuid');

                $menuId = 0;
                $insertQuery->bind(':moduleid', $moduleId, ParameterType::INTEGER);
                $insertQuery->bind(':menuid', $menuId, ParameterType::INTEGER);

                $db->setQuery($insertQuery);
                $db->execute();
            }
        } catch (\Throwable $exception) {
            // Ignore failures
        }
    }

    /**
     * Update module params to set column_position for cpanel display.
     *
     * @param   DatabaseInterface  $db        Database instance.
     * @param   int                $moduleId  The module ID.
     * @param   array              $newParams The params to merge (specifically column_position).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function updateModuleColumnPosition(DatabaseInterface $db, int $moduleId, array $newParams): void
    {
        try {
            // Get current params
            $query = $db->getQuery(true)
                ->select($db->quoteName('params'))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('id') . ' = :id');

            $query->bind(':id', $moduleId, ParameterType::INTEGER);
            $db->setQuery($query);
            $currentParamsJson = $db->loadResult();

            $currentParams = json_decode($currentParamsJson, true) ?: [];
            
            // Only update column_position if it's set in newParams
            if (isset($newParams['column_position'])) {
                $currentParams['column_position'] = $newParams['column_position'];
                
                $updatedParamsJson = json_encode($currentParams);

                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__modules'))
                    ->set($db->quoteName('params') . ' = :params')
                    ->where($db->quoteName('id') . ' = :id');

                $updateQuery->bind(':params', $updatedParamsJson);
                $updateQuery->bind(':id', $moduleId, ParameterType::INTEGER);

                $db->setQuery($updateQuery);
                $db->execute();
            }
        } catch (\Throwable $exception) {
            // Ignore failures
        }
    }
}

