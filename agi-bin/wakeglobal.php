<?php

/**
 * Simulate playback functionality like the dialplan
 * @param  object 		$AGI  The AGI Object
 * @param  string|array $file Audio files combined by/with '&' or using array
 * 							  We can add silences with the following format "silence|500",
 * 							  in this way we get a silence of 500 milliseconds.
 * 							  To read a number digit by digit we will use "d|157".
 */
function sim_playback($AGI, $file)
{
	if (! is_array($file) ) { $files = explode('&', $file); }
	else 					{ $files = $file; }
	
	foreach($files as $f)
	{
		if (strlen(trim($f)) == 0) 		{ continue; }
		if (find_silence($f, true) > 0) { continue; }
		if (find_SayUnixTime($AGI, $f)) { continue; }
		if (find_digits($AGI, $f)) 		{ continue; }

		if (is_numeric($f)) { $AGI->say_number($f);  }
		else 				{ $AGI->stream_file($f); }
	}
}

/**
 * Simulate background playback with added functionality
 * @param  object  $AGI      The AGI Object
 * @param  string  $file     Audio files combined by/with '&'
 * @param  string  $digits   Allowed digits (if we are prompting for them)
 * @param  string  $length   Length of allowed digits (if we are prompting for them)
 * @param  string  $escape   Escape character to exit
 * @param  integer $timeout  Timeout
 * @param  integer $maxLoops Max timeout loops
 * @param  integer $loops    Total loops
 */
function sim_background($AGI, $file, $digits='', $length='1', $escape='#', $timeout=15000, $maxLoops=1, $loops=0)
{
	if (! is_array($file) ) { $files = explode('&', $file); }
	else 					{ $files = $file; }

	$number = '';
	foreach($files as $f) 
	{
		if (strlen(trim($f)) == 0) 		{ continue; }
		if (find_silence($f, true) > 0) { continue; }
		// if (find_SayUnixTime($AGI, $f)) { continue; }

		if (find_digits($AGI, $f, true))
		{
			$AGI->say_digits($f, $digits);
		}
		elseif (is_numeric($f))
		{
			$AGI->say_number($f, $digits);
		}
		else
		{
			$ret = $AGI->stream_file($f, $digits);
		}

		// Fix as return of $ret['result'] = -1
		// -1 is returned due to an error when executing "stream_file" which 
		// may be for example that one of the audio files does not exist
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
			$ret = $AGI->wait_for_digit($timeout);
			if($loops > 0)
			{
				sim_playback($AGI, getMessage("retry"));
			}
			if($ret['code'] == 200 && $ret['result'] == 0)
			{
				sim_playback($AGI, getMessage("invalidDialing"));
			}
			elseif($ret['code'] == 200 & $ret['result'] > 0)
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
					sim_playback($AGI, getMessage("invalidDialing"));
				}
			}
			else
			{
				sim_playback($AGI, getMessage("error"));
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
function find_silence($text, $sleep = false)
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
function find_SayUnixTime($AGI, $text)
{
	$find =  explode ("|", $text, 2);
	if (count($find) > 1) 
	{
		if ( $find[0] == "SayUnixTime")
		{
			$AGI->exec( sprintf('SayUnixTime "%s,,%s"', $find[1], getMessage("SayUnixTime")) );
			return true;
		}
	}
	return false;
}

function find_digits($AGI, $text, $onlyCheck = false)
{
	
	$find =  explode ("|", $text, 2);
	if (count($find) > 1) 
	{
		if ( $find[0] == "d")
		{
			if (! $onlyCheck)
			{
				$AGI->say_digits($find[1]);
			}
			return true;
		}
	}
	return false;
}

function getMessage($msg, $param = array())
{
	global $AGI;
	global $hotelwakeup;
	$defaulParams = array(
		"lang" => $AGI->request['agi_language']
	);
	$msgParams = array_merge($defaulParams, $param);
	return $hotelwakeup->getMessage($msg, $msgParams);
}
