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
    /**
     * @return string
     * @throws Exception
     */
    public static function getAjax()
    {
        require_once __DIR__ . '/library/DateTime.php';

        $app = JFactory::getApplication();

        $event  = $app->input->getString('time');
        $now    = $app->input->getString('date');
        $offset = 0 - ($app->input->getInt('offset', 0) * 60); // Javascript provides inverse minutes

        // We're expecting a timezone designator in parentheses
        if (preg_match('/\(([^\)]*)\)/', $now, $match)) {
            $timezoneString = $match[1];
            if (!in_array($timezoneString, timezone_identifiers_list())) {
                // Try to find Timezone from abbreviation
                $words = preg_split('/\s/', $timezoneString);

                $timezoneAbbreviation = '';
                foreach ($words as $word) {
                    $timezoneAbbreviation .= strtoupper($word[0]);
                }

                $timezoneString = timezone_name_from_abbr($timezoneAbbreviation, $offset);
                if (!$timezoneString) {
                    // Abbreviation didn't work either, just try offset
                    // Note we aren't attempting to determine DST
                    $timezoneString = timezone_name_from_abbr('', $offset);
                }
            }

            if ($timezoneString) {
                try {
                    $userTimezone = new DateTimeZone($timezoneString);

                } catch (Exception $e) {
                    // Nothing worked
                }
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
