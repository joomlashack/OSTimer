<?php
/**
 * @package   OSTimer
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Alledia\OSTimer\Free\Joomla;

defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\AbstractModule;
use Alledia\Framework\Factory;
use Alledia\OSTimer\Free\Countdown;
use JUri;
use JText;
use stdClass;

class Module extends AbstractModule
{
    protected static $instance;

    protected static $timeStamp;

    public static function getInstance($namespace = null, $module = null)
    {
        return parent::getInstance('OSTimer', $module);
    }

    public function init()
    {
        $params = $this->params;

        $eventDisplayTitle = @$params->get('ev_dtitle', 1);
        $eventTitle        = @$params->get('ev_tit');
        $eventDisplayDate  = @$params->get('ev_ddate', 1);
        $eventDateFormat   = @$params->get('ev_ddate_format', 1);
        $eventDay          = @$params->get('ev_d', 1);
        $eventMonth        = @$params->get('ev_m', 1);
        $eventYear         = @$params->get('ev_y', 2015);
        $eventDDaysLeft    = @$params->get('ev_ddleft', 1);
        $eventDisplayHour  = @$params->get('ev_dhour', 1);
        $eventHour         = @$params->get('ev_h', 0);
        $eventMinutes      = @$params->get('ev_min', 0);
        $eventDisplayURL   = @$params->get('ev_dlink', 1);
        $eventURLTitle     = @$params->get('ev_ltitle', '');
        $eventURL          = @$params->get('ev_l', '');
        $eventJs           = @$params->get('ev_js', 1);
        $eventEndTime      = @$params->get('ev_endtime', 'Time\'s Up');
        $loadCSS           = @$params->get('loadcss', 1);
        $transDays         = JText::_(@$params->get('ev_trans_days', 'Days'));
        $transHour         = JText::_(@$params->get('ev_trans_hr', 'Hr.'));
        $transMin          = JText::_(@$params->get('ev_trans_min', 'Min.'));
        $transSec          = JText::_(@$params->get('ev_trans_sec', 'Sec.'));
        $timezone          = @$params->get('timezone', 'UTC');

        $this->moduleClassSfx = @$params->get('moduleclass_sfx', '');
        $this->showZeroDay    = @$params->get('show_zero_day', 1);
        $this->eventColor     = @$params->get('ev_color', '#2B7CBE');

        $this->event = new stdClass;

        $timeLeft = Countdown::calculate($eventHour, $eventMinutes, 0, $eventMonth, $eventDay, $eventYear, $timezone);

        if ($eventDisplayTitle) {
            $this->event->title = $eventTitle;
        }

        if ($eventDisplayDate) {
            if ($eventDateFormat == 1){
                $this->event->date = $eventMonth.'.'.$eventDay.'.'.$eventYear.' '.$eventHour.':'.$eventMinutes;
            } else {
                $this->event->date = $eventDay.'.'.$eventMonth.'.'.$eventYear.' '.$eventHour.':'.$eventMinutes;
            }
        }

        if ($eventDDaysLeft == '1') {
            $this->event->textDays = $transDays;
        }

        $this->event->days = $timeLeft['days'];

        static::$timeStamp++;
        $this->event->timestamp = static::$timeStamp;
        if (($eventDisplayHour == '1') && ($eventJs == '1')) {
            $this->event->DetailCount  = '<span id="clockJS'.static::$timeStamp.'"></span>';
            $this->event->JS_enable    = $eventJs;
            $this->event->JS_month     = $eventMonth;
            $this->event->JS_day       = $eventDay;
            $this->event->JS_year      = $eventYear;
            $this->event->JS_hour      = $eventHour;
            $this->event->JS_min       = $eventMinutes;
            $this->event->JS_endtime   = $eventEndTime;
            $this->event->JS_offset    = '';
            $this->event->JS_trans_hr  = $transHour;
            $this->event->JS_trans_min = $transMin;
            $this->event->JS_trans_sec = $transSec;
        } else if (($eventDisplayHour == '1') && ($eventJs == '0')) {
            $curmin = date('i');

            if ($curmin >= $eventMinutes) {
                $min = $curmin - $eventMinutes;
            } else {
                $min = $eventMinutes - $curmin;
            }

            $this->event->DetailCount = $hour.' ' . $transHour . ' '.$min.' ' . $transMin;
        } else {
            if ($days <= 0) {
                $this->event->DetailCount = $eventEndTime;
            }
        }

        if (($eventDisplayURL == '1') && $eventURL && $eventURLTitle ) {
            $this->event->detailLink = '<a href="'.$eventURL.'" title="'.$eventURLTitle.'">'.$eventURLTitle.'</a>';
        }

        if ((bool) $loadCSS) {
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
        $eventOffset,
        $transHour,
        $transMin,
        $transSec,
        $id
    )
    {
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

            function calcage<?php echo($id);?>(secs, num1, num2) {
                s = ((Math.floor(secs/num1))%num2).toString();
                if (LeadingZero<?php echo($id);?> && s.length < 2) {
                    s = "0" + s;
                }

                return s;
            }

            function CountBack<?php echo($id);?>(secs) {
                if (secs < 0) {
                    document.getElementById("clockJS<?php echo($id);?>").innerHTML = FinishMessage<?php echo($id);?>;

                    return;
                }

                if (calcage<?php echo($id);?>(secs,86400,100000)==0 && calcage<?php echo($id);?>(secs,3600,24) == 0) {
                    DisplayFormat<?php echo($id);?> = "%%M%% <?php echo $transMin; ?> %%S%% <?php echo $transSec; ?>";
                }

                if (calcage<?php echo($id);?>(secs,86400,100000)==0 && calcage<?php echo($id);?>(secs,3600,24) == 0 && calcage<?php echo($id);?>(secs,60,60) == 0) {
                    DisplayFormat<?php echo($id);?> = "%%S%% <?php echo $transSec; ?>";
                }

                DisplayStr = DisplayFormat<?php echo($id);?>.replace(/%%D%%/g, calcage<?php echo($id);?>(secs,86400,100000));
                DisplayStr = DisplayStr.replace(/%%H%%/g, calcage<?php echo($id);?>(secs,3600,24));
                DisplayStr = DisplayStr.replace(/%%M%%/g, calcage<?php echo($id);?>(secs,60,60));
                DisplayStr = DisplayStr.replace(/%%S%%/g, calcage<?php echo($id);?>(secs,1,60));

                document.getElementById("clockJS<?php echo($id);?>").innerHTML = DisplayStr;

                if (CountActive<?php echo($id);?>) {
                    setTimeout("CountBack<?php echo($id);?>(" + (secs+CountStepper<?php echo($id);?>) + ")", SetTimeOutPeriod<?php echo($id);?>);
                }
            }

            CountStepper<?php echo($id);?> = Math.ceil(CountStepper<?php echo($id);?>);

            if (CountStepper<?php echo($id);?> == 0) {
                CountActive<?php echo($id);?> = false;
            }

            var SetTimeOutPeriod<?php echo($id);?> = (Math.abs(CountStepper<?php echo($id);?>)-1)*1000 + 990;
            var dthen<?php echo($id);?>            = new Date(TargetDate<?php echo($id);?>);
            var dnow<?php echo($id);?>             = new Date();

            if (CountStepper<?php echo($id);?>>0) {
                ddiff<?php echo($id);?> = new Date(dnow<?php echo($id);?>-dthen<?php echo($id);?>);
            } else {
                ddiff<?php echo($id);?> = new Date(dthen<?php echo($id);?>-dnow<?php echo($id);?>);
            }

            gsecs<?php echo($id);?> = Math.floor(ddiff<?php echo($id);?>.valueOf()/1000);

            CountBack<?php echo($id);?>(gsecs<?php echo($id);?>);
        </script>
        <?php
    }
}
