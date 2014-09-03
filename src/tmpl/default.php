<?php
/**
 * @package   mod_ostimer
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="countdown<?php echo $params->get('moduleclass_sfx'); ?>">

<?php foreach ($list as $item) :?>
<?php if ($item->title) { ?>
	<span class="countdown_title"><?php echo $item->title; ?></span>
<?php } else {} ?>

<?php if ($item->displaydate) { ?>
	<span class="countdown_displaydate"><?php echo $item->displaydate; ?></span>
<?php } else {} ?>
<?php if (($params->get("show_zero_day") && $item->daycount ==0) || $item->daycount > 0):?>
    <span class="countdown_daycount" style="color:<?php echo $params->get('ev_color'); ?>;"><?php echo $item->daycount; ?></span>

    <?php if ($item->dney) { ?>
        <span class="countdown_dney"><?php echo $item->dney; ?></span>
    <?php } else {} ?>

    <?php echo $item->DetailCount; ?>

<?php else:?>
    <span class="countdown_hourcount" style="color:<?php echo $params->get('ev_color'); ?>;"><?php echo $item->DetailCount; ?></span>

<?php endif;?>

<?php if ($item->DetailLink) { ?>
	<span class="countdown_link"><?php echo $item->DetailLink; ?></span>
<?php } else {} ?>

<?php
if ($item->JS_enable == '1') {
	echo countdounJS($item->JS_month, $item->JS_day, $item->JS_year, $item->JS_hour, $item->JS_min, $item->JS_endtime, $item->JS_offset, $item->JS_trans_hr, $item->JS_trans_min, $item->JS_trans_sec, $item->timestamp);
} else {}
?>
<?php endforeach; ?>
</div>
