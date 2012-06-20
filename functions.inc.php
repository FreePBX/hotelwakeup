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

						if ($fc != '')
							$fname($fc);
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
	$ext->add($id, $c, '', new ext_AGI(wakeupphp));
	$ext->add($id, $c, '', new ext_Hangup);
	}


function hotelwakeup_saveconfig($c) {

	# clean up
	$operator_mode = mysql_escape_string($_POST['operator_mode']);
	$extensionlength = mysql_escape_string($_POST['extensionlength']);
	$operator_extensions = mysql_escape_string($_POST['operator_extensions']);
	$waittime = mysql_escape_string($_POST['waittime']);
	$retrytime = mysql_escape_string($_POST['retrytime']);
	$maxretries = mysql_escape_string($_POST['maxretries']);
	$calleridtext = mysql_escape_string($_POST['calleridtext']);
	$calleridnumber = mysql_escape_string($_POST['calleridnumber']);

	# Make SQL thing
	$sql = "UPDATE `hotelwakeup` SET";
	$sql .= " `maxretries`='{$maxretries}'";
	$sql .= ", `waittime`='{$waittime}'";
	$sql .= ", `retrytime`='{$retrytime}'";
	$sql .= ", `extensionlength`='{$extensionlength}'";
	$sql .= ", `cnam`='{$calleridtext}'";
	$sql .= ", `cid`='{$calleridnumber}'";
	$sql .= ", `operator_mode`='{$operator_mode}'";
	$sql .= ", `operator_extensions`='{$operator_extensions}'";
	$sql .= " LIMIT 1;";

	sql($sql);
}

function hotelwakeup_getconfig() {
// this function gets the values from the wakeup database, and returns them in an associative array

	$sql = "SELECT * FROM hotelwakeup LIMIT 1";
	$query = mysql_query($sql);
	$results = mysql_fetch_array($query, MYSQL_BOTH);
	return $results;

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
