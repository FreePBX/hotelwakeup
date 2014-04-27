<?php
/*************** Wakeup Calls Module  ***************
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

History:
Put into module format by tshif 2/17/2009
PHP Programming by Swordsteel 2/17/2009
Modified by Les Desser starting 9 Apr 2014 based on version 2.11.3

Currently maintained by the PBX Open Source Software Alliance
https://github.com/POSSA/Hotel-Style-Wakeup-Calls
**********************************************************/

$tabindex = 0;

#-----------------------------------------------------------------------------
# Save the Config values if the 'Submit' button (B1) been pressed
# The design allows for multiple config records from which the user
# could choose.  
# Initially, the config key 'WUC' is hard coded.
if (isset($_POST['B1'])){hotelwakeup_saveconfig("WUC");}
#-----------------------------------------------------------------------------
# ?? Temp button/code to force creation of call files that are due
if (isset($_POST['G1'])){hotelwakeup_genalldue("");}
#-----------------------------------------------------------------------------
# ?? Temp button/code to force creation of call file for 1 schedule
if (isset($_POST['G2'])){hotelwakeup_genalldue($_POST['pkey']);}
#-----------------------------------------------------------------------------
# ?? Temp button/code to run special code
if (isset($_POST['G3'])){include('install.php');}
#-----------------------------------------------------------------------------
# ?? Temp button/code to run special code
if (isset($_POST['G4'])){include('aatest.php');}
#-----------------------------------------------------------------------------
# Delete selected alarm file if DELETE button clicked
# This will delete the selected .call file
# This is not relevant for schedules set up in the SQL database - see DELETE2
if(isset($_POST['DELETE'])) {
	if (file_exists($_POST['filename'])) {
		unlink($_POST['filename']);
	}
}
#-----------------------------------------------------------------------------
# Delete selected SQL scheduled alarm if DELETE2 button clicked
# This will delete the selected SQL DB record
if(isset($_POST['DELETE2'])) {
	hotelwakeup_deleterow('hotelwakeup_calls',$_POST['pkey'],'id_schedule',"I",&$OK);
	if ($OK=="YES") {
		echo "<script type='text/javascript'>\n";
		echo "alert('Schedule Deleted OK')";
		echo "</script>";
	}
}
#-----------------------------------------------------------------------------
# Process form if Schedule button clicked
if(isset($_POST['SCHEDULE'])) {
	$HH=$_POST['HH'];
	$MM=$_POST['MM'];
	$EXT=$_POST['ExtBox'];
	$DD=$_POST['DD'];
	$MON = $_POST['MON'];
	$YYYY = $_POST['YYYY'];
	$REPP = $_POST['thisItem'];

# Validate destination (EXT), date and time format as PHP accepts anything 
	$baddatetime = false;
	$msg="";
	if ($EXT == "") {
		# Set error if not already error
		if (!$baddatetime) {
			$msg="Destination is blank";
			$baddatetime = true; 
		}
	}
	if ($HH == "") {$HH = "0";}
	if ($MM == "") {$MM = "0";}
	if ($HH <= -1 || $HH > 23 || $MM <= -1 || $MM >59 ) {
		if (!$baddatetime) {
			$msg="Time is invalid";
			$baddatetime = true; 
		}
	}
	if (!checkdate($MON , $DD , $YYYY )) {
		if (!$baddatetime) {
			$msg="Date is invalid";
			$baddatetime = true;
		}
	}
# date+time must be in the future
	$time_now = time( );
	$timewakeup = mktime($HH , $MM, 0, $MON, $DD, $YYYY );
	if ($timewakeup <= $time_now) {
		if (!$baddatetime) {
			$msg="Date/Time is in the past";
			$baddatetime = true; 
		}
	}	
	if ($baddatetime) {
# Abandon .call file creation and pop up a js alert to the user if error
		echo "<script type='text/javascript'>\n";
		echo "alert('Cannot schedule the call due to ".$msg."')";
		echo "</script>";
	}
	else
	{

# All OK: Here starts code to process the new schedule input data
# Old code removed and moved to end of file as may be useful later (hotelwakeup_gencallfile)

# Convert 'repeat' value to single letter code
	if ($REPP == "NONE") {$repcode="X";}
		elseif ($REPP == "day") {$repcode="D";}
		elseif ($REPP == "week") {$repcode="W";}
		elseif ($REPP == "month") {$repcode="M";}
	# Save details in DB and return error message - if any.
		$errmsg=hotelwakeup_saveschedule("WUC", $EXT,$timewakeup,$repcode);
		if ($errmsg<>"") {
			$tmp= explode("|", $errmsg); # split into 2 parts (err msg + sql)
			hotelwakeup_reportsqlerror($tmp[1],$tmp[0],"I",'page.hotelwakeup.php');
		}
		else {
			# Confirm that it has been done
			echo "<script type='text/javascript'>\n";
			echo "alert('Schedule has been saved')";
			echo "</script>";		
		}
# Here ends the block of code if the Schedule button is clicked 
	}
}
#===============================================================================
# All options fall through to here
#===============================================================================
# Displays a blank form with existing config data
# together with the list of existing scheduled calls

