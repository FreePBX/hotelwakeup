<?php
print "Hotel Style Wakeups are being uninstalled.<br>";

// drop the hotelwakup table
$sql = "DROP TABLE IF EXISTS hotelwakeup";
$check = $db->query($sql);
if (DB::IsError($check)) die_freepbx( "Can not delete `hotelwakeup` table: " . $check->getMessage() .  "\n");

// drop the hotelwakup_calls table
$sql = "DROP TABLE IF EXISTS hotelwakeup_calls";
$check = $db->query($sql);
if (DB::IsError($check)) die_freepbx( "Can not delete `hotelwakeup_calls` table: " . $check->getMessage() .  "\n");

# Delete the cron job associated with this application
$run = wuc_delete_cron();

// Consider adding code here to scan thru the spool/asterisk/outgoing directory and removing 
// already wakeup calls that have been scheduled
#=============================================================================== 
function wuc_delete_cron() {
	$wuc_comment_string = "Required for POSSA Wakeup Calls Module";   // Never change this string, comment is stored with cron job so we can automate cron install/un-install 
	$temp_file = sys_get_temp_dir()."/wuc_install";
	unlink($temp_file);
	$foo = shell_exec('crontab -l');
	$pos = strpos($foo, $wuc_comment_string); 
	if ($pos) {
		// remove cron jobs previously added by wuc module
		$regex = "~.*?".$wuc_comment_string."~";
		$foo = preg_replace($regex ,"",$foo);
		file_put_contents($temp_file, $foo);
		echo exec('crontab '.$temp_file);
	}
}