<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('_JEXEC') || die('Restricted Access');

use AcyMailing\Helpers\CronHelper;

?>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2">
		<div class="acym__title acym__title__secondary"><?php echo acym_escapeHtml(acym_translation('ACYM_CONFIGURATION_QUEUE')); ?></div>
		<div class="grid-x grid-margin-x margin-y">
			<div class="cell medium-3"><?php echo acym_escapeHtml(acym_translation('ACYM_CONFIGURATION_QUEUE_PROCESSING')); ?></div>
			<div class="cell medium-9">
                <?php
                $disabledOptions = [];
                if (!acym_level(ACYM_ESSENTIAL)) {
                    $disabledOptions = [
                        'auto' => ['tooltipTxt' => acym_translation('ACYM_PRO_ONLY'), 'disabledClass' => 'acym__disabled'],
                        'automan' => ['tooltipTxt' => acym_translation('ACYM_PRO_ONLY'), 'disabledClass' => 'acym__disabled'],
                    ];
                }
                acym_radio(
                    [
                        'auto' => acym_translation('ACYM_CONFIGURATION_QUEUE_AUTOMATIC'),
                        'automan' => acym_translation('ACYM_CONFIGURATION_QUEUE_AUTOMAN'),
                        'manual' => acym_translation('ACYM_CONFIGURATION_QUEUE_MANUAL'),
                    ],
                    'config[queue_type]',
                    $this->config->get('queue_type', 'automan'),
                    [
                        'related' => [
                            'auto' => 'automatic_only',
                            'automan' => 'automatic_manual',
                            'manual' => 'manual_only',
                        ],
                    ],
                    [],
                    false,
                    $disabledOptions
                );
                ?>
			</div>
			<div class="cell medium-3 automatic_only automatic_manual">
                <?php
                echo acym_escapeHtml(acym_translation('ACYM_AUTO_SEND_PROCESS'));
                acym_info(['textShownInTooltip' => 'ACYM_AUTO_SEND_PROCESS_DESC']);
                ?>
			</div>
			<div class="cell medium-9 grid-x automatic_only automatic_manual">
				<div class="cell large-9 grid-x margin-y">
					<div class="cell">
                        <?php
                        $cronFrequency = (int)$this->config->get('cron_frequency');
                        $valueBatch = acym_level(ACYM_ENTERPRISE) ? $this->config->get('queue_batch_auto', 1) : 1;
                        if (!function_exists('curl_multi_exec') && ($cronFrequency < 900 || intval($valueBatch) > 1)) {
                            acym_display(acym_translation('ACYM_MULTI_CURL_DISABLED'), 'error', false);
                        } elseif (empty($cronFrequency) && !empty($this->config->get('active_cron', 0))) {
                            acym_display(acym_translation('ACYM_EMPTY_FREQUENCY'), 'error', false);
                        }

                        $disabledBatch = acym_level(ACYM_ENTERPRISE) ? '' : 'disabled';
                        $delayTypeAuto = $data['typeDelay'];
                        $delayHtml = '<span data-acym-tooltip="'.acym_escape(acym_translation('ACYM_CRON_TRIGGERED_DESC')).'">';
                        ob_start();
                        $delayTypeAuto->display(
                            'config[cron_frequency]',
                            $cronFrequency,
                            \AcyMailing\Types\DelayType::TYPE_MINUTES_HOURS,
                            '',
                            'auto_sending_input'
                        );
                        $delayHtml .= ob_get_clean().'</span>';
                        echo acym_escapeHtmlWithAllowedTags(
                            acym_translationSprintf(
                                'ACYM_SEND_X_BATCH_OF_X_EMAILS_EVERY_Y',
                                '<input '.$disabledBatch.' class="intext_input auto_sending_input" type="number" min="1" max="10" name="config[queue_batch_auto]" value="'.intval(
                                    $valueBatch
                                ).'" />',
                                '<input class="intext_input auto_sending_input" type="number" min="1" max="900" name="config[queue_nbmail_auto]" value="'.intval(
                                    $this->config->get('queue_nbmail_auto')
                                ).'" />',
                                $delayHtml
                            ),
                            [
                                'input' => [
                                    'disabled' => true,
                                    'class' => true,
                                    'type' => true,
                                    'min' => true,
                                    'max' => true,
                                    'name' => true,
                                    'value' => true,
                                    'id' => true,
                                ],
                                'span' => [
                                    'data-acym-tooltip' => true,
                                ],
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
                        echo '<span id="automatic_sending_speed_too_many_batches">';
                        acym_info(['textShownInTooltip' => 'ACYM_TOO_MANY_BATCHES', 'isWarning' => true]);
                        echo '</span>';
                        ?>
					</div>
					<div class="cell">
                        <?php
                        echo acym_escapeHtmlWithAllowedTags(
                            acym_translationSprintf(
                                'ACYM_WAIT_X_SECONDS_BETWEEN_MAILS',
                                '<input class="intext_input auto_sending_input" type="number" min="0" name="config[email_frequency]" value="'.acym_escape(
                                    $this->config->get(
                                        'email_frequency',
                                        0
                                    )
                                ).'" />'
                            ),
                            [
                                'input' => [
                                    'class' => true,
                                    'type' => true,
                                    'min' => true,
                                    'name' => true,
                                    'value' => true,
                                ],
                            ]
                        );
                        ?>
					</div>
				</div>
			</div>
			<div class="cell medium-3 automatic_only automatic_manual"></div>
			<div class="cell medium-9 automatic_only automatic_manual margin-bottom-2">
                <?php echo acym_escapeHtmlWithAllowedTags(
                    acym_translationSprintf('ACYM_SEND_X_EMAILS_PER_HOUR', '<span id="automatic_sending_speed_preview">280</span>'),
                    [
                        'span' => [
                            'id' => true,
                        ],
                    ]
                ); ?>
				<span id="automatic_sending_speed_too_much">
					<?php
                    acym_info(
                        [
                            'textShownInTooltip' => acym_translation('ACYM_ONE_SECOND_PER_EMAIL_WARNING'),
                        ]
                    );
                    ?>
				</span>
				<span id="automatic_sending_speed_no_wait">
					<?php
                    acym_info(
                        [
                            'textShownInTooltip' => acym_translation('ACYM_SEND_FASTER_DECREASE_WAIT'),
                        ]
                    );
                    ?>
				</span>
			</div>
			<div class="cell medium-3 automatic_only automatic_manual"><?php echo acym_escapeHtml(acym_translation('ACYM_SEND_RESTRICTIONS')); ?></div>
			<div class="cell medium-9 grid-x margin-y automatic_only automatic_manual acym_auto_send_time">
				<div class="cell">
                    <?php
                    $hoursFrom = acym_select(
                        $data['listHours'],
                        'config[queue_send_from_hour]',
                        $this->config->get('queue_send_from_hour', '00'),
                        ['class' => 'intext_select']
                    );
                    $minutesFrom = acym_select(
                        $data['listAllMinutes'],
                        'config[queue_send_from_minute]',
                        $this->config->get('queue_send_from_minute', '00'),
                        ['class' => 'intext_select']
                    );
                    $hoursTo = acym_select(
                        $data['listHours'],
                        'config[queue_send_to_hour]',
                        $this->config->get('queue_send_to_hour', '23'),
                        ['class' => 'intext_select']
                    );
                    $minutesTo = acym_select(
                        $data['listAllMinutes'],
                        'config[queue_send_to_minute]',
                        $this->config->get('queue_send_to_minute', '59'),
                        ['class' => 'intext_select']
                    );
                    echo acym_escapeHtmlWithAllowedTags(
                        acym_translationSprintf('ACYM_SEND_FROM_TO', $hoursFrom, $minutesFrom, $hoursTo, $minutesTo),
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
				<div class="cell grid-x">
					<div class="cell shrink margin-right-1">
                        <?php echo acym_escapeHtml(acym_translation('ACYM_DONT_SEND_WEEKEND')); ?>
					</div>
					<div class="cell shrink">
                        <?php acym_switch(['name' => 'config[queue_stop_weekend]', 'value' => $this->config->get('queue_stop_weekend', 0)]); ?>
					</div>
					<div class="cell auto"></div>
				</div>
			</div>
			<div class="cell medium-3 manual_only automatic_manual"><?php echo acym_escapeHtml(acym_translation('ACYM_MANUAL_SEND_PROCESS')); ?></div>
			<div class="cell medium-9 manual_only automatic_manual">
                <?php
                echo acym_escapeHtmlWithAllowedTags(
                    acym_translationSprintf(
                        'ACYM_SEND_X_EMAILS_WAIT_Y_SECONDS',
                        '<input class="intext_input" type="number" min="1" name="config[queue_nbmail]" value="'.intval($this->config->get('queue_nbmail')).'" />',
                        '<input class="intext_input" type="number" min="0" name="config[queue_pause]" value="'.intval($this->config->get('queue_pause')).'" />'
                    ),
                    [
                        'input' => [
                            'class' => true,
                            'type' => true,
                            'min' => true,
                            'name' => true,
                            'value' => true,
                        ],
                    ]
                );
                ?>
			</div>
			<div class="cell medium-3"><?php echo '<span>'.acym_escapeHtml(acym_translation('ACYM_MAX_NB_TRY')).'</span>';
                acym_info(['textShownInTooltip' => 'ACYM_MAX_NB_TRY_DESC']); ?></div>
			<div class="cell medium-9">
                <?php echo acym_escapeHtmlWithAllowedTags(
                    acym_translationSprintf(
                        'ACYM_CONFIG_TRY',
                        '<input class="intext_input" type="number" min="0" name="config[queue_try]" value="'.intval($this->config->get('queue_try')).'">'
                    ),
                    [
                        'input' => [
                            'class' => true,
                            'type' => true,
                            'min' => true,
                            'name' => true,
                            'value' => true,
                        ],
                    ]
                );

                $failaction = $data['failaction'];
                echo ' '.acym_escapeHtmlWithAllowedTags(
                        acym_translationSprintf('ACYM_CONFIG_TRY_ACTION', $failaction->display('maxtry', $this->config->get('bounce_action_maxtry', 'noaction'))),
                        [
                            'select' => [
                                'class' => true,
                                'style' => true,
                                'onchange' => true,
                                'name' => true,
                                'id' => true,
                            ],
                            'option' => [
                                'value' => true,
                                'selected' => true,
                            ],
                            'span' => [
                                'id' => true,
                                'style' => true,
                            ],
                        ]
                    ); ?>
			</div>
			<div class="cell medium-3">
                <?php
                echo acym_escapeHtml(acym_translation('ACYM_NUMBER_OF_DAYS_TO_CLEAN_QUEUE'));
                acym_info(
                    [
                        'textShownInTooltip' => 'ACYM_NUMBER_OF_DAYS_TO_CLEAN_QUEUE_DESC',
                    ]
                );
                ?>
			</div>
			<div class="cell medium-9 grid-x">
				<div class="cell medium-6 large-4 xlarge-3 xxlarge-2">
                    <?php
                    $queueDelete = $this->config->get('queue_delete_days', 0);
                    if (!acym_level(ACYM_ESSENTIAL)) {
                        $inputContent = '<input type="number" class="intext_input" disabled min="0" name="config[queue_delete_days]" value="'.acym_escape($queueDelete).'">';
                        acym_tooltip(
                            [
                                'hoveredText' => $inputContent,
                                'textShownInTooltip' => acym_translation('ACYM_PRO_ONLY'),
                            ]
                        );
                    } else {
                        echo '<input type="number" class="intext_input" min="0" name="config[queue_delete_days]" value="'.acym_escape($queueDelete).'">';
                    }
                    ?>
				</div>
			</div>
			<div class="cell medium-3">
                <?php
                echo acym_escapeHtml(acym_translation('ACYM_RECORD_STATS_EACH_EMAIL'));
                acym_info(['textShownInTooltip' => 'ACYM_RECORD_STATS_EACH_EMAIL_DESC']); ?>
			</div>
			<div class="cell medium-9 grid-x">
				<div class="cell medium-6 large-4 xlarge-3 xxlarge-2">
                    <?php acym_switch(['name' => 'config[queue_statistics_by_batch]', 'value' => $this->config->get('queue_statistics_by_batch', 1)]); ?>
				</div>
			</div>
            <?php if (acym_level(ACYM_ENTERPRISE)) { ?>
				<div class="cell medium-3">
                    <?php
                    echo acym_escapeHtml(acym_translation('ACYM_SET_FOLLOWUP_PRIORITY'));
                    acym_info(['textShownInTooltip' => 'ACYM_SET_FOLLOWUP_PRIORITY_DESC']); ?>
				</div>
				<div class="cell medium-9 grid-x">
					<div class="cell medium-6 large-4 xlarge-3 xxlarge-2">
                        <?php acym_switch(['name' => 'config[followup_max_priority]', 'value' => $this->config->get('followup_max_priority', 0)]); ?>
					</div>
				</div>
				<div class="cell medium-3 automatic_only automatic_manual">
                    <?php
                    echo acym_escapeHtml(acym_translation('ACYM_USE_DEDICATED_SENDING_PROCESS'));
                    acym_info(['textShownInTooltip' => 'ACYM_USE_DEDICATED_SENDING_PROCESS_DESC']); ?>
				</div>
				<div class="cell medium-9 grid-x automatic_only automatic_manual">
					<div class="cell medium-6 large-4 xlarge-3 xxlarge-2">
                        <?php
                        acym_switch([
                            'name' => 'config[dedicated_send_process]',
                            'value' => $this->config->get('dedicated_send_process', 0),
                        ]);
                        ?>
					</div>
				</div>
            <?php } ?>
		</div>
	</div>
<?php
if (acym_level(ACYM_ESSENTIAL)) {
    ?>
	<div class="acym__content acym_area padding-horizontal-2 acym__configuration__advanced">
		<div class="cell grid-x acym__configuration__showmore-head">
			<div class="acym__title acym__title__secondary cell auto margin-bottom-0"><?php echo acym_escapeHtml(acym_translation('ACYM_CONFIGURATION_ADVANCED')); ?></div>
			<div class="cell shrink">
                <?php acym_showMore('acym__configuration__queue__advanced__content'); ?>
			</div>
		</div>
		<div id="acym__configuration__queue__advanced__content" style="display:none;">
			<div class="margin-bottom-2">
				<div class="acym__title acym__title__secondary"><?php echo acym_escapeHtml(acym_translation('ACYM_REPORT')); ?></div>
				<div class="grid-x grid-margin-x margin-y">
					<div class="cell large-2 medium-3"><label for="cronsendreport"><?php echo acym_escapeHtml(acym_translation('ACYM_REPORT_SEND'));
                            acym_info(
                                ['textShownInTooltip' => 'ACYM_REPORT_SEND_DESC']
                            ); ?></label>
					</div>
					<div class="cell large-4 medium-9">
                        <?php
                        $cronReportTypes = [
                            CronHelper::SEND_REPORT_NO => 'ACYM_NO',
                            CronHelper::SEND_REPORT_EACH_TIME => 'ACYM_EACH_TIME',
                            CronHelper::SEND_REPORT_ONLY_ON_ACTION => 'ACYM_ONLY_ACTION',
                            CronHelper::SEND_REPORT_ONLY_ON_ERROR => 'ACYM_ONLY_SOMETHING_WRONG',
                        ];
                        acym_select(
                            $cronReportTypes,
                            'config[cron_sendreport]',
                            $this->config->get('cron_sendreport', CronHelper::SEND_REPORT_NO),
                            [
                                'class' => 'acym__select',
                                'acym-data-infinite' => '',
                            ],
                            'value',
                            'text',
                            'cronsendreport',
                            true,
                            true
                        );
                        ?>
					</div>
					<div class="cell large-2 medium-3">
						<label for="cron_sendto">
                            <?php
                            echo acym_escapeHtml(acym_translation('ACYM_REPORT_SEND_TO'));
                            acym_info(['textShownInTooltip' => 'ACYM_REPORT_SEND_TO_DESC']);
                            ?>
						</label>
					</div>
					<div class="cell large-4 medium-9">
                        <?php
                        $emails = [];
                        $receivers = $this->config->get('cron_sendto');
                        if (!empty($receivers)) {
                            $receivers = explode(',', $receivers);
                            foreach ($receivers as $value) {
                                $emails[$value] = $value;
                            }
                        }
                        acym_selectMultiple(
                            $emails,
                            'config[cron_sendto]',
                            $emails,
                            [
                                'id' => 'acym__configuration__cron__report--send-to',
                                'placeholder' => acym_translation('ACYM_YOUR_EMAIL'),
                            ],
                            'value',
                            'text',
                            true
                        );
                        ?>
					</div>
					<div class="cell large-2 medium-3">
						<label for="cronsavereport">
                            <?php
                            echo acym_escapeHtml(acym_translation('ACYM_REPORT_SAVE'));
                            acym_info(['textShownInTooltip' => 'ACYM_REPORT_SAVE_DESC']);
                            ?>
						</label>
					</div>
					<div class="cell large-4 medium-9">
                        <?php
                        acym_select(
                            $cronReportTypes,
                            'config[cron_savereport]',
                            (int)$this->config->get('cron_savereport', CronHelper::SEND_REPORT_ONLY_ON_ACTION),
                            [
                                'class' => 'acym__select',
                                'acym-data-infinite' => '',
                            ],
                            'value',
                            'text',
                            'cronsavereport',
                            true,
                            true
                        );
                        ?>
					</div>
                    <?php
                    $displaySavePath = true;
                    if ($displaySavePath) { ?>
						<div class="cell large-2 medium-3">
							<label for="cron_savepath"><?php echo acym_escapeHtml(acym_translation('ACYM_REPORT_SAVE_TO'));
                                acym_info(
                                    ['textShownInTooltip' => 'ACYM_REPORT_SAVE_TO_DESC']
                                ); ?></label>
						</div>
						<div class="cell large-4 medium-9">
							<input id="cron_savepath" type="text" name="config[cron_savepath]" value="<?php echo acym_escape($this->config->get('cron_savepath')); ?>">
						</div>
                    <?php } ?>
					<div class="cell">
						<button type="submit" data-task="deletereport" class="margin-next-1 button acy_button_submit">
                            <?php echo acym_escapeHtml(acym_translation('ACYM_REPORT_DELETE')); ?>
						</button>
                        <?php
                        acym_modal(
                            acym_translation('ACYM_REPORT_SEE'),
                            '',
                            null,
                            [],
                            [
                                'class' => 'button',
                                'data-ajax' => 'true',
                                'data-iframe' => '&ctrl=configuration&task=seereport',
                            ]
                        );
                        ?>
					</div>
				</div>
			</div>

			<div class="">
				<div class="acym__title acym__title__secondary"><?php echo acym_escapeHtml(acym_translation('ACYM_LAST_CRON')); ?></div>
				<div class="grid-x grid-margin-x margin-y">
					<div class="cell medium-2"><?php echo acym_escapeHtml(acym_translation('ACYM_LAST_RUN'));
                        acym_info(['textShownInTooltip' => 'ACYM_LAST_RUN_DESC']); ?></div>
					<div class="cell medium-9">
                        <?php
                        $cronLast = $this->config->get('cron_last', 0);
                        $diff = intval((time() - $cronLast) / 60);
                        if ($diff > 500) {
                            if (empty($cronLast)) {
                                echo acym_escapeHtml(acym_translation('ACYM_NEVER'));
                            } else {
                                echo acym_escapeHtml(acym_date($cronLast, acym_getDateTimeFormat()));
                                echo ' <span style="font-size:10px">('.acym_escapeHtml(
                                        acym_translationSprintf('ACYM_CURRENT_TIME', acym_date('now', acym_getDateTimeFormat()))
                                    ).')</span>';
                            }
                        } else {
                            echo acym_escapeHtml(acym_translationSprintf('ACYM_MINUTES_AGO', $diff));
                        }
                        ?>
					</div>
					<div class="cell medium-2"><?php echo acym_escapeHtml(acym_translation('ACYM_CRON_TRIGGERED_IP'));
                        acym_info(
                            ['textShownInTooltip' => 'ACYM_CRON_TRIGGERED_IP_DESC']
                        ); ?></div>
					<div class="cell medium-9">
                        <?php echo acym_escapeHtml($this->config->get('cron_fromip')); ?>
					</div>
					<div class="cell medium-2"><?php echo acym_escapeHtml(acym_translation('ACYM_REPORT'));
                        acym_info(['textShownInTooltip' => 'ACYM_REPORT_DESC']); ?></div>
					<div class="cell medium-9">
                        <?php echo nl2br(acym_escapeHtml($this->config->get('cron_report'))); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
    <?php
}
if (!acym_level(ACYM_ESSENTIAL)) {
    echo '<div class="acym_area">
            <div class="acym__title acym__title__secondary">'.acym_escapeHtml(acym_translation('ACYM_CRON')).'</div>';
    include acym_getView('configuration', 'upgrade_license');
    echo '</div>';
}
