<?php
/**
 * @package   com_osdownloads
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Alledia\OSTimer\Free\Joomla;

defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\AbstractModule;
use Alledia\Framework\Factory;
use JUri;

class Module extends AbstractModule
{
    protected static $instance;

    public static function getInstance($namespace = null, $module = null)
    {
        return parent::getInstance('OSTimer', $module);
    }

    public function init()
    {
        $this->list   = $this->getList();

        parent::init();
    }

    protected function getList()
    {
        $app = Factory::getApplication();

        $eventDisplayTitle = @$this->params->get('ev_dtitle');
        $eventTitle        = @$this->params->get('ev_tit');
        $eventDisplayDate  = @$this->params->get('ev_ddate');
        $eventDateFormat   = @$this->params->get('ev_ddate_format');
        $eventDay          = @$this->params->get('ev_d');
        $eventMonth        = @$this->params->get('ev_m');
        $eventYear         = @$this->params->get('ev_y');
        $eventDDaysLeft    = @$this->params->get('ev_ddleft');
        $eventDisplayHour  = @$this->params->get('ev_dhour');
        $eventHour         = @$this->params->get('ev_h');
        $eventMinutes      = @$this->params->get('ev_min');
        $eventColor        = @$this->params->get('ev_color');
        $eventDisplayURL   = @$this->params->get('ev_dlink');
        $eventURLTitle     = @$this->params->get('ev_ltitle');
        $eventURL          = @$this->params->get('ev_l');
        $eventJs           = @$this->params->get('ev_js');
        $eventEndTime      = @$this->params->get('ev_endtime');
        $eventOffset       = @$this->params->get('ev_offset');
        $loadCSS           = @$this->params->get('loadcss');
        $transDays         = @$this->params->get('ev_trans_days');
        $transHour         = @$this->params->get('ev_trans_hr');
        $transMin          = @$this->params->get('ev_trans_min');
        $transSec          = @$this->params->get('ev_trans_sec');
        // $eventHour         = $eventHour+$eventOffset;

        $eventTime = mktime($eventHour, $eventMinutes, 0, $eventMonth, $eventDay, $eventYear);
        $now       = time();

        $diff = $eventTime - $now;
        $days = floor($diff / 86400);

        if ($days * 86400 + $now > $eventTime) {
            $days--;
        }

        $h1   = floor($diff / 3600);
        $m1   = floor($diff / 60);
        $hour = floor($diff / 3600 - $days * 24);
        $min  = floor($diff / 60 - $hour * 60);

        //collect data in an array
        $i         = 0;
        $lists     = array();
        $lists[0]  = 0;
        $lists[$i] = (object) $lists[$i];

        if ($eventDisplayTitle) {
            $lists[$i]->title = $eventTitle;
        }


        if ($eventDisplayDate) {
            if ($eventDateFormat == 1){
                $lists[$i]->displaydate = $eventMonth.'.'.$eventDay.'.'.$eventYear.' '.$eventHour.':'.$eventMinutes;
            } else {
                $lists[$i]->displaydate = $eventDay.'.'.$eventMonth.'.'.$eventYear.' '.$eventHour.':'.$eventMinutes;
            }
        }

        if ($eventDDaysLeft == '1') {
            $lists[$i]->dney = $transDays;
        }

        $lists[$i]->daycount = $days;
        static $timeStamp;

        //$timeStamp = microtime(true);
        $timeStamp++;
        $lists[$i]->timestamp = $timeStamp;
        if (($eventDisplayHour == '1') && ($eventJs == '1')) {
            $lists[$i]->DetailCount  = '<span id="clockJS'.$timeStamp.'"></span>';
            $lists[$i]->JS_enable    = $eventJs;
            $lists[$i]->JS_month     = $eventMonth;
            $lists[$i]->JS_day       = $eventDay;
            $lists[$i]->JS_year      = $eventYear;
            $lists[$i]->JS_hour      = $eventHour;
            $lists[$i]->JS_min       = $eventMinutes;
            $lists[$i]->JS_endtime   = $eventEndTime;
            $lists[$i]->JS_offset    = $eventOffset;
            $lists[$i]->JS_trans_hr  = $transHour;
            $lists[$i]->JS_trans_min = $transMin;
            $lists[$i]->JS_trans_sec = $transSec;
        } else if (($eventDisplayHour == '1') && ($eventJs == '0')) {
            $curmin = date('i');

            if ($curmin >= $eventMinutes) {
                $min = $curmin - $eventMinutes;
            } else {
                $min = $eventMinutes - $curmin;
            }

            $lists[$i]->DetailCount = $hour.' Hrs. '.$min.' Min.';
        } else {
            if ($days <= 0) {
                $lists[$i]->DetailCount = $eventEndTime;
            }
        }

        // Need to set it to an open string in order to get rid of: Notice: Undefined property: stdClass::$DetailLink in modules\mod_ostimer\tmpl\default.php on line 27
        $lists[$i]->DetailLink ="";

        if (($eventDisplayURL == '1') && $eventURL && $eventURLTitle ) {
            $lists[$i]->DetailLink = '<a href="'.$eventURL.'" title="'.$eventURLTitle.'">'.$eventURLTitle.'</a>';
        }

        if ($loadCSS == '1') {
            $header = '';
            $header .= '<link rel="stylesheet" href="'.JUri::base().'modules/mod_ostimer/tmpl/style.css" type="text/css" />';

            $docContainer = Factory::getDocument();
            $docContainer->addCustomTag($header);
            //$app->addCustomTag($header);
            //$app->addCustomHeadTag($header);
        }

        return $lists;
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
