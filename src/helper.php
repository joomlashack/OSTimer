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

        $event         = $app->input->getString('time');
        $now           = $app->input->getString('date');
        $offsetMinutes = $app->input->getInt('offset', 0);
        $offsetSeconds = 0 - ($offsetMinutes * 60); // Javascript reports offset in inverse minutes

        // We're expecting a timezone designator in parentheses
        if (preg_match('/\(([^\)]*)\)/', $now, $match)) {
            $timezoneString = $match[1];
            if (!in_array($timezoneString, timezone_identifiers_list())) {
                $timezoneString = static::findTimezone($timezoneString, $offsetSeconds);
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

        if ($app->input->getInt('debug', 0)) {
            $debugData = array(
                $offsetMinutes,
                '<br>',
                $now,
                '<hr>',
                date('c (e)'),
                '<br>',
                gmdate('c (e)')
            );
            $dateString .= sprintf(
                '<div class="alert-error" style="text-align: left;">%s</div>',
                join('', $debugData)
            );
        }

        return $dateString;
    }

    /**
     * @param string $tzString
     * @param int    $offset
     *
     * @return string
     */
    protected static function findTimezone($tzString, $offset)
    {
        // Try to find Timezone from abbreviation
        $words = preg_split('/\s/', $tzString);

        $tzAbbreviation = '';
        foreach ($words as $word) {
            $tzAbbreviation .= strtolower($word[0]);
        }

        $tzList = timezone_abbreviations_list();
        if (isset($tzList[$tzAbbreviation])) {
            // Maybe we'll find it by abbreviation
            foreach ($tzList[$tzAbbreviation] as $tzData) {
                if ($tzData['offset'] == $offset) {
                    return $tzData['timezone_id'];
                }
            }
        }

        // Try to find it by offset
        $result = null;
        foreach ($tzList as $key => $codes) {
            foreach ($codes as $code) {
                if ($code['offset'] == $offset) {
                    $result = $code['timezone_id'];
                    if (stripos($result, $words[0])) {
                        // The first word is often geographic and may match the timezone ID
                        return $result;
                    }
                }
            }
        }

        // Return whatever lame match we might have found
        return $result;
    }
}
