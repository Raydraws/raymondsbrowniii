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
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

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
// Enable core and bootstrap bundles before template scripts
// $wa->useScript('core');
// $this->addScript(Uri::root(true) . '/media/system/js/core.min.js', ['version' => 'auto']);
// $this->addScript(Uri::root(true) . '/media/vendor/bootstrap/js/bootstrap-es5.min.js', ['version' => 'auto']);
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
// Ensure jQuery is available for legacy scripts such as com_cpanel/tree.js
HTMLHelper::_('jquery.framework');
// Bootstrap bundle is required by various legacy admin scripts (dropdowns, tooltips, etc.)
HTMLHelper::_('bootstrap.framework');
// Load specific template related JS
// Register and use template JS directly
$wa->registerAndUseScript(
	'template.poweradmin',
	'administrator/templates/' . $this->template . '/js/template.js',
	['version' => 'auto'],
	['type' => 'module'],
	['core']
);
HTMLHelper::_('script', 'com_cpanel/tree.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'vendor/focus-visible/focus-visible.js', ['version' => 'auto', 'relative' => true]);
// Load css-vars-ponyfill before using it
$wa->useAsset('script', 'css-vars-ponyfill');

// Fix modal.js issues: ensure Joomla.sanitizeHtml exists and patch getComputedStyle calls
$this->addScriptDeclaration('
(function() {
	// Ensure Joomla.sanitizeHtml exists before modal.js tries to use it
	if (typeof Joomla === "undefined") {
		window.Joomla = {};
	}
	
	// Create a fallback sanitizeHtml if it doesn\'t exist
	if (typeof Joomla.sanitizeHtml !== "function") {
		Joomla.sanitizeHtml = function(html, allowList, sanitizeFn) {
			return html; // Fallback: return HTML as-is
		};
	}
	
	// Patch Joomla.initialiseModal after modal.js loads
	function patchModalInitialisation() {
		if (typeof Joomla.initialiseModal !== "function" || !window.bootstrap || !window.bootstrap.Modal) {
			return;
		}
		
		const originalInit = Joomla.initialiseModal;
		const allowed = { iframe: ["src", "name", "width", "height"] };
		
		Joomla.initialiseModal = function(modal, options) {
			if (!(modal instanceof Element)) {
				return;
			}
			
			// Initialize Bootstrap modal
			new window.bootstrap.Modal(modal, options);
			modal.open = function() {
				window.bootstrap.Modal.getInstance(modal).show(modal);
			};
			modal.close = function() {
				window.bootstrap.Modal.getInstance(modal).hide();
			};
			
			// Patched show.bs.modal event with sanitizeHtml check
			modal.addEventListener("show.bs.modal", function() {
				Joomla.Modal.setCurrent(modal);
				if (modal.dataset.url) {
					const modalBody = modal.querySelector(".modal-body");
					if (!modalBody) return;
					
					const iframe = modalBody.querySelector("iframe");
					if (iframe) {
						const addData = modal.querySelector("joomla-field-mediamore");
						if (addData) {
							addData.parentNode.removeChild(addData);
						}
						iframe.parentNode.removeChild(iframe);
					}
					
					if (modal.dataset.iframe) {
						const sanitizeHtml = (typeof Joomla !== "undefined" && typeof Joomla.sanitizeHtml === "function") 
							? Joomla.sanitizeHtml 
							: function(html) { return html; };
						
						if (modal.dataset.iframe.indexOf("document.getElementById") > 0) {
							const iframeTextArr = modal.dataset.iframe.split("+");
							const idFieldArr = iframeTextArr[1].split(\'"\');
							let el;
							idFieldArr[0] = idFieldArr[0].replace(/&quot;/g, \'"\');
							if (!document.getElementById(idFieldArr[1])) {
								const fn = new Function("return " + idFieldArr[0]);
								el = fn.call(null);
							} else {
								el = document.getElementById(idFieldArr[1]).value;
							}
							modalBody.insertAdjacentHTML("afterbegin", sanitizeHtml(iframeTextArr[0] + el + iframeTextArr[2], allowed));
						} else {
							modalBody.insertAdjacentHTML("afterbegin", sanitizeHtml(modal.dataset.iframe, allowed));
						}
					}
				}
			});
			
			// Patched shown.bs.modal event with fixed getComputedStyle calls
			modal.addEventListener("shown.bs.modal", function() {
				const modalBody = modal.querySelector(".modal-body");
				const modalHeader = modal.querySelector(".modal-header");
				const modalFooter = modal.querySelector(".modal-footer");
				let modalHeaderHeight = 0;
				let modalFooterHeight = 0;
				let maxModalBodyHeight = 0;
				let modalBodyPadding = 0;
				let modalBodyHeightOuter = 0;
				
				if (modalBody) {
					if (modalHeader) {
						const modalHeaderRects = modalHeader.getBoundingClientRect();
						modalHeaderHeight = modalHeaderRects.height;
						modalBodyHeightOuter = modalBody.offsetHeight;
					}
					if (modalFooter) {
						// Fix: remove null parameter
						const footerStyle = getComputedStyle(modalFooter);
						if (footerStyle && footerStyle.height) {
							modalFooterHeight = parseFloat(footerStyle.height.replace("px", ""));
						}
					}
					// Fix: remove null parameter
					const bodyStyle = getComputedStyle(modalBody);
					if (bodyStyle && bodyStyle.height) {
						const modalBodyHeight = parseFloat(bodyStyle.height.replace("px", ""));
						const padding = modalBody.offsetTop;
						// Fix: remove null parameter
						const bodyStyle2 = getComputedStyle(document.body);
						if (bodyStyle2 && bodyStyle2.height) {
							const maxModalHeight = parseFloat(bodyStyle2.height.replace("px", "")) - padding * 2;
							modalBodyPadding = modalBodyHeightOuter - modalBodyHeight;
							maxModalBodyHeight = maxModalHeight - (modalHeaderHeight + modalFooterHeight + modalBodyPadding);
						}
					}
					
					if (modal.dataset.url) {
						const iframe = modalBody.querySelector("iframe");
						if (iframe) {
							// Fix: remove null parameter and add null check
							const iframeStyle = getComputedStyle(iframe);
							if (iframeStyle && iframeStyle.height) {
								const iframeHeight = parseFloat(iframeStyle.height.replace("px", ""));
								if (iframeHeight > maxModalBodyHeight) {
									modalBody.style.maxHeight = maxModalBodyHeight + "px";
									modalBody.style.overflowY = "auto";
									iframe.style.maxHeight = (maxModalBodyHeight - modalBodyPadding) + "px";
								}
							}
						}
					}
				}
			});
			
			modal.addEventListener("hide.bs.modal", function() {
				const modalBody = modal.querySelector(".modal-body");
				if (modalBody) {
					modalBody.style.maxHeight = "initial";
				}
			});
			
			modal.addEventListener("hidden.bs.modal", function() {
				Joomla.Modal.setCurrent("");
			});
		};
	}
	
	// Patch when DOM is ready and after a delay to catch late-loading scripts
	document.addEventListener("DOMContentLoaded", patchModalInitialisation);
	setTimeout(patchModalInitialisation, 100);
	setTimeout(patchModalInitialisation, 500);
})();
');

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
$this->addStyleDeclaration("
    :root {
        --skeleton-bg: url(". Uri::root() . 'administrator/templates/poweradmin/images/skeleton.gif' .");
    }
");

// Getting user accessibility settings
$a11y_mono      = (bool) $app->getIdentity()->getParam('a11y_mono', '');
$a11y_contrast  = (bool) $app->getIdentity()->getParam('a11y_contrast', '');
$a11y_highlight = (bool) $app->getIdentity()->getParam('a11y_highlight', '');
$a11y_font      = (bool) $app->getIdentity()->getParam('a11y_font', '');
$darkMode       = \JHtmlPoweradmin::isDarkMode();

$monochrome = (bool) $this->params->get('monochrome');
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
<?php
$htmlClasses = [];
if ($a11y_font) {
	$htmlClasses[] = 'a11y_font';
}
if ($darkMode) {
	$htmlClasses[] = 'poweradmin-dark-mode';
}
$htmlClassAttr = !empty($htmlClasses) ? ' class="' . implode(' ', $htmlClasses) . '"' : '';
?>
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

	<?php //if (!$hiddenMenu) : ?>

	<div id="sidebar-wrapper" class="sidebar-wrapper sidebar-menu" <?php echo $hiddenMenu ? 'data-hidden="' . $hiddenMenu . '"' : ''; ?>>
		<div id="sidebarmenu">
			<jdoc:include type="modules" name="menu" style="none" />
		</div>
	</div>

	<span class="navbar-mobile-overlay navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#sidebar-wrapper" aria-controls="sidebar-wrapper" aria-expanded="false"></span>

	<?php //endif; ?>

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
					<main>
						<jdoc:include type="component" />
					</main>
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
