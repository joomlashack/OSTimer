<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2025 Joomlashack.com. All rights reserved
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
use Alledia\Framework\Joomla\Extension\AbstractFlexibleModule;
use DateTimeZone;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

abstract class AbstractModule extends AbstractFlexibleModule
{
    /**
     * @var string
     */
    protected $moduleClassSfx = null;

    /**
     * @var bool
     * @deprecated v3.0.0: Use $showDay
     */
    protected $showZeroDay = null;

    /**
     * @var bool
     */
    protected $showDay = null;

    /**
     * @var string
     */
    protected $eventColor = null;

    /**
     * @var object
     */
    protected $event = null;

    /**
     * @var int
     */
    protected static $instance = 0;

    /**
     * @param object $module
     *
     * @return AbstractModule
     */
    public static function getInstance(object $module): ?self
    {
        $nameSpace = '\\Alledia\\OSTimer\\%s\\Joomla\\Module';

        $proClass = sprintf($nameSpace, 'Pro');
        if (class_exists($proClass)) {
            return new $proClass('OSTimer', $module);
        }

        $freeClass = sprintf($nameSpace, 'Free');
        if (class_exists($freeClass)) {
            return new $freeClass('OSTimer', $module);
        }

        return null;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function init()
    {
        $params = $this->params;

        $eventImage        = $params->get('ev_image');
        $eventDisplayTitle = $params->get('ev_dtitle', 1);
        $eventTitle        = $params->get('ev_tit');
        $eventDisplayDate  = (bool)$params->get('ev_ddate', true);
        $eventDisplayHour  = (bool)$params->get('ev_dhour', true);
        $eventDate         = preg_replace('/\s*\d+:\d+:\d+/', '', $params->get('ev_date') ?: date('Y-01-01'));
        $eventHour         = (int)$params->get('ev_h', 0);
        $eventMinutes      = (int)$params->get('ev_min', 0);
        $eventDisplayURL   = (bool)$params->get('ev_dlink', true);
        $eventTargetURL    = $params->get('ev_tlink', '_self');
        $eventURL          = $params->get('ev_l', '');
        $eventURLTitle     = $params->get('ev_ltitle') ?: $eventURL;
        $eventJs           = (bool)$params->get('ev_js', true);
        $eventEndTime      = $params->get('ev_endtime', Text::_('MOD_OSTIMER_TIME_HAS_COME_DEFAULT'));

        $loadCSS  = $params->get('loadcss', 1);
        $timezone = $params->get('timezone', 'UTC');

        $fullDate      = sprintf('%s %02d:%02d:00', $eventDate, $eventHour, $eventMinutes);
        $eventTimezone = new DateTimeZone($timezone);
        $eventTime     = new DateTime($fullDate, $eventTimezone);
        $now           = new DateTime('now', $eventTimezone);

        if (!$this->checkEventDisplay($eventTime, $now)) {
            return;
        }

        $this->moduleClassSfx = $params->get('moduleclass_sfx', '');
        $this->eventColor     = $params->get('ev_color', '#2B7CBE');


        static::$instance++;

        $timeLeft = $now->diff($eventTime);

        $this->event = (object)[
            'instanceId'  => static::$instance,
            'datetime'    => $eventTime,
            'date'        => null,
            'seconds'     => $eventTime->getTimestamp() - $now->getTimestamp(),
            'title'       => $eventDisplayTitle ? $eventTitle : null,
            'textEnd'     => $eventEndTime,
            'days'        => $timeLeft->format('%r%a'),
            'expired'     => $timeLeft->invert == 1,
            'JS_enable'   => $eventDisplayHour && $eventJs,
            'DetailCount' => null,
            'detailLink'  => null,
            'image'       => $eventImage
        ];

        $this->showDay = $this->showZeroDay = (
            $this->event->expired == false
            && (($params->get('show_zero_day', true) && $this->event->days == 0) || $this->event->days > 0)
        );

        if ($eventDisplayDate) {
            $dateFormat     = Text::_($params->get('ev_ddate_format', 'MOD_OSTIMER_DATE_FORMAT_US'));
            $timeFormat     = Text::_($params->get('ev_dtime_format'));
            $timezoneFormat = $params->get('show_timezone', '');

            $userTime = $params->get('ev_user');
            if ($userTime || $eventJs) {
                HTMLHelper::_('jquery.framework');
            }

            if ($userTime) {
                $this->event->date = Text::_('MOD_OSTIMER_AJAX_LOADING_USER_TZ');

                $ajaxData = json_encode([
                    'option'  => 'com_ajax',
                    'module'  => 'ostimer',
                    'format'  => 'raw',
                    'time'    => $this->event->datetime->format('Y-m-d H:i e'),
                    'display' => trim($dateFormat . ' ' . $timeFormat),
                    'tz'      => $timezoneFormat,
                    'date'    => null,
                    'offset'  => null,
                    'debug'   => $this->params->get('debug', 0)
                ]);
                $jScript  = <<<JSCRIPT
jQuery(document).ready(function() {
    var ajaxData = {$ajaxData},
        now = new Date();
    ajaxData.date = now.toString();
    ajaxData.offset = now.getTimezoneOffset();
    if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
        ajaxData.tzid = Intl.DateTimeFormat().resolvedOptions().timeZone
    }

    jQuery('.ostimer-mod-{$this->id} .countdown_displaydate').load('index.php', ajaxData);
});
JSCRIPT;
                Factory::getDocument()->addScriptDeclaration($jScript);

            } else {
                $this->event->date = $eventTime->localeFormat(trim($dateFormat . ' ' . $timeFormat));
                if ($timeFormat && $timezoneFormat) {
                    $this->event->date .= ' ' . str_replace('_', ' ', $eventTime->format($timezoneFormat));
                }
            }
        }

        if ($this->event->expired) {
            $this->event->DetailCount = $eventEndTime;

        } elseif ($this->event->JS_enable) {
            $this->event->DetailCount = '<span id="clockJS' . static::$instance . '"></span>';

        } elseif ($eventDisplayHour && !$eventJs) {
            $this->event->DetailCount = join(
                ' ',
                [
                    Text::plural('MOD_OSTIMER_TRANSLATE_HOUR', $timeLeft->h),
                    Text::plural('MOD_OSTIMER_TRANSLATE_MINUTE', $timeLeft->i)
                ]
            );

        }

        if ($eventDisplayURL && $eventURL && $eventURLTitle) {
            $this->event->detailLink = HTMLHelper::_(
                'link',
                $eventURL,
                $eventURLTitle,
                [
                    'title'  => $eventURLTitle,
                    'target' => $eventTargetURL
                ]
            );
        }

        if ($loadCSS) {
            HTMLHelper::_('stylesheet', 'mod_ostimer/style.min.css', ['relative' => true]);
        }

        parent::init();
    }

