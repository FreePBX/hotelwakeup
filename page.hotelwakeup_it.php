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
Last modified Oct 15, 2012
**********************************************************/

/***** remove check for updates now that module is pushed from FreePBX repo ***
// check to see if user has automatic updates enabled in FreePBX settings
$cm =& cronmanager::create($db);
$online_updates = $cm->updates_enabled() ? true : false;

// check dev site to see if new version of module is available
if ($online_updates && $foo = hotelwakeup_vercheck()) {
	print "<br>A <b>new version of this module is available</b> from the <a target='_blank' href='http://pbxossa.org'>PBX Open Source Software Alliance</a><br>";
	}
******************************************************************************/

// Process form if button B1 is clicked
if (isset($_POST['B1'])){
	hotelwakeup_saveconfig();
	}

// Process form if delete button clicked
if(isset($_POST['DELETE'])) {
	if (file_exists($_POST['filename'])) {
		unlink($_POST['filename']);
	}
}

//  Process form if Schedule button clicked
if(isset($_POST['SCHEDULE'])) {
	$HH=$_POST['HH'];
	$MM=$_POST['MM'];
	$Ext=$_POST['ExtBox'];
	$DD=$_POST['DD'];
	$MON = $_POST['MON'];
	$YYYY = $_POST['YYYY'];

	//  check to prevent user from scheduling a call in the past
	if ($MM == "") {
		$MM = "0";
	}
	$time_wakeup = mktime( $HH , $MM, 0, $MON, $DD, $YYYY );
	$time_now = time( );
	$badtime = false;
	if ( $time_wakeup <= $time_now )  {
		$badtime = true;
	}

	// check for insufficient data
	if ($HH == "" || $Ext == "" || $DD == "" || $MON == "" || $YYYY == "" || $badtime )  {
		// abandon .call file creation and pop up a js alert to the user
		echo "<script type='text/javascript'>\n";
		echo "alert('Non e\' possibile pianificare la chiamata, o a causa di dati insufficienti o perche\' il tempo inserito e\' gia\' passato.');\n";
		echo "</script>";
    }
	else
	{

	// Get module config info for writing the file $parm_application and $parm_data are used to define what the wakup call
	// does when answered.  Currently these are not part of the module config options but need to be to allow users to choose
	// their own destination
	$date = hotelwakeup_getconfig();  // module config provided by user
	$parm_application = 'AGI';
	$parm_data = 'wakeconfirm.php';

	$foo = array(
		time  => $time_wakeup,
		date => 'unused',
		ext => $Ext,
		maxretries => $date[maxretries],
		retrytime => $date[retrytime],
		waittime => $date[waittime],
		callerid => $date[cnam]." <".$date[cid].">",
		application => $parm_application,
		data => $parm_data,
	);

	hotelwakeup_gencallfile($foo);
	// Can't decide if I should clear the schedule variables ($HH, $MM, etc.) here to refresh schedule fields in GUI
	}
}

// Get module config info
$date = hotelwakeup_getconfig();
	$module_local = hotelwakeup_xml2array("modules/hotelwakeup/module.xml");

// Prepopulate date fields with current day if $_POST values unavailable
$w = getdate();
if (!$MON) { $MON  = $w['mon'];}
if (!$DD)  { $DD   = $w['mday'];}
if (!$YYYY){ $YYYY = $w['year'];}

?>
<h1><b>Funzione sveglia</b></h1>
<hr><br>
La funzione sveglia, puo' essere utilizzata per programmare un promemoria o campanello d'allarme per qualsiasi destinazione valida.<br>
Per pianificare una chiamata, chiamare il codice funzione specificato nei codici funzione FreePBX oppure utilizzare il
modulo qui sotto.<br><br>

<h2><b>Pianificare una nuova chiamata:</b></h2>

