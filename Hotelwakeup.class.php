<?php
namespace FreePBX\modules;
/*
 * Class stub for BMO Module class
 * In _Construct you may remove the database line if you don't use it
 * In getActionbar change "modulename" to the display value for the page
 * In getActionbar change extdisplay to align with whatever variable you use to decide if the page is in edit mode.
 *
 */
class Hotelwakeup implements \BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new Exception("Not given a FreePBX Object");
		}
		$this->FreePBX = $freepbx;
		$this->db = $freepbx->Database;
	}
	public function install() {}
	public function uninstall() {}
	public function backup() {}
	public function restore($backup) {}
	public function doConfigPageInit($page) {}

	public function getActionBar($request) {
		$buttons = array();
		switch($request['display']) {
			case 'hotelwakeup':
				$buttons = array(
					'reset' => array(
						'name' => 'reset',
						'id' => 'reset',
						'value' => _('Reset'),
						'class' => 'hidden'
					),
					'submit' => array(
						'name' => 'submit',
						'id' => 'submit',
						'value' => _('Submit'),
						'class' => 'hidden'
					)
				);
			break;
		}
		return $buttons;
	}

	public function ajaxRequest($req, &$setting) {
		$setting['authenticate'] = false;
		$setting['allowremote'] = false;
		switch($req) {
			case "savecall":
			case "getable":
				return true;
			break;
		}
		return false;
	}

	public function ajaxHandler() {
		switch($_REQUEST['command']) {
			case "savecall":
				dbug($_POST);
			break;
			case "getable":
				return $this->getAllCalls();
			break;
		}
		return true;
	}

	public function showPage() {
		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == "delete" && !empty($_REQUEST['id']) && !empty($_REQUEST['ext'])) {
			$file = $this->FreePBX->Config->get('ASTSPOOLDIR')."/outgoing/wuc.".$_REQUEST['id'].".ext.".$_REQUEST['ext'].".call";
			if(file_exists($file)) {
				unlink($file);
			}
		}
		$action = !empty($_POST['action']) ? $_POST['action'] : '';
		$fcc = new \featurecode('hotelwakeup', 'hotelwakeup');
		$code = $fcc->getCode();
		switch($action) {
			case "settings":
				preg_match('/"(.*)" <(.*)>/',$_POST['callerid'],$matches);
				$this->saveConfig(array(
					"operator_mode" => $_POST['operator_mode'],
					"extensionlength" => $_POST['extensionlength'],
					"operator_extensions" => $_POST['operator_extensions'],
					"waittime" => $_POST['waittime'],
					"retrytime" => $_POST['retrytime'],
					"maxretries" => $_POST['maxretries'],
					"cid" => !empty($matches[2]) ? $matches[2] : $code,
					"cnam" => !empty($matches[1]) ? $matches[1] : _("Wake Up Calls")
				));
			break;
		}
		$content = load_view(__DIR__."/views/grid.php", array("code" => $code, "config" => $this->getConfig()));
		return load_view(__DIR__."/views/main.php",array("pageContent" => $content));
	}

	public function getConfig() {
		$sql = "SELECT * FROM hotelwakeup LIMIT 1";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		$fa = $sth->fetch(\PDO::FETCH_ASSOC);
		$fa['callerid'] = '"'.$fa['cnam'].'" <'.$fa['cid'].'>';
		return $fa;
	}

	public function saveConfig($options) {
		if(empty($options)) {
			return false;
		}
		$sql = "UPDATE `hotelwakeup` SET `maxretries` = ?, `waittime` = ?, `retrytime` = ?, `extensionlength` = ?, `cnam` = ?, `cid` = ?, `operator_mode` = ?, `operator_extensions` = ? LIMIT 1";
		$sth = $this->db->prepare($sql);
		return $sth->execute(array($options['maxretries'], $options['waittime'], $options['retrytime'], $options['extensionlength'], $options['cnam'], $options['cid'], $options['operator_mode'], $options['operator_extensions']));
	}

	public function CheckWakeUpProp($file) {
		$myresult = '';
		$file = basename($file);
		$WakeUpTmp = explode(".", $file);
		$myresult = null;
		if(!empty($WakeUpTmp[1]) && !empty($WakeUpTmp[3])) {
			$myresult[0] = $WakeUpTmp[1];
			$myresult[1] = $WakeUpTmp[3];
		}
		return $myresult;
	}

	public function getAllCalls() {
		$calls = array();
		foreach(glob($this->FreePBX->Config->get('ASTSPOOLDIR')."/outgoing/wuc*.call") as $file) {
			$res = $this->CheckWakeUpProp($file);
			if(!empty($res)) {
				$filedate = date('M d Y',filemtime($file)); //create a date string to display from the file timestamp
				$filetime = date('H:i',filemtime($file));   //create a time string to display from the file timestamp
				$wucext = $res[1];
				$calls[] = array(
					"time" => $filetime,
					"date" => $filedate,
					"destination" => $wucext,
					"actions" => '<a href="?display=hotelwakeup&amp;action=delete&amp;id='.$res[0].'&amp;ext='.$res[1].'"><i class="fa fa-times"></i></a>'
				);
			}
		}
		return $calls;
	}
}


//wuc.1435321800.ext.1001.call
