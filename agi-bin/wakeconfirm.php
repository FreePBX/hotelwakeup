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

// Connect to AGI:
//
require_once "phpagi.php";
$AGI = new AGI();
$AGI->answer();

$lang = $AGI->request['agi_language'];
$number = $AGI->request['agi_extension'];


// Import Global Function Wake-Up Call
require_once "wakeglobal.php";
$hotelwakeup = \FreePBX::Hotelwakeup();


usleep(500);
sim_playback($AGI, getMessage("welcome"));

$digit = sim_background($AGI, getMessage("wakeConfirmMenu"), "0123456789", 1);

$params = array(
	'values' => array(
		"delay" => 0,
	)
);
switch($digit)
{
	case 1: //5 minut
		$params['values']['delay'] = 5;
		break;

	case 2: //10 minut
		$params['values']['delay'] = 10;
		break;

	case 3: //15 minut
		$params['values']['delay'] = 15;
		break;
}


if ($params['values']['delay'] > 0)
{
	$time_wakeup = time();
	$time_wakeup += $params['values']['delay'] * 60;
	$hotelwakeup->addWakeup($number, $time_wakeup, $lang);
	sim_playback($AGI, getMessage("wakeConfirmDelay", $params));
}

sim_playback($AGI, getMessage("goodbye"));
$AGI->hangup();
