<?php
namespace FreePBX\modules;
use PDO;

class Hotelwakeup extends \FreePBX_Helpers implements \BMO {

	const ASTERISK_SECTION = 'app-hotelwakeup';

	public static $defaultConfig = [
        'maxretries' => 3, 
        'waittime' => 60, 
        'retrytime' => 60, 
        'cnam' => 'Wake Up Calls', 
        'cid' => '*68', 
        'operator_mode' => '1', 
        'operator_extensions' => [], 
        'extensionlength' => '4', 
        'application' => 'AGI', 
        'data' => 'wakeconfirm.php',
		'language' => '',
	];

	public static $defaultMessage = [
		'SayUnixTime'    => "IMpABd",
		'welcome' 		 => 'hello,this-is-yr-wakeup-call,silence|500',
		'goodbye'		 => "goodbye,silence|500",
		'error' 		 => "an-error-has-occurred,silence|500",
		'retry'			 => "please-try-again,silence|500",
		'optionInvalid'  => "option-is-invalid,silence|500",
		'invalidDialing' =>	"you-entered,bad,digits,silence|500",

		'operatorSelectExt' => [
			'group' => 'operator',
			'value' => [
				"please-enter-the",
				"number",
				"for",
				"your",
				"wakeup-call",
				"silence|500",
			],
		],
		'operatorEntered' => array(
			'group' => 'operator',
			'value' => "you-entered,d|{number},silence|500",
		),

		'wakeupMenu' => [
			'group' => 'wakeup',
			'value' => "for-wakeup-call,press-1,silence|400,list,press-2,silence|500",
		],
		'wakeupAdd' => [
			'group' => 'wakeup',
			'value' => [
				"please-enter-the",
				"time",
				"for",
				"your",
				"wakeup-call",
			],
		],
		'wakeupAddType12H' => [
			'group' => 'wakeup',
			'value' => "1-for-am-2-for-pm",
		],
		'wakeupAddOk' => [
			'group' => 'wakeup',
			'value' => array("wakeup-call", "added", "digits/at", "SayUnixTime|{time}", "silence|500"),
		],

		'wakeupList' => [
			'group' => 'wakeup',
			'value' => "vm-youhave,{count},wakeup-call,silence|500",
		],
		'wakeupListEmpty' => [
			'group' => 'wakeup',
			'value' => "vm-youhave,{count},wakeup-call,silence|500",
		],
		'wakeupListInfoCall' => [
			'group' => 'wakeup',
			'value' => [
				"wakeup-call",
				"number",
				'{number}',
				"digits/at",
				'SayUnixTime|{time}',
				"silence|500",
			],
		],
		'wakeupListMenu' => [
			'group' => 'wakeup',
			'value' => [
				"to-cancel-wakeup",
				"press-1",
				"silence|400",
				"list",
				"press-2",
				"silence|400",
				"menu",
				"press-3",
				"silence|500",
			],
		],
		'wakeupListCancelCall' => [
			'group' => 'wakeup',
			'value' => "wakeup-call-cancelled,silence|500",
		],


		'wakeConfirmMenu' => [
			'group' => 'confirm',
			'value' => [
				"to-snooze-for",
				"5",
				"minutes",
				"press-1",
				"silence|400",
				"to-snooze-for",
				"10",
				"minutes",
				"press-2",
				"silence|400",
				"to-snooze-for",
				"15",
				"minutes",
				"press-3",
			],
		],
		'wakeConfirmDelay' => [
			'group' => 'confirm',
			'value' => [
				"rqsted-wakeup-for",
				"{delay}",
				"minutes",
				"vm-from",
				"now",
				"silence|500"
			],
		],
	];

	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}

		$this->FreePBX    = $freepbx;
		$this->db 		  = $freepbx->Database;

		$this->media 	  = $freepbx->Media();
		$this->extensions = $freepbx->Extensions();

		//Modules
		$this->Recordings = $freepbx->Recordings;
		$this->Soundlang  = $freepbx->Soundlang;
	}

	public function getPath($name, $backslashend = true)
	{
		$path_lib 	 = $this->FreePBX->Config->get("ASTVARLIBDIR");
		$path_spool  = $this->FreePBX->Config->get('ASTSPOOLDIR');
		$data_return = "";

		switch($name)
		{
			case "outgoing":
				$data_return .= $path_spool."/outgoing";
			break;

			case "outgoing_done";
				$data_return .= $path_spool."/outgoing_done";
			break;

			case "tmp":
				$data_return .= $path_spool."/tmp";
			break;

			case "sounds":
				$data_return .= $path_lib."/sounds";
			break;
			
			default:
				throw new \Exception( sprintf(_("Path name (%s) not supported!"), $name) );
		}
		if (! empty($data_return))
		{
			if ($backslashend)
			{
				$data_return .= "/";
			}

		}
		return $data_return;
	}

	public function setDatabase($database){
		$this->Database = $database;
		return $this;
	}

	public function resetDatabase(){
		$this->Database = $this->FreePBX->Database;
		return $this;
	}

	public function install() 
	{
        $fcc = new \featurecode('hotelwakeup', 'hotelwakeup');
        $fcc->setDescription(self::$defaultConfig['cnam']);
        $fcc->setDefault(self::$defaultConfig['cid']);
        $fcc->update();
		unset($fcc);
		

		//Migrate Table > KVSTORE
		$sql = "SHOW TABLES LIKE 'hotelwakeup'";
		$count = $this->Database->query($sql)->rowCount();
		if($count > 0)
		{
			$sql = "SELECT * FROM hotelwakeup LIMIT 1";
			$sth = $this->Database->prepare($sql);
			$sth->execute();
			$fa = $sth->fetch(PDO::FETCH_ASSOC);
			$this->saveSetting($fa);
			unset($fa);
			unset($sth);

			$sql = "DROP TABLE IF EXISTS hotelwakeup";
			$this->Database->query($sql);
		}
		unset($count);
		unset($sql);
		
		//Create default values
		$currentConfig = $this->getSetting();
		if( empty($currentConfig) ) 
		{
			$this->saveSetting(self::$defaultConfig);
		}
    }

	public function uninstall() {}
	public function doConfigPageInit($page) {}
	
	public function getActionBar($request)
	{
		$buttons = array();
		$show_btn = false;
		switch($request['display'])
		{
			case 'hotelwakeup_settings':
				switch (true)
				{
					case ($request['action'] == "settings"):
					case ($request['action'] == "messages" && $request['option'] == "edit"):
						$show_btn = true;
					break;
				}
			break;
		}
		if ($show_btn)
		{
			$buttons = array(
				'save' => array(
					'id' => 'btn_save_settings',
					'value' => _("Save"),
					'type' => 'button'
				),
				'reload' => array(
					'id' => 'btn_load_settings',
					'value' => _("Reload"),
					'type' => 'button'
				)
			);
		}
		return $buttons;
	}

	public function getRightNav($request, $params = array()) 
	{
		$data_return = "";
		$data = array(
			"hotelwakeup" => $this,
			"request" 	  => $request
		);
		$data = array_merge($data, $params);
		switch($request['display'])
		{
			case 'hotelwakeup_settings':
				$data_return = load_view(__DIR__.'/views/rnav.php', $data);
			break;
		}
		return $data_return;
	}

	public function ajaxRequest($req, &$setting) {
		// ** Allow remote consultation with Postman **
		// ********************************************
		// $setting['authenticate'] = false;
		// $setting['allowremote'] = true;
		// return true;
		// ********************************************
		switch($req)
		{
			case "savecall":
			case "gettable":
			case "removecall":
			case "getsettings":
			case "setsettings":
			case "gettablemessagelang":
			case "getmessagedefault":
			case "getmessagekeys":
			case "getmessage":
			case "setmessage":
			case "i18n":
			case "gethtml5":
			case "getsupportedhtml5":
			case "playback":
				return true;
			break;
		}
		return false;
	}

	public function ajaxCustomHandler() {
		switch($_REQUEST['command']) {
			case "playback":
				$this->media->getHTML5File($_REQUEST['file']);
			break;
		}
	}

	public function ajaxHandler()
	{
		$command = isset($_REQUEST['command']) ? trim($_REQUEST['command']) : '';
		switch($command)
		{
			case "savecall":
				$params = array(
					'day' 			=> empty($_POST['day']) 		? '' : $_POST['day'],
					'time' 			=> empty($_POST['time']) 		? '' : $_POST['time'],
					'destination' 	=> empty($_POST['destination']) ? '' : $_POST['destination'],
					'language' 		=> empty($_POST['language'])	? '' : $_POST['language'],
				);
				return $this->run_action("wakeup_create", $params);
				break;

			case "gettable":
				$calls = $this->getAllWakeup();
				foreach($calls as &$call)
				{
					//Add action column
					$call['actions'] = sprintf('<a href="#" onclick="removeWakeup(%s,%s);return false;"><i class="fa fa-trash"></i></a>', $call['id'], $call['destination']);
				}
				return $calls;
				break;

			case "removecall":
				$params = array(
					'id' 	=> empty($_POST['id'])  ? '' : $_POST['id'],
					'ext' 	=> empty($_POST['ext']) ? '' : $_POST['ext'],
				);
				return $this->run_action("wakeup_delete", $params);
				break;

			case "getsettings":
				return $this->run_action("settings_get");
				break;

			case "setsettings":
				return $this->run_action("settings_set", $_POST);
				break;

			case "gettablemessagelang":
				$list = array();
				foreach ($this->getLanguages() as $k => $v)
				{
					$list[] = array(
						'language' => $k,
						'description' => $v,
						'action' => sprintf('<a href="?display=hotelwakeup_settings&amp;action=messages&amp;option=edit&amp;language=%s"><i class="fa fa-edit"></i></a>', $k),
					);
				}
				return $list;
				break;

			case "getmessagedefault":
				$key = empty($_POST['msgkey'])  ? '' : $_POST['msgkey'];

				return $this->getMessageDefault($key);
				break;

			case "getmessagekeys":
				return array_keys(self::$defaultMessage);
				break;

			case 'getmessage':
				$language = empty($_POST['language'])  ? '' : $_POST['language'];

				$data_return = array(
					'status'   => false,
					'message'  => "",
					'data' 	   => array(),
					'language' => $language,
				);

				if (empty($language))
				{
					$data_return['message'] = 'Required parameters are missing!';
				}
				elseif( ! $this->isLanguagesAvailable($language) )
				{
					$data_return['message'] = 'The specified language is not available!';
				}
				else
				{
					$data_return['status'] = true;
					$data_return['data']   = $this->getMessageAll($language);
					$data_return['message'] = 'Settings Load Successfully';
				}
				return $data_return;
				break;
			
			case 'setmessage':
				$language = empty($_POST['language'])  ? '' : $_POST['language'];
				$messages = empty($_POST['messages'])  ? '' : $_POST['messages'];

				$data_return = array(
					'status'   => false,
					'message'  => "",
					'data' 	   => array(),
					'count'  => array(
						'all' => 0,
						'ok'  => 0,
						'err' => 0,
					),
					'language' => $language,
				);

				if ( empty($language) || empty($messages) )
				{
					$data_return['message'] = 'Required parameters are missing!';
				}
				elseif( ! $this->isLanguagesAvailable($language) )
				{
					$data_return['message'] = 'The specified language is not available!';
				}
				elseif( ! is_array($messages) || count($messages) < 1 )
				{
					$data_return['message'] = 'No messages have been received!';
				}
				else
				{
					$data_return['count']['all'] = count($messages);
					foreach($messages as $k => $v)
					{
						if (! $this->isMessageExists($k)) 
						{
							$data_return['count']['err'] += 1;
							$data[$k] = false;
							continue;	
						}
						$data_return['count']['ok'] += 1;
						$this->setMessage($k, $v, $language);
						$data[$k] = true;
					}
					$data_return['status'] = true;
					$data_return['message'] = 'Settings Saved Successfully';
				}
				return $data_return;
				break;

			case 'i18n':
				$filejs = isset($_REQUEST['filejs']) ? $_REQUEST['filejs'] : NULL;
				switch( strtolower($filejs) ) 
				{
					case "messages":
						$data_return = array("status" => true, "i18n" => array(
							'SETTING_RELOAD_OK'			  => _('Setting Restart Successfully'),
							'PLAY_FILE_NOT_FOUND'		  => _('The following files do not exist:'),
							'PLAY_FILE_WAIT_PLAYBACK_END' => _('Wait for the current playback to finish.'),
						));
						break;
						
					case "settings":
						$data_return = array("status" => true, "i18n" => array(
							"EXTENSIONLENGTH"			 		=> _("Max Destination Length"),
							"WAITTIME"					 		=> _("Ring Time"),
							"RETRYTIME"					 		=> _("Retry Time"),
							"MAXRETRIES"				 		=> _("Max Retries"),
							"CALLERID"					 		=> _("Wake Up Caller ID"),
							"OPERATOR_EXTENSIONS"				=> _("Operator Extensions"),
							"VALIDATE_ERROR_BLANK" 		 		=> _("%s can not be blank."),
							"VALIDATE_ERROR_ONLY_NUMBER" 		=> _("%s only allow numbers."),
							"VALIDATE_ERROR_CHARACTERS_INVALID" => _("Detected invalid characters in %s."),
							"INVALID_CHAR"						=> _("Detected invalid characters!"),
							"NO_NUMBER" 						=> _("No number specified!"),
							"NUMBER_IN_LIST" 					=> _("The number is already in the operator list."),
						));
						break;

					default:
						$data_return = array("status" => false, "message" => _("File not found!"), "i18n" => array());
				}
				return $data_return;
				break;
			
			
			case "gethtml5":
				$language  = empty($_POST['language'])  ? '' : $_POST['language'];
				$filenames = empty($_POST['filenames'])  ? '' : $_POST['filenames'];
				
				$data_return = array(
					'status'   => false,
					'message'  => "",
					'files'    => array(),
					'language' => $language,
				);
				
				switch(true)
				{
					case ( empty($language) || empty($filenames) ):
						$data_return['message'] = 'Required parameters are missing!';
						break;

					case ( ! $this->isLanguagesAvailable($language) ):
						$data_return['message'] = 'The specified language is not available!';
						break;

					case ( ! is_array($filenames) || count($filenames) < 1 ):
						$data_return['message'] = 'No filenames have been received!';
						break;
				}
				if (empty($data_return['message']))
				{
					$filenames_parsed = array();
					foreach($filenames as $filename)
					{
						if (empty($filename)) { continue; }
						if ( preg_match ('/\{(.*)\}/', $filename) )
						{
							$filenames_parsed[] = "letters/ascii123"; // == {
							$filenames_parsed[] = "letters/ascii125"; // == }
							continue;
						}
						$filenames_parsed[] = $filename;
					}

					foreach($filenames_parsed as $filename)
					{
						$path_filename = "";

						$optionNoFile = explode("|", $filename, 2);
						if ( count($optionNoFile) > 1 ) 
						{	
							$data_return['files'][] = array(
								'type' => "option",
								"key" => $optionNoFile[0],
								"val" => $optionNoFile[1],
							);
							continue;
						}

						$info = pathinfo($filename);
						if(empty($info['extension']))
						{
							$status = $this->fileStatus($filename);
							if(!empty($status[$language])) {
								$path_filename = $this->getPath("sounds", true) . $language . "/" . reset($status[$language]);
							}
						}
						else
						{
							$path_filename = $this->getPath("sounds", true) . $filename;
						}
						
						if (empty($path_filename))
						{
							$data_return['files'][] = array (
								"type"		=> "file",
								"status"	=> false,
								"filename" 	=> $filename,
							);
						}
						else
						{
							$this->media->load($path_filename);
							$files = $this->media->generateHTML5();
							foreach($files as $format => $fname)
							{
								$data_return['files'][] = array (
									"type"		=> "file",
									"status"	=> true,
									"filename" 	=> $filename,
									"url" 	 	=> "ajax.php?module=hotelwakeup&command=playback&file=".$fname,
									"format" 	=> $format,
								);
							}
						}
					}
					$data_return['status'] = true;
				}
				return $data_return;
			
			case "getsupportedhtml5":
				$formats = $this->media->getSupportedHTML5Formats();
    			return array(
					"status" => true,
					"data" 	 => implode(",", $formats),
					'count'  => count($formats),
				);
				break;
			
			default:
				return array("status" => false, "message" => _("Command not found!"), "command" => $command);
		}
	}

	private function check_action_settings_messages($language, $message, $message_required = false, $message_noCheckIsExist = false)
	{
		switch(true)
		{
			case empty($language):
				$data_return = array("status" => false, "message" => _("No language has been specified!"));
				break;

			case (! $this->isLanguagesAvailable($language)):
				$data_return = array("status" => false, "message" => _("The specified language is not available!"));
				break;
			
			case ( ($message_required) && (empty($message)) ):
				$data_return = array("status" => false, "message" => _("No message specified!"));
				break;
			
			//TODO: "IsMessageExists" is not executed if $message is an array.
			case ( (! $message_noCheckIsExist) && ( (! empty($message) && (! is_array($message) ) && (! $this->isMessageExists($message)) ) ) ):	
				$data_return = array("status" => false, "message" => _("The specified message does not exist!"));
				break;

			default:
				$data_return = array("status" => true);
		}
		return $data_return;
	}

	public function run_action($action, $params = array())
	{
		switch($action)
		{
			case "wakeup_create":
				if(empty($params['day']) || empty($params['time']) || empty($params['destination'])) 
				{
					$data_return = array("status" => false, "message" => _("Cannot schedule the call, due to insufficient data!"));
				}
				else 
				{
					$lang = empty($params['language']) ? '' :  $params['language'];
					$time_wakeup = strtotime($params['day']." ".$params['time']);
					$time_now = time();
	
					// check for insufficient data
					if ( $time_wakeup === false || $time_wakeup <= $time_now )
					{
						// abandon .call file creation and pop up a js alert to the user
						$data_return = array("status" => false, "message" => sprintf(_("Cannot schedule the call the scheduled time is in the past. [Time now: %s] [Wakeup Time: %s]"),date(DATE_RFC2822,$time_now),date(DATE_RFC2822,$time_wakeup)));
					}
					else
					{
						$this->addWakeup($params['destination'], $time_wakeup, $lang);
						$data_return = array("status" => true);
					}
				}
				break;

			case "wakeup_get":
				if(empty($params['id']) || empty($params['ext'])) 
				{
					$data_return = array("status" => false, "message" => _("Cannot get info the call, due to insufficient data!"));
				}
				else 
				{
					$call = $this->getWakeup($params['id'], $params['ext']);
					if (empty($call)) 
					{
						$data_return = array("status" => false, "message" => _("Cannot get info the call, due to not exist the call!"));
					} 
					else
					{
						$data_return = array("status" => true, "data" => $call);
					}
				}
				break;

			case "wakeup_delete":
				if (empty($params['id']) || empty($params['ext'])) 
				{
					$data_return = array("status" => false, "message" => _("Cannot delete the call, due to insufficient data!"));
				}
				else
				{
					if (! $this->existWakeup($params['id'], $params['ext']))
					{
						$data_return = array("status" => false, "message" => _("Wake up call does not exist!"));
					}
					else
					{
						$this->removeWakeupByIdExt($params['id'], $params['ext']);
						$data_return = array("status" => true);
					}
				}
				break;

			case "settings_get":
				$data_return = array(
					"status"  => true,
					"message" => _("Settings loaded successfully"),
					"config"  => $this->getSetting(),
					"extensions" => $this->HookGetExtensions(),
				);

				$data_return['config']['operator_mode'] = ($data_return['config']['operator_mode']) ? "yes" : "no";
				break;
			
			case "settings_set": 
				$list_options = array(
					"callerid" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
					"operator_mode" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
					"extensionlength" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
					"operator_extensions" => array(
						"requiered" => false,
						"type" 		=> "string"
					),
					"waittime" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
					"retrytime" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
					"maxretries" => array(
						"requiered" => true,
						"type" 		=> "numeric"
					),
				);
				$new_options = array();
				$missing_options = array();
				$invalid_value = array();

				foreach ($list_options as $key => $value)
				{
					if ( empty($params[$key]) && $value['requiered'] )
					{
						$missing_options[] = $key;
						continue;
					}
					switch($key)
					{
						case "callerid":
							preg_match('/"(.*)" <(.*)>/',$params[$key],$matches);
							$new_options['cid']  = !empty($matches[2]) ? $matches[2] : $this->getCode();
							$new_options['cnam'] = !empty($matches[1]) ? $matches[1] : self::$defaultConfig['cnam'];
						break;

						case "operator_mode":
							$new_options[$key] = ($params[$key] == "yes") ? "1": "0";
						break;

						default:
							$new_options[$key] = $params[$key];
							if ( $value['type'] == "numeric" )
							{
								if ( ! is_numeric( $params[$key] ) ) 
								{
									$invalid_value[] = $key;
								}
							}
					}
				}

				if (count($missing_options) == 0)
				{
					if (count($invalid_value) == 0)
					{
						$this->saveSetting($new_options);
						$data_return = array("status" => true, "message" => _("Settings saved successfully"));
					}
					else
					{
						$data_return = array("status" => false, "message" => _("Save failed, invalid values!"), "errorIn" => $invalid_value);
					}
				}
				else
				{
					$data_return = array("status" => false, "message" => _("Save failed, missing parameters!"));
				}
				break;

			

			case "settings_messages_get":
				$language  	 = $params['language'];
				$message   	 = $params['message'];
				$data_return = $this->check_action_settings_messages($language, $message);
				if ( $data_return['status'] )
				{
					$data_return['message']  = _("Message setting loaded successfully");
					$data_return['language'] = $language;
					
					foreach ($this->getMessageAll($language) as $k => $v)
					{
						if ( ! empty($message) && $k != $message) { continue; }
						$msg_default = $this->getMessageDefault($k, true);
						$data_return['messages'][$k] = array(
							'value'   	=> $v,
							'default' 	=> $msg_default,
							'isDefault' => ($v == $msg_default),
						);
					}
				}
				break;
			
			case "settings_messages_set":
				$language  	 = $params['language'];
				$message   	 = $params['message'];
				$data_return = $this->check_action_settings_messages($language, $message, true, true);
				if ( $data_return['status'] )
				{
					$data_return['message'] = _("Message setting update successfully");
					foreach ($message as $k => $v)
					{
						if (! $this->isMessageExists($k))
						{
							$data_return["unknown"][] = $k;
							continue;
						}
						$this->setMessage($k, $v, $language);
					}
				}
				break;

			case "settings_messages_delete":
				$language  	 = $params['language'];
				$message   	 = $params['message'];
				$data_return = $this->check_action_settings_messages($language, $message);
				if ( $data_return['status'] )
				{
					$data_return['message'] = _("Message setting cleared successfully");
					foreach ($this->getMessageAll($language) as $k => $v)
					{
						if ( ! empty($message) && $k != $message) { continue; }
						$this->delMessage($k, $language);
					}
				}
				break;
			
			default:
				$data_return = array("status" => false, "message" => _("Invalid action!"));
		}
		return $data_return;
	}

	public function addWakeup($destination, $time, $lang)
	{
		if(empty($lang))
		{
			$lang = $this->getLanguage();
		}
		
		$data = $this->getSetting();  // module config provided by user
		$this->generateCallFile(array(
			"time"			=> $time,
			"ext"			=> $destination,
			"language"		=> $lang,
			"maxretries"	=> $data['maxretries'],
			"retrytime"		=> $data['retrytime'],
			"waittime"		=> $data['waittime'],
			"callerid"		=> $data['cnam']." <".$data['cid'].">",
			"application"	=> $data['application'],
			"data"			=> $data['data'],
			"AlwaysDelete"	=> "Yes",
			"Archive"		=> "Yes"
		));
	}

	public function showPage($page, $params = array())
	{
		$data = array(
			"hotelwakeup" => $this,
			'request'	  => $_REQUEST,
			'page' 		  => $page,
		);
		$data = array_merge($data, $params);
		switch ($page) 
		{
			case "wakeup":
				$data_return = load_view(__DIR__."/views/page.wakeup.php", $data);
			break;

			case "wakeup.grid":
				$data_return = load_view(__DIR__.'/views/view.wakeup.grid.php', $data);
			break;

			case "wakeup.grid.create":
				$data_return = load_view(__DIR__.'/views/view.wakeup.grid.create.php', $data);
			break;



			case "settings":
				$data_return = load_view(__DIR__."/views/page.settings.php", $data);
			break;
			
			case "settings.message.list":
				$data_return = load_view(__DIR__.'/views/view.settings.message.list.php', $data);
			break;

			case "settings.message.edit":
				$data_return = load_view(__DIR__.'/views/view.settings.message.edit.php', $data);
				break;

			case "settings.message.edit.line":
				$data_return = load_view(__DIR__.'/views/view.settings.message.edit.line.php', $data);
				break;

			case "settings.settings":
				$data_return = load_view(__DIR__.'/views/view.settings.settings.php', $data);
				break;

			default:
				$data_return = sprintf(_("Page Not Found (%s)!!!!"), $page);
		}
		return $data_return;
	}

	public function getCode() {
		$fcc = new \featurecode('hotelwakeup', 'hotelwakeup');
		$code = $fcc->getCode();
		return $code;
	}

	public function getCodeActive() {
		$fcc = new \featurecode('hotelwakeup', 'hotelwakeup');
		$fc = $fcc->getCodeActive();
		return $fc;
	}

	public function getSetting($option = null)
	{
		$data_return = $this->getAll("setting");
		if (empty($data_return))
		{
			$data_return = self::$defaultConfig;
		}
		
        $not_allow_empty = array("application", "data");
        foreach ($not_allow_empty as $opt)
        {
            if (!isset($data_return[$opt]) || empty($data_return[$opt]))
            {
                $data_return[$opt] = self::$defaultConfig[$opt];
            }
        }

		$data_return['callerid'] = sprintf('"%s" <%s>', $data_return['cnam'], $data_return['cid']);
		if ( ! is_array($data_return['operator_extensions']) )
		{
			$options['operator_extensions'] = [];
			if ( preg_match('/^[0-9,\n\s]+$/', $data_return['operator_extensions']) )
			{
				$options['operator_extensions'] = explode(",", $options['operator_extensions']);
			}
		}

		foreach($data_return['operator_extensions'] as &$ext)
		{
			$ext = trim($ext);
		}

		if (! empty($option))
		{
			if ( array_key_exists($option, $data_return) )
			{
				$data_return = $data_return[$option];
			}
			else
			{
				throw new \Exception("Option ($option) not valid!");
			}
		}

		return $data_return;
	}

	public function saveSetting($options)
	{
		unset($options['callerid']);
		if(! empty($options)) 
		{
			if ( ! is_array($options['operator_extensions']) )
			{
				if ( preg_match('/^[0-9,+\s]+$/', $options['operator_extensions']) )
				{
					$options['operator_extensions'] = explode(",", $options['operator_extensions']);
				}
				else
				{
					$options['operator_extensions'] = [];
				}
			}

			foreach($options as $key => $value)
			{
				$this->setConfig($key, $value, 'setting');
			}
			return true;
		}
		return false;
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

	public function removeWakeup_done($file) {
		$file = basename($file);
		$file_full_path = $this->getPath("outgoing_done").$file;
		if(file_exists($file_full_path)) {
			unlink($file_full_path);
		}
		return true;
	}

	public function removeWakeupByIdExt($id, $ext) {
		$file = $this->getFileNameCall($id, $ext);
		return $this->removeWakeup($file);
	}

	public function removeWakeup($file) {
		$file = basename($file);
		$file_full_path = $this->getPath("outgoing").$file;
		if(file_exists($file_full_path)) {
			unlink($file_full_path);
		}
		return true;
	}

	public function existWakeup($id, $ext)
	{
		if (empty($id) || empty($ext))
		{
			return false;
		}
		$file = $this->getFileNameCall($id, $ext);
		$file_full_path = $this->getPath("outgoing").$file;
		return file_exists($file_full_path);
	}

	public function getFileNameCall($id, $ext)
	{
		return sprintf("wuc.%s.ext.%s.call", $id, $ext);
	}

	public function getWakeup($id, $ext) 
	{
		$data_return = array();
		if ($this->existWakeup($id, $ext))
		{
			foreach($this->getAllWakeup() as $call)
			{
				if ($call['id'] == $id && $call['destination'] == $ext)
				{
					$data_return = $call;
					break;
				}
			}
		}
		return $data_return;
	}

	public function getAllWakeup($ext = "")
	{
		$calls = array();
		foreach(glob($this->getPath("outgoing")."wuc*.call") as $file) {
			$res = $this->CheckWakeUpProp($file);
			if(!empty($res)) 
			{
				$wucext = $res[1];
				if (! empty($ext) && $wucext != $ext ) { continue; }

				$filedate = date('M d Y',filemtime($file)); //create a date string to display from the file timestamp
				$filetime = date('H:i',filemtime($file));   //create a time string to display from the file timestamp
				$wucid = $res[0];
				$wuclang = "";
				foreach(file($file) as $line)
				{
					$data =  explode (":", $line, 2);
					if ( strtolower(trim($data[0])) == "set")
					{
						$val =  explode ("=", trim($data[1]), 2);
						if ( $val[0] == "CHANNEL(language)")
						{
							$wuclang = trim($val[1]);
						}
					}
				}
				$calls[] = array(
					"id" 		  => $wucid,
					"filename" 	  => basename($file),
					"timestamp"   => filemtime($file),
					"time" 		  => $filetime,
					"date" 		  => $filedate,
					"destination" => $wucext,
					"language" 	  => $wuclang,
				);
			}
		}
		return $calls;
	}

	public function generateCallFile($foo) {
		if (empty($foo['tempdir'])) {
			$ast_tmp_path = $this->getPath("tmp");
			if(!file_exists($ast_tmp_path)) {
				mkdir($ast_tmp_path,0777,true);
			}
			$foo['tempdir'] = $ast_tmp_path;
		}

		if (empty($foo['outdir'])) {
			$foo['outdir'] = $this->getPath("outgoing");
		}

		$foo['ext'] = preg_replace("/[^\d@\+\#]/","",$foo['ext']);
		if (empty($foo['filename'])) {
			$foo['filename'] = $this->getFileNameCall($foo['time'], $foo['ext']);
		}

		$foo['filename'] = basename($foo['filename']);

		$tempfile = $foo['tempdir'].$foo['filename'];
		$outfile = $foo['outdir'].$foo['filename'];

		// Delete any old .call file with the same name as the one we are creating.
		if(file_exists($outfile) ) {
			unlink($outfile);
		}

		// Create up a .call file, write and close
		$defaultConfig  = self::$defaultConfig;
		$maxretries 	= !empty($foo['maxretries'])? $foo['maxretries'] : $defaultConfig["maxretries"];
		$retrytime 		= !empty($foo['retrytime'])? $foo['retrytime'] : $defaultConfig["retrytime"];
		$waittime 		= !empty($foo['waittime'])? $foo['waittime'] : $defaultConfig['waittime'];
		$language 		= !empty($foo['language'])? $foo['language'] : $defaultConfig['language'];
		$application 	= !empty($foo['application'])? $foo['application'] : $defaultConfig['application'];
		$data 			= !empty($foo['data'])? $foo['data'] : $defaultConfig['data'];
		
		$wuc = fopen($tempfile, 'w');
		fputs( $wuc, "channel: Local/".$foo['ext']."@originate-skipvm\n" );
		fputs( $wuc, "maxretries: ".$maxretries."\n");
		fputs( $wuc, "retrytime: ".$retrytime."\n");
		fputs( $wuc, "waittime: ".$waittime."\n");
		fputs( $wuc, "callerid: ".$foo['callerid']."\n");
		fputs( $wuc, 'set: CHANNEL(language)='.$language."\n");
		fputs( $wuc, "application: ".$application."\n");
		fputs( $wuc, "data: ".$data."\n");
		fputs( $wuc, "AlwaysDelete: ".$foo['AlwaysDelete']."\n");
		fputs( $wuc, "Archive: ".$foo['Archive']."\n");
		fclose( $wuc );

		// set time of temp file and move to outgoing
		touch( $tempfile, $foo['time'], $foo['time'] );
		rename( $tempfile, $outfile );
	}

	public function parseMessage($val)
	{
		if (! is_array($val)) 
		{
			$val = explode(',', $val);
		}
		foreach($val as &$item)
		{
			$item = trim($item);
		}
		return $val;
	}

	private function getMessagesIdKVStore($lang)
	{
		return 'message_'.$lang;
	}

	public function setMessage($msg, $val, $lang)
	{
		$val = $this->parseMessage($val);
		switch(true)
		{
			case ( empty($val) ):
			case ( is_array($val) && empty(array_filter($val)) ):
			case ( $this->getMessageDefault($msg) == $val ):
				$val = false;
		}
		$this->setConfig($msg, $val, $this->getMessagesIdKVStore($lang));
	}

	public function delMessage($msg, $lang)
	{
		$this->setMessage($msg, false, $lang);
	}

	public function getMessageAll($lang)
	{
		$msg_all  = $this->getAll($this->getMessagesIdKVStore($lang));
		$msg_diff = array_diff_key(self::$defaultMessage, $msg_all);
		if (count($msg_diff) > 0)
		{
			foreach($msg_diff as $key => $val)
			{
				if (! array_key_exists($key, $msg_all)) 
				{
					$msg_all[$key] = $this->getMessageDefault($key, true);
				}
			}
		}

		foreach($msg_all as $key => &$val)
		{
			$val = $this->parseMessage($val);
		}
		return $msg_all;
	}

	public function getMessage($msg, $lang, $params = array())
	{
		$message = "";
		if ( $this->isMessageExists($msg) )
		{
			$message = $this->getConfig($msg, $this->getMessagesIdKVStore($lang));
			$message = array_diff($message, array(''));

			if (empty($message))
			{
				$message = $this->getMessageDefault($msg, true);
				// $this->setMessage($msg, $message, $lang);
			}
		}

		if (empty($message))
		{
			//If the message is unknown
			$data_return = "option-is-invalid";
		}
		else
		{
			$message = $this->parseMessage($message);
			$reg_find = "/{(.*)}/";

			$msg_detect = preg_grep($reg_find, $message);
			if (count($msg_detect) > 0)
			{
				foreach ($message as $key => &$value)
				{
					if (! in_array($value, $msg_detect)) { continue; }
					
					$new_value = "";
					if (! empty($params))
					{
						preg_match($reg_find, $value, $find_value);
						$key_find 	 = $find_value[1];	//Ex: Test
						$key_replace = $find_value[0];	//Ex: {Test}
						if ( array_key_exists($key_find, $params) )
						{
							$new_value = str_replace($key_replace, $params[$key_find], $value);
						}
					}
					$value = $new_value;
				}
			}
			//Remove items empty in array
			$data_return = array_diff($message, array(''));
		}
		return $data_return;
	}

	public function getMessageDefault($key = "", $msgOnly = false)
	{
		$default 	 = self::$defaultMessage;
		$data_return = array();
		
		if (! empty($key))
		{
			if ( $this->isMessageExists($key))
			{
				$val = $default[$key];
				$data_return[$key] = $this->parseMessage(empty($val['value']) ? $val : $val['value']);
			}
			if ($msgOnly)
			{
				$data_return = array_values ($data_return);
				$data_return = count($data_return) == 0 ? '' : $data_return[0] ;
			}
		}
		else
		{
			foreach($default as $k => $v)
			{
				$data_return[$k] = $this->parseMessage(empty($v['value']) ? $v : $v['value']);
			}
		}
		return $data_return;
	}

	public function isMessageExists($msg)
	{
		return array_key_exists($msg, self::$defaultMessage);
	}

	public function getGroupsMessages()
	{
		$grp = array(
            'global'  => array(),
            'wakeup'  => array(),
            'confirm' => array(),
            'operator'=> array(),
        );
        foreach( self::$defaultMessage as $k => $v )
        {
			if ( empty($v['group']) ) { $v_grp = ""; }
			else 					  { $v_grp = $v['group']; }
			switch($v_grp)
			{
				case 'operator':
				case 'wakeup':
				case 'confirm':
				case 'global':
					$grp[$v_grp][] = $k;
				break;

				default:
					$grp['global'][] = $k;
				break;
			}
		}
		return $grp;
	}

	public function getLanguage()
	{
		//the language configured in the soundlang module will be used
		$lang = $this->getLanguageSoundlang();

		if (empty($lang))
		{
			//If no language is detected, the language configured in the 
			//general configuration of freepbx will be used. 
			$lang = $this->FreePBX->Config->get("UIDEFAULTLANG");
		}
		return $lang;
	}

	public function isLanguagesAvailable($lang)
	{
		return array_key_exists($lang, $this->getLanguages());
	}

	/**
	 * This is used to generate a language selection field using the sound
	 * packets installed on the system.
	 * @param  string $id   The name and id of the form field
	 * @param  string $value   The current value
	 * @param  string $nonelabel  What you want shown if nothing is chosen
	 * @return html input containing timezones
	 */
	public function languageDrawSelect($id, $value='', $nonelabel='')
	{
		$nonelabel = !empty($nonelabel) ? $nonelabel : _("Select a Language");
		
		$input = '<select name="'.$id.'" id="'.$id.'" class="form-control">';
		$input .= '<option value="">'.$nonelabel.'</option>';
		foreach($this->getLanguages() as $lang => $display)
		{
			$input .= '<option value="'.$lang.'" '.(($lang == $value) ? 'selected' : '').'>'.$display.'</option>';
		}
		$input .= '</select>';
		$input .= '<script type="text/javascript">';
		$input .= '$(document).ready(function() {';
		$input .= '$("#'.$id.'").multiselect({enableCaseInsensitiveFiltering: true, inheritClass: true, onChange: function(element, checked) { $("#'.$id.'").trigger("onchange",[element, checked]) }});';
		$input .= '});';
		$input .= '</script>';
		return $input;
	}

	//Legacy \ Maintain for PMS module compatibility.
	public function getAllCalls() 
	{
		$calls = $this->getAllWakeup();
		foreach($calls as &$call) 
		{
			$call['actions'] = sprintf('<a href="?display=hotelwakeup&amp;action=delete&amp;id=%s&amp;ext=%s"><i class="fa fa-times"></i></a>', $call['id'], $call['destination']);
		}
		return $calls;
	}

	/**
	 * Functions Proxy other Modules
	 */
	public function getLanguages()
	{
		return $this->Soundlang->getLanguages();
	}

	public function getLanguageSoundlang()
	{
		return $this->Soundlang->getLanguage();
	}

	public function fileStatus($file) 
	{
		return $this->Recordings->fileStatus($file, true);
	}

	/**
	 * Dialplan hooks
	 * 
	 */
	public function myDialplanHooks() {
		return true;
	}
	public function doDialplanHook(&$ext, $engine, $priority) {
		if ($engine != "asterisk") { return; }

		$section = self::ASTERISK_SECTION;
		$fc = $this->getCodeActive();
		if (!empty($fc))
		{
			$ext->addInclude('from-internal-additional', $section); // Add the include from from-internal
			$ext->add($section, $fc, '', new \ext_Macro('user-callerid'));
			$ext->add($section, $fc, '', new \ext_answer(''));
			$ext->add($section, $fc, '', new \ext_wait(1));
			$ext->add($section, $fc, '', new \ext_AGI('wakeup'));
			$ext->add($section, $fc, '', new \ext_Hangup);
		}
	}


	/**
	 * Hook Extensions
	 */
	public function HookGetExtensions()
	{
		// It is necessary to call "loadAllFunctionsInc" since otherwise "extensions->checkusage" returns an empty array.
		$this->FreePBX->Modules->loadAllFunctionsInc();

		$data_return = array();
		$results = $this->extensions->checkUsage(true);
		$results = is_array($results) ? $results : array();
		foreach($results as $key => $value)
		{
			if (in_array($key, array('core', 'customappsreg')))
			{
				foreach($value as $subkey => $subvalue)
				{
					$data_return[$subkey] = $subvalue['description'];
				}
			}
		}
		return $data_return;
	}

}
