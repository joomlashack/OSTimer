<?php
/**
 * @package    OSTimer
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2017 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
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

use Alledia\OSTimer\DateTime;

defined('_JEXEC') or die();

abstract class ModOstimerHelper
{
    public static function getAjax()
    {
        require_once __DIR__ . '/library/DateTime.php';

        $app = JFactory::getApplication();

        $event  = $app->input->getString('time');
        $now    = $app->input->getString('date');
        $offset = 1 - ($app->input->getInt('offset', 0) * 60); // Javascript provides inverse minutes

        // We're expecting a timezone designator in parentheses
        if (preg_match('/\(([^\)]*)\)/', $now, $match)) {
            $timezoneString = $match[1];
            if (strlen($timezoneString) > 3 && !in_array($timezoneString, timezone_identifiers_list())) {
                //
                $words          = preg_split('/\s/', $timezoneString);
                $timezoneString = '';
                foreach ($words as $word) {
                    $timezoneString .= strtoupper($word[0]);
                }
            }

            if (strlen($timezoneString) === 3) {
                $timezoneString = timezone_name_from_abbr($timezoneString, $offset);
            }

            try {
                $userTimezone = new DateTimeZone($timezoneString);

            } catch (Exception $e) {
                // Nothing worked
            }
        }

        if (empty($userTimezone || !$userTimezone instanceof DateTimeZone)) {
            try {
                // Nothing worked. let's get the first available for the offset
                $timezoneString = timezone_name_from_abbr('', $offset);
                $userTimezone   = new DateTimeZone($timezoneString);

            } catch (Exception $e) {
                // shoot! Display will have to be in event timezone
            }
        }

        $eventTime = new DateTime($event);
        if (!empty($userTimezone) && $userTimezone instanceof DateTimeZone) {
            $eventTime->setTimezone($userTimezone);
        }

        $format   = $app->input->getString('display');
        $tzFormat = $app->input->getString('tz');

        $dateString = $eventTime->localeFormat($format);
        if ($tzFormat) {
            $dateString .= ' ' . str_replace('_', ' ', $eventTime->format($tzFormat));
        }

        return $dateString;
    }
}
