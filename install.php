<?php
print "Installing Hotel Style Wake Up Calls<br>";
// list of the columns that need to be included in the hotelwakup table.  Add/subract values to this list and trigger a reinstall to alter the table
// this table is used to store module config info
$cols['maxretries'] = "INT NOT NULL";
$cols['waittime'] = "INT NOT NULL";
$cols['retrytime'] = "INT NOT NULL";
$cols['extensionlength'] = "INT NOT NULL";
$cols['wakeupcallerid'] = "VARCHAR(30)";
$cols['operator_mode'] = "INT NOT NULL";
$cols['operator_extensions'] = "VARCHAR(30)";
//new config table columns
$cols['application'] = "VARCHAR(30)";
$cols['data'] = "VARCHAR(30)";

// list of columns that need to be in the hotelwakeup_calls table.  Add/subract values to this list and trigger a reinstall to alter the table
// this table is used to store scheduled calls info
$sc_cols['time'] = "INT NOT NULL";
$sc_cols['ext'] = "INT NOT NULL";
$sc_cols['maxretries'] = "INT NOT NULL";
$sc_cols['retrytime'] = "INT NOT NULL";
$sc_cols['waittime'] = "INT NOT NULL";
$sc_cols['callerid'] = "VARCHAR(30)";
$sc_cols['application'] = "VARCHAR(30)";
$sc_cols['data'] = "VARCHAR(30)";
$sc_cols['tempdir'] = "VARCHAR(100)";
$sc_cols['outdir'] = "VARCHAR(100)";
$sc_cols['filename'] = "VARCHAR(100)";
$sc_cols['frequency'] = "INT NOT NULL";


// create the hotelwakeup table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS hotelwakeup (";
foreach($cols as $key=>$val)
{
	$sql .= $key.' '.$val.', ';
}
$sql .= "PRIMARY KEY (maxretries))";
$check = $db->query($sql);
if (DB::IsError($check))
{
	die_freepbx( "Can not create hotelwakeup table: ".$sql." - ".$check->getMessage() .  "<br>");
}

// create the hotelwakeup_calls table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS hotelwakeup_calls (";
foreach($sc_cols as $key=>$val)
{
	$sql .= $key.' '.$val.', ';
}
$sql .= "PRIMARY KEY (time))";
$check = $db->query($sql);
if (DB::IsError($check))
{
	die_freepbx( "Can not create hotelwakeup_calls table: ".$sql." - ".$check->getMessage() .  "<br>");
}

//check status of exist columns in the hotelwakup table and change/drop as required
$curret_cols = array();
$sql = "DESC hotelwakeup";
$res = $db->query($sql);
while($row = $res->fetchRow())
{
	if(array_key_exists($row[0],$cols))
	{
		$curret_cols[] = $row[0];
		//make sure it has the latest definition
		$sql = "ALTER TABLE hotelwakeup MODIFY ".$row[0]." ".$cols[$row[0]];
		$check = $db->query($sql);
		if (DB::IsError($check))
		{
			die_freepbx( "Can not update column ".$row[0].": " . $check->getMessage() .  "<br>");
		}
	}
	else
	{
		//remove the column
		$sql = "ALTER TABLE hotelwakeup DROP COLUMN ".$row[0];
		$check = $db->query($sql);
		if(DB::IsError($check))
		{
			die_freepbx( "Can not remove column ".$row[0].": " . $check->getMessage() .  "<br>");
		}
		else
		{
			print 'Removed no longer needed column '.$row[0].' from hotelwakup table.<br>';
		}
	}
}
//add missing columns to the hotelwakeup table
foreach($cols as $key=>$val)
{
	if(!in_array($key,$curret_cols))
	{
		$sql = "ALTER TABLE hotelwakeup ADD ".$key." ".$val;
		$check = $db->query($sql);
		if (DB::IsError($check))
		{
			die_freepbx( "Can not add column ".$key.": " . $check->getMessage() .  "<br>");
		}
		else
		{
			print 'Added column '.$key.' to hotelwakeup table.<br>';
		}
	}
}

//check status of exist columns in the hotelwakup_calls table and change/drop as required
$sc_curret_cols = array();
$sql = "DESC hotelwakeup_calls";
$res = $db->query($sql);
while($row = $res->fetchRow())
{
	if(array_key_exists($row[0],$sc_cols))
	{
		$sc_curret_cols[] = $row[0];
		//make sure it has the latest definition
		$sql = "ALTER TABLE hotelwakeup_calls MODIFY ".$row[0]." ".$sc_cols[$row[0]];
		$check = $db->query($sql);
		if (DB::IsError($check))
		{
			die_freepbx( "Can not update column ".$row[0].": " . $check->getMessage() .  "<br>");
		}
	}
	else
	{
		//remove the column
		$sql = "ALTER TABLE hotelwakeup_calls DROP COLUMN ".$row[0];
		$check = $db->query($sql);
		if(DB::IsError($check))
		{
			die_freepbx( "Can not remove column ".$row[0].": " . $check->getMessage() .  "<br>");
		}
		else
		{
			print 'Removed no longer needed column '.$row[0].' from hotelwakeup_calls table.<br>';
		}
	}
}
//add missing columns to the hotelwakeup_calls table
foreach($sc_cols as $key=>$val)
{
	if(!in_array($key,$sc_curret_cols))
	{
		$sql = "ALTER TABLE hotelwakeup_calls ADD ".$key." ".$val;
		$check = $db->query($sql);
		if (DB::IsError($check))
		{
			die_freepbx( "Can not add column ".$key.": " . $check->getMessage() .  "<br>");
		}
		else
		{
			print 'Added column '.$key.' to hotelwakeup_calls table.<br>';
		}
	}
}


/******************** temporarily removed, need to figure out a way to put defaults without overwriting existing values
print "Installing Default Values<br>";
# the easy why to debug your SQL Q its missing a value or something do let me do this :P
# is  that telling yo how yur puting it upp you dont need to have them in a serten order as long as the value ar in teh same place
$sql ="INSERT INTO hotelwakeup (maxretries, waittime, retrytime, wakeupcallerid,  operator_mode, operator_extensions, extensionlength) ";
$sql .= "               VALUES ('3',        '60',     '60',      '\"Wake Up Calls\" <*68>', '1',          '00 , 01',           '4')";

$check = $db->query($sql);
if (DB::IsError($check)) {
        die_freepbx( "Can not create default values in `hotelwakeup` table: " . $check->getMessage() .  "\n");
}
**********************/

// Register FeatureCode - Hotel Wakeup;
$fcc = new featurecode('hotelwakeup', 'hotelwakeup');
$fcc->setDescription('Wake Up Calls');
$fcc->setDefault('*68');
$fcc->update();
unset($fcc);
?>
