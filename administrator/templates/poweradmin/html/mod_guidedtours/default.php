<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_guidedtours
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('script', 'system/joomla-callout.min.js', ['version' => 'auto', 'relative' => true]);

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

// Load the webcomponent helper
$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';
if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}

$hideLinks = $app->getInput()->getBool('hidemainmenu');

if ($hideLinks || !$tours) {
	return;
}

$lang       = $app->getLanguage();
$extension  = $app->getInput()->get('option');
$listTours  = [];
$allTours   = [];
$toursCount = (int) $params->get('tourscount', 7);

foreach ($tours as $tour) {
	if ($toursCount > 0 && count(array_intersect(['*', $extension], $tour->extensions))) {
		$listTours[] = $tour;
		$toursCount--;
	}

	$uri = new Uri($tour->url);
	$key = $uri->getVar('option') ?? Text::_('MOD_GUIDEDTOURS_GENERIC_TOUR');

	if (!isset($allTours[$key])) {
		$lang->load("$key.sys", JPATH_ADMINISTRATOR)
			|| $lang->load("$key.sys", JPATH_ADMINISTRATOR . '/components/' . $key);

		$allTours[$key] = [];
	}

	$allTours[$key][] = $tour;
}

$triggerId    = 'header-guidedtours';
$visibleTours = count($listTours);
?>

<div class="header-item-content">
	<a href="#" id="<?php echo $triggerId; ?>" class="header-item-link<?php echo ($hideLinks ? ' disabled' : ''); ?>" role="button" aria-haspopup="true" aria-expanded="false">
		<span class="header-item-icon icon-map-signs" aria-hidden="true"></span>
		<span class="header-item-text"><?php echo Text::_('MOD_GUIDEDTOURS_MENU'); ?></span>
		<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('right-arrow', 14, 'currentColor'); ?></span>
		<?php if ($visibleTours) : ?>
			<span class="badge badge-pill badge-info"><?php echo $visibleTours; ?></span>
		<?php endif; ?>
	</a>
</div>

<?php if (!$hideLinks) : ?>
	<joomla-callout for="#<?php echo $triggerId; ?>" dismiss="true" position="bottom">
		<div class="callout-title">
			<?php echo Text::_('MOD_GUIDEDTOURS_MENU'); ?>
		</div>
		<div class="callout-content">
			<?php if (!$visibleTours) : ?>
				<p class="text-muted mb-0"><?php echo Text::_('MOD_GUIDEDTOURS_NONE'); ?></p>
			<?php else : ?>
				<?php foreach ($listTours as $tour) : ?>
					<button type="button" class="button-start-guidedtour callout-list-item" data-id="<?php echo (int) $tour->id; ?>">
						<span class="icon-bullseye icon-fw" aria-hidden="true"></span>
						<span><?php echo htmlspecialchars($tour->title, ENT_QUOTES, 'UTF-8'); ?></span>
					</button>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="callout-footer">
			<button type="button" class="btn btn-link fw-semibold px-0" onclick="document.getElementById('modGuidedTours-modal').open();">
				<?php echo Text::_('MOD_GUIDEDTOURS_SHOW_ALL'); ?>
			</button>
		</div>
	</joomla-callout>
<?php endif; ?>

<?php

$modalHtml   = [];
$modalHtml[] = '<div class="p-3">';
$modalHtml[] = '<div class="row">';
foreach ($allTours as $extensionName => $extensionTours) {
	$modalHtml[] = '<div class="col-lg-6">';
	$modalHtml[] = '<h4>' . Text::_($extensionName) . '</h4>';
	$modalHtml[] = '<ul class="list-unstyled">';
	foreach ($extensionTours as $tour) {
		$modalHtml[] = '<li>';
		$modalHtml[] = '<a href="#" class="button-start-guidedtour" data-id="' . (int) $tour->id . '">' . htmlentities($tour->title) . '</a>';
		$modalHtml[] = '</li>';
	}
	$modalHtml[] = '</ul>';
	$modalHtml[] = '</div>';
}
$modalHtml[] = '</div>';
$modalHtml[] = '</div>';

$modalBody = implode('', $modalHtml);

// Render webcomponent modal
echo JHtmlWebcomponent::renderModal(
	'modGuidedTours-modal',
	array(
		'title'       => Text::_('MOD_GUIDEDTOURS_START_TOUR'),
		'width'       => '80vw',
		'height'      => 'auto',
		'appendToBody' => true,
		'footer'      => '<button type="button" class="btn btn-secondary" data-dismiss onclick="document.getElementById(\'modGuidedTours-modal\').close();">'
			. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
	),
	$modalBody
);

// Add JavaScript to handle modal opening and focus management
Factory::getDocument()->addScriptDeclaration(
	"(function() {
		var initModal = function() {
			var modal = document.getElementById('modGuidedTours-modal');
			if (!modal) {
				// Modal not yet in DOM, try again after a short delay
				setTimeout(initModal, 100);
				return;
			}
			
			// Handle focus management when modal opens
			var handleModalShow = function() {
				var focusableElements = 'button, [href], a';
				var firstFocusableElement = modal.querySelectorAll(focusableElements)[0];
				var focusableContent = modal.querySelectorAll(focusableElements);
				var lastFocusableElement = focusableContent[focusableContent.length - 1];
				
				if (firstFocusableElement) {
					setTimeout(function() {
						firstFocusableElement.focus();
					}, 100);
				}
				
				// Handle tab key navigation
				var handleKeydown = function(e) {
					if (!modal.classList.contains('show')) return;
					
					var isTabPressed = e.key === 'Tab' || e.keyCode === 9;
					if (!isTabPressed) return;
					
					if (e.shiftKey) {
						if (document.activeElement === firstFocusableElement) {
							lastFocusableElement.focus();
							e.preventDefault();
						}
					} else {
						if (document.activeElement === lastFocusableElement) {
							firstFocusableElement.focus();
							e.preventDefault();
						}
					}
				};
				
				modal.addEventListener('keydown', handleKeydown);
				
				// Remove keydown listener when modal closes
				modal.addEventListener('joomla.modal.closed', function() {
					modal.removeEventListener('keydown', handleKeydown);
				}, { once: true });
			};
			
			modal.addEventListener('joomla.modal.show', handleModalShow);
		};
		
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initModal);
		} else {
			initModal();
		}
	})();"
);
