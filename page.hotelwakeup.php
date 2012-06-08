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

Currently maintained by the PBX Open Source Software Alliance
https://github.com/POSSA/Hotel-Style-Wakeup-Calls
Last modified Jun 8, 2012
**********************************************************/

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
        $DD=$_POST['DD'];
        $MON = $_POST['MON'];
        $YYYY = $_POST['YYYY'];

	//  could use a check here to prevent user from scheduling a call in the past

        // check for insufficient data
     if ($HH == "" || $Ext == "" || $DD == "" || $MON == "" || $YYYY == ""  )
     {
		// abandon .call file creation
     }
     else
     {

        // Get module config info for writing the file $parm_application and $parm_data are used to define what the wakup call
        // does when answered.  Currently these are not part of the module config options but need to be to allow users to choose
        // their own destination
	$date = hotelwakeup_getconfig();  // module config provided by user
	$parm_application = 'AGI';
	$parm_data = 'wakeconfirm.php';
        if ($MM == ""){
        	$MM = "0";
           	}

	$foo = array(
		time  => mktime( $HH , $MM, 0, $MON, $DD, $YYYY ),
		date => 'unused',
		ext => $Ext,
		maxretries => $date[0],
		retrytime => $date[2],
		waittime => $date[1],
		callerid => $date[4],
	        application => $parm_application,
	        data => $parm_data,
		);

	hotelwakeup_gencallfile($foo);
      }
endif;

// Get module config info
$date = hotelwakeup_getconfig();
$w = getdate();


?>
<h1><b>Wake Up Calls</b></h1>
<hr><br>
Wake Up calls can be used to schedule a hotel-style wakeup call to any valid destination.<br>
To schedule a call, dial the feature code assigned in FreePBX Feature Codes or use the<br>
form below.<br><br>

<h2><b>Schedule a new call:</b></h2>

<?php
echo "<FORM NAME=\"InsertFORM\"  ACTION=\"\" METHOD=POST>Destination: <INPUT TYPE=\"TEXTBOX\" NAME=\"ExtBox\" SIZE=\"12\" MAXLENGTH=\"20\">HH:MM <INPUT TYPE=\"TEXTBOX\" NAME=\"HH\" SIZE=\"2\" MAXLENGTH=\"2\">:\n";
echo "<INPUT TYPE=\"TEXTBOX\" NAME=\"MM\" SIZE=\"2\" MAXLENGTH=\"2\">DD:MM:YYYY <INPUT TYPE=\"TEXTBOX\" NAME=\"DD\" SIZE=\"2\" MAXLENGTH=\"2\" VALUE=".$w['mday'].">:\n";
echo "<INPUT TYPE=\"TEXTBOX\" NAME=\"MON\" SIZE=\"2\" MAXLENGTH=\"2\" VALUE=".$w['mon'].">:<INPUT TYPE=\"TEXTBOX\" NAME=\"YYYY\" SIZE=\"4\" MAXLENGTH=\"4\" VALUE=".$w['year'].">\n";
echo "<INPUT TYPE=\"SUBMIT\" NAME=\"SCHEDULE\" VALUE=\"SCHEDULE\">\n";
echo "</FORM>\n";

echo "<br><h2><b>Scheduled Calls:</b></h2>\n";
echo "<FORM NAME=\"UpdateFORM\" ACTION=\"\" METHOD=POST>\n";
echo "<TABLE cellSpacing=1 cellPadding=1 width=900 border=1 >\n" ;
echo "<TD>Time</TD><TD>Date</TD><TD>Destination</TD><TD>Delete</TD></TR>\n" ;

// check spool directory and create a table listing all .call files created by this module
$count = 0;
$files = glob("/var/spool/asterisk/outgoing/wuc*.call");
foreach($files as $file) {
	$myresult = CheckWakeUpProp($file);
	$filedate = date(M,filemtime($file))." ".date(d,filemtime($file))." ".date(Y,filemtime($file))  ; //create a date string to display from the file timestamp
        $filetime = date(H,filemtime($file)).":".date(i,filemtime($file));   //create a time string to display from the file timestamp
	If ($myresult <> '') {
		$h = substr($myresult[0],0,2);
		$m = substr($myresult[0],2,3);
		$wucext = $myresult[1];
 		echo "<TR><TD><FONT face=verdana,sans-serif>" . $filetime . "</TD><TD>".$filedate."</TD><TD>" .$wucext ."</TD><TD><INPUT NAME=\"DELETE\" TYPE=\"SUBMIT\" VALUE=\"".$myresult[0]. "-" . $wucext ."\"></TD>\n";
		}
	$count++;
	}
echo "</TABLE></FORM>\n";
if (!$count){
	print "No scheduled calls";
        }
?>
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
