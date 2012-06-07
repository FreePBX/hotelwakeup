<?php
// HotelWakeUp put into module format by tshif 2/17/2009
//PHP Programming by Swordsteel 2/17/2009
// including Wakeup scripts last modified by Jcoulter (credits included within)
//
// Last modified Jun 7, 2012



// Process form if button B1 is clicked
if (isset($_POST['B1'])){
	hotelwakeup_saveconfig();
	}

// Process form if delete button clicked
if(isset($_POST['DELETE'])) :
	$WakeUpTmp = explode("-", $_POST['DELETE']);
	$filename = "/var/spool/asterisk/outgoing/wuc.".$WakeUpTmp[0].".ext.".$WakeUpTmp[1].".call";
	if (file_exists($filename)) {
		unlink($filename);
		}
endif;

//  Process form if Schedule button clicked
if(isset($_POST['SCHEDULE'])) :
	$HH=$_POST['HH'];
	$MM=$_POST['MM'];
	$Ext=$_POST['ExtBox'];

	$parm_call_dir = '/var/spool/asterisk/outgoing/';
	$parm_temp_dir = '/var/spool/asterisk/tmp/';

	$wtime = $HH.$MM;
	$w = getdate();
	$time_wakeup = mktime( substr( $wtime, 0, 2 ), substr( $wtime, 2, 2 ), 0, $w['mon'], $w['mday'], $w['year'] );
	$time_now = time( );
	if ( $time_wakeup <= $time_now ){
		$time_wakeup += 86400; // Add One Day on if time is in the past
		}

	$wakefile = "$parm_temp_dir/wuc.$wtime.ext.$Ext.call";
	$callfile = "$parm_call_dir/wuc.$wtime.ext.$Ext.call";
        // Get module config info for writing the file
	$date = hotelwakeup_getconfig();
	$parm_application = 'AGI';
	$parm_data = 'wakeconfirm.php';

	// Delete any old Wakeup call files this one will override
	if( file_exists( "$callfile" ) )
	{
		unlink( "$callfile" );
	}

	// Open up a wakeup file, write and close
	$wuc = fopen( $wakefile, 'w');
	fputs( $wuc, "channel: Local/$Ext@from-internal\n" );
	fputs( $wuc, "maxretries: $date[0]\n");
	fputs( $wuc, "retrytime: $date[2]\n");
	fputs( $wuc, "waittime: $date[1]\n");
	fputs( $wuc, "callerid: $date[4]\n");
	fputs( $wuc, "application: $parm_application\n");
	fputs( $wuc, "data: $parm_data\n");
	fclose( $wuc );

	// fix time of temp file and move to outgoing
	touch( $wakefile, $time_wakeup, $time_wakeup );
	rename( $wakefile, $callfile );
endif;

// Get module config info
$date = hotelwakeup_getconfig();


?>
<h1><b>Wake Up Calls</b></h1>
<hr><br>
Wake Up calls can be used to schedule a hotel-style wakeup call to any valid destination.<br>
To schedule a call, dial the feature code assigned in FreePBX Feature Codes or use the<br>
form below.<br><br>

<h2><b>Schedule a new call:</b></h2>

<FORM NAME="InsertFORM"  ACTION="" METHOD=POST>
Destination: <INPUT TYPE="TEXTBOX" NAME="ExtBox" SIZE="12" MAXLENGTH="20">
HH:MM <INPUT TYPE="TEXTBOX" NAME="HH" SIZE="2" MAXLENGTH="2">:<INPUT TYPE="TEXTBOX" NAME="MM" SIZE="2" MAXLENGTH="2">
<INPUT TYPE="SUBMIT" NAME="SCHEDULE" VALUE="SCHEDULE">
</FORM>
<br>
<h2><b>Scheduled Calls:</b></h2><?PHP

