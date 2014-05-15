<?php
if (!defined('FREEPBX_IS_AUTH')) { die("No direct script access allowed<br>"); }
# Based on <https://github.com/lgaetz/freepbx-Its_Lenny/blob/master/install.php>
# The code here should be reusable with only the sections marked with
# "### Change this section only" needing to be changed
echo("Installing Hotel Style Wake Up Calls<br>");
//This file is part of FreePBX.
//
//    This is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 2 of the License, or
//    (at your option) any later version.
//
//    This module is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    see <http://www.gnu.org/licenses/>.
//

// Check FreePBX db engine
if($amp_conf["AMPDBENGINE"] != "mysql")  {
	echo "This module has not been tested on systems not running MySql.<br>File reports at http://pbxossa.org<br>";
	}
	
# Set debug to 1 and then use fputx for debug logging.
$parm_debug_on=0;
$parm_error_log =  '/var/log/asterisk/wakeup.log';
if ($parm_debug_on)  {
	$stdlog = fopen( $parm_error_log, 'w' );
	fputx( $stdlog, "---Start of log---\n" );
	}	
	
// The following lines define the table name and an array of column names for the database. Adding, removing and updating the
// database is done automatically based on these definitions
### Change this section only ###################################################
$tablename1 = "hotelwakeup";
$cols1['id-cfg'] = "VARCHAR(6) NOT NULL PRIMARY KEY";
$cols1['id-cfg'] = "VARCHAR(6) NOT NULL";
$cols1['description'] = "VARCHAR(150)";
$cols1['maxretries'] = "INT NOT NULL DEFAULT '3'";
$cols1['waittime'] = "INT NOT NULL DEFAULT '60'";
$cols1['retrytime'] = "INT NOT NULL DEFAULT '60'";
$cols1['extensionlength'] = "INT NOT NULL DEFAULT '4'";
$cols1['cid'] = "VARCHAR(30) DEFAULT '*68'";
$cols1['cnam'] = "VARCHAR(30) DEFAULT 'Wake Up Calls'";
$cols1['operator_mode'] = "INT NOT NULL DEFAULT '0'";
$cols1['operator_extensions'] = "VARCHAR(30)";
$cols1['application'] = "VARCHAR(30) DEFAULT 'AGI'";
$cols1['data'] = "VARCHAR(30) DEFAULT 'wakeconfirm.php'";
$cols1['context'] = "VARCHAR(30) DEFAULT 'app-hotelwakeup-wakeconfirm'";
$cols1['extension'] = "VARCHAR(30) DEFAULT 's'";
$cols1['priority'] = "VARCHAR(30) DEFAULT '1'";
# ?? consider adding index on time
### End of section #############################################################

# Create the 1st table
create_table($tablename1, $cols1);

### Change this section only ###################################################
# Populate table with default values if this is a new install
$sql = "SELECT COUNT(*) FROM `$tablename1`";
$check = $db->getAll($sql,DB_FETCHMODE_ASSOC);
if(DB::IsError($res)) exit("Failed @ Install-Main-1");

# If there are no existing records then create one
if ($check['0']['COUNT(*)'] == 0) {

#Specify primary key and leave other fields as default
# 'WUC' is the default config record but the design allows for multiple configs
	$sql ="INSERT INTO `$tablename1` (`id-cfg`) VALUES ('WUC')";
echo "sql: ".$sql."<br>";
	$check = $db->query($sql);
	if (DB::IsError($check)) {
			echo "cannot add default row to $tablename1 ($sql)<br>";
			exit("Failed @ Install-Main-2");	
	} 
	else echo "Default row has been added to $tablename1<br>";
} 
# Must check if there is an existing record from before upgrade with a null key
# If there is 1 record then it must have key of 'WUC' 
# (if more than 1 then we assume all is well as it has been already upgraded)
else if ($check['0']['COUNT(*)'] == 1) {

	# If record already has correct key then do nothing
	$sql = "SELECT COUNT(*) FROM `$tablename1` WHERE `id-cfg` = 'WUC'";
	$check = $db->getAll($sql,DB_FETCHMODE_ASSOC);
	if(DB::IsError($res)) exit("Failed @ Install-Main-3");
	# If no WUC key then update the existing one with that key
	if ($check['0']['COUNT(*)'] == 0) {
	$sql ="UPDATE `$tablename1` SET `id-cfg` = 'WUC'";
# echo "sql: ".$sql."<br>";
		$check = $db->query($sql);
		if (DB::IsError($check)) {
				echo "cannot update primary key of $tablename1 to 'WUC' ($sql)<br>";
				exit("Failed @ Main-2");	
		} 
		else echo "Primary key of $tablename1 updated to 'WUC'<br>";
	}
} 
else {echo "Existing settings already exist in $tablename1 - nothing changed<br>";}
# Set the description for the default record
$sql ="UPDATE `$tablename1` SET `description` = 'Default Configuration - always used by the phone based call setup' WHERE `id-cfg` = 'WUC'";
$check = $db->query($sql);
if (DB::IsError($check)) {
	echo "cannot update description of 'WUC' record in $tablename1 ($sql)<br>";
	exit("Failed @ Main-4");	
} 

