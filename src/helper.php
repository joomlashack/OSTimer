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

        $debugData = array($now, '<br>', $offsetMinutes, '<hr>');

        // We're expecting a timezone designator in parentheses
        if (preg_match('/\(([^\)]*)\)/', $now, $match)) {
            $userTimezone = static::createTimezone($match[1], $offsetSeconds);
        }

        $eventTime = new DateTime($event);
        if (!empty($userTimezone)) {
            $eventTime->setTimezone($userTimezone);
        }

        $format   = $app->input->getString('display');
        $tzFormat = $app->input->getString('tz');

        $dateString = $eventTime->localeFormat($format);
        if ($tzFormat) {
            $dateString .= ' ' . str_replace('_', ' ', $eventTime->format($tzFormat));
        }

        if ($app->input->getInt('debug', 0)) {
            $debugData = array_merge(
                $debugData,
                array(
                    $event . '<br>',
                    $eventTime->format('c (e)') . '<hr>',
                    date('c (e)') . '<br>' . gmdate('c (e)')
                )
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
     * @return DateTimeZone
     */
    protected static function createTimezone($tzString, $offset)
    {
        try {
            $now = new DateTime();

            try {
                if (in_array($tzString, timezone_identifiers_list())) {
                    $timezone = new DateTimeZone($tzString);
                    if ($timezone->getOffset($now) == $offset) {
                        return $timezone;
                    }
                }

            } catch (Exception $error) {
                // ignore it here
            }

            // Try to find Timezone from offset
            $tzList = timezone_abbreviations_list();
            foreach ($tzList as $key => $codes) {
                foreach ($codes as $tzData) {
                    $tzId     = $tzData['timezone_id'];
                    $tzOffset = $tzData['offset'];

                    if ($tzOffset == $offset) {
                        try {
                            $timezone = new DateTimeZone($tzId);
                            if ($timezone->getOffset($now) == $offset) {
                                return $timezone;
                            }

                        } catch (Exception $error) {
                            // ignore this and carry on
                        }
                    }

                }
            }

        } catch (Exception $error) {
            // Just bail!
        }

        return null;
    }
}
