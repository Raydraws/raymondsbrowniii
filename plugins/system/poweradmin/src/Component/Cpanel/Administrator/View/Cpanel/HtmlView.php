<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Cpanel\Administrator\View\Cpanel;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML View class for the Cpanel component
 *
 * @since  1.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Array of cpanel modules
     *
     * @var  array
     */
    protected $modules = null;

    /**
     * Array of cpanel modules
     *
     * @var  array
     */
    protected $quickicons = null;

    /**
     * Moduleposition to load
     *
     * @var  string
     */
    protected $position = null;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $app       = Factory::getApplication();
        $dashboard = $app->getInput()->getCmd('dashboard', '');
        $toolbar   = Toolbar::getInstance();

        $position = OutputFilter::stringURLSafe($dashboard);

        // Generate a title for the view cpanel
        if (!empty($dashboard)) {
            $parts     = explode('.', $dashboard);
            $component = $parts[0];

            if (strpos($component, 'com_') === false) {
                $component = 'com_' . $component;
            }

            // Need to load the language file
            $lang = $this->getLanguage();
            $lang->load($component, JPATH_BASE)
            || $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component);
            $lang->load($component);

            // Lookup dashboard attributes from component manifest file
            $manifestFile = JPATH_ADMINISTRATOR . '/components/' . $component . '/' . str_replace('com_', '', $component) . '.xml';

            if (is_file($manifestFile)) {
                $manifest = simplexml_load_file($manifestFile);

                if ($dashboardManifests = $manifest->dashboards) {
                    foreach ($dashboardManifests->children() as $dashboardManifest) {
                        if ((string) $dashboardManifest === $dashboard) {
                            $title = Text::_((string) $dashboardManifest->attributes()->title);
                            $icon  = (string) $dashboardManifest->attributes()->icon;

                            break;
                        }
                    }
                }
            }

            if (empty($title)) {
                // Try building a title
                $prefix = strtoupper($component) . '_DASHBOARD';

                $sectionkey = !empty($parts[1]) ? '_' . strtoupper($parts[1]) : '';
                $key        = $prefix . $sectionkey . '_TITLE';
                $keyIcon    = $prefix . $sectionkey . '_ICON';

                // Search for a component title
                if ($lang->hasKey($key)) {
                    $title = Text::_($key);
                } else {
                    // Try with a string from CPanel
                    $key = 'COM_CPANEL_DASHBOARD_' . $parts[0] . '_TITLE';

                    if ($lang->hasKey($key)) {
                        $title = Text::_($key);
                    } else {
                        $title = Text::_('COM_CPANEL_DASHBOARD_BASE_TITLE');
                    }
                }

                // Define the icon
                if (empty($parts[1])) {
                    // Default core icons.
                    if ($parts[0] === 'components') {
                        $icon = 'icon-puzzle-piece';
                    } elseif ($parts[0] === 'system') {
                        $icon = 'icon-wrench';
                    } elseif ($parts[0] === 'help') {
                        $icon = 'help-2';
                    } elseif ($lang->hasKey($keyIcon)) {
                        $icon = Text::_($keyIcon);
                    } else {
                        $icon = 'icon-home';
                    }
                } elseif ($lang->hasKey($keyIcon)) {
                    $icon = Text::_($keyIcon);
                }
            }
        } else {
            // Home Dashboard
            $title = Text::_('COM_CPANEL_DASHBOARD_BASE_TITLE');
            $icon  = 'icon-home';
        }

        // Set toolbar items for the page
        ToolbarHelper::title($title, $icon . ' cpanel');
        $toolbar->help('screen.cpanel');

        // Display the cpanel modules
        $this->position = $position ? 'cpanel-' . $position : 'cpanel';
		$this->modules = ModuleHelper::getModules($this->position);

		// Grouping the dashboard modules by quickicons, left column, right columns
		$modules = array(
			'quickicon' => array(),
			'left_column' => array(),
			'right_column' => array()
		);

		foreach ($this->modules as $module)
		{
			if ($module->module === 'mod_quickicon')
			{
				$modules['quickicon'][] = $module;
			}
			else
			{
				$params = json_decode($module->params);

				if (!empty($params->column_position))
				{
					if ($params->column_position === 0)
					{
						$modules['left_column'][] = $module;
					}
					else
					{
						$modules['right_column'][] = $module;
					}
				}
				else
				{
					$modules['left_column'][] = $module;
				}
			}
		}

		// Sort left column by ordering key.
		\usort($modules['left_column'],
			function ($a, $b)
			{
				return $a->ordering <=> $b->ordering;
			}
		);

		// Sort rigth column by ordering key
		\usort($modules['right_column'],
			function ($a, $b)
			{
				return $a->ordering <=> $b->ordering;
			}
		);

		$this->modules = $modules;

        $quickicons       = $position ? 'icon-' . $position : 'icon';
        $this->quickicons = ModuleHelper::getModules($quickicons);

        $this->modules['quickicon'] = $this->quickicons;

        parent::display($tpl);
    }
}
