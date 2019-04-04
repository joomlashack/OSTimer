<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSTimer.
 *
 * OSTimer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSTimer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSTimer.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

$event = $this->event;

?>
<div class="<?php echo 'countdown' . $this->moduleClassSfx; ?> ostimer-wrapper">
    <?php
    if ($event->image) :
		?>
		<div class="countdown_image">
			<?php
			echo JHtml::_(
				'image',
				$event->image,
				htmlspecialchars($event->title)
			);
			?>
		</div>
    <?php
    endif;

    if (!empty($event->title)) :
        ?>
        <span class="countdown_title"><?php echo $event->title; ?></span>
    <?php
    endif;

    if ($event->date) :
        ?>
        <span class="countdown_displaydate"><?php echo $event->date; ?></span>
    <?php
    endif;

    if (($this->showZeroDay && $event->days == 0) || $event->days > 0) :
        ?>
        <span class="countdown_daycount" style="<?php echo 'color: ' . $this->eventColor . ';'; ?>">
            <span id="<?php echo 'clockDayJS' . static::$instance ?>">
                <?php echo JText::plural('MOD_OSTIMER_TRANSLATE_DAY', $event->days); ?>
            </span>
        </span>

        <?php
        echo $event->DetailCount;
    else :
        ?>
        <span class="countdown_hourcount" style="<?php echo 'color: ' . $this->eventColor . ';'; ?>">
        <?php echo $event->DetailCount; ?>
        </span>
    <?php
    endif;

    if (!empty($event->detailLink)) :
        ?>
        <span class="countdown_link"><?php echo $event->detailLink; ?></span>
    <?php
    endif;

    if ($event->JS_enable) :
        $this->printCountDounJS();
    endif;
    ?>
</div>
