#!/usr/bin/env php
<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//

// Bootstrap FreePBX but don't include any modules (so you won't get anything
// from the functions.inc.php files of all the modules.)
//
$restrict_mods = true;
include '/etc/freepbx.conf';
set_time_limit(0);
error_reporting(0);

require_once "wakeglobal.php";
$hotelwakeup = new AGI_Hotelwakeup();

usleep(500);
$hotelwakeup->sim_playback("welcome");

$digit = $hotelwakeup->sim_background("wakeConfirmMenu", "0123456789", 1);

$params = array(
	"delay" => 0,
);
switch($digit)
{
	case 1: //5 minut
		$params['delay'] = 5;
		break;

	case 2: //10 minut
		$params['delay'] = 10;
		break;

	case 3: //15 minut
		$params['delay'] = 15;
		break;
}

if ($params['delay'] > 0)
{
	$time_wakeup = time();
	$time_wakeup += $params['delay'] * 60;
	$hotelwakeup->addWakeup($time_wakeup);
	$hotelwakeup->sim_playback("wakeConfirmDelay", $params);
}

$hotelwakeup->sim_playback("goodbye");
$hotelwakeup->hangup();