# Get module config info
$cfg_data = hotelwakeup_getconfig("WUC");

# Get various module properties (name, version number, etc)
$module_local = hotelwakeup_xml2array("modules/hotelwakeup/module.xml");

# Pre-populate some input fields with current day if $_POST values unavailable
# Get current date/time values into associative array
$w = getdate();
if (!$MON) { $MON  = $w['mon'];}
if (!$DD)  { $DD   = $w['mday'];}
if (!$YYYY){ $YYYY = $w['year'];}

?>
<h1><b>Wake Up Calls</b></h1>
<hr>
Wake Up calls can be used to schedule a reminder or wakeup call to any valid destination.<br>
To schedule a call, dial the feature code assigned in FreePBX Feature Codes or use the<br>
form below.<br>

<h2><b>Schedule a New Call:</b></h2>
<?php
echo "<FORM NAME='InsertFORM'  ACTION='' METHOD=POST>Destination: <INPUT TYPE='TEXTBOX' NAME='ExtBox' VALUE='$EXT' SIZE='12' MAXLENGTH='20'>";
echo "HH:MM <INPUT TYPE='TEXTBOX' NAME='HH' autocomplete='off' VALUE='$HH' SIZE='1' MAXLENGTH='2'>:";
echo "<INPUT TYPE='TEXTBOX' NAME='MM' autocomplete='off' VALUE='$MM' SIZE='1' MAXLENGTH='2'>";
echo "DD / MM / YYYY <INPUT TYPE='TEXTBOX' NAME='DD' autocomplete='off' SIZE='1' MAXLENGTH='2' VALUE='$DD'>/";
echo "<INPUT TYPE='TEXTBOX' NAME='MON' autocomplete='off' SIZE='1' MAXLENGTH='2' VALUE='$MON'>/";
echo "<INPUT TYPE='TEXTBOX' NAME='YYYY' autocomplete='off' SIZE='1' MAXLENGTH='4' VALUE='$YYYY'>";
echo 'Frequency: <SELECT id="repeat_cycle" name="thisItem" tabindex="<?php echo ++$tabindex;?>">
	<OPTION VALUE="NONE">Once</option>
	<OPTION VALUE="day" <?php if ($thisItem=="day") echo _("selected=\"selected\""); ?>Daily</OPTION>
	<OPTION VALUE="week" <?php if ($thisItem=="week") echo _("selected=\"selected\""); ?>Weekly</OPTION>
	<OPTION VALUE="month" <?php if ($thisItem=="month") echo _("selected=\"selected\""); ?>Monthly</OPTION>
	</SELECT>';
