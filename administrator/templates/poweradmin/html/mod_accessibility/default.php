<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_accessibility
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}
?>
<div class="mod-accessibility module-<?php echo $module->id; ?>" id="mod-accessibility-<?php echo $module->id; ?>">

    <a class="header-item-link" id="accessibility-collapse-control" href="javascript:void(0);">
        <span class="header-item-icon" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('accessibility', 18, 'currentColor'); ?></span>
        <span class="header-item-text">
            <?php echo Text::_('MOD_ACCESSIBILITY'); ?>
        </span>
    </a>

    <!-- /.offcanvas -->
    <div class="accessibility-sidebar j-card font-unresizable">
        <div class="j-card-body">
            <div class="accessibility-scaling">
                <div class="card-content font-unresizable">
					<div class="accessibility-scaling-header">
						<span class="icon-content-scalling"></span>
						<h3 class="font-unresizable"><?php echo Text::_('MOD_ACCESSIBILITY_CONTENT_SCALING'); ?></h3>
					</div>
                    <div class="font-size font-unresizable accessibility-scaling-btn-group">
                        <button class="font-unresizable icon-minus-sign" id="decrement"></button>
                        <span class="font-percent-value font-unresizable">0%</span>
                        <button class="font-unresizable icon-save-new" id="increment"></button>
                    </div>
                </div>
            </div> <!-- .accessibility-scaling -->
            <div class="accessibility-items-wrap">
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="grayscale" href="javascript:void(0);">
                        <div>
							<span class="duotone icon-grayscale" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_GRAYSCALE'); ?>
							</span>
						</div>
                    </a>
                </div>
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="nomotion" href="javascript:void(0);">
                        <div>
							<span class="duotone icon-disable-motion" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_DISABLE_MOTION'); ?>
							</span>
						</div>
                    </a>
                </div>
            </div> <!-- /.accessibility-items-wrap -->

            <div class="accessibility-items-wrap">
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="bbcursor" href="javascript:void(0);">
                        <div>
							<span class="icon-big-black-cursor" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_BIG_BLACK_CURSOR'); ?>
							</span>
						</div>
                    </a>
                </div>
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="bhcursor" href="javascript:void(0);">
                        <div>
							<span class="icon-big-white-cursor" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_BIG_WHITE_CURSOR'); ?>
							</span>
						</div>
                    </a>
                </div>
            </div> <!-- /.accessibility-items-wrap -->

            <div class="accessibility-items-wrap">
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="magnifier" href="javascript:void(0);">
                        <div>
							<span class="icon-magnifier" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_MAGNIFIER'); ?>
							</span>
						</div>
                    </a>
                </div>
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="contrast" href="javascript:void(0);">
                        <div>
							<span class="icon-increase-contrast" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_CONTRAST'); ?>
							</span>
						</div>
                    </a>
                </div>
            </div> <!-- /.accessibility-items-wrap -->

            <div class="accessibility-items-wrap">
                <div class="accessibility--item">
                    <a class="accessible-action-btn" data-type="mouse-highlighter" href="javascript:void(0);">
                        <div>
							<span class="icon-click" aria-hidden="true"></span>
							<span class="d-block">
								<?php echo Text::_('MOD_ACCESSIBILITY_MOUSE_HIGHLIGHTER'); ?>
							</span>
						</div>
                    </a>
                </div>
            </div> <!-- /.accessibility-items-wrap -->
        </div> <!-- /.j-card-body -->

        <div class="j-card-footer j-card-footer-sm accessibility-mouse-highligther pt-4">
            <a href="javascript:void(0);" class="btn btn-secondary" id="accessible-reset-btn"><?php echo Text::_('MOD_ACCESSIBILITY_RESET'); ?></a>
        </div> <!-- /.j-card-footer -->
    </div>

    <div class="a11y-cursor-pointer"><div class="a11y-cursor-pointer-inner"></div></div>
</div>
