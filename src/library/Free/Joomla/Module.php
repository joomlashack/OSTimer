<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Alledia\OSTimer\Free\Joomla;

defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\AbstractFlexibleModule;
use Alledia\Framework\Factory;
use Alledia\OSTimer\Free\Countdown;
use DateTime;
use DateTimeZone;
use JFactory;
use JUri;
use JText;
use stdClass;

class Module extends AbstractFlexibleModule
{
    /**
     * @var string
     */
    protected $moduleClassSfx = null;

    /**
     * @var int
     */
    protected $showZeroDay = null;

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
    protected static $timeStamp = 0;

    public function init()
    {
        $app    = JFactory::getApplication();
        $user   = JFactory::getUser();
        $params = $this->params;

        $eventDisplayTitle = $params->get('ev_dtitle', 1);
        $eventTitle        = $params->get('ev_tit');
        $eventDisplayDate  = $params->get('ev_ddate', 1);
        $eventDDaysLeft    = $params->get('ev_ddleft', 1);
        $eventDisplayHour  = $params->get('ev_dhour', 1);
        $eventDate         = preg_replace('/\s*\d+:\d+:\d+/', '', $params->get('ev_date', '01-11-2017'));
        $eventHour         = $params->get('ev_h', 0);
        $eventMinutes      = $params->get('ev_min', 0);
        $eventDisplayURL   = $params->get('ev_dlink', 1);
        $eventURLTitle     = $params->get('ev_ltitle', '');
        $eventURL          = $params->get('ev_l', '');
        $eventJs           = $params->get('ev_js', 1);
        $eventEndTime      = $params->get('ev_endtime', JText::_('MOD_OSTIMER_TIME_HAS_COME_DEFAULT'));
        $loadCSS           = $params->get('loadcss', 1);
        $transDays         = JText::_($params->get('ev_trans_days', JText::_('MOD_OSTIMER_TRANSLATE_DAYS_DEFAULT')));
        $transDay          = JText::_($params->get('ev_trans_day', JText::_('MOD_OSTIMER_TRANSLATE_DAY_DEFAULT')));
        $transHour         = JText::_($params->get('ev_trans_hr', JText::_('MOD_OSTIMER_TRANSLATE_HOURS_DEFAULT')));
        $transMin          = JText::_($params->get('ev_trans_min', JText::_('MOD_OSTIMER_TRANSLATE_MINUTES_DEFAULT')));
        $transSec          = JText::_($params->get('ev_trans_sec', JText::_('MOD_OSTIMER_TRANSLATE_SECONDS_DEFAULT')));
        $timezone          = $params->get('timezone', 'UTC');

        $eventTimezone = new DateTimeZone($timezone);
        $userTimezone  = new DateTimeZone($user->getParam('timezone', $app->get('offset')));

        $fullDate  = sprintf('%s %s:%s', $eventDate, $eventHour, $eventMinutes);
        $eventTime = new DateTime($fullDate, $eventTimezone);
        $eventTime->setTimezone($userTimezone);

        $this->moduleClassSfx = $params->get('moduleclass_sfx', '');
        $this->showZeroDay    = $params->get('show_zero_day', 1);
        $this->eventColor     = $params->get('ev_color', '#2B7CBE');

        $this->event = new stdClass;

        if ($eventDisplayDate) {
            $dateFormat = $params->get('ev_ddate_format', 1);
            $timeFormat = JText::_($params->get('ev_dtime_format', 'MOD_OSTIMER_TIME_FORMAT_12H_UPPER'));
            if ($dateFormat == '1') {
                // U.S. format
                $dateFormat = 'm.d.Y';
            } elseif ($dateFormat == '0') {
                // International format
                $dateFormat = 'd.m.Y';
            } else {
                $dateFormat = JText::_($dateFormat);
            }
            $this->event->date = $eventTime->format($dateFormat . ' ' . $timeFormat);
        }

        $now = new DateTime();
        $now->setTimezone($userTimezone);
        $timeLeft = $now->diff($eventTime);

        if ($eventDisplayTitle) {
            $this->event->title = $eventTitle;
        }


        if ($eventDDaysLeft == '1') {

            if ($timeLeft->format('%a') == 1) {
                // Print "Day" (singular)
                $this->event->textDays = $transDay;
            } else {
                // Print "Days" (plural)
                $this->event->textDays = $transDays;
            }
        }

        $this->event->days      = $timeLeft->format('%a');
        $this->event->JS_enable = false;

        static::$timeStamp++;
        $this->event->timestamp = static::$timeStamp;
        if (($eventDisplayHour == '1') && ($eventJs == '1')) {
            $this->event->DetailCount  = '<span id="clockJS' . static::$timeStamp . '"></span>';
            $this->event->JS_enable    = true;
            $this->event->JS_month     = $eventTime->format('m');
            $this->event->JS_day       = $eventTime->format('d');
            $this->event->JS_year      = $eventTime->format('Y');
            $this->event->JS_hour      = $eventTime->format('H');
            $this->event->JS_min       = $eventTime->format('i');
            $this->event->JS_endtime   = $eventEndTime;
            $this->event->JS_offset    = '';
            $this->event->JS_trans_hr  = $transHour;
            $this->event->JS_trans_min = $transMin;
            $this->event->JS_trans_sec = $transSec;

        } else {
            if (($eventDisplayHour == '1') && ($eventJs == '0')) {
                $this->event->DetailCount = join(
                    ' ',
                    array(
                        $timeLeft->format('%h'),
                        $transHour,
                        $timeLeft->format('%i'),
                        $transMin
                    )
                );

            } else {
                if ($timeLeft->format('%d') <= 0) {
                    $this->event->DetailCount = $eventEndTime;
                }
            }
        }

        if (($eventDisplayURL == '1') && $eventURL && $eventURLTitle) {
            $this->event->detailLink = \JHtml::_('link', $eventURL, $eventTitle, ' title="' . $eventURLTitle . '"');
        }

        if ((bool)$loadCSS) {
            $doc = Factory::getDocument();
            $doc->addStylesheet(JUri::base() . 'modules/mod_ostimer/tmpl/style.css');
        }

        parent::init();
    }

