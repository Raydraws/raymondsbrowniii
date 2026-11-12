<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php


namespace AcyCheckerCmsServices;


class Database
{
    public static function escapeDB($value)
    {
        $acydb = Miscellaneous::getGlobal('db');

        return $acydb->quote($value);
    }

    public static function query($query)
    {
        $acydb = Miscellaneous::getGlobal('db');
        $acydb->setQuery($query);

        $method = ACYC_J40 ? 'execute' : 'query';

        $result = $acydb->$method();
        if (!$result) {
            return false;
        }

        return $acydb->getAffectedRows();
    }

    public static function loadObjectList($query, $key = '', $offset = null, $limit = null)
    {
        $acydb = Miscellaneous::getGlobal('db');
        $acydb->setQuery($query, $offset, $limit);

        return $acydb->loadObjectList($key);
    }

    public static function loadArrayList($query, $key = '', $offset = null, $limit = null)
    {
        $acydb = Miscellaneous::getGlobal('db');
        $acydb->setQuery($query, $offset, $limit);

        return $acydb->loadAssocList($key);
    }

    public static function prepareQuery($query)
    {
        $query = str_replace('#__', Database::getPrefix(), $query);

        return $query;
    }

    public static function addLimit(&$query, $limit = 1, $offset = null)
    {
        if (strpos($query, 'LIMIT ') !== false) return;

        $query .= ' LIMIT ';
        if (!empty($offset)) $query .= intval($offset).',';
        $query .= intval($limit);
    }

    public static function loadObject($query)
    {
        Database::addLimit($query);

        $acydb = Miscellaneous::getGlobal('db');
        $acydb->setQuery($query);

        return $acydb->loadObject();
    }

    public static function loadResult($query)
    {
        $acydb = Miscellaneous::getGlobal('db');

        $acydb->setQuery($query);

        return $acydb->loadResult();
    }

    public static function loadResultArray($query)
    {
        if (is_string($query)) {
            $acydb = Miscellaneous::getGlobal('db');
            $acydb->setQuery($query);
        } else {
            $acydb = $query;
        }

        return $acydb->loadColumn();
    }

    public static function getDBError()
    {
        // Joomla decided to remove the getErrorMsg function in J4 and only use PHP exceptions
        if (ACYC_J40) return '';

        $acydb = Miscellaneous::getGlobal('db');

        return $acydb->getErrorMsg();
    }

    public static function insertObject($table, $element)
    {
        $acydb = Miscellaneous::getGlobal('db');
        $acydb->insertObject($table, $element);

        return $acydb->insertid();
    }

    public static function updateObject($table, $element, $pkey)
    {
        $acydb = Miscellaneous::getGlobal('db');

        return $acydb->updateObject($table, $element, $pkey, true);
    }

    public static function getPrefix()
    {
        $acydb = Miscellaneous::getGlobal('db');

        return $acydb->getPrefix();
    }

    public static function getTableList()
    {
        $acydb = Miscellaneous::getGlobal('db');

        return $acydb->getTableList();
    }

    public static function getCMSConfig($varname, $default = null)
    {
        $acyapp = Miscellaneous::getGlobal('app');

        return $acyapp->getCfg($varname, $default);
    }

    public static function secureDBColumn($fieldName)
    {
        if (!is_string($fieldName) || preg_match('|[^a-z0-9#_.-]|i', $fieldName) !== 0) {
            die('field, table or database "'.Security::escape($fieldName).'" not secured');
        }

        return $fieldName;
    }

    public static function getColumns($table, $acyTable = true, $addPrefix = true)
    {
        if ($addPrefix) {
            $prefix = $acyTable ? '#__acyc_' : '#__';
            $table = $prefix.$table;
        }

        return Database::loadResultArray('SHOW COLUMNS FROM '.Database::secureDBColumn($table));
    }
}