### End of section #############################################################

### Change this section only ###################################################
$tablename2 = "hotelwakeup_calls";
$cols2['id_schedule'] = "INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
$cols2['time'] = "INT NOT NULL";
$cols2['timezone'] = "INT NOT NULL";
$cols2['ext'] = "VARCHAR(30) NOT NULL";
$cols2['per'] = "VARCHAR(1)";
$cols2['id_cfg'] = "VARCHAR(6) NOT NULL";
##$cols2['maxretries'] = "INT NOT NULL";
##$cols2['retrytime'] = "INT NOT NULL";
##$cols2['waittime'] = "INT NOT NULL";
##$cols2['cid'] = "VARCHAR(30)";
##$cols2['cnam'] = "VARCHAR(30)";
##$cols2['application'] = "VARCHAR(30)";
##$cols2['data'] = "VARCHAR(30)";
##$cols2['filename'] = "VARCHAR(100)";

### End of section #############################################################

# Create the 2nd table
create_table($tablename2, $cols2);
# End of creation/updating of tables
#===============================================================================
# The following section is specific to this application
### Change this section only ###################################################

// Register FeatureCode - Hotel Wakeup;
$fcc = new featurecode('hotelwakeup', 'hotelwakeup');
$fcc->setDescription('Wake Up Calls');
$fcc->setDefault('*68');
$fcc->update();
unset($fcc);
#------------------------------------------------------------------------------
/* Scheduled calls are stored in the database. A script is run periodically to 
 * generate .call files, this part creates the cron job
 */
# Set to run every hour at 1 minute before the hour
$wuc_cron_string = "59 * * * * ".$amp_conf['ASTAGIDIR']."/hotelwakeup_genalldue.php";
$run = wuc_add_cron($wuc_cron_string);


// Register FeatureCode - Hotel Wakeup;
$fcc = new featurecode('hotelwakeup', 'hotelwakeup');
$fcc->setDescription('Wake Up Calls');
$fcc->setDefault('*68');
$fcc->update();
unset($fcc);


# The following is written as a function in case it is needed elsewhere
function wuc_add_cron($cron) {
	// WARNING:	Never change the following string, as comment is stored with cron job so we can 
	//			identify 'our' cron job and automate cron install/un-install 
	$wuc_comment_string = "Required for POSSA Wakeup Calls Module";
	
	$temp_file = sys_get_temp_dir()."/wuc_install";
	// Delete temp work file in case it already exists
	unlink($temp_file);
	// List all cron jobs
	$foo = shell_exec('crontab -l');

	// backup cron entries if this is a first time install can be used to manually restore if issues
	$wuc_cron_backup = sys_get_temp_dir()."/wuc_cron_backup.txt";
	if (!file_exists($wuc_cron_backup)) {
		file_put_contents($wuc_cron_backup, $foo);
	}

	// Find and remove past cron entry created by wuc as identified by the comment string
	$pos = strpos($foo, $wuc_comment_string); 
	if ($pos) {
		$regex = "~.*?".$wuc_comment_string."~";
		$foo = preg_replace($regex ,"",$foo);
	} 

	// Add cron job
	file_put_contents($temp_file, $foo.$cron." #".$wuc_comment_string.PHP_EOL);
	echo exec('crontab '.$temp_file);
}
### End of section #############################################################
#===============================================================================