    public function printCountDounJS(
        $eventMonth,
        $eventDay,
        $eventYear,
        $eventHour,
        $eventMinutes,
        $eventEndTime,
        $transHour,
        $transMin,
        $transSec,
        $id
    ) {
        if ($eventHour >= '12') {
            $curHour = $eventHour - '12';
            $curSet  = 'PM';
        } else {
            $curHour = $eventHour;
            $curSet  = 'AM';
        }

        $targetDate = sprintf(
            '%s/%s/%s %s:%s %s',
            $eventMonth,
            $eventDay,
            $eventYear,
            $curHour,
            $eventMinutes,
            $curSet
        );

        $displayFormat = '%%H%% ' . $transHour . ' %%M%% ' . $transMin . ' %%S%% ' . $transSec;
        ?>
        <script language="JavaScript" type="text/javascript">
            (function(timerId) {
                var clockJS = document.getElementById('clockJS' + timerId);
                if (!clockJS) {
                    console.log(timerId + ' Not found');
                    return;
                }

                var TargetDate    = '<?php echo $targetDate; ?>',
                    CountActive   = true,
                    CountStepper  = -1,
                    LeadingZero   = true,
                    DisplayFormat = '<?php echo addslashes($displayFormat); ?>',
                    FinishMessage = '<?php echo addslashes($eventEndTime); ?>',
                    clockDayJS    = document.getElementById('clockDayJS' + timerId);

                var calcage = function(secs, num1, num2, doublezero) {
                    // The default value for doublezero was removed from the method
                    // signature to avoid issues with IE
                    if (doublezero !== false) {
                        doublezero = true;
                    }

                    s = ((Math.floor(secs / num1)) % num2).toString();
                    if (LeadingZero && s.length < 2 && doublezero) {
                        s = "0" + s;
                    }

                    return s;
                };

                var CountBack = function(secs) {
                    if (secs < 0) {
                        clockJS.innerHTML = FinishMessage;

                        return;
                    }

                    if (calcage(secs, 86400, 100000) === 0 && calcage(secs, 3600, 24) === 0) {
                        DisplayFormat = "%%M%% <?php echo $transMin; ?> %%S%% <?php echo $transSec; ?>";
                    }

                    if (calcage(secs, 86400, 100000) === 0
                        && calcage(secs, 3600, 24) === 0
                        && calcage(secs, 60, 60) === 0
                    ) {
                        DisplayFormat = "%%S%% <?php echo $transSec; ?>";
                    }

                    if (clockDayJS && secs > 0) {
                        CountBackDays(secs);
                    }

                    var DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs, 86400, 100000));
                    DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs, 3600, 24));
                    DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs, 60, 60));
                    DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs, 1, 60));

                    clockJS.innerHTML = DisplayStr;
                };

                var CountBackDays = function(secs) {
                    WaitingDays = calcage(secs, 86400, secs, false);
                    clockDayJS.innerHTML = WaitingDays;
                };

                CountStepper = Math.ceil(CountStepper);

                if (CountStepper === 0) {
                    CountActive = false;
                }

                var SetTimeOutPeriod = (Math.abs(CountStepper) - 1) * 1000 + 990,
                    dthen            = new Date(TargetDate),
                    dnow             = new Date();

                var ddiff = null;
                if (CountStepper > 0) {
                    ddiff = new Date(dnow - dthen);
                } else {
                    ddiff = new Date(dthen - dnow);
                }

                var secs = Math.floor(ddiff.valueOf() / 1000);
                if (CountActive) {
                    var repeatFunc = function()
                    {
                        secs += CountStepper;
                        CountBack(secs);
                        setTimeout(repeatFunc, SetTimeOutPeriod);
                    };
                    repeatFunc();
                } else {
                    CountBack(secs);
                }
            })(<?php echo (int)$id; ?>);
        </script>
        <?php
    }
}
