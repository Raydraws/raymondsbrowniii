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

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'system/joomla-toolbar-button.min.js', ['version' => 'auto', 'relative' => true]);

/**
 * @var  string  $id
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
 * @var  string  $message          Confirmation message before run the task
 *
 */
extract($displayData, EXTR_OVERWRITE);

$tagName  = $tagName ?? 'button';

$taskAttr = '';
$idAttr   = !empty($id)             ? ' id="' . $id . '"' : '';
$listAttr = !empty($listCheck)      ? ' list-selection' : '';
$formAttr = !empty($form)           ? ' form="' . $this->escape($form) . '"' : '';
$validate = !empty($formValidation) ? ' form-validation' : '';
$msgAttr  = !empty($message)        ? ' confirm-message="' . $this->escape($message) . '"' : '';

if (!empty($task))
{
	$taskAttr = ' task="' . $task . '"';
}
elseif (!empty($onclick))
{
	$htmlAttributes .= ' onclick="' . $onclick . '"';
}

$iconClass = '';
$classKey  = preg_replace('/^icon-/', '', (string) $class);

switch($classKey)
{
	case 'apply':
	case 'save':
	case 'save-new':
	case 'save-copy':
	case 'cancel':
		$iconClass = false;
	break;
	case 'featured':
		$iconClass = 'featured';
	break;
	case 'checkin':
		$iconClass = 'checkin';
	break;
	default:
		$iconClass = $classKey;
}

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
	'checkin'     => 'checkin',
	'archive'     => 'archive',
	'trash'       => 'trash',
	'delete'      => 'trash',

];
$svgIconKey = $svgIconMap[$iconClass] ?? null;
?>

<joomla-toolbar-button <?php echo $idAttr.$taskAttr.$listAttr.$formAttr.$validate.$msgAttr; ?>>
<?php if (!empty($group)) : ?>
<a href="#" class="dropdown-item">
	<?php if ($svgIconKey) : ?>
		<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
	<?php elseif (!empty($iconClass)) : ?>
		<span class="<?php echo trim($iconClass ?? ''); ?> icon-md"></span>
	<?php endif; ?>
	<?php echo $text ?? ''; ?>
</a>
<?php else : ?>
<<?php echo $tagName; ?>
	class="<?php echo $btnClass ?? ''; ?>"
	<?php echo $htmlAttributes ?? ''; ?>
	>
	<?php if ($svgIconKey) : ?>
		<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
	<?php elseif (!empty($iconClass)) : ?>
		<span class="<?php echo trim($iconClass ?? ''); ?> icon-md" aria-hidden="true"></span>
	<?php endif; ?>
	<?php echo $text ?? ''; ?>
</<?php echo $tagName; ?>>
<?php endif; ?>
</joomla-toolbar-button>