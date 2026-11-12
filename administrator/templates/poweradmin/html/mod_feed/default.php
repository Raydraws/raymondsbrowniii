<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_feed
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Check if feed URL has been set
if (empty($rssurl)) {
    echo '<div>' . Text::_('MOD_FEED_ERR_NO_URL') . '</div>';

    return;
}

if (!empty($feed) && is_string($feed)) {
    echo $feed;
} else {
    $lang      = $app->getLanguage();
    $myrtl     = $params->get('rssrtl', 0);
    $direction = ' ';

    if ($lang->isRtl() && $myrtl == 0) {
        $direction = ' redirect-rtl';
    } elseif ($lang->isRtl() && $myrtl == 1) {
        // Feed description
        $direction = ' redirect-ltr';
    } elseif ($lang->isRtl() && $myrtl == 2) {
        $direction = ' redirect-rtl';
    } elseif ($myrtl == 0) {
        $direction = ' redirect-ltr';
    } elseif ($myrtl == 1) {
        $direction = ' redirect-ltr';
    } elseif ($myrtl == 2) {
        $direction = ' redirect-rtl';
    }

    if ($feed != false) :
        ?>
        <div class="feed" style="direction: <?php echo $rssrtl ? 'rtl' : 'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' : 'left'; ?>;">
        <?php

        // Feed title
        if (!is_null($feed->title) && $params->get('rsstitle', 1)) : ?>
            <h2 class="<?php echo $direction; ?>">
                <a href="<?php echo str_replace('&', '&amp;', $rssurl); ?>" target="_blank">
                <?php echo $feed->title; ?></a>
            </h2>
        <?php endif;
        // Feed date
        if ($params->get('rssdate', 1) && ($feed->publishedDate !== null)) : ?>
            <h3>
            <?php echo HTMLHelper::_('date', $feed->publishedDate, Text::_('DATE_FORMAT_LC3')); ?>
            </h3>
        <?php endif; ?>

        <?php // Feed description ?>
        <?php if ($params->get('rssdesc', 1)) : ?>
            <?php echo $feed->description; ?>
        <?php endif; ?>

        <?php // Feed image ?>
        <?php if ($params->get('rssimage', 1) && $feed->image) : ?>
            <img class="w-100" src="<?php echo $feed->image->uri; ?>" alt="<?php echo $feed->image->title; ?>"/>
        <?php endif; ?>


        <?php // Show items ?>
        <?php if (!empty($feed)) : ?>
        <div class="newsfeed">
            <?php for ($i = 0; $i < $params->get('rssitems', 3); $i++) :
                if (!$feed->offsetExists($i)) :
                    break;
                endif;
                $uri  = $feed[$i]->uri || !$feed[$i]->isPermaLink ? trim($feed[$i]->uri) : trim($feed[$i]->guid);
                $uri  = !$uri || stripos($uri, 'http') !== 0 ? $rssurl : $uri;
                $text = $feed[$i]->content !== '' ? trim($feed[$i]->content) : '';
                
                // Parse date for event-item format
                $dayNumber = '';
                $monthYear = '';
                if ($feed[$i]->publishedDate !== null) {
                    $dateObj = $feed[$i]->publishedDate;
                    // Joomla Date object has format method
                    if (is_object($dateObj) && method_exists($dateObj, 'format')) {
                        $dayNumber = $dateObj->format('j'); // Day without leading zeros (1-31)
                        $monthYear = $dateObj->format('M Y'); // Month abbreviation and year (e.g., "Nov 2025")
                    } elseif (is_object($dateObj) && method_exists($dateObj, 'toFormat')) {
                        // Alternative Date method
                        $dayNumber = $dateObj->toFormat('j');
                        $monthYear = $dateObj->toFormat('M Y');
                    } else {
                        // Fallback: use HTMLHelper to format, then parse
                        $formattedDate = HTMLHelper::_('date', $dateObj, 'Y-m-d');
                        if ($formattedDate) {
                            $timestamp = strtotime($formattedDate);
                            if ($timestamp) {
                                $dayNumber = date('j', $timestamp);
                                $monthYear = date('M Y', $timestamp);
                            }
                        }
                    }
                }
                ?>
                <div class="j-card-event-item">
                    <?php if ($feed[$i]->publishedDate !== null) : ?>
                        <time>
                            <span><?php echo htmlspecialchars($dayNumber, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="month-text"><?php echo htmlspecialchars($monthYear, ENT_QUOTES, 'UTF-8'); ?></span>
                        </time>
                    <?php endif; ?>
                    <div class="j-card-event-content">
                        <?php if (!empty($uri)) : ?>
                            <h5 class="feed-link">
                                <a href="<?php echo htmlspecialchars($uri, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <?php echo htmlspecialchars(trim($feed[$i]->title), ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h5>
                        <?php else : ?>
                            <h5 class="feed-link"><?php echo htmlspecialchars(trim($feed[$i]->title), ENT_QUOTES, 'UTF-8'); ?></h5>
                        <?php endif; ?>

                        <?php if ($params->get('rssitemdesc', 1) && $text !== '') : ?>
                            <div class="feed-item-description">
                            <?php
                                // Strip the images.
                                $text = OutputFilter::stripImages($text);
                                $text = HTMLHelper::_('string.truncate', $text, $params->get('word_count', 0), true, false);
                                echo str_replace('&apos;', "'", $text);
                            ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif;
}

