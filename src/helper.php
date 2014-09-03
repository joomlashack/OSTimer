<?php
/**
 * @package   mod_ostimer
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class modOSTimerHelper
{
	static function getList(&$params)
	{
		global $mainframe;

		$ev_displaytitle = @$params->get('ev_dtitle');
		$ev_title        = @$params->get('ev_tit');
		$ev_displaydate  = @$params->get('ev_ddate');
		$ev_dateformat   = @$params->get('ev_ddate_format');
		$ev_day          = @$params->get('ev_d');
		$ev_month        = @$params->get('ev_m');
		$ev_year         = @$params->get('ev_y');
		$ev_ddaysleft    = @$params->get('ev_ddleft');
		$ev_displayhour  = @$params->get('ev_dhour');
		$ev_hour         = @$params->get('ev_h');
		$ev_minutes      = @$params->get('ev_min');
		$ev_color        = @$params->get('ev_color');
		$ev_displayURL   = @$params->get('ev_dlink');
		$ev_URLtitle     = @$params->get('ev_ltitle');
		$ev_URL			 = @$params->get('ev_l');
		$ev_js			 = @$params->get('ev_js');
		$ev_endtime		 = @$params->get('ev_endtime');
		$ev_offset		 = @$params->get('ev_offset');
        $loadcss		 = @$params->get('loadcss');
		$trans_days 	 = @$params->get('ev_trans_days');
		$trans_hr 		 = @$params->get('ev_trans_hr');
		$trans_min 		 = @$params->get('ev_trans_min');
		$trans_sec 		 = @$params->get('ev_trans_sec');
        //$ev_hour = $ev_hour+$ev_offset;

		$eventdown = mktime($ev_hour, $ev_minutes, 0, $ev_month, $ev_day, $ev_year);
		$today = time();
		$sec	= $eventdown - $today;
		$days	= floor(($eventdown - $today) /86400);
		if ($days * 86400 + $today > $eventdown)
			$days--;
		$h1		= floor(($eventdown - $today) /3600);
		$m1		= floor(($eventdown - $today) /60);
		$hour	= floor($sec/60/60 - $days*24);
		$min	= floor($sec/60 - $hour*60);

		//collect data in an array
		$i		= 0;
		$lists	= array();
		$lists[0] = 0;
		$lists[$i] = (object) $lists[$i];

		if ($ev_displaytitle) {
			$lists[$i]->title = $ev_title;
		} else {}


				if ($ev_displaydate) {
				if ($ev_dateformat == 1){
					$lists[$i]->displaydate = $ev_month.'.'.$ev_day.'.'.$ev_year.' '.$ev_hour.':'.$ev_minutes;
					}
				else{
					$lists[$i]->displaydate = $ev_day.'.'.$ev_month.'.'.$ev_year.' '.$ev_hour.':'.$ev_minutes;
					}
		} else {}

				if($ev_ddaysleft == '1') {
	        	$lists[$i]->dney = $trans_days;
		} else {}

				$lists[$i]->daycount = $days;
		static $timestamp;
		//$timestamp = microtime(true);
		$timestamp++;
		$lists[$i]->timestamp = $timestamp;
		if (($ev_displayhour == '1') && ($ev_js == '1')) {
          	    	$lists[$i]->DetailCount = '<span id="clockJS'.$timestamp.'"></span>';
            $lists[$i]->JS_enable 		= $ev_js;
            $lists[$i]->JS_month 		= $ev_month;
            $lists[$i]->JS_day 			= $ev_day;
            $lists[$i]->JS_year 		= $ev_year;
            $lists[$i]->JS_hour 		= $ev_hour;
            $lists[$i]->JS_min 			= $ev_minutes;
            $lists[$i]->JS_endtime 		= $ev_endtime;
            $lists[$i]->JS_offset		= $ev_offset;
			$lists[$i]->JS_trans_hr 	= $trans_hr;
			$lists[$i]->JS_trans_min	= $trans_min;
			$lists[$i]->JS_trans_sec	= $trans_sec;
	 	} else if (($ev_displayhour == '1') && ($ev_js == '0')) {
            $curmin = date('i');
            if ($curmin >= $ev_minutes) {
            	$min = $curmin - $ev_minutes;
            } else {
            	$min = $ev_minutes - $curmin;
            }
	 		$lists[$i]->DetailCount = $hour.' Hrs. '.$min.' Min.';

	 	} else {
				if ($days <= 0)
				{
					$lists[$i]->DetailCount = $ev_endtime;
				}
			}
			// Need to set it to an open string in order to get rid of: Notice: Undefined property: stdClass::$DetailLink in modules\mod_ostimer\tmpl\default.php on line 27
			$lists[$i]->DetailLink ="";

			 	if(($ev_displayURL == '1') && $ev_URL && $ev_URLtitle ) {
        	$lists[$i]->DetailLink = '<a href="'.$ev_URL.'" title="'.$ev_URLtitle.'">'.$ev_URLtitle.'</a>'; }

        if ($loadcss == '1') {
			$header = '';
			$header .= '<link rel="stylesheet" href="'.JURI::base().'modules/mod_ostimer/tmpl/style.css" type="text/css" />';

			$docContainer = JFactory::getDocument();
			$docContainer->addCustomTag($header);
			//$mainframe->addCustomTag($header);
			//$mainframe->addCustomHeadTag($header);
		} else {}
        return $lists;
	}
}

function countdounJS($ev_month, $ev_day, $ev_year, $ev_hour, $ev_minutes, $ev_endtime, $ev_offset, $trans_hr, $trans_min, $trans_sec, $id)
	{
	if ($ev_hour >= '12') {
		$curHour = $ev_hour - '12';
		$curSet = 'PM';
	} else {
		$curHour = $ev_hour;
		$curSet = 'AM';
	}
	//echo $curHour.'<br />';

     ?>
	<script language="JavaScript" type="text/javascript">
	TargetDate<?php echo($id);?> = "<?php echo $ev_month; ?>/<?php echo $ev_day; ?>/<?php echo $ev_year; ?> <?php echo $curHour; ?>:<?php echo $ev_minutes; ?> <?php echo $curSet; ?>";
	CountActive<?php echo($id);?> = true;
	CountStepper<?php echo($id);?> = -1;
	LeadingZero<?php echo($id);?> = true;

	DisplayFormat<?php echo($id);?> = "%%H%% <?php echo $trans_hr; ?> %%M%% <?php echo $trans_min; ?> %%S%% <?php echo $trans_sec; ?>";
	FinishMessage<?php echo($id);?> = "<?php echo $ev_endtime; ?>";
	function calcage<?php echo($id);?>(secs, num1, num2) {
	  s = ((Math.floor(secs/num1))%num2).toString();
	  if (LeadingZero<?php echo($id);?> && s.length < 2)
	    s = "0" + s;
	  return s;
	}
	function CountBack<?php echo($id);?>(secs) {
	  if (secs < 0) {
	    document.getElementById("clockJS<?php echo($id);?>").innerHTML = FinishMessage<?php echo($id);?>;
	    return;
	  }
	  if (calcage<?php echo($id);?>(secs,86400,100000)==0 && calcage<?php echo($id);?>(secs,3600,24) == 0)
		DisplayFormat<?php echo($id);?> = "%%M%% <?php echo $trans_min; ?> %%S%% <?php echo $trans_sec; ?>";
	  if (calcage<?php echo($id);?>(secs,86400,100000)==0 && calcage<?php echo($id);?>(secs,3600,24) == 0 && calcage<?php echo($id);?>(secs,60,60) == 0)
		DisplayFormat<?php echo($id);?> = "%%S%% <?php echo $trans_sec; ?>";

	  DisplayStr = DisplayFormat<?php echo($id);?>.replace(/%%D%%/g, calcage<?php echo($id);?>(secs,86400,100000));
	  DisplayStr = DisplayStr.replace(/%%H%%/g, calcage<?php echo($id);?>(secs,3600,24));
	  DisplayStr = DisplayStr.replace(/%%M%%/g, calcage<?php echo($id);?>(secs,60,60));
	  DisplayStr = DisplayStr.replace(/%%S%%/g, calcage<?php echo($id);?>(secs,1,60));
	  document.getElementById("clockJS<?php echo($id);?>").innerHTML = DisplayStr;
	  if (CountActive<?php echo($id);?>)
	    setTimeout("CountBack<?php echo($id);?>(" + (secs+CountStepper<?php echo($id);?>) + ")", SetTimeOutPeriod<?php echo($id);?>);
	}

	CountStepper<?php echo($id);?> = Math.ceil(CountStepper<?php echo($id);?>);
	if (CountStepper<?php echo($id);?> == 0)
	  CountActive<?php echo($id);?> = false;
	var SetTimeOutPeriod<?php echo($id);?> = (Math.abs(CountStepper<?php echo($id);?>)-1)*1000 + 990;
	var dthen<?php echo($id);?> 	= new Date(TargetDate<?php echo($id);?>);
	var dnow<?php echo($id);?> 	= new Date();
	if(CountStepper<?php echo($id);?>>0)
	  ddiff<?php echo($id);?> = new Date(dnow<?php echo($id);?>-dthen<?php echo($id);?>);
	else
	  ddiff<?php echo($id);?> = new Date(dthen<?php echo($id);?>-dnow<?php echo($id);?>);
	gsecs<?php echo($id);?> = Math.floor(ddiff<?php echo($id);?>.valueOf()/1000);
	CountBack<?php echo($id);?>(gsecs<?php echo($id);?>);
	</script>

	<?php
	}
