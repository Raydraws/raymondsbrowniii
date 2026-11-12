<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

extract($displayData);

/**
 * Layout variables
 * ---------------------
 * 	$text         : (string)  The label text
 * 	$for          : (string)  The id of the input this label is for
 * 	$required     : (boolean) True if a required field
 * 	$classes      : (array)   A list of classes
 */

$classes = array_filter((array) $classes);

$sronly = strtolower($displayData['field']->type) === 'editor' ? 'sr-only' : '';

$id    = $for . '-lbl';

if ($required)
{
	$classes[] = 'required';
}

if (!empty($sronly))
{
	$classes[] = $sronly;
}

if(!isset($displayData['field']->skipLabelFor) || $displayData['field']->skipLabelFor !== true)
{
	$forAttr = 'for="' . $for . '"';
}
?>
<label id="<?php echo $id; ?>" for="<?php echo $for; ?>"<?php if (!empty($classes)) { echo ' class="' . implode(' ', $classes) . '"';} ?>>
	<?php echo $text; ?><?php if ($required) : ?><span class="star" aria-hidden="true">&#160;*</span><?php endif; ?>
</label>
