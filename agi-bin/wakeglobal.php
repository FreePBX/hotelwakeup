<?php

require_once "phpagi.php";

class AGI_Hotelwakeup
{
	// public $AGI 		= null;
	// public $FreePBX		= null;
	// public $Hotelwakeup = null;

	public function __construct() {
		$this->FreePBX = \FreePBX::create();
		if ($this->FreePBX == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		$this->AGI 			= new AGI();
		$this->Hotelwakeup 	= $this->FreePBX->Hotelwakeup;
		$this->init();
	}

	private function init()
	{
		$this->AGI->answer();
	}

	public function hangup()
	{
		$this->AGI->hangup();
	}

	/**
	 * Get language call
	 * 
	 * @return string lang
	 */
	public function getLang()
	{
		return $this->AGI->request['agi_language'];
	}

	/**
	 * Get the extension number of the call destination.
	 * 
	 * @return string Number
	 */
	public function getDestinationExtension()
	{
		return $this->AGI->request['agi_extension'];
	}

	/**
	 * Get the extension number of the call origin.
	 * 
	 * @return string Number
	 */
	public function getOriginExtension()
	{
		$cid = $this->getCID();
		return $cid['username'];
	}

	/**
	 * Get  info of the call origin.
	 * 
	 * @return array (name, protocol, username, host, port)
	 */
	public function getCID()
	{
		return $this->AGI->parse_callerid();
	}


	public function wait_for_digit($time_wait)
	{
		return $this->AGI->wait_for_digit($time_wait);
	}

	/**
	 * Get setting specified of the module Hotelwakeup.
	 * 
	 * @param  string $option Option Name
	 * 
	 * @return mixed          Option value.
	 */

	public function getSetting($option)
	{
		return $this->Hotelwakeup->getSetting($option);
	}

	/**
	 * Simulate playback functionality like the dialplan
	 * @param  string|array $file 	Audio files combined by/with '&' or MessageID or array.
	 * 							  	We can add silences with the following format "silence|500",
	 * 							  	in this way we get a silence of 500 milliseconds.
	 * 							  	To read a number digit by digit we will use "d|157".
	 * @param	array		$param 	Array with parameters that can be used in the generation of file names.
	 */
	public function sim_playback($file, $param = array())
	{
		if ($this->Hotelwakeup->isMessageExists($file))
		{
			$file = $this->getMessage($file, $param);
		}
		$files = $this->parseMessage($file);

		foreach($files as $f)
		{
			if (strlen(trim($f)) == 0) 				{ continue; }
			if ($this->find_silence($f, true) > 0) 	{ continue; }
			if ($this->find_SayUnixTime($f)) 		{ continue; }
			if ($this->find_digits($f)) 			{ continue; }

			if (is_numeric($f)) { $this->AGI->say_number($f);  }
			else 				{ $this->AGI->stream_file($f); }
		}
	}

	/**
	 * Simulate background playback with added functionality
	 * @param  string|array  $file     Audio files combined by/with '&' or MessageID or array.
	 * @param  string        $digits   Allowed digits (if we are prompting for them)
	 * @param  string        $length   Length of allowed digits (if we are prompting for them)
	 * @param  string        $escape   Escape character to exit
	 * @param  integer       $timeout  Timeout
	 * @param  integer       $maxLoops Max timeout loops
	 * @param  integer       $loops    Total loops
	 * @param  array         $param    Array with parameters that can be used in the generation of file names.
	 */
	public function sim_background($file, $digits='', $length='1', $escape='#', $timeout=15000, $maxLoops=1, $loops=0, $param=array())
	{
		if ($this->Hotelwakeup->isMessageExists($file))
		{
			$file = $this->getMessage($file, $param);
		}
		$files = $this->parseMessage($file);

		$number = '';
		foreach($files as $f) 
		{
			if (strlen(trim($f)) == 0) 				{ continue; }
			if ($this->find_silence($f, true) > 0) 	{ continue; }
			// if ($this->find_SayUnixTime($f))		{ continue; }

			if ($this->find_digits($f, true))
			{
				$this->AGI->say_digits($f, $digits);
			}
			elseif (is_numeric($f))
			{
				$this->AGI->say_number($f, $digits);
			}
			else
			{
				$ret = $this->AGI->stream_file($f, $digits);
			}
			
			if($ret['code'] == 200)
			{
				if ($ret['result'] > 0)
				{
					$number .= chr($ret['result']);
				}
				elseif ($ret['result'] < 0)
				{
					dbug(sprintf("Something has failed the run stream_file(%s)!!", $f));
				}
			}
			if(strlen($number) >= $length)
			{
				break;
			}
		}
		if(trim($digits) != '' && strlen($number) < $length)
		{
			while(strlen($number) < $length && $loops < $maxLoops)
			{
				$ret = $this->AGI->wait_for_digit($timeout);
				if($loops > 0)
				{
					$this->sim_playback($this->getMessage("retry"));
				}
				if($ret['code'] == 200 && $ret['result'] == 0)
				{
					$this->sim_playback($this->getMessage("invalidDialing"));
				}
				elseif($ret['code'] == 200 && $ret['result'] > 0)
				{
					$digit = chr($ret['result']);
					if($digit == $escape)
					{
						break;
					}
					if(strpos($digits, $digit) !== false)
					{
						$number .= $digit;
						continue; //dont count loops as we are good
					}
					else
					{
						$this->sim_playback($this->getMessage("invalidDialing"));
					}
				}
				else
				{
					$this->sim_playback($this->getMessage("error"));
				}
				$loops++;
			}
		}
		$number = trim($number);
		return $number;
	}

	/**
	 * It looks for if the text corresponds to a silence and the time of silence returns.
	 * @param  string  $text  Text to search
	 * @param  boolean $sleep True runs a sleep of detected silence, False skips the sleep.
	 * 
	 * @return int 	   Returns the number of milliseconds that has been specified, if silence 
	 * 				   is not detected it will return 0.
	 */
	public function find_silence($text, $sleep = false)
	{
		$data_return = 0;
		$silence =  explode ("|", $text, 2);
		if (count($silence) > 1) 
		{
			if ( strtolower($silence[0]) == "silence") 
			{
				$data_return = ($silence[1] * 1000); //convert microseconds to milliseconds
			}
		}

		if ($data_return > 0 && $sleep) {
			usleep ($data_return);
		}
		return $data_return;
	}

	//https://www.voip-info.org/asterisk-cmd-sayunixtime/
	public function find_SayUnixTime($text)
	{
		$find =  explode ("|", $text, 2);
		if (count($find) > 1) 
		{
			if ( $find[0] == "SayUnixTime")
			{
				$SayUnixTimeFormat = $this->getMessage("SayUnixTime")[0];
				$cmd = sprintf('SayUnixTime "%s,,%s"', $find[1], $SayUnixTimeFormat);
				$this->AGI->exec($cmd, array());
				return true;
			}
		}
		return false;
	}

	public function find_digits($text, $onlyCheck = false)
	{
		$find =  explode ("|", $text, 2);
		if (count($find) > 1) 
		{
			if ( $find[0] == "d")
			{
				if (! $onlyCheck)
				{
					$this->AGI->say_digits($find[1]);
				}
				return true;
			}
		}
		return false;
	}

	public function getMessage($msg, $param = array())
	{
		$defaulParams = array(
			"lang" => $this->getLang()
		);
		$msgParams = array_merge($defaulParams, $param);
		return $this->Hotelwakeup->getMessage($msg, $defaulParams['lang'], $msgParams);
	}

	public function parseMessage($val)
	{
		return $this->Hotelwakeup->parseMessage($val);
	}

	public function addWakeup($time_wakeup, $number = null)
	{
		if (empty($number)) {
			$number = $this->getDestinationExtension();
		}
		$this->Hotelwakeup->addWakeup($number, $time_wakeup, $this->getLang());
	}

	public function removeWakeup($file)
	{
		$this->Hotelwakeup->removeWakeup($file);
	}

	public function getAllWakeup($number)
	{
		return $this->Hotelwakeup->getAllWakeup($number);
	}

	public function convert_time($time)
	{
		$w = getdate();
		$time = trim($time);
		switch(strlen($time))
		{
			case 4: //0130
				$time_wakeup = mktime( substr( $time, 0, 2 ), substr( $time, 2, 2 ), 0, $w['mon'], $w['mday'], $w['year'] );
				break;

			case 3: //130
				$time_wakeup = mktime( substr( $time, 0, 1 ), substr( $time, 1, 2 ), 0, $w['mon'], $w['mday'], $w['year'] );
				break;

			case 2:	//30. EG 0030
			case 1:	//5.  EG 0005
				$time_wakeup = mktime( 0, $time, 0, $w['mon'], $w['mday'], $w['year'] );
				break;

			default:
				$time_wakeup = false;
				break;
		}
		return $time_wakeup;
	}

}