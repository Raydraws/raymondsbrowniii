<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.poweradmin
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/** @var JDocumentHtml $this */

$lang = Factory::getLanguage();

// Add JavaScript Frameworks
// HTMLHelper::_('script', 'vendor/focus-visible/focus-visible.min.js', ['version' => 'auto', 'relative' => true]);

// Check if this is a Helix Ultimate AJAX request
$app = Factory::getApplication();
$input = $app->input;
$option = $input->get('option', '');
$isHelixUltimate = $option === 'com_ajax' && $input->get('helix') === 'ultimate';
$isSpPageBuilder = $option === 'com_sppagebuilder' && $input->get('view') === 'editor';

require_once __DIR__ . '/Service/HTML/Poweradmin.php';

// Load template CSS file
HTMLHelper::_('stylesheet', 'bootstrap.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'fontawesome.css', ['version' => 'auto', 'relative' => true]);

if (!$isHelixUltimate && !$isSpPageBuilder) {
	HTMLHelper::_('stylesheet', 'template' . ($this->direction === 'rtl' ? '-rtl' : '') . '.css', ['version' => 'auto', 'relative' => true]);
	HTMLHelper::_('stylesheet', 'form-select-bridge.css', ['version' => 'auto', 'relative' => true]);
}

// Load custom CSS file
HTMLHelper::_('stylesheet', 'user.css', array('version' => 'auto', 'relative' => true));

// Load specific language related CSS
HTMLHelper::_('stylesheet', 'administrator/language/' . $lang->getTag() . '/' . $lang->getTag() . '.css', array('version' => 'auto'));

$wa = $this->getWebAssetManager();
$wa->useAsset('script', 'css-vars-ponyfill');

$monochrome = (bool) $this->params->get('monochrome');
if (!HTMLHelper::getServiceRegistry()->hasService('poweradmin')) {
	HTMLHelper::getServiceRegistry()->register('poweradmin', 'JHtmlPoweradmin');
}
HTMLHelper::_('poweradmin.rootcolors', $this->params);

$a11y_mono      = (bool) $app->getIdentity()->getParam('a11y_mono', '');
$a11y_contrast  = (bool) $app->getIdentity()->getParam('a11y_contrast', '');
$a11y_highlight = (bool) $app->getIdentity()->getParam('a11y_highlight', '');
$a11y_font      = (bool) $app->getIdentity()->getParam('a11y_font', '');
$darkMode       = \JHtmlPoweradmin::isDarkMode();

$htmlClasses = [];
if ($a11y_font) {
	$htmlClasses[] = 'a11y_font';
}
if ($darkMode) {
	$htmlClasses[] = 'poweradmin-dark-mode';
}
$htmlClassAttr = !empty($htmlClasses) ? ' class="' . implode(' ', $htmlClasses) . '"' : '';

$bodyTheme = ($monochrome || $a11y_mono ? ' monochrome' : '') . ($a11y_contrast ? ' a11y_contrast' : '') . ($a11y_highlight ? ' a11y_highlight' : '') . ($darkMode ? ' poweradmin-dark-mode' : '');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $htmlClassAttr; ?>>
<head>
	<jdoc:include type="head" />
</head>
<body class="contentpane component<?php echo $bodyTheme; ?>">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
