<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
$hotelwakeupClass = \FreePBX::Hotelwakeup();
echo $hotelwakeupClass->showPage("settings");
