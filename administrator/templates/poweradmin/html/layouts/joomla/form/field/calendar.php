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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

extract($displayData);

// Get some system objects.
$document = Factory::getDocument();
$language = Factory::getLanguage();

// Ensure locale and helper paths are defined to avoid notices when they are missing in display data.
if (empty($localesPath)) {
	$tag         = $language->getTag();
	$tagLower    = strtolower($tag);
	$tagSimple   = strtolower(str_replace('-', '_', $tag));
	$candidates  = array_filter([
		'system/fields/calendar-locales/' . $tagLower . '.js',
		'system/fields/calendar-locales/' . $tagSimple . '.js',
		(strlen($tagLower) >= 2) ? 'system/fields/calendar-locales/' . substr($tagLower, 0, 2) . '.js' : null,
		'system/fields/calendar-locales/en.js',
	]);

	foreach ($candidates as $candidate) {
		if (is_file(JPATH_ROOT . '/media/' . $candidate)) {
			$localesPath = $candidate;
			break;
		}
	}

	// Fallback if nothing matched (e.g. when media files are missing).
	$localesPath = $localesPath ?? 'system/fields/calendar-locales/en.js';
}

if (empty($helperPath)) {
	$helperPath   = 'system/fields/calendar-locales/date/gregorian/date-helper.min.js';
	$calendarType = $language->getCalendar();

	if ($calendarType && is_dir(JPATH_ROOT . '/media/system/js/fields/calendar-locales/date/' . strtolower($calendarType))) {
		$helperPath = 'system/fields/calendar-locales/date/' . strtolower($calendarType) . '/date-helper.min.js';
	}
}

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * Calendar Specific
 * @var   string   $localesPath     The relative path for the locale file
 * @var   string   $helperPath      The relative path for the helper file
 * @var   string   $minYear         The minimum year, that will be subtracted/added to current year
 * @var   string   $maxYear         The maximum year, that will be subtracted/added to current year
 * @var   integer  $todaybutton     The today button
 * @var   integer  $weeknumbers     The week numbers display
 * @var   integer  $showtime        The time selector display
 * @var   integer  $filltable       The previous/next month filling
 * @var   integer  $timeformat      The time format
 * @var   integer  $singleheader    Display different header row for month/year
 * @var   integer  $direction       The document direction
 */

$inputvalue = '';

// Build the attributes array.
$attributes = array();

empty($size)      ? null : $attributes['size'] = $size;
empty($maxlength) ? null : $attributes['maxlength'] = $maxLength;
empty($class)     ? $attributes['class'] = 'form-control' : $attributes['class'] = 'form-control ' . $class;
!$readonly        ? null : $attributes['readonly'] = 'readonly';
!$disabled        ? null : $attributes['disabled'] = 'disabled';
empty($onchange)  ? null : $attributes['onchange'] = $onchange;

if ($required)
{
	$attributes['required'] = '';
}

// Handle the special case for "now".
if (strtoupper($value) === 'NOW')
{
	$value = Factory::getDate()->format('Y-m-d H:i:s');
}

$readonly = isset($attributes['readonly']) && $attributes['readonly'] === 'readonly';
$disabled = isset($attributes['disabled']) && $attributes['disabled'] === 'disabled';

if (is_array($attributes))
{
	$attributes = ArrayHelper::toString($attributes);
}

$cssFileExt = ($direction === 'rtl') ? '-rtl.css' : '.css';

// The static assets for the calendar
HTMLHelper::_('script', $localesPath, ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', $helperPath, ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'system/fields/calendar.min.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'system/fields/calendar' . $cssFileExt, ['version' => 'auto', 'relative' => true]);
?>
<div class="field-calendar">
	<?php if (!$readonly && !$disabled) : ?>
	<div class="input-group">
		<?php endif; ?>
		<input
			type="text"
			id="<?php echo $id; ?>"
			name="<?php echo $name; ?>"
			value="<?php echo htmlspecialchars(($value !== '0000-00-00 00:00:00') ? $value : '', ENT_COMPAT, 'UTF-8'); ?>"
			<?php echo !empty($description) ? ' aria-describedby="' . $name . '-desc"' : ''; ?>
			<?php echo $attributes; ?>
			<?php echo !empty($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : ''; ?>
			data-alt-value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" autocomplete="off">
		<span class="input-group-append">
			<button type="button" class="<?php echo ($readonly || $disabled) ? 'hidden ' : ''; ?>btn btn-primary"
				id="<?php echo $id; ?>_btn"
				data-inputfield="<?php echo $id; ?>"
				data-dayformat="<?php echo $format; ?>"
				data-button="<?php echo $id; ?>_btn"
				data-firstday="<?php echo Factory::getLanguage()->getFirstDay(); ?>"
				data-weekend="<?php echo Factory::getLanguage()->getWeekEnd(); ?>"
				data-today-btn="<?php echo $todaybutton; ?>"
				data-week-numbers="<?php echo $weeknumbers; ?>"
				data-show-time="<?php echo $showtime; ?>"
				data-time-title="<?php echo Text::_('COM_CALENDAR_CHOOSE_A_TIME'); ?>"
				data-show-others="<?php echo $filltable; ?>"
				data-time-24="<?php echo $timeformat; ?>"
				data-only-months-nav="<?php echo $singleheader; ?>"
				<?php echo isset($minYear) && strlen($minYear) ? 'data-min-year="' . $minYear . '"' : ''; ?>
				<?php echo isset($maxYear) && strlen($maxYear) ? 'data-max-year="' . $maxYear . '"' : ''; ?>
				title="<?php echo Text::_('JLIB_HTML_BEHAVIOR_OPEN_CALENDAR'); ?>"
			><span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('calendar', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span></button>
		</span>
		<?php if (!$readonly && !$disabled) : ?>
	</div>
<?php endif; ?>
</div>
