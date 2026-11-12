<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.poweradmin
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @since       4.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/** @var JDocumentError $this */

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
$menuSettings = $app->input->cookie->get('main-sidebar');

$sidebarCollapsed = ($hiddenMenu || $menuSettings == 'closed');
$hiddenMenuClass = $sidebarCollapsed ? 'main-sidebar-collapsed' : '';
$hiddenMenuClass .= $hiddenMenu ? ' hide-sidebar-toggler' : '';

$joomlaLogo = $this->baseurl . '/templates/' . $this->template . '/images/logo.svg';

require_once __DIR__ . '/Service/HTML/Poweradmin.php';

// Template params
$siteLogo  = $this->params->get('siteLogo')
	? Uri::root() . $this->params->get('siteLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo-joomla.svg';
$smallLogo = $this->params->get('smallLogo')
	? Uri::root() . $this->params->get('smallLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo.svg';

$logoAlt = htmlspecialchars($this->params->get('altSiteLogo', ''), ENT_COMPAT, 'UTF-8');
$logoSmallAlt = htmlspecialchars($this->params->get('altSmallLogo', ''), ENT_COMPAT, 'UTF-8');

// Enable assets
$wa->registerStyle('template.active', '', [], [], ['template.poweradmin.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr')]);
HTMLHelper::_('image', 'media/templates/administrator/atum/images/template_thumbnail.png', '');
HTMLHelper::_('stylesheet', 'bootstrap.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'fontawesome.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'template' . ($this->direction === 'rtl' ? '-rtl' : '') . '.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'user.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'form-select-bridge.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'searchtools.css', ['version' => 'auto', 'relative' => true]);

// Load specific language related CSS
HTMLHelper::_('stylesheet', 'administrator/language/' . $lang->getTag() . '/' . $lang->getTag() . '.css', ['version' => 'auto']);

// Load customer stylesheet if available
HTMLHelper::_('stylesheet', 'custom.css', ['version' => 'auto', 'relative' => true]);

// Ensure jQuery is available for legacy scripts
HTMLHelper::_('jquery.framework');
// Bootstrap bundle is required by various legacy admin scripts (dropdowns, tooltips, etc.)
HTMLHelper::_('bootstrap.framework');

// Load specific template related JS
$wa->registerAndUseScript(
	'template.poweradmin',
	'administrator/templates/' . $this->template . '/js/template.js',
	['version' => 'auto'],
	['type' => 'module'],
	['core']
);
HTMLHelper::_('script', 'com_cpanel/tree.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'vendor/focus-visible/focus-visible.min.js', ['version' => 'auto', 'relative' => true]);

// Load css-vars-ponyfill before using it
$wa->useAsset('script', 'css-vars-ponyfill');

// Set some meta data
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
// @TODO sync with _variables.scss
$this->setMetaData('theme-color', '#1c3d5c');

$this->addStyleDeclaration("
    :root {
        --skeleton-bg: url(". Uri::root() . 'administrator/templates/poweradmin/images/skeleton.gif' .");
    }
");

$monochrome = (bool) $this->params->get('monochrome');

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

// Register poweradmin service if not already registered
if (!HTMLHelper::getServiceRegistry()->hasService('poweradmin')) {
	HTMLHelper::getServiceRegistry()->register('poweradmin', 'JHtmlPoweradmin');
}
HTMLHelper::_('poweradmin.rootcolors', $this->params);

$quickmenuModules      = ModuleHelper::getModules('quickmenu');
$hasQuickmenuModule    = !empty($quickmenuModules);
$quickMenuFallbackHtml = '';

if (!$hasQuickmenuModule)
{
	$quickMenuFallbackModule = (object) [
		'id'        => 0,
		'title'     => 'Mobile Quickmenu',
		'module'    => 'mod_submenu',
		'position'  => 'quickmenu',
		'showtitle' => 0,
		'content'   => '',
		'params'    => new Registry(
			[
				'menutype' => '*',
				'preset'   => 'quickmenu',
				'layout'   => 'quickmenu',
			]
		),
	];

	$quickMenuFallbackHtml = ModuleHelper::renderModule($quickMenuFallbackModule, ['style' => 'none']) ?? '';
}
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

<?php // Wrapper ?>
<div id="wrapper" class="d-flex wrapper <?php echo $hiddenMenuClass; ?>">
	<?php // Header ?>
	<header id="header" class="header">
		<div class="logo-header">
			<div class="main-logo d-flex align-items-center">
				<?php // No home link in edit mode (so users can not jump out) and control panel (for a11y reasons) ?>
				<?php if ($hiddenMenu || $cpanel) : ?>
					<div class="logo">
						<img class="logo-main" src="<?php echo $siteLogo; ?>" alt="<?php echo $logoAlt; ?>">
						<img class="logo-small" src="<?php echo $smallLogo; ?>" alt="<?php echo $logoSmallAlt; ?>">
					</div>
				<?php else : ?>
					<a class="logo" href="<?php echo Route::_('index.php'); ?>"
					   aria-label="<?php echo Text::_('TPL_BACK_TO_CONTROL_PANEL'); ?>">
						<img class="logo-main" src="<?php echo $siteLogo; ?>" alt="">
						<img class="logo-small" src="<?php echo $smallLogo; ?>" alt="">
					</a>
				<?php endif; ?>
				<div class="joomla-version mx-3 d-none d-sm-inline-flex" title="<?php echo JVERSION; ?>">
					<span class="sr-only"><?php echo Text::sprintf('MOD_VERSION_CURRENT_VERSION_TEXT', JVERSION); ?></span>
					<span class="text-truncate" aria-hidden="true"><?php echo JVERSION; ?></span>
				</div>
			</div>
			<?php if (!$hiddenMenu) : ?>
				<div class="sidebar-toggle d-none d-sm-block">
					<a id="menu-collapse" href="#" title="<?php echo Text::_('JTOGGLE_SIDEBAR_MENU'); ?>">
						<span id="menu-collapse-icon" class="menu-collapse-icon" aria-hidden="true">
							<span class="menu-collapse-icon-svg menu-collapse-icon-svg--expanded<?php echo $sidebarCollapsed ? ' d-none' : ''; ?>"><?php echo \JHtmlPoweradmin::icon('double-arrow-left', 16, 'currentColor'); ?></span>
							<span class="menu-collapse-icon-svg menu-collapse-icon-svg--collapsed<?php echo $sidebarCollapsed ? '' : ' d-none'; ?>"><?php echo \JHtmlPoweradmin::icon('double-arrow-right', 16, 'currentColor'); ?></span>
						</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="header-title">
			<jdoc:include type="modules" name="title" />
		</div>
		<div class="header-items">
			<jdoc:include type="modules" name="status" style="header-item" />
		</div>
		<div class="navbar-wrap">
			<div class="navbar-mobile-quick-wrap">
				<?php if ($hasQuickmenuModule) : ?>
					<jdoc:include type="modules" name="quickmenu" style="none" />
				<?php else : ?>
					<?php echo $quickMenuFallbackHtml; ?>
				<?php endif; ?>
			</div>
			<?php HTMLHelper::_('bootstrap.collapse', '.toggler-burger'); ?>
			<button class="navbar-toggler toggler-burger collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-wrapper" aria-controls="sidebar-wrapper" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</div>
	</header>

	<?php // Sidebar ?>
	<div id="sidebar-wrapper" class="sidebar-wrapper sidebar-menu" <?php echo $hiddenMenu ? 'data-hidden="' . $hiddenMenu . '"' : ''; ?>>
		<div id="sidebarmenu">
			<jdoc:include type="modules" name="menu" style="none" />
		</div>
	</div>

	<span class="navbar-mobile-overlay navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#sidebar-wrapper" aria-controls="sidebar-wrapper" aria-expanded="false"></span>

	<?php // container-fluid ?>
	<div class="container-fluid container-main">
		<?php if (!$cpanel) : ?>
			<?php // Subheader ?>
			<button type="button" class="toggle-toolbar mx-auto btn btn-secondary my-3 d-md-none d-lg-none d-xl-none" data-bs-toggle="collapse"
					data-bs-target=".subhead"><?php echo Text::_('TPL_POWERADMIN_TOOLBAR'); ?>
				<span class="icon-chevron-down" aria-hidden="true"></span>
			</button>
			<div id="subhead" class="subhead mb-4">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row">
					<div class="col-md-12">
						<jdoc:include type="modules" name="toolbar" style="no" />
					</div>
				</div>
			</div>
		<?php endif; ?>
		<section id="content" class="content">
			<?php // Begin Content ?>
			<jdoc:include type="modules" name="top" style="xhtml" />
			<div class="row">
				<div class="col-md-12">
					<h1><?php echo Text::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?></h1>
					<blockquote class="blockquote">
						<span class="badge badge-secondary"><?php echo $this->error->getCode(); ?></span>
						<?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
					</blockquote>
					<?php if ($this->debug) : ?>
						<div>
							<?php echo $this->renderBacktrace(); ?>
							<?php // Check if there are more Exceptions and render their data as well ?>
							<?php if ($this->error->getPrevious()) : ?>
								<?php $loop = true; ?>
								<?php // Reference $this->_error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly ?>
								<?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions ?>
								<?php $this->setError($this->_error->getPrevious()); ?>
								<?php while ($loop === true) : ?>
									<p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
									<p><?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
									<?php echo $this->renderBacktrace(); ?>
									<?php $loop = $this->setError($this->_error->getPrevious()); ?>
								<?php endwhile; ?>
								<?php // Reset the main error object to the base error ?>
								<?php $this->setError($this->error); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<p>
						<a href="<?php echo $this->baseurl; ?>" class="btn btn-secondary">
							<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('dashboard', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span>
							<?php echo Text::_('JGLOBAL_TPL_CPANEL_LINK_TEXT'); ?></a>
					</p>
				</div>

				<?php if ($this->countModules('bottom')) : ?>
					<jdoc:include type="modules" name="bottom" style="xhtml" />
				<?php endif; ?>
			</div>
			<?php // End Content ?>
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
