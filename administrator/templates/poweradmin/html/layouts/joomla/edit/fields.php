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

$form			= $displayData['data']->getForm();
$form_style		= isset($displayData['form_style']) && $displayData['form_style'] ? $displayData['form_style'] : 'form-no-margin';
$group			= isset($displayData['group']) && $displayData['group'] ? $displayData['group'] : '';

$fields 		= ( isset($displayData['fields']) && $displayData['fields'] ) ? $displayData['fields'] : array();

$html   		= array();
$html[] 		= '<fieldset class="'. $form_style .'">';

foreach ($fields as $field)
{
	foreach ((array) $field as $f)
	{
		if ($form->getField($f, $group))
		{
			$html[] = $form->renderField($f);
			break;
		}
	}
}

$html[] 		= '</fieldset>';

echo implode('', $html);
