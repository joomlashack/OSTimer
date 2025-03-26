<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2017-2025 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSTimer.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\OSTimer;

use Alledia\Framework\Factory;

defined('_JEXEC') or die();

class DateTime extends \DateTime
{
    /**
     * A custom method to use locale aware formats
     *
     * @param string $format
     *
     * @return string
     */
    public function localeFormat(string $format): string
    {
        $systemLocale = setlocale(LC_TIME, 0);
        $language     = Factory::getLanguage();

        setlocale(LC_TIME, $language->getLocale());

        $stringDate = strftime($format, strtotime(parent::format('Y-m-d H:i:s')));

        setlocale(LC_TIME, $systemLocale);

        return $stringDate;
    }

    /**
     * Get an array appropriate for use with javascript Date.UTC constructor
     *
     * @return array
     */
    public function getJSUTCArray(): array
    {
        $utcDate = clone $this;
        $utcDate->setTimezone(new \DateTimeZone('UTC'));

        return [
            $utcDate->format('Y'),
            $utcDate->format('m') - 1,
            $utcDate->format('d'),
            $utcDate->format('H'),
            $utcDate->format('i')
        ];
    }
}
