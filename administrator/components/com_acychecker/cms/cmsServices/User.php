<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;

class User
{
    public static function getCmsUserDbStructure()
    {
        $acymCmsUserVars = new \stdClass();
        $acymCmsUserVars->table = '#__users';
        $acymCmsUserVars->name = 'name';
        $acymCmsUserVars->username = 'username';
        $acymCmsUserVars->id = 'id';
        $acymCmsUserVars->email = 'email';
        $acymCmsUserVars->registered = 'registerDate';
        $acymCmsUserVars->blocked = 'block';

        return $acymCmsUserVars;
    }

    /**
     * @param null $userid
     * @param null $recursive
     * @param bool $names Return an array of ids or names
     *
     * @return array
     */
    public static function getGroupsByUser($userid = null, $recursive = null, $names = false)
    {
        if ($userid === null) {
            $userid = User::currentUserId();
            $recursive = true;
        }

        jimport('joomla.access.access');

        $groups = Access::getGroupsByUser($userid, $recursive);

        if ($names) {
            Security::arrayToInteger($groups);
            $groups = Database::loadResultArray(
                'SELECT ugroup.title 
                FROM #__usergroups AS ugroup 
                JOIN #__user_usergroup_map AS map ON ugroup.id = map.group_id 
                WHERE map.user_id = '.intval($userid).' AND ugroup.id IN ('.implode(',', $groups).')'
            );
        }

        return $groups;
    }

    public static function getGroups()
    {
        return Database::loadObjectList(
            'SELECT `groups`.*, `groups`.title AS text, `groups`.id AS `value`, COUNT(ugm.user_id) AS nbusers 
        FROM #__usergroups AS `groups` 
        LEFT JOIN #__user_usergroup_map ugm ON `groups`.id = ugm.group_id 
        GROUP BY `groups`.id',
            'id'
        );
    }

    public static function currentUserId()
    {
        $acymy = Factory::getUser();

        return $acymy->id;
    }

    public static function currentUserName($userid = null)
    {
        if (!empty($userid)) {
            $special = Factory::getUser($userid);

            return $special->name;
        }

        $acymy = Factory::getUser();

        return $acymy->name;
    }

    public static function currentUserEmail($userid = null)
    {
        if (!empty($userid)) {
            $special = Factory::getUser($userid);

            return $special->email;
        }

        $acymy = Factory::getUser();

        return $acymy->email;
    }

    public static function getCmsUserEdit($userId)
    {
        return 'index.php?option=com_users&task=user.edit&id='.intval($userId);
    }
}
