<?php
// HotelWakeUp put into module format by tshif 2/17/2009
//PHP Programming by Swordsteel 2/17/2009
// including Wakeup scripts last modified by Jcoulter (credits included within)
//
// Last modified May 25, 2012

if(count($_POST)){
	hotelwakeup_saveconfig();
}
$date = hotelwakeup_getconfig();

//removed by lgaetz may 2012, xml2array is not defined in functions.inc.php and was causing issues on FreePBX distro
//$module_info = xml2array("modules/hotelwakeup/module.xml");
?>
<h1><b>Wake Up Calls</b></h1>
<hr><br>
Wake Up calls can be used to schedule a hotel-style wakeup call to any valid destination.<br>
To use the Wake-Up feature, dial the feature code assigned in FreePBX Feature Codes.<br><br>

<form method="POST" action="">
<h3><u>Operator Mode</u></h3>
By default, Wake Up calls are only made back to the Caller ID of the user which requests them. <br>When the Operator Mode is enabled, certain extensions are identified to be able to request a Wake Up call for any valid internal or external destination.<br><br>
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
<p><h3><u>General Configuration</u></h3></p>
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
<p align="center">
<br>
<input type="submit" value="Submit" name="B1"><br><br>
<?php
print '<p align="center" style="font-size:11px;">This module is based upon, and includes previous wake-up scripts whose authors are individually credited within.<br>Hotel Style Wakup Calls was put into FreePBX Module format by Tony Shiffer.<br>
The module is maintained by the developer community at <a target="_blank" href="https://github.com/POSSA/Hotel-Style-Wakeup-Calls"> https://github.com/POSSA/Hotel-Style-Wakeup-Calls</a><br></p>';
?>
