<?php
require_once 'DB.php';

define("AMP_CONF", "/etc/amportal.conf");

$amp_conf = parse_amportal_conf(AMP_CONF);
if (count($amp_conf) == 0) {
	fatal("FAILED");
}

function parse_amportal_conf($filename) {
	$file = file($filename);
	foreach ($file as $line) {
		if (preg_match("/^\s*([a-zA-Z0-9_]+)\s*=\s*(.*)\s*([;#].*)?/",$line,$matches)) { 
			$conf[ $matches[1] ] = $matches[2];
		}
	}
	return $conf;
}


# DataBase config
$dbConf['user'] = $amp_conf['AMPDBUSER'];
$dbConf['pass'] = $amp_conf['AMPDBPASS'];
$dbConf['name'] = $amp_conf['AMPDBNAME'];

# the class is pict out from Angel By Swordsteel
class MySQL {
	
	private $dbId = null;
	private $dbPers = null;
	private $dbUser = null;
	private $dbPass = null;
	private $dbHost = null;
	private $dbName = null;
	private $dbQuery = null;
	
	private $row = array();
	private $rowset = array();
	private $num_queries = 0;

	/**
 	* clsse construct
	*/
	public function __construct($dbConf) {
		$this->dbPers = (isset($dbConf['pers']))? $dbConf['pers']: true;
		$this->dbUser = (isset($dbConf['user']))? $dbConf['user']: '';
		$this->dbPass = (isset($dbConf['pass']))? $dbConf['pass']: '';
		$this->dbHost = (isset($dbConf['host']))? $dbConf['host']: 'localhost';
		$this->dbName = (isset($dbConf['name']))? $dbConf['name']: '';		
	}

	/**
 	* clsse destruct
	*/
	public function __destruct() {
		$this->disconnect();
	}
	
	/**
 	* Open a connection to a MySQL Server.
 	*
	* @return Returns a MySQL link identifier on success, or FALSE on failure.
	*/
	public function connect() {
		if($this->dbPers) {
			$this->dbId = @mysql_pconnect($this->dbHost, $this->dbUser, $this->dbPass);
		}else {
			$this->dbId = @mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
		}
		if($this->dbId) {
			if($this->dbName != '') {
				$dbSel = @mysql_select_db($this->dbName);
				if(!$dbSel) {
					@mysql_close($this->db_id);
					$this->dbId = $dbSel;
				}
			}
			return $this->dbId;
		}else {
			return false;
		}
	}

	/**
 	* Close MySQL connection.
 	*
	* @return Returns TRUE on success or FALSE on failure.
	*/
	public function disconnect() {
		if($this->dbId) {
			if($this->dbQuery) {
				@mysql_free_result($this->dbQuery);
			}
			$result = @mysql_close($this->dbId);
			return $result;
		}else {
			return false;
		}		
	}

	/**
 	* Returns the text and the numerical value of the error message from previous MySQL operation.
 	*
	* @return error numer and error text.
	*/
	function error() {
		$result[] = "MySQL";
		$result[] = @mysql_errno($this->db_connect_id);
		$result[] = @mysql_error($this->db_connect_id);
		return $result;
	}

	/**
 	* Send a MySQL query
 	*
	* @param string $query
	* @return a resource on success, or FALSE on error.
	*/
	public function query($query) {
		unset($this->dbQuery);
		if($query != "") {
			$this->dbQuery = @mysql_query($query, $this->dbId);
		}
		if($this->dbQuery) {
			unset($this->row[$this->dbQuery]);
			unset($this->rowset[$this->dbQuery]);
			return $this->dbQuery;
		}else {
			return false;
		}
	}


	/**
 	* Fetch a result row as an associative array.
 	*
	* @param unknown_type $qId
	* @param Integer $type result type
	* @return Returns an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows. 
	*/
	public function fetch_assoc($qId = 0) {
		if(!$qId) {
			$qId = $this->dbQuery;
		}
		if($qId) {
			$this->row[$qId] = @mysql_fetch_assoc($qId);
			return $this->row[$qId];
		}else {
			return false;
		}
	}

}

