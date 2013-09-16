<?php
/**
* @author Beliyadm / OSTraining.com @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modOSTimerHelper
{
	function getList(&$params)
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
		$min	= floor($sec/60 - $hours*60);

		//collect data in an array
		$i		= 0;
		$lists	= array();

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
	
		if (($ev_displayhour == '1') && ($ev_js == '1')) {
          	    	$lists[$i]->DetailCount = '<span id="clockJS"></span>';
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

			 	if(($ev_displayURL == '1') && $ev_URL && $ev_URLtitle ) {
        	$lists[$i]->DetailLink = '<a href="'.$ev_URL.'" title="'.$ev_URLtitle.'">'.$ev_URLtitle.'</a>'; }

        if ($loadcss == '1') {
			$header = '';
			$header .= '<link rel="stylesheet" href="'.JURI::base().'modules/mod_ostimer/tmpl/style.css" type="text/css" />';
			$mainframe->addCustomHeadTag($header);
		} else {}
        return $lists;
	}
}

function countdounJS($ev_month, $ev_day, $ev_year, $ev_hour, $ev_minutes, $ev_endtime, $ev_offset, $trans_hr, $trans_min, $trans_sec)
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
	TargetDate = "<?php echo $ev_month; ?>/<?php echo $ev_day; ?>/<?php echo $ev_year; ?> <?php echo $curHour; ?>:<?php echo $ev_minutes; ?> <?php echo $curSet; ?>";
	CountActive = true;
	CountStepper = -1;
	LeadingZero = true;

	DisplayFormat = "%%H%% <?php echo $trans_hr; ?> %%M%% <?php echo $trans_min; ?> %%S%% <?php echo $trans_sec; ?>";
	FinishMessage = "<?php echo $ev_endtime; ?>";
	function calcage(secs, num1, num2) {
	  s = ((Math.floor(secs/num1))%num2).toString();
	  if (LeadingZero && s.length < 2)
	    s = "0" + s;
	  return s;
	}
	function CountBack(secs) {
	  if (secs < 0) {
	    document.getElementById("clockJS").innerHTML = FinishMessage;
	    return;
	  }
	  if (calcage(secs,86400,100000)==0 && calcage(secs,3600,24) == 0)
		DisplayFormat = "%%M%% <?php echo $trans_min; ?> %%S%% <?php echo $trans_sec; ?>";
	  if (calcage(secs,86400,100000)==0 && calcage(secs,3600,24) == 0 && calcage(secs,60,60) == 0)
		DisplayFormat = "%%S%% <?php echo $trans_sec; ?>";

	  DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs,86400,100000));
	  DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
	  DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
	  DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
	  document.getElementById("clockJS").innerHTML = DisplayStr;
	  if (CountActive)
	    setTimeout("CountBack(" + (secs+CountStepper) + ")", SetTimeOutPeriod);
	}
	CountStepper = Math.ceil(CountStepper);
	if (CountStepper == 0)
	  CountActive = false;
	var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
	var dthen 	= new Date(TargetDate);
	var dnow 	= new Date();
	if(CountStepper>0)
	  ddiff = new Date(dnow-dthen);
	else
	  ddiff = new Date(dthen-dnow);
	gsecs = Math.floor(ddiff.valueOf()/1000);
	CountBack(gsecs);
	</script>

	<?php
	}

