<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_logged
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Joomla\Module\Logged\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

/**
 * Helper for mod_logged
 *
 * @since  1.5
 */
class LoggedHelper
{
	/**
	 * Joomla 5 dispatcher expects getUsers(); proxy to legacy getList().
	 *
	 * @param   Registry           $params  Module params.
	 * @param   CMSApplication     $app     Application instance.
	 * @param   DatabaseInterface  $db      Database connector.
	 *
	 * @return  array
	 *
	 * @since   5.0.0
	 */
	public static function getUsers(Registry $params, CMSApplication $app, DatabaseInterface $db)
	{
		return static::getList($params, $app, $db);
	}

	/**
	 * Get a list of logged users.
	 *
	 * @param   Registry           $params  The module parameters
	 * @param   CMSApplication     $app     The application
	 * @param   DatabaseInterface  $db      The database
	 *
	 * @return  mixed  An array of users, or false on error.
	 *
	 * @throws  \RuntimeException
	 */
	public static function getList(Registry $params, CMSApplication $app, DatabaseInterface $db)
	{
		$user  = $app->getIdentity();
		$query = $db->getQuery(true)
			->select('s.time, s.client_id, u.id, u.name, u.username, u.email')
			->from('#__session AS s')
			->join('LEFT', '#__users AS u ON s.userid = u.id')
			->where('s.guest = 0')
			->setLimit($params->get('count', 5), 0);

		$db->setQuery($query);

		try
		{
			$results = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			throw $e;
		}

		foreach ($results as $k => $result)
		{
			$results[$k]->logoutLink = '';

			if ($user->authorise('core.manage', 'com_users'))
			{
				$results[$k]->editLink   = Route::_('index.php?option=com_users&task=user.edit&id=' . $result->id);
				$results[$k]->logoutLink = Route::_(
					'index.php?option=com_login&task=logout&uid=' . $result->id . '&' . Session::getFormToken() . '=1'
				);
			}

			if ($params->get('name', 1) == 0)
			{
				$results[$k]->name = $results[$k]->username;
			}
		}

		return $results;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param   \Joomla\Registry\Registry  $params  The module parameters.
	 *
	 * @return  string    The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		return Text::plural('MOD_LOGGED_TITLE', $params->get('count', 5));
	}
}