# General purpose function to create or modify a table
function create_table($tablename, $cols) {
 
	global $db;
 
	# create a table if none present 
	# The temp column will be automatically removed by the checking code below.
	$sql = "CREATE TABLE IF NOT EXISTS `$tablename` (`zzaa` INT);";
	$check = $db->query($sql);
	if (DB::IsError($check)) {
		die_freepbx( "cannot create table $tablename<br>($sql)<br>" . $check->getMessage() . "<br>");    
	}
	
	// Check all columns in $tablename and remove auto-increments which interfere with dropping primary key
	$sql = "DESCRIBE `$tablename`";
	$res = $db->query($sql);
	if (DB::IsError($res)) {
		die_freepbx( "SQL failed $tablename<br>($sql)<br>" . $res->getMessage() . "<br>");   
	}
	while($row = $res->fetchRow())  {
		if(array_key_exists($row[0],$cols)) {
			if ($row[5] == "auto_increment") {
				$sql ="ALTER TABLE $tablename MODIFY ".$row[0]." INT";
				$check = $db->query($sql);
				if (DB::IsError($check)) {
					die_freepbx( "Removing auto increment from ".$row[0]." ". $check->getMessage() .  "<br>");
				}
			}
		}
	}
	// Now that auto increments are gone, drop all primary keys
	$sql = "ALTER TABLE `$tablename` DROP PRIMARY KEY";  
	$check = $db->query($sql);   // ignoring errors because will get error if $tablename has no primary keys
 
 
	// Check to see that columns are defined properly and drop unnecessary columns
	// Scan through all existing columns in $tablename to ensure they match the definitions in $cols array
	$curret_cols = array();  // array of existing columns, needed below to add missing columns
	$sql = "DESCRIBE `$tablename`";
	$res = $db->query($sql);
	if (DB::IsError($res)) {
		die_freepbx( "SQL failed $tablename<br>($sql)<br>" . $res->getMessage() . "<br>");   
	}
	while($row = $res->fetchRow())  {
		if(array_key_exists($row[0],$cols)) {
			$curret_cols[] = $row[0];
			//make sure it has the latest definition
			$sql = "ALTER TABLE `$tablename` MODIFY `".$row[0]."` ".$cols[$row[0]];
			$check = $db->query($sql);
			if (DB::IsError($check)) {
			die_freepbx( "In table $tablename cannot update column ".$row[0]."<br>($sql)<br>" . $check->getMessage() .  "<br>");
			}
		}  
	}
 
	//add any missing columns that are not already in the table
	foreach($cols as $key=>$val)  {
		if(!in_array($key,$curret_cols)) {
			$sql = "ALTER TABLE `$tablename` ADD `".$key."` ".$val;
			$check = $db->query($sql);
			if (DB::IsError($check)) {
			die_freepbx( "In table $tablename cannot add column ".$key."<br>($sql)<br>" . $check->getMessage() . "<br>");
			} else {
				echo "In table $tablename added column ".$key."<br>";
			}
		}
	}
 
	
	// remove unneeded columns from $tablename
	$sql = "DESCRIBE `$tablename`";
	$res = $db->query($sql);
	if (DB::IsError($res)) {
		die_freepbx( "SQL failed $tablename<br>($sql)<br>" . $res->getMessage() . "<br>");   
	}
	while($row = $res->fetchRow())  {
		if(!array_key_exists($row[0],$cols)) {
			//remove the column
			$sql = "ALTER TABLE `$tablename` DROP COLUMN `".$row[0]."`";
			$check = $db->query($sql);
			if(DB::IsError($check)) {
			echo "In table $tablename cannot remove column ".$row[0]."<br>($sql)<br>" . $check->getMessage() . "<br>";  //not fatal error
			} else {
				echo "In table $tablename removed unused column ".$row[0]."<br>";
			}
		}
	}	
}
#====================================================================================
function fputx ($stdlog,$msg)
# Output $msg with date/time prefix
{
#	$w = getdate();
#	$tmp=  $w['hours'].':'.$w['minutes'].':'.$w['seconds'].'  '.$w['mday'].' '.$w['month'].' '.$w['year'].': ';
	$tmp=date('H:i:s  j M Y  ');
	fputs( $stdlog, $tmp.$msg);
}
#====================================================================================
