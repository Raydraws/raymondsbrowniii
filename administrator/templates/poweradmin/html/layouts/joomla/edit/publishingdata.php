<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

$app  = Factory::getApplication();
$form = $displayData->getForm();

$fields = $displayData->get('fields') ?: array(
	'publish_up',
	'publish_down',
	array('created', 'created_time'),
	array('created_by', 'created_user_id'),
	'created_by_alias',
	array('modified', 'modified_time'),
	array('modified_by', 'modified_user_id'),
	'version',
	'hits',
	'id'
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();

foreach ($fields as $field)
{
	foreach ((array) $field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			echo $form->renderField($f);
			break;
		}
	}
}
