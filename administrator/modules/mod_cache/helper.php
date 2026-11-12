<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_cache
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\Component\Cache\Administrator\Model\CacheModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Response\JsonResponse;

/**
 * mod_cache_helper helper class for the module
 * 
 */
class ModCacheHelper
{
    public static function clearCacheAjax()
	{
		$app = Factory::getApplication();
		// get module params
		$module = ModuleHelper::getModule('mod_cache', 'Cache');
		$params = new Registry($module->params);
		// get Cache unit
		$cacheUnit = $params->get('size_unit', 'MB');

		// declare variables
		$data = array();
		$allCleared = true;
		$data['status'] = false;
		
        // load cache model
        $cache_model = new CacheModel;
        $mCache = $cache_model->getCache();
		foreach ($mCache->getAll() as $cache)
		{
			if ($mCache->clean($cache->group) === false)
			{
                $data['messsage']   = Text::sprintf('COM_CACHE_EXPIRED_ITEMS_DELETE_ERROR', Text::_('JADMINISTRATOR') . ' > ' . $cache->group);
				$allCleared         = false;
			}
		}

		if ($allCleared)
		{
            $data['size']     	= static::getCacheSize($cacheUnit);
            $data['status']     = true;
            $data['messsage']   = Text::_('MOD_CACHE_MSG_ALL_CACHE_GROUPS_CLEARED');
		}
		else
		{
			$data['size'] 		= 0;
            $data['messsage']   = Text::_('MOD_CACHE_MSG_SOME_CACHE_GROUPS_CLEARED');
            $data['status']     = true;
        }
        
        echo new JsonResponse($data);
		$app->close();
    }
    
    public static function getCacheSize($cacheUnit = 'MB') 
    {
        $cache_model = new CacheModel;
		// Set limit to 0 to get all cache data, not just paginated results
		// Must set state BEFORE calling getData() since it caches results
		$cache_model->setState('list.limit', 0);
		$cache_model->setState('list.start', 0);
		
		// Get cache data directly from storage to bypass model caching issues
		try {
			$cache = $cache_model->getCache();
			$data = $cache->getAll();
			
			// getAll() returns false if caching is disabled, or an associative array of CacheStorageHelper objects
			if ($data === false || !is_array($data)) {
				$data = [];
			} else {
				// Convert associative array to numeric array for iteration
				$data = array_values($data);
			}
		} catch (\Exception $e) {
			$data = [];
		}
		
		$size = 0;
		$result = array();
		if (!empty($data))
		{
			foreach ($data as $d)
			{
				$size += $d->size;
			}
		}

		// Number bytes are returned in format xxx.xx MB
		$bytes = HTMLHelper::_('number.bytes', $size, $cacheUnit, 1);
		
		if (!empty($bytes))
		{
			$sizeExplode = explode(' ', $bytes);
			$result['raw'] = $bytes;
			$result['size'] = $sizeExplode[0];
			$result['unit'] = $sizeExplode[1];
		}
		else
		{
			$result['raw'] = 0;
			$result['size'] = 0;
			$result['unit'] = null;
        }
        
        return $result;
    }
}