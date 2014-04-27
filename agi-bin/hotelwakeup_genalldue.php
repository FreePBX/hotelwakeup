#!/usr/bin/php
<?php
//include bootstrap
$bootstrap_settings['freepbx_auth'] = false;
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) 
	include_once('/etc/asterisk/freepbx.conf');
	hotelwakeup_genalldue("");
