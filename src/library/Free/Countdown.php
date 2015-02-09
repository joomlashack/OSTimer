<?php
/**
 * @package   OSTimer
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Alledia\OSTimer\Free;

defined('_JEXEC') or die();


abstract class Countdown
{
    public static function calculate($hour, $minutes, $seconds, $month, $day, $year, $timezone = 'UTC')
    {
        // Set the custom timezone
        $originalTimezone = date_default_timezone_get();
        date_default_timezone_set($timezone);

        // Get the time left
        $time = mktime($hour, $minutes, $seconds, $month, $day, $year);
        $timeLeft = $time - time();

        // Restore the original timezone
        date_default_timezone_set($originalTimezone);

        if ($timeLeft < 0) {
            return false;
        }

        $days    = floor($timeLeft / 86400);
        $timeLeft -= $days * 86400;

        $hours   = floor($timeLeft / 3600);
        $timeLeft -= $hours * 3600;

        $minutes = floor($timeLeft / 60);
        $timeLeft -= $minutes * 60;

        $seconds = $timeLeft;

        return array(
            'days'    => $days,
            'hours'   => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        );
    }
}