echo "<INPUT TYPE=\"SUBMIT\" NAME=\"SCHEDULE\" VALUE=\"SCHEDULE\">\n";
echo "</FORM>\n";
#-----------------------------------------------------------------------------
echo "<hr>";
echo "<h2><b>Scheduled Calls:</b></h2>\n";
#-----------------------------------------------------------------------------
# List schedule records in SQL db
# Page is static, so add button to refresh table
echo "<FORM NAME=\"refresh2\" ACTION=\"\" METHOD=POST><INPUT NAME=\"RefreshTable2\" TYPE=\"SUBMIT\" VALUE=\"Refresh Table\"></form>\n";
# Table header
echo "<TABLE cellSpacing=1 cellPadding=1 width=500 border=1 >\n" ;
echo "<TD>Time</TD><TD>Date</TD><TD>Destination</TD><TD>Repeating?</TD><TD>Delete</TD></TR>\n" ;

# Fill table based on SQL table
$count2 = 0;
# Include only schedules with IDs equal to "WUC" (later enhancement will allow user to select key)
$results = hotelwakeup_listschedule("WUC");
# Get all rows 
if (isset($results)) {
	foreach ($results as $schedule) {
		# Create a date string to display from the file timestamp = scheduled date for alarm
		$stime = $schedule['time'];
		$filedate = date(M,$stime)." ".date(d,$stime)." ".date(Y,$stime);
		# Create a time string to display from the file timestamp = scheduled time for alarm
		$filetime = date(H,$stime).":".date(i,$stime);
		$idschedule=$schedule['id_schedule'];
		# Convert 'repeat' code to text
		$rep=$schedule['per'];
		if ($rep == "X") {$repcode="No";}
		elseif ($rep == "D") {$repcode="Daily";}
		elseif ($rep == "W") {$repcode="Weekly";}
		elseif ($rep == "M") {$repcode="Monthly";}
		echo "<TR><TD><FORM NAME=\"UpdateFORM\" ACTION=\"\" METHOD=POST><FONT face=verdana,sans-serif>"
		.$filetime
		."</TD><TD>".$filedate
		."</TD><TD>".$schedule['ext']
		."</TD><TD>".$repcode
		."</TD><TD><input type=\"hidden\" id=\"pkey\" name=\"pkey\" value=\"$idschedule\">
		<INPUT NAME=\"DELETE2\" TYPE=\"SUBMIT\" VALUE=\"Delete\">
		<INPUT NAME=\"G2\" TYPE=\"SUBMIT\" VALUE=\"Generate\">
		</TD></FORM>\n";
		$count2++;
	}
}
echo "</TABLE>\n";
if (!$count2){print "There are no scheduled calls";}		
#-----------------------------------------------------------------------------
# List of existing .call files
# Page is static, so add button to refresh table
echo "<FORM NAME=\"refresh\" ACTION=\"\" METHOD=POST><INPUT NAME=\"RefreshTable\" TYPE=\"SUBMIT\" VALUE=\"Refresh Table\"></form>\n";
echo "<TABLE cellSpacing=1 cellPadding=1 width=400 border=1 >\n" ;
echo "<TD>Time</TD><TD>Date</TD><TD>Destination</TD><TD>Delete</TD></TR>\n" ;

# Check spool directory and create a table listing all .call files created by this module
$count = 0;
# Include only file names starting with "WUC" and ending in ".call"
$files = glob("/var/spool/asterisk/outgoing/wuc*.call");
foreach($files as $file) {
# Create a date string to display from the file timestamp = scheduled date for alarm
	$filedate = date(M,filemtime($file))." ".date(d,filemtime($file))." ".date(Y,filemtime($file));
# Create a time string to display from the file timestamp = scheduled time for alarm
	$filetime = date(H,filemtime($file)).":".date(i,filemtime($file));
# Break up file name into parts and use relevant parts
	$filenamebits = explode(".", $file); 
	If ($filenamebits <> '') {
		$wucext = $filenamebits[3];  # [3] = Extension number 
 		echo "<TR><TD><FORM NAME=\"UpdateFORM\" ACTION=\"\" METHOD=POST><FONT face=verdana,sans-serif>"
			.$filetime
			."</TD><TD>".$filedate
			."</TD><TD>".$wucext
			."</TD><TD><input type=\"hidden\" id=\"filename\" name=\"filename\" value=\"$file\">
			<INPUT NAME=\"DELETE\" TYPE=\"SUBMIT\" VALUE=\"Delete\">
			</TD></FORM>\n";
	}
	$count++;
}
echo "</TABLE>\n";
if (!$count){print "There are no existing call files";}
#-----------------------------------------------------------------------------
?>
<br>
<hr>
<form NAME="SAVECONFIG" id="SAVECONFIG" method="POST" action="">
<h2><b>Module Configuration:</b></h2>
By default, Wake Up calls are only made back to the Caller ID of the user which requests them.<br>
When the Operator Mode is enabled, certain extensions are identified to be able to request a <br>
Wake Up call for any valid internal or external destination.<br><br>
<table border="0" width="430" id="table1">
  <tr>
    <td width="153"><a href="javascript: return false;" class="info">Operator Mode: <span><u>ENABLE</u> Operator Mode to allow designated extentions to create wake up calls for any valid destination.<br><u>DISABLE</u> Calls can only be placed back to the caller ID of the user scheduling the wakeup call.</span></a></td>
    <td width="129">
