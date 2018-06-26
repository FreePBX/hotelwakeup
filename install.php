<?php

//  Set default values - need mechanism to prevent overwriting existing values 
out("Installing Default Values");
$sql ="INSERT INTO hotelwakeup (maxretries, waittime, retrytime, cnam,             cid,    operator_mode, operator_extensions, extensionlength, application, data) ";
$sql .= "               VALUES ('3',        '60',     '60',      'Wake Up Calls',  '*68',  '1',           '00 , 01',           '4',             'AGI',        'wakeconfirm.php')";

$check = $db->query($sql);

// Register FeatureCode - Hotel Wakeup;
$fcc = new featurecode('hotelwakeup', 'hotelwakeup');
$fcc->setDescription('Wake Up Calls');
$fcc->setDefault('*68');
$fcc->update();
unset($fcc);
