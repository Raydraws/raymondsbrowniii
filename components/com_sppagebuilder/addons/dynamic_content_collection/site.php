<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2026 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use JoomShaper\SPPageBuilder\DynamicContent\Constants\CollectionIds;
use JoomShaper\SPPageBuilder\DynamicContent\Site\CollectionRenderer;

class SppagebuilderAddonDynamic_content_collection extends SppagebuilderAddons
{
    protected static $cssContent = [];

    public function render()
    {
        $addon = $this->addon;
        
        if(empty($addon->settings->source)) {
            return '';
        }

        $isCarousel = isset($addon->settings->layout_style) && $addon->settings->layout_style === 'carousel';

        $renderer = new CollectionRenderer($addon);
        if ($addon->settings->source === CollectionIds::ARTICLES_COLLECTION_ID || $addon->settings->source === CollectionIds::TAGS_COLLECTION_ID) {
            $output = $renderer->renderArticles();
            $pagination = $isCarousel ? '' : $renderer->renderArticlesPagination();
        } else {
            $output = $renderer->render();
            $pagination = $isCarousel ? '' : $renderer->renderPagination();
        }

        $css = $renderer->generateCSS();

        return $output . $pagination . $css;
    }

    /**
     * Generate the CSS for the collection addon
     *
     * @return string The generated CSS
     * 
     * @since 5.5.0
     */
    public function css()
    {
        $settings = $this->addon->settings;
        $addon_id = '#sppb-addon-' . $this->addon->id;
        $idSelector = '#sppb-dynamic-content-' . $this->addon->id;
        $cssHelper = new CSSHelper($addon_id);
        $css = '';

        $layoutStyle = $settings->layout_style ?? 'default';
        $isCarousel = $layoutStyle === 'carousel';

        $display = $settings->display ?? 'grid';
        $flexDirection = $settings->flex_direction ?? 'row';

        if (!$isCarousel) {
            $props = [
                'display' => 'display',
                'column_gap' => 'column-gap',
                'row_gap' => 'row-gap'
            ];

            $units = [
                'display' => false,
                'column_gap' => 'px',
                'row_gap' => 'px'
            ];

            if ($display === 'grid') {
                $props['grid_columns'] = 'grid-template-columns: repeat(%s, 1fr)';
                $units['grid_columns'] = false;
            }

            if ($display === 'flex') {
                $props['flex_direction'] = 'flex-direction';
                $props['flex_wrap'] = 'flex-wrap';

                if ($flexDirection === 'row') {
                    $props['vertical_alignment'] = 'align-items';
                    $props['horizontal_alignment'] = 'justify-content';
                } else {
                    $props['vertical_alignment'] = 'justify-content';
                    $props['horizontal_alignment'] = 'align-items';
                }

                $units['flex_direction'] = false;
                $units['flex_wrap'] = false;
                $units['vertical_alignment'] = false;
                $units['horizontal_alignment'] = false;
            }

            $css .= $cssHelper->generateStyle(
                ':no-parent' . $idSelector . '.sppb-dynamic-content-collection',
                $settings,
                $props,
                $units
            );
        } else {
            $halfGap = ((float) $this->resolveResponsiveValue($settings->column_gap ?? null, 32)) / 2;
            $liStyle = 'box-sizing: border-box; padding: 0 ' . $halfGap . 'px;';
            $settings->content_vertical_alignment = $settings->content_vertical_alignment ?? 1;
            if (!empty($settings->content_vertical_alignment)) {
                $liStyle .= ' display: flex; align-items: center;';
            }
            $css .= $idSelector . ' .sppb-dc-collection-bxslider > li { ' . $liStyle . ' }';

            if (($settings->content_container_option ?? 'bootstrap') === 'custom') {
                $css .= $cssHelper->generateStyle(
                    ':no-parent' . $idSelector . ' .sppb-dc-collection-bxslider > li > .sppb-dynamic-content-collection__item',
                    $settings,
                    ['content_container_width' => 'max-width'],
                    ['content_container_width' => '%']
                );
                $css .= $idSelector . ' .sppb-dc-collection-bxslider > li > .sppb-dynamic-content-collection__item { margin-left: auto; margin-right: auto; }';
            }

            // A sitewide unscoped rule (from the articles-carousel addon) sets
            // .bx-next's position via `left` instead of `right`, clumping both
            // arrows on the left for every bxSlider instance. Restore the
            // opposite-edges position here, same as the DC gallery field does
            // for its own carousel (.sppb-dynamic-content-gallery override).
            $css .= $idSelector . ' div.bx-wrapper .bx-controls-direction a.bx-next { left: unset; }';

            $css .= $this->generateBulletsCss($idSelector, $settings, $cssHelper);
            $css .= $this->generateArrowsCss($idSelector, $settings, $cssHelper);
        }

        $settings->item_box_shadow = CSSHelper::parseBoxShadow($settings, 'item_box_shadow');
        $settings->item_transition = 'all 300ms ease-in-out';

        // Matches items both in the default flat layout (direct child) and in carousel
        // mode (nested one level deeper inside the bxSlider <ul><li> track), without
        // matching down into a nested child Collection addon's own items.
        $itemSelector = ':no-parent' . $idSelector . ' > .sppb-dynamic-content-collection__item,'
            . ':no-parent' . $idSelector . ' > .sppb-dc-collection-bxslider > li > .sppb-dynamic-content-collection__item';
        $itemHoverSelector = ':no-parent' . $idSelector . ' > .sppb-dynamic-content-collection__item:hover,'
            . ':no-parent' . $idSelector . ' > .sppb-dc-collection-bxslider > li > .sppb-dynamic-content-collection__item:hover';

        $itemProps = [
            'item_padding' => 'padding',
            'item_background' => 'background-color',
            'item_border_radius' => 'border-radius',
            'item_box_shadow' => 'box-shadow',
            'item_transition' => 'transition',
            'item_width' => 'width',
        ];
        $itemUnits = [
            'item_padding' => false,
            'item_background' => false,
            'item_box_shadow' => false,
            'item_transition' => false,
            'item_width' => false
        ];

        if ($isCarousel) {
            $settings->carousel_item_min_height = $settings->carousel_item_min_height ?? 250;

            // .bx-wrapper is a direct child of $idSelector's own div (bxSlider wraps
            // the <ul> in place), so `>` keeps this from also matching a nested
            // child Collection addon's own carousel further down the tree.
            $css .= $cssHelper->generateStyle(
                ':no-parent' . $idSelector . ' > .bx-wrapper',
                $settings,
                ['carousel_item_min_height' => 'min-height'],
                ['carousel_item_min_height' => 'px']
            );
        }

        $itemBorderStyle = $cssHelper->border($itemSelector, $settings, 'item_border');
        $itemStyle = $cssHelper->generateStyle($itemSelector, $settings, $itemProps, $itemUnits);

        $settings->item_box_shadow_hover = CSSHelper::parseBoxShadow($settings, 'item_box_shadow_hover');
        $itemHoverStyle = $cssHelper->generateStyle($itemHoverSelector, $settings, [
            'item_padding_hover'       => 'padding',
            'item_background_hover'    => 'background-color',
            'item_border_radius_hover' => 'border-radius',
            'item_box_shadow_hover'    => 'box-shadow',
        ], [
            'item_padding_hover' => false,
            'item_background_hover' => false,
            'item_box_shadow_hover' => false
        ]);

        $css .= $itemStyle;
        $css .= $itemHoverStyle;
        $css .= $itemBorderStyle;

        if (!$isCarousel) {
            $css .= $cssHelper->generateStyle('.sppb-dynamic-content-collection__pagination', $settings, [
                'pagination_buttons_position' => 'justify-content',
                'pagination_padding' => 'padding',
                'pagination_margin' => 'margin',
            ], [
                'pagination_buttons_position' => false,
                'pagination_padding' => false,
                'pagination_margin' => false,
            ]);
        }

        return $css;
    }

