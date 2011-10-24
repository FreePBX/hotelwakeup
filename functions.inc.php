<?php
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

function hotelwakeup_hotelwakeup($c) {
	global $ext;
	global $asterisk_conf;

	$id = "app-hotelwakeup"; // The context to be included

	$ext->addInclude('from-internal-additional', $id); // Add the include from from-internal
	$ext->add($id, $c, '', new ext_Macro('user-callerid'));
	$ext->add($id, $c, '', new ext_answer(''));
	$ext->add($id, $c, '', new ext_wait(1));
	$ext->add($id, $c, '', new ext_AGI(WAKEUPPHP));
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
//	$sql .= ", `wakeupcallerid`='\"{$calleridtext}\" <{$calleridnumber}>'";
	$sql .= ", `wakeupcallerid`='{$calleridtext} <{$calleridnumber}>'";
	$sql .= ", `operator_mode`='{$operator_mode}'";
	$sql .= ", `operator_extensions`='{$operator_extensions}'";
	$sql .= " LIMIT 1;";


	sql($sql);
}

function hotelwakeup_getconfig() {
// this function shall get the valuses from the wakeup database, and load them into the Wake Up calls COnfig Screen positions.
#	$sql = "SELECT maxretries, waittime, retrytime, extensionlength, wakeupcallerid, operator_mode, operator_extensions FROM hotelwakeup";
# This is the same as your but smaller this get everthing. ok i understand.  its ok to do this way.

	#print_r($results);
	#die();

	#foreach($results as $result){
	#	$configitems[] = array($result[0],$result[1]);
	#}
	#return isset($configitems)?$configitems:null;

	$sql = "SELECT * FROM hotelwakeup LIMIT 1";
	$results= sql($sql, "getAll");
	$tmp = $results[0][4];
	$tmp = eregi_replace('"', '', $tmp);
	$tmp = eregi_replace('>', '', $tmp);
	$res = explode('<', $tmp);
	$results[0][] = trim($res[1]);
	$results[0][] = trim($res[0]);
	return $results[0];
}