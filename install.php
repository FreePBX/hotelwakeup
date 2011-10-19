Installing Hotel Style Wake Up Calls<br>
<?php

// create the tables
$sql = "CREATE TABLE IF NOT EXISTS hotelwakeup (
	maxretries INT NOT NULL,
	waittime INT NOT NULL,
	retrytime INT NOT NULL,
	extensionlength INT NOT NULL,
	wakeupcallerid VARCHAR(30),
	operator_mode INT NOT NULL,
	operator_extensions VARCHAR(30)
);";
$check = $db->query($sql);
if (DB::IsError($check)) {
        die_freepbx( "Can not create `hotelwakeup` table: " . $check->getMessage() .  "\n");
}


?>Installing Default Values<br>
<?
# the easy why to debug your SQL Q its missing a value or something do let me do this :P
# is  that telling yo how yur puting it upp you dont need to have them in a serten order as long as the value ar in teh same place
$sql ="INSERT INTO hotelwakeup (maxretries, waittime, retrytime, wakeupcallerid,  operator_mode, operator_extensions, extensionlength) ";
$sql .= "               VALUES ('3',        '60',     '60',      '\"Wake Up Calls\" <*68>', '1',          '00 , 01',           '4')";

$check = $db->query($sql);
if (DB::IsError($check)) {
        die_freepbx( "Can not create default values in `hotelwakeup` table: " . $check->getMessage() .  "\n");
}


// Register FeatureCode - Hotel Wakeup;
$fcc = new featurecode('hotelwakeup', 'hotelwakeup');
$fcc->setDescription('Wake Up Calls');
$fcc->setDefault('*68');
$fcc->update();
unset($fcc);
?>