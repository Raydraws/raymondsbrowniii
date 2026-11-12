<?php

/**
 * @package     Poweradmin
 * @subpackage  mod_extension_updates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Module\ExtensionUpdates\Administrator\Helper;

defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Version;

/**
 * mod_extension_updates_helper helper class for the module
 *
 * @since 4.0.0
 */
abstract class ExtensionUpdatesHelper
{
	/**
	 * Extract all the extensions contents i.e. Total number of extensions,
	 * Total updatable extensions, Joomla! core update status
	 *
	 * @return	array	Extensions contents
	 *
	 * @since 4.0.0
	 */
	public static function extractExtensionsContent() : array
	{
		$totalExtensions = static::getTotalInstalledExtensions();
		$updatableExtensions = static::getUpdatableExtensions();

		$content = array();

		$content['percentage'] = static::calculatePercentage($totalExtensions, $updatableExtensions);
		$content['updatableCount'] = count($updatableExtensions);
		$content['updatableInfo'] = static::groupExtensions($updatableExtensions);
		$content['updateJoomla'] = static::checkJoomlaUpdate();
		$content['isUpdatable'] = static::isUpdatable($content['updateJoomla'] ?? null);
		return $content;
	}

	/**
	 * Group extensions by it's types
	 *
	 * @param	array	$extensions		Updatable extensions
	 *
	 * @return	array	Grouped updatable extensions
	 *
	 * @since 4.0.0
	 */
	private static function groupExtensions($extensions) : array
	{
		$result = array(
			'component' => 0,
			'plugin' => 0,
			'template' => 0,
			'module' => 0,
			'library' => 0,
			'package' => 0
		);

		if (empty($extensions))
		{
			return $result;
		}

		foreach ($extensions as $extension)
		{
			$result[$extension->type]++;
		}

		return $result;
	}

	/**
	 * Calculation of updated extensions percentage.
	 * This is assuming that the Joomla! alone takes 50% of the total extensions,
	 * That means if Joomla! is outdated then the 50% of the system is outdated
	 *
	 * @param	int	$total		Total number of extensions
	 * @param	int	$updatable	Total number of updatable extensions
	 *
	 * @return	float	Percentage of system update status
	 *
	 * @since	4.0.0
	 */
	private static function calculatePercentage($total, $updatable) : float
	{
		$updateJoomla = static::checkJoomlaUpdate();
		$updatedExtensions = $total - count($updatable);
		$updatePercentage = (float) ($updatedExtensions * .5 * 100) / $total;

		if (!$updateJoomla)
		{
			$updatePercentage = $updatePercentage + 50;
		}

		return floor($updatePercentage);
	}

	/**
	 * Check Joomla! core update status
	 *
	 * @return	boolean	Joomla! is updated or not
	 *
	 * @since 4.0.0
	 */
	private static function checkJoomlaUpdate()
	{
		$updateModel = Factory::getApplication()->bootComponent('com_installer')
			->getMVCFactory()->createModel('Update', 'Administrator', ['ignore_request' => true]);

		$extension = ExtensionHelper::getExtensionRecord('joomla', 'file');
		$eid = $extension ? $extension->extension_id : null;
		
		if (!$eid) {
			return false;
		}
		
		$updateModel->setState('filter.extension_id', $eid);
		$extensions = $updateModel->getItems();

		return !empty($extensions) ? $extensions[0]->version : false;
	}


	/**
	 * Get updatable extensions
	 *
	 * @return	array	Updatable extensions
	 *
	 * @since 4.0.0
	 */
	private static function getUpdatableExtensions() : array
	{
		$updateModel = Factory::getApplication()->bootComponent('com_installer')
			->getMVCFactory()->createModel('Update', 'Administrator', ['ignore_request' => true]);

		$extensions = $updateModel->getItems();

		return $extensions;
	}


	/**
	 * Get total installed extensions
	 *
	 * @return	array	Total installed extensions
	 *
	 * @since	4.0.0
	 */
	private static function getTotalInstalledExtensions() : int
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(extension_id)')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('name') . ' <> ' . $db->quote('files_joomla'));
		$db->setQuery($query);

		return $db->loadResult();
	}

	private static function isUpdatable($updateJoomlaVersion)
	{
		$version = new Version();
		$currentVersion = $version->getShortVersion();

		return version_compare($currentVersion, $updateJoomlaVersion, '<');
	}
}
