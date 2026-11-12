<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'system/joomla-toolbar-button.min.js', ['relative' => true, 'version' => 'auto', 'detectDebug' => true]);

/**
 * @var  int     $id
 * @var  string  $onclick
 * @var  string  $class
 * @var  string  $text
 * @var  string  $btnClass
 * @var  string  $tagName
 * @var  string  $htmlAttributes
 * @var  string  $task             The task which should be executed
 * @var  bool    $listCheck        Boolean, whether selection from a list is needed
 * @var  string  $form             CSS selector for a target form
 * @var  bool    $formValidation   Whether the form need to be validated before run the task
 */
extract((array) $displayData, EXTR_OVERWRITE);

$tagName = $tagName ?? 'button';
$svgIconMap = [
	'select'      => 'hand',
	'info-circle' => 'help-2',
	'options'     => 'settings',
	'publish'     => 'publish',
	'unpublish'   => 'unpublish',
	'featured'    => 'featured',
	'unfeatured'  => 'unfeatured',
	'batch'       => 'batch',
	'square'      => 'batch',
	'feature'     => 'featured',
	'unfeature'   => 'unfeatured',
];
$iconLookup = preg_replace('/^icon-/', '', (string) ($class ?? ''));
$svgIconKey = $svgIconMap[$iconLookup] ?? null;

$taskAttr = '';
$title    = '';
$idAttr   = !empty($id)             ? ' id="' . $id . '"' : '';
$listAttr = !empty($listCheck)      ? ' list-selection' : '';
$formAttr = !empty($form)           ? ' form="' . $this->escape($form) . '"' : '';
$validate = !empty($formValidation) ? ' form-validation' : '';
$msgAttr  = !empty($message)        ? ' confirm-message="' . $this->escape($message) . '"' : '';

if ($id === 'toolbar-help')
{
	$title = ' title="' . Text::_('JGLOBAL_OPENS_IN_A_NEW_WINDOW') . '"';
}

if (!empty($task))
{
	$taskAttr = ' task="' . $task . '"';
}
elseif (!empty($onclick))
{
	$htmlAttributes .= ' onclick="' . $onclick . '"';
}

?>
<joomla-toolbar-button <?php echo $idAttr.$taskAttr.$listAttr.$formAttr.$validate.$msgAttr; ?>>
<<?php echo $tagName; ?>
	class="<?php echo $btnClass ?? ''; ?>"
	<?php echo $htmlAttributes ?? ''; ?>
	<?php echo $title; ?>
	>
	<?php if ($svgIconKey) : ?>
		<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
	<?php else : ?>
		<span class="<?php echo trim($class ?? ''); ?> icon-md" aria-hidden="true"></span>
	<?php endif; ?>
	<?php echo $text ?? ''; ?>
	<?php if(isset($hasButtons) && !empty($dropdownItems)) : ?>
		<span class="down-arrow" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('down-arrow', 14, 'currentColor'); ?></span>
	<?php endif; ?>
</<?php echo $tagName; ?>>
</joomla-toolbar-button>
