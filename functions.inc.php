<?php

// this function required to make the feature code work
function hotelwakeup_get_config($engine) {
	$modulename = 'hotelwakeup';

	// This generates the dialplan
	global $ext;
	global $asterisk_conf;
	switch($engine) {
		case "asterisk":
			if (is_array($featurelist = featurecodes_getModuleFeatures($modulename))) {
				foreach($featurelist as $item) {
					$featurename = $item['featurename'];
					$fname = $modulename.'_'.$featurename;
					if (function_exists($fname)) {
						$fcc = new featurecode($modulename, $featurename);
						$fc = $fcc->getCodeActive();
						unset($fcc);

						if ($fc != '') {
							$fname($fc);
						}
					} else {
						$ext->add('from-internal-additional', 'debug', '', new ext_noop($modulename.": No func $fname"));
					}
				}
			}
		break;
	}
}

// this function required to make the feature code work
function hotelwakeup_hotelwakeup($c) {
	global $ext;
	global $asterisk_conf;

	$id = "app-hotelwakeup"; // The context to be included

	$ext->addInclude('from-internal-additional', $id); // Add the include from from-internal
	$ext->add($id, $c, '', new ext_Macro('user-callerid'));
	$ext->add($id, $c, '', new ext_answer(''));
	$ext->add($id, $c, '', new ext_wait(1));
	$ext->add($id, $c, '', new ext_AGI('wakeupphp'));
	$ext->add($id, $c, '', new ext_Hangup);
}

function hotelwakeup_gencallfile($foo) {
// This function will generate the wakeup call file based on the array provided

/**** array format ******
array(
	time  => timestamp value,
	ext => phone number,
	maxretries => int value seconds,
	retrytime => int value seconds,
	waittime => int value seconds,
	callerid => in 'name <number>' format,
	application => value,
	data => value,
	tempdir => path to temp directory including trailing slash
	outdir => path to outgoing directory including trailing slash
	filename => filename to use for call file
)
**** array format ******/

	if ($foo['tempdir'] == "") {
		$foo['tempdir'] = "/var/spool/asterisk/tmp/";
	}
	if ($foo['outdir'] == "") {
		$foo['outdir'] = "/var/spool/asterisk/outgoing/";
	}
	if ($foo['filename'] == "") {
		$foo['filename'] = "wuc.".$foo['time'].".ext.".$foo['ext'].".call";
	}

	$tempfile = $foo['tempdir'].$foo['filename'];
	$outfile = $foo['outdir'].$foo['filename'];

	// Delete any old .call file with the same name as the one we are creating.
	if( file_exists( "$callfile" ) )
	{
		unlink( "$callfile" );
	}

	// Create up a .call file, write and close
	$wuc = fopen( $tempfile, 'w');
	fputs( $wuc, "channel: Local/".$foo['ext']."@from-internal\n" );
	fputs( $wuc, "maxretries: ".$foo['maxretries']."\n");
	fputs( $wuc, "retrytime: ".$foo['retrytime']."\n");
	fputs( $wuc, "waittime: ".$foo['waittime']."\n");
	fputs( $wuc, "callerid: ".$foo['callerid']."\n");
	fputs( $wuc, "application: ".$foo['application']."\n");
	fputs( $wuc, "data: ".$foo['data']."\n");
	fclose( $wuc );

	// set time of temp file and move to outgoing
	touch( $tempfile, $foo['time'], $foo['time'] );
	rename( $tempfile, $outfile );
}