    /**
     * Generate the CSS for the carousel's bullet/dot pager.
     *
     * @return string The generated CSS
     */
    private function generateBulletsCss($idSelector, $settings, $cssHelper)
    {
        $settings->dot_controllers = $settings->dot_controllers ?? 1;
        $dotControllers = !empty($settings->dot_controllers);

        if (!$dotControllers) {
            return '';
        }

        $css = '';
        $dotPosition = $settings->dot_controllers_position ?? 'bottom_center';

        switch ($dotPosition) {
            case 'bottom_left':
                $positionCss = 'left: 0; right: auto; top: auto; transform: none;';
                break;
            case 'bottom_right':
                $positionCss = 'right: 0; left: auto; top: auto; transform: none;';
                break;
            case 'vertical_left':
                $positionCss = 'left: 0; right: auto; top: 50%; bottom: auto; transform: translateY(-50%); flex-direction: column;';
                break;
            case 'vertical_right':
                $positionCss = 'right: 0; left: auto; top: 50%; bottom: auto; transform: translateY(-50%); flex-direction: column;';
                break;
            case 'bottom_center':
            default:
                $positionCss = 'left: 50%; right: auto; top: auto; transform: translateX(-50%);';
                break;
        }

        // bxSlider's own base CSS gives .bx-pager an explicit width (80%);
        // reset it to shrink-to-fit so the dots pack at the box's own start
        // (flex-start) instead of floating centered inside that wide box,
        // while the container's own left/right/transform (set above per
        // dotPosition) still handles overall group placement.
        $css .= $idSelector . ' .bx-pager.bx-default-pager { position: absolute; width: max-content; display: flex; align-items: center; justify-content: flex-start; z-index: 5; ' . $positionCss . ' }';

        $settings->dot_controllers_bottom_gap = $settings->dot_controllers_bottom_gap ?? -40;
        $settings->dot_controllers_left_gap = $settings->dot_controllers_left_gap ?? 0;
        $settings->dot_controllers_right_gap = $settings->dot_controllers_right_gap ?? 50;
        $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-pager', $settings, [
            'dot_controllers_bottom_gap' => 'bottom',
            'dot_controllers_left_gap' => 'left',
            'dot_controllers_right_gap' => 'right',
        ], [
            'dot_controllers_bottom_gap' => 'px',
            'dot_controllers_left_gap' => 'px',
            'dot_controllers_right_gap' => 'px',
        ]);

        $settings->dot_ctlr_height = $settings->dot_ctlr_height ?? 18;
        $settings->dot_ctlr_width = $settings->dot_ctlr_width ?? 18;
        $pagerLinkSelector = ':no-parent' . $idSelector . ' .bx-pager-link';
        $css .= $cssHelper->generateStyle($pagerLinkSelector, $settings, [
            'dot_ctlr_height' => 'height',
            'dot_ctlr_width' => 'width',
            'dot_ctlr_bg' => 'background-color',
            'dot_ctlr_border_width' => 'border-width',
            'dot_ctlr_border_color' => 'border-color',
            'dot_ctlr_margin' => 'margin',
        ], [
            'dot_ctlr_height' => 'px',
            'dot_ctlr_width' => 'px',
            'dot_ctlr_bg' => false,
            'dot_ctlr_border_width' => 'px',
            'dot_ctlr_border_color' => false,
            'dot_ctlr_margin' => false,
        ]);
        if (!empty($settings->dot_ctlr_border_width) || !empty($settings->dot_ctlr_border_color)) {
            $css .= $pagerLinkSelector . ' { border-style: solid; }';
        }

        $css .= $pagerLinkSelector . ' { border-radius: 50%; }';

        // Deliberately .active only - combining this with :hover meant a
        // pager dot the mouse happened to rest over kept looking "active"
        // even after autoplay moved the real active class to a different
        // dot, so two dots could appear active at once.
        $activeSelector = ':no-parent' . $idSelector . ' .bx-pager-link.active';
        $css .= $cssHelper->generateStyle($activeSelector, $settings, [
            'dot_ctlr_hover_height' => 'height',
            'dot_ctlr_hover_width' => 'width',
            'dot_ctlr_center_bg' => 'background-color',
            'dot_ctlr_hover_border_color' => 'border-color',
        ], [
            'dot_ctlr_hover_height' => 'px',
            'dot_ctlr_hover_width' => 'px',
            'dot_ctlr_center_bg' => false,
            'dot_ctlr_hover_border_color' => false,
        ]);

        return $css;
    }

    /**
     * Generate the CSS for the carousel's prev/next arrows.
     *
     * @return string The generated CSS
     */
    private function generateArrowsCss($idSelector, $settings, $cssHelper)
    {
        $settings->arrow_controllers = $settings->arrow_controllers ?? 1;
        $arrowControllers = !empty($settings->arrow_controllers);

        if (!$arrowControllers) {
            return '';
        }

        $css = '';
        $css .= $idSelector . ' a.bx-prev:hover, ' . $idSelector . ' a.bx-next:hover { color: inherit; }';
        $arrowStyle = $settings->arrow_controllers_style ?? 'spread';

        if ($arrowStyle === 'along') {
            $position = $settings->arrow_controllers_position ?? 'bottom_center';

            switch ($position) {
                case 'bottom_left':
                    $positionCss = 'left: 0; right: auto; transform: none;';
                    break;
                case 'bottom_right':
                    $positionCss = 'right: 0; left: auto; transform: none;';
                    break;
                case 'bottom_center':
                default:
                    $positionCss = 'left: 50%; right: auto; transform: translateX(-50%);';
                    break;
            }

            $css .= $idSelector . ' .bx-controls-direction { position: absolute; bottom: 0; top: auto; display: flex; gap: 8px; z-index: 6; ' . $positionCss . ' }';
            $css .= $idSelector . ' .bx-controls-direction a { position: relative; top: auto; margin-top: 0; left: auto; right: auto; }';

            $settings->arrow_controllers_bottom_gap = $settings->arrow_controllers_bottom_gap ?? 50;
            $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-controls-direction', $settings, [
                'arrow_controllers_bottom_gap' => 'bottom',
            ], [
                'arrow_controllers_bottom_gap' => 'px',
            ]);

            if ($position === 'bottom_left') {
                $settings->arrow_controllers_left_gap = $settings->arrow_controllers_left_gap ?? 50;
                $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-controls-direction', $settings, [
                    'arrow_controllers_left_gap' => 'left',
                ], [
                    'arrow_controllers_left_gap' => 'px',
                ]);
            } elseif ($position === 'bottom_right') {
                $settings->arrow_controllers_right_gap = $settings->arrow_controllers_right_gap ?? 50;
                $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-controls-direction', $settings, [
                    'arrow_controllers_right_gap' => 'right',
                ], [
                    'arrow_controllers_right_gap' => 'px',
                ]);
            }
        } else {
            // Reserve a gutter so the spread-apart arrows (absolutely positioned
            // at the edges of .bx-wrapper) don't overlap the first/last item.
            $css .= $idSelector . ' { padding: 0 34px; box-sizing: border-box; }';

            $settings->arrow_spread_controllers_left_gap = $settings->arrow_spread_controllers_left_gap ?? -50;
            $settings->arrow_spread_controllers_right_gap = $settings->arrow_spread_controllers_right_gap ?? -50;
            $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-controls-direction a.bx-prev', $settings, [
                'arrow_spread_controllers_left_gap' => 'left',
            ], [
                'arrow_spread_controllers_left_gap' => 'px',
            ]);
            $css .= $cssHelper->generateStyle(':no-parent' . $idSelector . ' .bx-controls-direction a.bx-next', $settings, [
                'arrow_spread_controllers_right_gap' => 'right',
            ], [
                'arrow_spread_controllers_right_gap' => 'px',
            ]);
        }

        $settings->arrow_ctlr_width = $settings->arrow_ctlr_width ?? 50;
        $settings->arrow_ctlr_height = $settings->arrow_ctlr_height ?? 50;
        $settings->arrow_ctlr_icon_weight = $settings->arrow_ctlr_icon_weight ?? 400;
        $arrowSelector = ':no-parent' . $idSelector . ' .bx-controls-direction a';
        $css .= $cssHelper->generateStyle($arrowSelector, $settings, [
            'arrow_ctlr_width' => 'width',
            'arrow_ctlr_height' => 'height',
            'arrow_ctlr_font_size' => 'font-size',
            'arrow_ctlr_icon_weight' => 'font-weight',
        ], [
            'arrow_ctlr_width' => 'px',
            'arrow_ctlr_height' => 'px',
            'arrow_ctlr_font_size' => 'px',
            'arrow_ctlr_icon_weight' => false,
        ]);
        $css .= $idSelector . ' .bx-controls-direction a { display: flex; align-items: center; justify-content: center; gap: 4px; white-space: nowrap; text-decoration: none; }';

        $settings->arrow_ctlr_border_radius = $settings->arrow_ctlr_border_radius ?? 50;
        $css .= $cssHelper->generateStyle($arrowSelector, $settings, [
            'arrow_ctlr_color' => 'color',
            'arrow_ctlr_background' => 'background-color',
            'arrow_ctlr_border_width' => 'border-width',
            'arrow_ctlr_border_color' => 'border-color',
            'arrow_ctlr_border_radius' => 'border-radius',
        ], [
            'arrow_ctlr_color' => false,
            'arrow_ctlr_background' => false,
            'arrow_ctlr_border_width' => 'px',
            'arrow_ctlr_border_color' => false,
            'arrow_ctlr_border_radius' => 'px',
        ]);
        if (!empty($settings->arrow_ctlr_border_width) || !empty($settings->arrow_ctlr_border_color)) {
            $css .= $idSelector . ' .bx-controls-direction a { border-style: solid; }';
        }

        $hoverSelector = ':no-parent' . $idSelector . ' .bx-controls-direction a:hover';
        $css .= $cssHelper->generateStyle($hoverSelector, $settings, [
            'arrow_ctlr_hover_color' => 'color',
            'arrow_ctlr_hover_background' => 'background-color',
            'arrow_ctlr_hover_border_color' => 'border-color',
        ], [
            'arrow_ctlr_hover_color' => false,
            'arrow_ctlr_hover_background' => false,
            'arrow_ctlr_hover_border_color' => false,
        ]);

        if (!empty($settings->arrow_on_hover)) {
            $css .= $idSelector . ' .bx-controls-direction a { opacity: 0; transition: opacity 0.3s ease-in-out; }';
            $css .= $idSelector . ':hover .bx-controls-direction a { opacity: 1; }';
        }

        return $css;
    }

    /**
     * Resolve a single effective value out of a (possibly responsive) setting.
     * Responsive fields are stored as an object keyed by breakpoint (xl/lg/md/sm/xs);
     * this picks the first non-empty one, widest first, matching the desktop-first
     * cascade the rest of the addon's CSS already follows.
     *
     * @param mixed $value    The raw setting value.
     * @param mixed $fallback Returned when nothing usable is found.
     * @return mixed
     */
    private function resolveResponsiveValue($value, $fallback)
    {
        if (is_object($value) || is_array($value)) {
            $value = (array) $value;

            foreach (['xl', 'lg', 'md', 'sm', 'xs'] as $device) {
                if (isset($value[$device]) && $value[$device] !== '') {
                    return $value[$device];
                }
            }

            return $fallback;
        }

        return ($value === null || $value === '') ? $fallback : $value;
    }

    public function stylesheets()
    {
        if (isset($this->addon->settings->layout_style) && $this->addon->settings->layout_style === 'carousel') {
            return [Uri::base(true) . '/components/com_sppagebuilder/assets/css/jquery.bxslider.min.css'];
        }
    }

    /**
     * Load the carousel's assets inline so it also previews correctly inside the
     * visual builder's live canvas.
     *
     * The canvas renders each addon through an AJAX fragment (render()/css()/js()
     * only - see components/com_sppagebuilder/views/ajax/view.json.php) rather than
     * a full Joomla page load, so scripts()/stylesheets() below - meant for a real
     * page's <head> - never run there, and the carousel would otherwise show up as
     * an unstyled, uninitialized stacked list while editing.
     *
     * @return string The JS string.
     */
    public function js()
    {
        if (!isset($this->addon->settings->layout_style) || $this->addon->settings->layout_style !== 'carousel') {
            return '';
        }

        $cssUrl = Uri::base(true) . '/components/com_sppagebuilder/assets/css/jquery.bxslider.min.css';
        $bxSliderJsUrl = Uri::base(true) . '/components/com_sppagebuilder/assets/js/jquery.bxslider.min.js';
        $initJsUrl = Uri::base(true) . '/components/com_sppagebuilder/assets/js/addons/dc-collection-bxslider.js';

        return '
        (function () {
            function loadOnce(tag, attr, url, onload) {
                if (document.querySelector(tag + "[" + attr + "=\'" + url + "\']")) {
                    if (onload) { onload(); }
                    return;
                }
                var el = document.createElement(tag);
                el[attr] = url;
                if (tag === "link") {
                    el.rel = "stylesheet";
                } else if (onload) {
                    el.onload = onload;
                }
                document.head.appendChild(el);
            }

            loadOnce("link", "href", "' . $cssUrl . '");
            loadOnce("script", "src", "' . $bxSliderJsUrl . '", function () {
                loadOnce("script", "src", "' . $initJsUrl . '");
            });
        })();';
    }

    public function scripts()
    {
        if (isset($this->addon->settings->layout_style) && $this->addon->settings->layout_style === 'carousel') {
            return [
                HTMLHelper::_('script', 'components/com_sppagebuilder/assets/js/jquery.bxslider.min.js', [], ['defer' => true]),
                HTMLHelper::_('script', 'components/com_sppagebuilder/assets/js/addons/dc-collection-bxslider.js', [], ['defer' => true]),
            ];
        }
    }

    public static function getTemplate() {
		$lodash = new Lodash('#sppb-addon-{{ data.id }}');
		$output  = '<style type="text/css">';

		$output .= $lodash->generateTransformCss('', 'data.transform');

		$output .= '</style>';

		return $output;
	}
}
