<?php
\defined('_JEXEC') or die;
if (empty($events)) { return; } // nothing upcoming: the card disappears
$link = $params->get('calendar_link', '');
?>
<div class="rsb-card rsb-homecard rsb-events">
  <div class="rsb-homecard__head"><span><?php echo htmlspecialchars($params->get('heading', 'COMING UP'), ENT_QUOTES, 'UTF-8'); ?></span><?php if ($link) : ?><a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">Calendar</a><?php endif; ?></div>
  <ul class="rsb-events__list">
  <?php foreach ($events as $e) : ?>
    <li>
      <span class="rsb-events__date"><span><?php echo strtoupper($e->start->format('M')); ?></span><strong><?php echo $e->start->format('j'); ?></strong></span>
      <span class="rsb-events__info">
        <?php if ($e->url) : ?><a href="<?php echo htmlspecialchars($e->url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($e->title, ENT_QUOTES, 'UTF-8'); ?></a><?php else : ?><strong><?php echo htmlspecialchars($e->title, ENT_QUOTES, 'UTF-8'); ?></strong><?php endif; ?>
        <span><?php echo htmlspecialchars(trim(($e->location ? $e->location . ' · ' : '') . ($e->allDay ? $e->start->format('l') : $e->start->format('D g:i A'))), ENT_QUOTES, 'UTF-8'); ?></span>
      </span>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