echo "<FORM NAME=\"UpdateFORM\" ACTION=\"\" METHOD=POST>\n";
echo "<TABLE cellSpacing=1 cellPadding=1 width=900 border=1 >\n" ;
echo "<TR><TD>Time</TD><TD>Extension</TD><TD>Delete</TD></TR>\n" ;


	$count = 0;

	$count++;
	$dir1 = "/var/spool/asterisk/outgoing";
	$files = glob($dir1."/wuc*.call");

       foreach($files as $file) {
		$myresult = CheckWakeUpProp($file);
		If ($myresult <> '') {
			$h = substr($myresult[0],0,2);
			$m = substr($myresult[0],2,3);
			$wucext = $myresult[1];
		 		echo "<TR><TD><FONT face=verdana,sans-serif>" . $h .":" . $m . "</TD><TD>" .$wucext ."</TD><TD><INPUT NAME=\"DELETE\" TYPE=\"SUBMIT\" VALUE=\"".$myresult[0]. "-" . $wucext ."\"></TD>\n";
			}
		}

echo "</TABLE>\n";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"num_rows\" VALUE=\"" .$count. "\">\n" ;
echo "</FORM>\n";?>
<br><br>

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
echo "<input type=\"radio\" value=\"0\" name=\"operator_mode\"".(($date[5]==0)?' checked':'').">\n";
?> 
Disabled&nbsp;</td>
    <td>
<?php
echo "<input type=\"radio\" value=\"1\" name=\"operator_mode\"".(($date[5]==1)?' checked':'').">\n";
?>
&nbsp; Enabled</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Max Destination Length: <span>This controls the maximum number of digits an operator can send a wakeup call to. Set to 10 or 11 to allow wake up calls to outside numbers.</span></a></td>
    <td width="129">&nbsp;
<?php
echo "<input type=\"text\" name=\"extensionlength\" size=\"8\" value=\"{$date[3]}\" style=\"text-align: right\">\n ";
?>Digits
</td>
    <td> &nbsp;</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Operator Extensions: <span>Enter the Caller ID's of each telephone you wish to be recognized as an `Operator`.  Operator extensions are allowed to create wakeup calls for any valid destination. Numbers entered must be formatted <i>exactly</i> as the caller ID of the device will be received by the system.</span></a></td>
    <td colspan="2">
<?php
echo "<input type=\"text\" name=\"operator_extensions\" size=\"37\" value=\"{$date[6]}\">\n";
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
echo "<input type=\"text\" name=\"waittime\" size=\"13\" value=\"{$date[1]}\" style=\"text-align: right\">\n";
?> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Retry Time:<span>The number of seconds to wait between retrys.  A 'retry' happens if the wakeup call is not answered.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"retrytime\" size=\"13\" value=\"{$date[2]}\" style=\"text-align: right\">\n";
?> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Max Retries:<span>The maximum number of times the system should attempt to deliver the wakeup call when there is no answer.  Zero retries means only one call will be placed.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"maxretries\" size=\"13\" value=\"{$date[0]}\" style=\"text-align: right\">\n";
?> Tries
    </td>
  </tr>

  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Wake Up Caller ID:<span><u>First Box: </u>Enter the CNAM (Caller ID Name) to be sent by the system when placing the wakeup calls.  Enclose this string with " if required by your system.<br><u>Second Box: </u>Enter the CID (Caller ID number) of the Caller ID to be sent when the system places wake up calls.</span></a></td>
    <td>
<?php
//echo "&quot;<input type=\"text\" name=\"calleridtext\" size=\"10\" value=\"{$date[8]}\" style=\"text-align: center\">&quot;\n";
echo "<input type=\"text\" name=\"calleridtext\" size=\"13\" value=\"{$date[8]}\" style=\"text-align: center\">\n";
echo "&lt;<input type=\"text\" name=\"calleridnumber\" size=\"5\" value=\"{$date[7]}\" style=\"text-align: center\">&gt;\n";
?>
    </td>
  </tr>
</table>
<small>*Some systems require quote marks around the textual caller ID. You may include the " " if needed by your system.</small>
<br><input type="submit" value="Submit" name="B1"><br><br>
</FORM>
<?php
print '<p align="center" style="font-size:11px;"><br>
The module is maintained by the developer community at the <a target="_blank" href="https://github.com/POSSA/Hotel-Style-Wakeup-Calls"> PBX Open Source Software Alliance</a><br></p>';


	function CheckWakeUpProp($file) {
		$myresult = '';
		$file =basename($file);
			$WakeUpTmp = explode(".", $file);
			$myresult[0] = $WakeUpTmp[1];
			$myresult[1] = $WakeUpTmp[3];
		return $myresult;
   	}

?>
