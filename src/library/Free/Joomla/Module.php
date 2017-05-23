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
            $this->event->textDays = $transDays;
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
        //echo $curHour.'<br />';

        ?>
        <script language="JavaScript" type="text/javascript">
            TargetDate<?php echo($id);?>   = "<?php echo $eventMonth; ?>/<?php echo $eventDay; ?>/<?php echo $eventYear; ?> <?php echo $curHour; ?>:<?php echo $eventMinutes; ?> <?php echo $curSet; ?>";
            CountActive<?php echo($id);?>  = true;
            CountStepper<?php echo($id);?> = -1;
            LeadingZero<?php echo($id);?>  = true;

            DisplayFormat<?php echo($id);?> = "%%H%% <?php echo $transHour; ?> %%M%% <?php echo $transMin; ?> %%S%% <?php echo $transSec; ?>";
            FinishMessage<?php echo($id);?> = "<?php echo $eventEndTime; ?>";

            function calcage<?php echo($id);?>(secs, num1, num2, doublezero = true) {
                s = ((Math.floor(secs / num1)) % num2).toString();
                if (LeadingZero<?php echo($id);?> && s.length < 2 && doublezero) {
                    s = "0" + s;
                }

                return s;
            }

            function CountBack<?php echo($id);?>(secs) {
                if (secs < 0) {
                    document.getElementById("clockJS<?php echo($id);?>").innerHTML = FinishMessage<?php echo($id);?>;

                    return;
                }

                if (calcage<?php echo($id);?>(secs, 86400, 100000) == 0 && calcage<?php echo($id);?>(secs, 3600, 24) == 0) {
                    DisplayFormat<?php echo($id);?> = "%%M%% <?php echo $transMin; ?> %%S%% <?php echo $transSec; ?>";
                }

                if (calcage<?php echo($id);?>(secs, 86400, 100000) == 0 && calcage<?php echo($id);?>(secs, 3600, 24) == 0 && calcage<?php echo($id);?>(secs, 60, 60) == 0) {
                    DisplayFormat<?php echo($id);?> = "%%S%% <?php echo $transSec; ?>";
                }

                if(document.getElementById("clockDayJS<?php echo($id);?>")) {
                    CountBackDays<?php echo($id);?>(secs);
                }

                DisplayStr = DisplayFormat<?php echo($id);?>.replace(/%%D%%/g, calcage<?php echo($id);?>(secs, 86400, 100000));
                DisplayStr = DisplayStr.replace(/%%H%%/g, calcage<?php echo($id);?>(secs, 3600, 24));
                DisplayStr = DisplayStr.replace(/%%M%%/g, calcage<?php echo($id);?>(secs, 60, 60));
                DisplayStr = DisplayStr.replace(/%%S%%/g, calcage<?php echo($id);?>(secs, 1, 60));

                document.getElementById("clockJS<?php echo($id);?>").innerHTML = DisplayStr;

                if (CountActive<?php echo($id);?>) {
                    setTimeout("CountBack<?php echo($id);?>(" + (secs + CountStepper<?php echo($id);?>) + ")", SetTimeOutPeriod<?php echo($id);?>);
                }
            }

            function CountBackDays<?php echo($id);?>(secs) {
                WaitingDays = calcage<?php echo($id);?>(secs, 86400, secs, false);
                document.getElementById("clockDayJS<?php echo($id);?>").innerHTML = WaitingDays;

                return;
            }

            CountStepper<?php echo($id);?> = Math.ceil(CountStepper<?php echo($id);?>);

            if (CountStepper<?php echo($id);?> == 0) {
                CountActive<?php echo($id);?> = false;
            }

            var SetTimeOutPeriod<?php echo($id);?> = (Math.abs(CountStepper<?php echo($id);?>) - 1) * 1000 + 990;
            var dthen<?php echo($id);?>            = new Date(TargetDate<?php echo($id);?>);
            var dnow<?php echo($id);?>             = new Date();

            if (CountStepper<?php echo($id);?>> 0) {
                ddiff<?php echo($id);?> = new Date(dnow<?php echo($id);?>- dthen<?php echo($id);?>);
            } else {
                ddiff<?php echo($id);?> = new Date(dthen<?php echo($id);?>- dnow<?php echo($id);?>);
            }

            gsecs<?php echo($id);?> = Math.floor(ddiff<?php echo($id);?>.valueOf() / 1000);

            CountBack<?php echo($id);?>(gsecs<?php echo($id);?>);
        </script>
        <?php
    }
}
