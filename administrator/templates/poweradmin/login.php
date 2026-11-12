<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.Poweradmin
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @since       4.0
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var JDocumentHtml $this */
$app   = Factory::getApplication();
$lang  = $app->getLanguage();
$input = $app->input;
$wa    = $this->getWebAssetManager();

// Detecting Active Variables
$option     = $input->get('option', '');
$view       = $input->get('view', '');
$layout     = $input->get('layout', 'default');
$task       = $input->get('task', 'display');
$itemid     = $input->get('Itemid', '');
$cpanel     = $option === 'com_cpanel';
$hiddenMenu = $app->input->get('hidemainmenu');
$joomlaLogo = $this->baseurl . '/templates/' . $this->template . '/images/logo.svg';
require_once __DIR__ . '/Service/HTML/Poweradmin.php';

// Template params
$siteLogo  = $this->params->get('siteLogo')
	? Uri::root() . $this->params->get('siteLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo-joomla.svg';
$loginLogo = $this->params->get('loginLogo')
	? Uri::root() . $this->params->get('loginLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo-colored.svg';
$smallLogo = $this->params->get('smallLogo')
	? Uri::root() . $this->params->get('smallLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo.svg';
$logoAlt = htmlspecialchars($this->params->get('altSiteLogo', ''), ENT_COMPAT, 'UTF-8');
$logoSmallAlt = htmlspecialchars($this->params->get('altSmallLogo', ''), ENT_COMPAT, 'UTF-8');

// Enable assets - use individual styles instead of preset to avoid dependency issues on login pages
$wa->registerStyle('template.active', '', [], [], ['template.poweradmin.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr')]);
HTMLHelper::_('stylesheet', 'form-select-bridge.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'bootstrap.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'fontawesome.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'template' . ($this->direction === 'rtl' ? '-rtl' : '') . '.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'user.css', ['version' => 'auto', 'relative' => true]);

// Load specific language related CSS
HTMLHelper::_('stylesheet', 'administrator/language/' . $lang->getTag() . '/' . $lang->getTag() . '.css', ['version' => 'auto']);

// Load customer stylesheet if available
HTMLHelper::_('stylesheet', 'custom.css', array('version' => 'auto', 'relative' => true));

// Load specific template related JS
$wa->registerAndUseScript(
	'template.poweradmin',
	'administrator/templates/' . $this->template . '/js/template.js',
	['version' => 'auto'],
	['type' => 'module']
);
// Load css-vars-ponyfill before using it
$wa->useAsset('script', 'css-vars-ponyfill');
// Set some meta data
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

// @TODO sync with _variables.scss
$this->setMetaData('theme-color', '#1c3d5c');
// Wait for css-vars-ponyfill to be available before calling cssVars()
// $this->addScriptDeclaration('
// 	if (typeof cssVars !== "undefined") {
// 		cssVars();
// 	} else {
// 		document.addEventListener("DOMContentLoaded", function() {
// 			if (typeof cssVars !== "undefined") {
// 				cssVars();
// 			}
// 		});
// 	}
// ');

// Opacity must be set before displaying the DOM, so don't move to a CSS file
$css = '
	.container-main > * {
		opacity: 0;
	}
	.sidebar-wrapper > * {
		opacity: 0;
	}
';

// $this->addStyleDeclaration($css);
$monochrome = (bool) $this->params->get('monochrome');
// Register poweradmin service if not already registered
if (!HTMLHelper::getServiceRegistry()->hasService('poweradmin')) {
	HTMLHelper::getServiceRegistry()->register('poweradmin', 'JHtmlPoweradmin');
}
HTMLHelper::_('poweradmin.rootcolors', $this->params);

// Match index.php: accessibility + dark theme on html/body
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

// Add cookie alert message
Text::script('JGLOBAL_WARNCOOKIES');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $htmlClassAttr; ?>>
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
</head>

<body class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ($task ? ' task-' . $task : '') . ($monochrome || $a11y_mono ? ' monochrome' : '') . ($a11y_contrast ? ' a11y_contrast' : '') . ($a11y_highlight ? ' a11y_highlight' : '') . ($darkMode ? ' poweradmin-dark-mode' : ''); ?>">

<noscript>
	<div class="j-alert j-alert-danger" role="alert">
		<?php echo Text::_('JGLOBAL_WARNJAVASCRIPT'); ?>
	</div>
</noscript>

<div id="wrapper" class="d-flex wrapper">
	<div class="container-fluid container-main order-1">
		<section id="content" class="content h-100">
			<main class="d-flex justify-content-center align-items-center h-100">
				<div class="main-wrap">
					<div class="login">
						<div class="d-flex justify-content-center">
							<img src="<?php echo $loginLogo; ?>"
							alt="<?php echo htmlspecialchars($this->params->get('altLoginLogo', ''), ENT_COMPAT, 'UTF-8'); ?>" width="150" height="150">
						</div>
						<div id="main-brand" class="main-brand">
							<h3><?php echo Text::_('TPL_POWERADMIN_BACKEND_LOGIN'); ?></h3>
						</div>
						<jdoc:include type="component" />
					</div>
				</div> <!-- /.main-wrap -->
			</main>
		</section>

		<div class="notify-alerts">
			<jdoc:include type="message" />
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug" style="none" />
<jdoc:include type="scripts" />
</body>
</html>