<?php
echo "<FORM NAME=\"InsertFORM\"  ACTION=\"\" METHOD=POST>Destinazione: <INPUT TYPE=\"TEXTBOX\" NAME=\"ExtBox\" VALUE=\"$Ext\" SIZE=\"12\" MAXLENGTH=\"20\">HH:MM <INPUT TYPE=\"TEXTBOX\" NAME=\"HH\" VALUE=\"$HH\" SIZE=\"2\" MAXLENGTH=\"2\">:\n";
echo "<INPUT TYPE=\"TEXTBOX\" NAME=\"MM\" VALUE=\"$MM\" SIZE=\"2\" MAXLENGTH=\"2\">DD / MM / YYYY <INPUT TYPE=\"TEXTBOX\" NAME=\"DD\" SIZE=\"2\" MAXLENGTH=\"2\" VALUE=\"$DD\">/\n";
echo "<INPUT TYPE=\"TEXTBOX\" NAME=\"MON\" SIZE=\"2\" MAXLENGTH=\"2\" VALUE=\"$MON\">/<INPUT TYPE=\"TEXTBOX\" NAME=\"YYYY\" SIZE=\"4\" MAXLENGTH=\"4\" VALUE=\"$YYYY\">\n";
echo "<INPUT TYPE=\"SUBMIT\" NAME=\"SCHEDULE\" VALUE=\"PIANIFICA\">\n";
echo "</FORM>\n";

echo "<br><h2><b>Chiamate programmate:</b></h2>\n";
// Page is static, so add button to refresh table
echo "<FORM NAME=\"refresh\" ACTION=\"\" METHOD=POST><INPUT NAME=\"RefreshTable\" TYPE=\"SUBMIT\" VALUE=\"Aggiorna Tabella\"></form>\n";
echo "<TABLE cellSpacing=1 cellPadding=1 width=900 border=1 >\n" ;
echo "<TD>Ora</TD><TD>Data</TD><TD>Destinazione</TD><TD>Cancella</TD></TR>\n" ;

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
 		echo "<TR><TD><FORM NAME=\"UpdateFORM\" ACTION=\"\" METHOD=POST><FONT face=verdana,sans-serif>" . $filetime . "</TD><TD>".$filedate."</TD><TD>" .$wucext ."</TD><TD><input type=\"hidden\" id=\"filename\" name=\"filename\" value=\"$file\"><INPUT NAME=\"DELETE\" TYPE=\"SUBMIT\" VALUE=\"Cancella\"></TD></FORM>\n";
	}
	$count++;
}
echo "</TABLE>\n";
if (!$count){
	print "Nessuna sveglia pianificata";
        }
?>
<br><br>

<form NAME="SAVECONFIG" id="SAVECONFIG" method="POST" action="">
<h2><b>Configurazione Modulo:</b></h2>
Per impostazione predefinita, il servizio sveglia e' attivabile solo per il proprio Caller ID, chiamando il codice di servizio.<br>
Quando la modalita' operatore e' abilitata, gli interni specificati sono abilitati ad attivare <br>
la sveglia per qualsiasi destinazione interna o esterna valida.<br><br>
<table border="0" width="430" id="table1">
  <tr>
    <td width="153"><a href="javascript: return false;" class="info">Modalita' Operatore: <span><u>ENABLE</u> Modalita' Operatore gli interni specificati sono abilitati ad attivare la sveglia per qualsiasi destinazione interna o esterna valida<br><u>DISABLE</u> il servizio sveglia e' attivabile solo per il proprio Caller ID, chiamando il codice di servizio.</span></a></td>
    <td width="129">
<?php 
echo "<input type=\"radio\" value=\"0\" name=\"operator_mode\"".(($date[operator_mode]==0)?' checked':'').">\n";
?> 
Disabilitata&nbsp;</td>
    <td>
<?php
echo "<input type=\"radio\" value=\"1\" name=\"operator_mode\"".(($date[operator_mode]==1)?' checked':'').">\n";
?>
&nbsp; Abilitata</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Lunghezza massima destinazione: <span>Questo controlla il numero massimo di cifre a cui un operatore puo' inviare una sveglia. Imposta a 10 o 11 per consentire la sveglia a numeri esterni.</span></a></td>
    <td width="129">&nbsp;
<?php
echo "<input type=\"text\" name=\"extensionlength\" size=\"8\" value=\"{$date[extensionlength]}\" style=\"text-align: right\">Numeri\n ";
?>
</td>
    <td> &nbsp;</td>
  </tr>
  <tr>
    <td width="180"><a href="javascript: return false;" class="info">Interni Operatore: <span>Inserisci il Caller ID di ogni telefono che si desidera essere riconosciuto come un `operatore`. Gli interni operatore sono autorizzati a creare sveglie telefoniche per qualsiasi destinazione valida. I numeri possono essere numeri interni, numeri ID chiamante completi o modelli di composizione Asterisk.</span></a></td>
    <td colspan="2">
<?php
echo "<input type=\"text\" name=\"operator_extensions\" size=\"37\" value=\"{$date[operator_extensions]}\">\n";
?>
    </td>
  </tr>
  <tr>
    <td width="153">&nbsp;</td>
    <td colspan="2">(Utilizzare un elenco separato da virgola)</td>
  </tr>
