<?php
// HotelWakeUp put into module format by tshif 2/17/2009
//PHP Programming by Swordsteel 2/17/2009
// including Wakeup scripts last modified by Jcoulter (credits included within)
//
if(count($_POST)){
	hotelwakeup_saveconfig();
}
$date = hotelwakeup_getconfig();

$module_info = xml2array("modules/hotelwakeup/module.xml");
?>
<h1><b>Wake Up Calls</b></h1>
<hr><br>
Wake Up calls produces hotel-style wakeup calls to any extension.<br>
To use the Wake-Up feature, dial the feature code assigned in FreePBX Feature Codes.<br><br>

<form method="POST" action="">
<h3><u>Operator Mode</u></h3>
By default, Wake Up calls are made only to the extension which requests them. <br>When the Operator Mode is enabled, certain extensions are identified to be able to request a Wake Up call for any extension on the system.<br><br>
<table border="0" width="430" id="table1">
  <tr>
    <td width="153"><a href="javascript: return false;" class="info">Operator Mode: <span><u>ENABLE</u> Operator Mode to allow designated extentions to create wake up calls for any valid destination.<br><u>DISABLE</u> Operator Mode requires an extension to set its own wake up calls.</span></a></td>
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
    <td width="153"><a href="javascript: return false;" class="info">Extension Length: <span>Set this control to the number of digits in your extensions. Set to 10 or 11 to allow wake up calls to outside numbers.</span></a></td>
    <td width="129">&nbsp;
<select size="1" name="extensionlength">
<?php
echo "<option".(($date[3]==2)?' selected':'').">2</option>\n";
echo "<option".(($date[3]==3)?' selected':'').">3</option>\n";
echo "<option".(($date[3]==4)?' selected':'').">4</option>\n";
echo "<option".(($date[3]==5)?' selected':'').">5</option>\n";
echo "<option".(($date[3]==6)?' selected':'').">6</option>\n";
echo "<option".(($date[3]==7)?' selected':'').">7</option>\n";
echo "<option".(($date[3]==8)?' selected':'').">8</option>\n";
echo "<option".(($date[3]==9)?' selected':'').">9</option>\n";
echo "<option".(($date[3]==10)?' selected':'').">10</option>\n";
echo "<option".(($date[3]==11)?' selected':'').">11</option>\n";

?>
</select></td>
    <td> &nbsp;</td>
  </tr>
  <tr>
    <td width="153"><a href="javascript: return false;" class="info">Operator Extensions: <span>Enter the Caller ID's of each telephone you wish to be recognized as an `Operator`.  Operator extensions are allowed to create wakeup calls for any valid destination. Numbers entered must be formatted <i>exactly</i> as the caller ID of the device will be received by the system.</span></a></td>
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
    <td width="155"><a href="javascript: return false;" class="info">Ring Time:<span>The number of seconds for the phone to ring. Must be set lower than the voicemail threshold or the wakeup call can end up going to voicemail.</span></a></td>
    <td>
<select size="1" name="waittime">
<?php
echo "<option".(($date[1]==20)?' selected':'').">20</option>\n";
echo "<option".(($date[1]==30)?' selected':'').">30</option>\n";
echo "<option".(($date[1]==40)?' selected':'').">40</option>\n";
echo "<option".(($date[1]==50)?' selected':'').">50</option>\n";
echo "<option".(($date[1]==60)?' selected':'').">60</option>\n";
?>
</select> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Retry Time:<span>The number of seconds to wait between retrys.  A 'retry' happens if the wakeup call is not answered.</span></a></td>
    <td>
<select size="1" name="retrytime">
<?php
echo "<option".(($date[2]==20)?' selected':'').">20</option>\n";
echo "<option".(($date[2]==30)?' selected':'').">30</option>\n";
echo "<option".(($date[2]==40)?' selected':'').">40</option>\n";
echo "<option".(($date[2]==50)?' selected':'').">50</option>\n";
echo "<option".(($date[2]==60)?' selected':'').">60</option>\n";
echo "<option".(($date[2]==75)?' selected':'').">75</option>\n";
echo "<option".(($date[2]==90)?' selected':'').">90</option>\n";
echo "<option".(($date[2]==105)?' selected':'').">105</option>\n";
echo "<option".(($date[2]==120)?' selected':'').">120</option>\n";
?>
</select> Seconds
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Max Retries:<span>The maximum number of times the system should attempt to deliver the wakeup call when there is no answer.</span></a></td>
    <td>
<select size="1" name="maxretries">
<?php
echo "<option".(($date[0]==2)?' selected':'').">2</option>\n";
echo "<option".(($date[0]==3)?' selected':'').">3</option>\n";
echo "<option".(($date[0]==4)?' selected':'').">4</option>\n";
echo "<option".(($date[0]==5)?' selected':'').">5</option>\n";
echo "<option".(($date[0]==6)?' selected':'').">6</option>\n";
echo "<option".(($date[0]==7)?' selected':'').">7</option>\n";
echo "<option".(($date[0]==8)?' selected':'').">8</option>\n";
echo "<option".(($date[0]==9)?' selected':'').">9</option>\n";
echo "<option".(($date[0]==10)?' selected':'').">10</option>\n";
?>
</select>
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Wake Up Caller ID:<span><u>First Box: </u>Enter the Textual Portion of the caller ID to be sent by the system when placing the wakeup calls.<br><u>Second Box: </u>Enter the numberic portion of the Caller ID to be sent when the system places wake up calls.</span></a></td>
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
The module is maintained by the developer community at <a target="_blank" href="http://projects.colsolgrp.net/projects/show/hotelwakeup"> CSG Software Project Management</a><br><strong>Module version '.$module_info['module']['version'].'</strong></p>';
?>