$db = new MySQL($dbConf);
$db->connect();

   	// ------------
	// CONFIG Parms
	// ------------
	
	// If the application is having problems you can log to this file
	$parm_error_log = '/tmp/wakeup.log';
	
	// Set to 1 to turn on the log file
	$parm_debug_on = 1;	
	
	// This is where the Temporary WakeUp Call Files will be created
	$parm_temp_dir = '/var/spool/asterisk/tmp';
	// Create Temporary WakeUp directory if it doesn't exist, as 0775 (rwxrwxrx), limited by overall umask
	if (!is_dir ($parm_temp_dir)) mkdir ($parm_temp_dir, 0775);
	
	// This is where the WakeUp Call Files will be moved to when finished
	$parm_call_dir = '/var/spool/asterisk/outgoing';
	
	// How many times to try the call
//	$parm_maxretries = 3;
	//$parm_maxretries = mysql_query('SELECT maxretries FROM hotelwakeup');

	
	// How long to keep the phone ringing
//	$parm_waittime = 60;		// How long to keep the phone ringing
	//$parm_waittime = mysql_query('SELECT waittime FROM hotelwakeup');

	
	// Number of seconds between retries
//	$parm_retrytime = 60;
	//$parm_retrytime = mysql_query('SELECT retrytime FROM hotelwakeup');

	
	// Caller ID of who the wakeup call is from Change this to the extension you want to display on the phone
//	$parm_wakeupcallerid = '"Wake Up Call" ';
	//$parm_wakeupcallerid = mysql_query('SELECT wakeupcallerid FROM hotelwakeup');
	
	
	// Set to 1 to use the Channel
	// Set to 0 for Caller ID,  Caller IS is assumed just a number ### or "Name Name" <##>
	// The big difference is when using caller ID, wakeup will call ANY phone with that extension number
	// Where using Channel will only wake up the one specific channel
	$parm_chan_ext = 0;
	
	// Set to 1 to allow 700# or 0700 to be entered for time, less than 4 digits you can press # key
	// Set to 0 if you require 4 digit entry for time 0500 or 1200 or 0000
	$parm_short_entry = 1;
	
	// ----------------------------------------------------
	// Which application to run when the call is connected.  
	//$parm_application = 'MusicOnHold';
	//$parm_data = '';
	
	// -- Use this for the ANNOY application
	$parm_application = 'AGI';
	$parm_data = 'wakeconfirm.php';
	// ----------------------------------------------------
	
	// Which method to use for time entry
	// 0 current method, if after 13:00 won't prompt for am/pm
	// 1 military time never prompt for am/pm
	// 2 always prompt for am/pm and only accept to 1259 for time
	$parm_prompt_ampm = 0;
	
	// Operator Mode, Allow an extension to key in wakeup calls for other extensions
//	$parm_operator_mode = 1;
	//$parm_operator_mode = mysql_query('SELECT operator_mode FROM hotelwakeup');
	
	// Operator Extension
	// Enter any extension that is allowed to enter in operator mode - Caller ID is used to validate
//	$parm_operator_extensions= array(  00, 00, 00);

	// The max length of an extension when entering by operator
//	$parm_operator_ext_len = 3;


	$sql="SELECT * FROM `hotelwakeup` LIMIT 1";
	$res = $db->query($sql);
	$row = $db->fetch_assoc($res);
	if(eregi(',', $row['operator_extensions'])) {
		$poe = explode(',', eregi_replace(' ', '', $row['operator_extensions']));
	}else {
		$poe = array($row['operator_extensions']);
	}
	$parm_operator_extensions = $poe;

$parm_operator_mode = $row['operator_mode'];
$parm_maxretries = $row['maxretries'];
$parm_waittime = $row['waittime'];

$parm_retrytime = $row['retrytime'];

$parm_wakeupcallerid = $row['wakeupcallerid'];
$parm_operator_ext_len = $row['extensionlength'];

	//-------------------
	// END CONFIG PARMS
	//-------------------

?>