</table>

<table border="0" width="428" id="table2">
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Durata Squillo:<span>Il numero di secondi che il telefono deve squillare. Impostare inferiore alla soglia segreteria telefonica o la sveglia telefonica puo' finire per andare alla segreteria.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"waittime\" size=\"13\" value=\"{$date[waittime]}\" style=\"text-align: right\">\n";
?> Secondi
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Tempo tra tentativi:<span>Il numero di secondi di attesa tra tentativi. Il tentativo avviene se non si risponde alla sveglia telefonica.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"retrytime\" size=\"13\" value=\"{$date[retrytime]}\" style=\"text-align: right\">\n";
?> Secondi
    </td>
  </tr>
  <tr>
    <td width="155"><a href="javascript: return false;" class="info">Numero massimo tentativi:<span>Il numero massimo di volte che il sistema deve tentare di inviare la sveglia telefonica quando non c'e' risposta. Zero tentativi significa solo una chiamata verra' effettuata.</span></a></td>
    <td>
<?php
echo "<input type=\"text\" name=\"maxretries\" size=\"13\" value=\"{$date[maxretries]}\" style=\"text-align: right\">\n";
?> Tentativi
    </td>
  </tr>

  <tr>
    <td width="155"><a href="javascript: return false;" class="info">ID chiamante della sveglia telefonica:<span><u>Primo Campo: </u>Inserisci il CNAM (Nome ID chiamante) che deve essere inviato dal sistema durante la sveglia telefonica. Racchiudere questa stringa con " se richiesto dal sistema.<br><u>Secondo Campo: </u>Inserisci il CID (numero ID chiamante), da inviare quando viene effettuata la sveglia telefonica.</span></a></td>
    <td>
<?php
//echo "&quot;<input type=\"text\" name=\"calleridtext\" size=\"10\" value=\"{$date[cnam]}\" style=\"text-align: center\">&quot;\n";
echo "<input type=\"text\" name=\"calleridtext\" size=\"13\" value=\"{$date[cnam]}\" style=\"text-align: center\">\n";
echo "&lt;<input type=\"text\" name=\"calleridnumber\" size=\"5\" value=\"{$date[cid]}\" style=\"text-align: center\">&gt;\n";
?>
    </td>
  </tr>
</table>
<small>* Alcuni sistemi richiedono le virgolette intorno al testo ID chiamante. E' possibile includere le " " se necessario per il vostro sistema.</small>

<br><input type="submit" value="Invia" name="B1"><br><br>
</FORM>

<h2><b>Impostazioni di sistema:</b></h2>
Per pianificare le chiamate da effettuare all'ora corretta, il fuso orario del sistema e l'ora corrente devono essere impostati correttamente.<br>
Il sistema riporta la seguente fuso orario e l'ora:<br>
<b>Fuso orario:</b>  <?php echo date_default_timezone_get() ?><br>
<?php echo _("<b>System time:</b> ")?> <span id="idTime">00:00:00</span>

<script>
var hour = <?php $l = localtime(); echo $l[2]?>;
var min  = <?php $l = localtime(); echo $l[1]?>;
var sec  = <?php $l = localtime(); echo $l[0]?>;

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

function updateTime()
{
	sec++;
	if (sec==60)
	{
		min++;
		sec = 0;
	}	
		
	if (min==60)
	{
		hour++;
		min = 0;
	}

	if (hour==24)
	{
		hour = 0;
	}
	
	document.getElementById("idTime").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
	setTimeout('updateTime()',1000);
}

updateTime();
$(document).ready(function(){
	$(".remove_section").click(function(){
    if (confirm('<?php echo _("This section will be removed from this time group and all current settings including changes will be updated. OK to proceed?") ?>')) {
      $(this).parent().parent().prev().remove();
      $(this).closest('form').submit();
    }
  });
});
</script>

<?php
print '<p align="center" style="font-size:11px;">Wake Up Calls Module version '.$module_local['module']['version'];
print '<br>The module is maintained by the developer community at the <a target="_blank" href="http://pbxossa.org"> PBX Open Source Software Alliance</a><br></p>';


	function CheckWakeUpProp($file) {
		$myresult = '';
		$file =basename($file);
			$WakeUpTmp = explode(".", $file);
			$myresult[0] = $WakeUpTmp[1];
			$myresult[1] = $WakeUpTmp[3];
		return $myresult;
   	}
?>
