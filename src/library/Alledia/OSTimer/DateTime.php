<?php

/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2017-2026 Joomlashack.com. All rights reserved
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
use IntlDateFormatter;

defined('_JEXEC') or die();

class DateTime extends \DateTime
{
    /**
     * @var string[]
     */
    protected array $icuFormat = [
        '%a' => 'E',
        '%A' => 'EEEE',
        '%d' => 'dd',
        '%e' => 'd',
        '%j' => 'D',
        '%u' => 'e',// not 100% correct
        '%w' => 'c',// not 100% correct
        '%U' => 'w',
        '%V' => 'ww',// not 100% correct
        '%W' => 'w',// not 100% correct
        '%b' => 'MMM',
        '%B' => 'MMMM',
        '%h' => 'MMM',// alias of %b
        '%m' => 'MM',
        '%C' => 'yy',// no replace for this
        '%g' => 'yy',// no replace for this
        '%G' => 'Y',// not 100% correct
        '%y' => 'yy',
        '%Y' => 'yyyy',
        '%H' => 'HH',
        '%k' => 'H',
        '%I' => 'hh',
        '%l' => 'h',
        '%M' => 'mm',
        '%p' => 'a',
        '%P' => 'a',// no replace for this
        '%r' => 'hh:mm:ss a',
        '%R' => 'HH:mm',
        '%S' => 'ss',
        '%T' => 'HH:mm:ss',
        '%X' => 'HH:mm:ss',// no replace for this
        '%z' => 'ZZ',
        '%Z' => 'v',// no replace for this
        '%c' => 'd/M/YYYY HH:mm:ss',// Buddhist era not converted.
        '%D' => 'MM/dd/yy',
        '%F' => 'yyyy-MM-dd',
        '%s' => '',// no replace for this
        '%x' => 'd/MM/yyyy',// Buddhist era not converted.
        '%n' => "\n",
        '%t' => "\t",
        '%%' => '%',
    ];

    /**
     * A custom method to use locale aware formats
     *
     * @param string $format
     *
     * @return string
     */
    public function localeFormat(string $format): string
    {
        $time   = strtotime(parent::format('Y-m-d H:i:s'));
        $locale = Factory::getApplication()->getLanguage()->getTag();

        if (class_exists(IntlDateFormatter::class)) {
            $pattern = $format;
            foreach ($this->icuFormat as $strfFormat => $icuFormat) {
                $pattern = str_replace($strfFormat, $icuFormat, $pattern);
            }

            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE);
            $formatter->setPattern($pattern);

            return $formatter->format($time);

        }

        // @TODO: can we drop this legacy method entirely?
        $systemLocale = setlocale(LC_TIME, 0);

        setlocale(LC_TIME, $locale);

        $stringDate = strftime($format, $time);

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
            $utcDate->format('i'),
        ];
    }
}
