<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

$event = $this->event;
?>
<div class="countdown<?php echo $this->moduleClassSfx; ?>">
    <?php if (isset($event->title)) : ?>
        <span class="countdown_title"><?php echo $event->title; ?></span>
    <?php endif; ?>

    <?php if (isset($event->date)) : ?>
        <span class="countdown_displaydate"><?php echo $event->date; ?></span>
    <?php endif; ?>

    <?php if (($this->showZeroDay && $event->days == 0) || $event->days > 0) : ?>
        <span class="countdown_daycount" style="color:<?php echo $this->eventColor; ?>;"><?php echo $event->days; ?></span>

        <?php if (isset($event->textDays)) : ?>
            <span class="countdown_dney"><?php echo $event->textDays; ?></span>
        <?php endif; ?>

        <?php echo $event->DetailCount; ?>
    <?php else: ?>
        <span class="countdown_hourcount" style="color:<?php echo $this->eventColor; ?>;"><?php echo $event->DetailCount; ?></span>
    <?php endif; ?>

    <?php if (isset($event->detailLink)) : ?>
        <span class="countdown_link"><?php echo $event->detailLink; ?></span>
    <?php endif; ?>

    <?php if ((bool) $event->JS_enable) : ?>
        <?php echo $this->printCountDounJS($event->JS_month, $event->JS_day, $event->JS_year, $event->JS_hour, $event->JS_min, $event->JS_endtime, $event->JS_offset, $event->JS_trans_hr, $event->JS_trans_min, $event->JS_trans_sec, $event->timestamp); ?>
    <?php endif; ?>
</div>
