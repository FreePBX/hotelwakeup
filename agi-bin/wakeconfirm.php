#!/usr/bin/env php
<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//

// Bootstrap FreePBX but don't include any modules (so you won't get anything
// from the functions.inc.php files of all the modules.)
//
$restrict_mods = true;
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
	include_once('/etc/asterisk/freepbx.conf');
}
set_time_limit(0);
error_reporting(0);

// Connect to AGI:
//
require_once "phpagi.php";
$AGI = new AGI();
$AGI->answer();
$lang = $AGI->request['agi_language'];
if($lang == 'ja') {
	sim_playback($AGI, "this-is-yr-wakeup-call");
} else {
	sim_playback($AGI, "hello&this-is-yr-wakeup-call");
}
$time_wakeup = time();
if($lang == 'ja') {
	$digit = sim_background($AGI, "wakeup-menu","1234",1);
} else { // Default back to English if channel doesn't match other languages
	$digit = sim_background($AGI, "to-cancel-wakeup&press-1&to-snooze-for&digits/5&minutes&press-2&to-snooze-for&digits/10&minutes&press-3&to-snooze-for&digits/15&minutes&press-4","1234",1);
}
$number = $AGI->request['agi_extension'];
switch($digit) {
	case 1:
		if($lang == 'ja') {
			sim_playback($AGI, "wakeup-call-cancelled");
		} else {
			sim_playback($AGI, "wakeup-call-cancelled");
		}
	break;
	case 2:
		$time_wakeup += 300;
		if($lang == 'ja') {
			sim_playback($AGI, "5-minutes-from-now&rqsted-wakeup-for");
		} else {
			sim_playback($AGI, "rqsted-wakeup-for&digits/5&minutes&vm-from&now");
		}
		FreePBX::Hotelwakeup()->addWakeup($number,$time_wakeup,$lang);
		$AGI->hangup();
	break;
	case 3:
		$time_wakeup += 600;
		if($lang == 'ja') {
			sim_playback($AGI, "10-minutes-from-now&rqsted-wakeup-for");
		} else {
			sim_playback($AGI, "rqsted-wakeup-for&digits/10&minutes&vm-from&now");
		}
		FreePBX::Hotelwakeup()->addWakeup($number,$time_wakeup,$lang);
		$AGI->hangup();
	break;
	case 4:
		$time_wakeup += 900;
		if($lang == 'ja') {
			sim_playback($AGI, "15-minutes-from-now&rqsted-wakeup-for");
		} else {
			sim_playback($AGI, "rqsted-wakeup-for&digits/15&minutes&vm-from&now");
		}
		FreePBX::Hotelwakeup()->addWakeup($number,$time_wakeup,$lang);
		$AGI->hangup();
	break;
}
sim_playback($AGI, "goodbye");
$AGI->hangup();

/**
 * Simulate playback functionality like the dialplan
 * @param  object $AGI  The AGI Object
 * @param  string $file Audio files combined by/with '&'
 */
function sim_playback($AGI, $file) {
	$files = explode('&',$file);
	foreach($files as $f) {
		$AGI->stream_file($f);
	}
}

/**
 * Simulate background playback with added functionality
 * @param  object  $AGI      The AGI Object
 * @param  string  $file     Audio files combined by/with '&'
 * @param  string  $digits   Allowed digits (if we are prompting for them)
 * @param  string  $length   Length of allowed digits (if we are prompting for them)
 * @param  string  $escape   Escape character to exit
 * @param  integer $timeout  Timeout
 * @param  integer $maxLoops Max timeout loops
 * @param  integer $loops    Total loops
 */
function sim_background($AGI, $file,$digits='',$length='1',$escape='#',$timeout=15000, $maxLoops=1, $loops=0) {
	$files = explode('&',$file);
	$number = '';
	$lang = $AGI->request['agi_language'];
	foreach($files as $f) {
		$ret = $AGI->stream_file($f,$digits);
		if($ret['code'] == 200 && $ret['result'] != 0) {
			$number .= chr($ret['result']);
		}
		if(strlen($number) >= $length) {
			break;
		}
	}
	if(trim($digits) != '' && strlen($number) < $length) {
		while(strlen($number) < $length && $loops < $maxLoops) {
			$ret = $AGI->wait_for_digit($timeout);
			if($loops > 0) {
				sim_playback($AGI, "please-try-again");
			}
			if($ret['code'] == 200 && $ret['result'] == 0) {
				if($lang == 'ja') {
					sim_playback($AGI, "you-entered-bad-digits");
				} else {
					sim_playback($AGI, "you-entered&bad&digits");
				}
			} elseif($ret['code'] == 200) {
				$digit = chr($ret['result']);
				if($digit == $escape) {
					break;
				}
				if(strpos($digits,$digit) !== false) {
					$number .= $digit;
					continue; //dont count loops as we are good
				} else {
					if($lang == 'ja') {
						sim_playback($AGI,"you-entered-bad-digits");
					} else {
						sim_playback($AGI,"you-entered&bad&digits");
					}
				}
			} else {
				sim_playback($AGI,"an-error-has-occurred");
			}
			$loops++;
		}
	}
	$number = trim($number);
	return $number;
}