<?php 
echo "<input type=\"radio\" value=\"0\" name=\"operator_mode\"".(($cfg_data[operator_mode]==0)?' checked':'').">\n";
?> 
Disabled&nbsp;</td>
    <td>
<?php
echo "<input type=\"radio\" value=\"1\" name=\"operator_mode\"".(($cfg_data[operator_mode]==1)?' checked':'').">\n";
?>
&nbsp; Enabled</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Max Dest. Length: <span>This controls the maximum number of digits an operator can send a wakeup call to. Set to 10 or 11 to allow wake up calls to outside numbers.</span></a></td>
    <td width="129">&nbsp;
<?php
echo "<input type=\"text\" name=\"extensionlength\" size=\"8\" value=\"{$cfg_data[extensionlength]}\" style=\"text-align: right\">Digits\n ";
?>
</td>
    <td> &nbsp;</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Operator Extensions: <span>Enter the Caller ID's of each telephone you wish to be recognized as an `Operator`.  Operator extensions are allowed to create wakeup calls for any valid destination. Numbers can be extension numbers, full caller ID numbers or Asterisk dialing patterns.</span></a></td>
    <td colspan="2">
<?php
echo "<input type=\"text\" name=\"operator_extensions\" size=\"37\" value=\"{$cfg_data[operator_extensions]}\">\n";
?>
    </td>
  </tr>
  <tr>
    <td width="153">&nbsp;</td>
    <td colspan="2">(Use a comma separated list)</td>
  </tr>
</table>

<table border="0" width="428" id="table2">
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Ring Time:<span>The number of seconds for the phone to ring. Consider setting lower than the voicemail threshold or the wakeup call can end up going to voicemail.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"waittime\" size=\"13\" value=\"{$cfg_data[waittime]}\" style=\"text-align: right\">\n";
?> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Retry Time:<span>The number of seconds to wait between retrys.  A 'retry' happens if the wakeup call is not answered.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"retrytime\" size=\"13\" value=\"{$cfg_data[retrytime]}\" style=\"text-align: right\">\n";
?> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Max Retries:<span>The maximum number of times the system should attempt to deliver the wakeup call when there is no answer.  Zero retries means only one call will be placed.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"maxretries\" size=\"13\" value=\"{$cfg_data[maxretries]}\" style=\"text-align: right\">\n";
?> Tries
    </td>
  </tr>

  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Wake Up Caller ID:<span><u>First Box: </u>Enter the CNAM (Caller ID Name) to be sent by the system when placing the wakeup calls.  Enclose this string with " if required by your system.<br><u>Second Box: </u>Enter the CID (Caller ID number) of the Caller ID to be sent when the system places wake up calls.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"cnam\" size=\"13\" value=\"{$cfg_data[cnam]}\" style=\"text-align: center\"> **\n";
