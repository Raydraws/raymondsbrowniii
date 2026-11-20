<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('_JEXEC') || die('Restricted Access');
use AcyMailing\Helpers\UpdatemeHelper;

if (acym_level(ACYM_ESSENTIAL)) {
    $licenseKey = acym_escape($this->config->get('license_key', ''));
    $automaticSend = acym_escape($this->config->get('active_cron', 0));
    ?>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2">
		<div class="acym__title acym__title__secondary"><?php echo acym_escapeHtml(acym_translation('ACYM_MY_LICENSE')); ?>
            <?php acym_externalLink('ACYM_GET_MY_LICENSE_KEY', ACYM_ACYMAILING_WEBSITE.'account/license/', true, true, ['margin-left-1']); ?>
		</div>
		<div class="grid-x grid-margin-x">
			<label for="acym__configuration__license-key" class="cell medium-3">
                <?php echo acym_escapeHtml(acym_translation('ACYM_YOUR_LICENSE_KEY')); ?>
			</label>
			<input type="text" name="config[license_key]" id="acym__configuration__license-key" class="cell medium-4" value="<?php echo acym_escape($licenseKey); ?>">
			<button type="button"
			        id="acym__configuration__button__license"
			        class="cell shrink button"
			        data-acym-linked="<?php echo empty($licenseKey) ? 0 : 1; ?>">
                <?php echo acym_escapeHtml(acym_translation(empty($licenseKey) ? 'ACYM_ATTACH_MY_LICENSE' : 'ACYM_UNLINK_MY_LICENSE')); ?>
			</button>
		</div>
        <?php
        acym_trigger('onAcymAttachedLicenseOption');
        if (!empty($licenseKey)) { ?>
			<div class="acym__title acym__title__secondary margin-top-1"><?php echo acym_escapeHtml(acym_translation('ACYM_CRON')); ?></div>
			<div class="margin-y">
				<div class="cell grid-x grid-margin-x acym_vcenter">
					<label class="cell medium-3"><?php echo acym_escapeHtml(acym_translation('ACYM_AUTOMATED_TASKS'));
                        acym_info(['textShownInTooltip' => 'ACYM_AUTOMATED_TASKS_DESC']); ?></label>
                    <?php
                    if (empty($automaticSend)) {
                        $class = 'acym__color__red';
                        $text = 'ACYM_DEACTIVATED';
                    } else {
                        $class = 'acym__color__green';
                        $text = 'ACYM_ACTIVATED';
                    }
                    ?>
					<label class="cell shrink <?php echo acym_escape($class); ?>"><strong><?php echo acym_escapeHtml(acym_translation($text)); ?></strong></label>
					<button data-acym-active="<?php echo acym_escape($automaticSend); ?>" id="acym__configuration__button__cron" class="cell shrink button margin-bottom-0">
                        <?php echo acym_escapeHtml(acym_translation(empty($automaticSend) ? 'ACYM_ACTIVATE_IT' : 'ACYM_DEACTIVATE_IT')); ?>
					</button>
				</div>
				<div class="cell grid-x grid-margin-x acym_vcenter">
					<label class="cell medium-3"><?php echo acym_escapeHtml(acym_translation('ACYM_EXECUTE_DAILY_TASKS'));
                        acym_info(['textShownInTooltip' => 'ACYM_DAILY_TASKS_DESC']); ?></label>
					<div class="cell auto acym_auto_tasks">
                        <?php
                        $hours = acym_select(
                            $data['listHours'],
                            'config[daily_hour]',
                            $this->config->get('daily_hour', '12'),
                            ['class' => 'intext_select']
                        );
                        $minutes = acym_select(
                            $data['listMinutes'],
                            'config[daily_minute]',
                            $this->config->get('daily_minute', '00'),
                            ['class' => 'intext_select']
                        );
                        echo acym_escapeHtmlWithAllowedTags(
                            acym_translationSprintf('ACYM_HOUR_MINUTE', $hours, $minutes),
                            [
                                'select' => [
                                    'class' => true,
                                    'name' => true,
                                    'id' => true,
                                ],
                                'option' => [
                                    'value' => true,
                                    'selected' => true,
                                ],
                            ]
                        );
                        ?>
					</div>
				</div>
                <?php
                $expirationDate = $this->config->get('expirationdate', 0);
                if (empty($expirationDate) || (time() - 604800) > $this->config->get('lastlicensecheck', 0)) {
                    UpdatemeHelper::getLicenseInfo();
                }

                if ($expirationDate > time()) {
                    $cronUrl = acym_frontendLink('cron&task=cron');
                    if (!empty($this->config->get('cron_security', 0)) && !empty($this->config->get('cron_key', ''))) {
                        $cronUrl = $cronUrl.'&cronKey='.$this->config->get('cron_key', '');
                    }
                    ?>
					<div class="cell grid-x grid-margin-x acym_vcenter">
						<label class="cell medium-3"><?php echo acym_escapeHtml(acym_translation('ACYM_CRON_URL'));
                            acym_info(['textShownInTooltip' => 'ACYM_CRON_URL_DESCRIPTION']); ?></label>
						<a class="cell shrink acym__color__blue" target="_blank" href="<?php echo acym_escapeUrl($cronUrl); ?>"><?php echo acym_escapeHtml($cronUrl); ?></a>
					</div>
					<div class="cell grid-x grid-margin-x acym_vcenter">
						<label class="cell medium-3"><?php echo acym_escapeHtml(acym_translation('ACYM_IMPORTANT_INFORMATION')); ?></label>
						<div class="cell shrink"><?php echo acym_escapeHtml(acym_translationSprintf('ACYM_CRON_WHITELIST', ACYM_YOURCRONTASK_IP)); ?></div>
					</div>
                    <?php
                }
                ?>
			</div>
        <?php } ?>
	</div>
<?php }
if (!acym_level(ACYM_ESSENTIAL)) {
    include acym_getView('configuration', 'upgrade_license', true);
}