    /**
     * @return string
     */
    protected function getModuleClasses(): string
    {
        $layout = explode(':', $this->params->get('layout', 'default'));
        $template = array_pop($layout);

        return join(' ', [
            'countdown' . $this->moduleClassSfx,
            'ostimer-wrapper',
            'ostimer-' . $template,
            'ostimer-mod-' . $this->id
        ]);
    }

    /**
     * @throws \Exception
     * @deprecated v3.0.0: Use printCountDownJS()
     */
    protected function printCountDounJS()
    {
        Factory::getApplication()->enqueueMessage(
            '[OSTimer] printCountDounJS() is deprecated. Use printCountDownJS() in your template override',
            'warning'
        );

        $this->printCountDownJS();
    }

    /**
     * Setup and display all js display code
     *
     * @return void
     */
    protected function printCountDownJS()
    {
        if (!$this->event->JS_enable) {
            return;
        }

        $transText = [
            'day'    => [
                Text::_('MOD_OSTIMER_TRANSLATE_DAY'),
                Text::_('MOD_OSTIMER_TRANSLATE_DAY_1')
            ],
            'hour'   => [
                Text::_('MOD_OSTIMER_TRANSLATE_HOUR'),
                Text::_('MOD_OSTIMER_TRANSLATE_HOUR_1')
            ],
            'minute' => [
                Text::_('MOD_OSTIMER_TRANSLATE_MINUTE'),
                Text::_('MOD_OSTIMER_TRANSLATE_MINUTE_1')
            ],
            'second' => [
                Text::_('MOD_OSTIMER_TRANSLATE_SECOND'),
                Text::_('MOD_OSTIMER_TRANSLATE_SECOND_1')
            ]
        ];
        ?>
        <script>
            ;(function(timerId) {
                let clockJS = document.getElementById('clockJS' + timerId);
                if (!clockJS) {
                    console.log(timerId + ' Not found');

                    return;
                }

                let secondsLeft   = <?php echo $this->event->seconds; ?>,
                    countActive   = true,
                    countStepper  = -1,
                    showZeroDay   = <?php echo $this->showZeroDay ? 'true' : 'false'; ?>,
                    transText     = <?php echo json_encode($transText); ?>,
                    finishMessage = '<?php echo addslashes($this->event->textEnd); ?>',
                    clockDayJS    = document.getElementById('clockDayJS' + timerId);

                let calcAge = function(timeLeft, num1, num2, doublezero) {
                    if (doublezero !== false) {
                        doublezero = true;
                    }

                    let seconds = ((Math.floor(timeLeft / num1)) % num2).toString();
                    if (seconds.length < 2 && doublezero) {
                        seconds = '0' + seconds;
                    }

                    return seconds;
                };

                let pluralize = function(strings, count, showZero) {
                    if (count > 0 || showZero) {
                        let string = +count === 1 ? strings[1] : strings[0];

                        return string.indexOf('%s') < 0 ?
                            count + ' ' + string :
                            string.replace('%s', count);
                    }

                    return '';
                };

                let formatTime = function(timeLeft) {
                    let displayArray = [
                        pluralize(transText.hour, calcAge(timeLeft, 3600, 24, false)),
                        pluralize(transText.minute, calcAge(timeLeft, 60, 60)),
                        pluralize(transText.second, calcAge(timeLeft, 1, 60), true)
                    ];

                    return displayArray.join(' ');
                };

                let countBack = function(timeLeft) {
                    if (timeLeft < 0) {
                        clockJS.innerHTML = finishMessage;

                        return;
                    }

                    clockJS.innerHTML = formatTime(timeLeft);

                    if (clockDayJS && timeLeft > 0) {
                        clockDayJS.innerHTML = pluralize(
                            transText.day,
                            calcAge(timeLeft, 86400, timeLeft, false),
                            showZeroDay
                        );
                    }
                };

                countStepper = Math.ceil(countStepper);

                if (countStepper === 0) {
                    countActive = false;
                }

                let SetTimeOutPeriod = (Math.abs(countStepper) - 1) * 1000 + 990;

                if (countActive) {
                    let repeatFunc = function() {
                        secondsLeft += countStepper;
                        countBack(secondsLeft);
                        setTimeout(repeatFunc, SetTimeOutPeriod);
                    };
                    repeatFunc();

                } else {
                    countBack(secondsLeft);
                }
            })(<?php echo $this->event->instanceId; ?>);
        </script>
        <?php
    }

    /**
     * Final check to determine if this event should be displayed at all
     *
     * @param DateTime $eventTime
     * @param DateTime $now
     *
     * @return bool
     */
    protected function checkEventDisplay(DateTime $eventTime, DateTime $now): bool
    {
        if ($now < $eventTime) {
            return true;
        }

        return (bool)$this->params->get('show_after_expired', 1);
    }
}