echo "&lt;<input type=\"text\" name=\"cid\" size=\"5\" value=\"{$cfg_data[cid]}\" style=\"text-align: center\">&gt;\n";
?>
    </td>
  </tr>
  
 <tr>
    <td width="155"><a href="javascript: return false;" class="info">Application Type:<span>Application to process the wake up call.  Usually set to: AGI</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"application\" size=\"13\" value=\"{$cfg_data[application]}\" style=\"text-align: center\">\n";
?>
    </td>
  </tr>  
<tr>
    <td width="155"><a href="javascript: return false;" class="info">Application Data:<span>PHP file name to call to process the call.  Usually set to: wakeconfirm.php</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"data\" size=\"37\" value=\"{$cfg_data[data]}\" style=\"text-align: left\">\n";
?>
    </td>
  </tr> 
  
</table>
<small>**Some systems require quote marks around the textual caller ID. You may include the " " if needed by your system.</small>
<br><br><input type="submit" value="Submit" name="B1">

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Generate Call Files" name="G1">
<input type="submit" value="Run Install.php" name="G3">
<input type="submit" value="Run aatest.php" name="G4">
</FORM>

<hr>
<h2><b>System Settings:</b></h2>
For scheduled calls to be delivered at the correct time, the system time zone and current time must be set properly.<br>
The system is reporting the following time zone and time:<br>
<b>Time zone:</b>  <?php echo date_default_timezone_get() ?><br>
<?php echo _("<b>System time:</b> ")?> <span id="idTime">00:00:00</span>

<script>
var hour = <?php $l = localtime(); echo $l[2]?>;
var min  = <?php $l = localtime(); echo $l[1]?>;
var sec  = <?php $l = localtime(); echo $l[0]?>;
//=============================================================================
//wakeupcalls stole this from timegroups
//who stole this from timeconditions
//who stole it from http://www.aspfaq.com/show.asp?id=2300
function PadDigits(n, totalDigits) 
{ 
	n = n.toString(); 
	var pd = ''; 
	if (totalDigits > n.length) 
	{ 
		for (i=0; i < (totalDigits-n.length); i++) 
		{ 
			pd += '0'; 
		} 
	} 
	return pd + n.toString(); 
} 
//=============================================================================
function updateTime() {
	sec++;
	if (sec==60) {min++; sec = 0;}	
	if (min==60) {hour++; min = 0;}
	if (hour==24) {hour = 0;}
	
	document.getElementById("idTime").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
	setTimeout('updateTime()',1000);
}

updateTime();
</script>

<?php
print '<p align="center" style="font-size:11px;">Wake Up Calls Module version '.$module_local['module']['version'];
print '<br>The module is maintained by the developer community at the <a target="_blank" href="http://pbxossa.org"> PBX Open Source Software Alliance</a></p>';
#=============================================================================
/*************** Removed old code ***************
	function CheckWakeUpProp($file) {
		$myresult = '';
		$file =basename($file);   #LD Extract file name without path
			$WakeUpTmp = explode(".", $file); #LD split into parts
			$myresult[0] = $WakeUpTmp[1]; #LD Time 
			$myresult[1] = $WakeUpTmp[3]; #LD Extension
      $myresult[2] = $WakeUpTmp[4]; #LD added: Repeat parameter
		return $myresult;
   	}
********************* End of removed code **************************/

/*************** Removed old code (SCHEDULE button) ***************
# Get module config info for writing the file
# The design allows for multiple config records from which the user
# could choose.  Initially, the default config key 'WUC' is hard-coded.
# To see the layout of the config record, look at install.php 

  	$cfg_data = hotelwakeup_getconfig("WUC");  // module config provided by user
if ($REPP == "") {$REPP = "NONE";}    ## ?? Temp  
  	$foo = array(
  		time  => $timewakeup,
  		ext => $EXT,
  		maxretries => $cfg_data[maxretries],
  		retrytime => $cfg_data[retrytime],
  		waittime => $cfg_data[waittime],
      	rep => $REPP,
  		callerid => $cfg_data[cnam]." <".$cfg_data[cid].">",
  		application => $cfg_data[application],
  		data => $cfg_data[data],
 	);

  	hotelwakeup_gencallfile($foo);
********************* End of removed code **************************/
?>