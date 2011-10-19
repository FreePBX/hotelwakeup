Hotel Style Wakeups are being uninstalled.<br>
<?php
// drop the tables
$sql = "DROP TABLE IF EXISTS hotelwakeup";

$check = $db->query($sql);
if (DB::IsError($check)) {
        die_freepbx( "Can not delete `hotelwakeup` table: " . $check->getMessage() .  "\n");
}

//global $asterisk_conf;
//require_once("modules/hotelwakeup/functions.inc.php");
//needreload();
?>