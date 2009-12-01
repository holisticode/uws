<?php
/*
 * UWS - Universal Wealth System
 * UNP_Parser.php
 * class UNP_Parser
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * This file contains the class UNP_Parser, responsible for the
 * parsing of the Uws Norm Protocol (UNP), a standard html format. 
 * It uses regular expressions to parse UNP files.
 * 
 * The parser parses the UNP file and calculates the chat time amount
 * for each user in a chat.
 */
 
class UNP_Parser 
{
	private $date_len = 17;
	private $user_tag = "<span class=\"";	
	//the regular expression for retrieving a single chat line
	private $trmatch = "/^[0-9].*<\/span /ismxU";
	
	function parse($htmlfile) 
	{
		try
		{
		
			$users = array();
			//match every single chat line
			if (preg_match_all($this->trmatch,$htmlfile, $matches))
			{				
				$previous_time = 0;
				$last_user = "";
				foreach ($matches as $match)
				{	
					
					$first = true;
					foreach($match as $elem)
					{
						//for every line
						//print $elem . "\n<br>"; //an $elem is a line
						
						//get the date
						$date = substr($elem,0,$this->date_len);
						$end  = strpos($elem, ">");
						
						//where is the first character of the username?					
						$first_ch_of_username_pos = $this->date_len + 1 +
									strlen($this->user_tag);
						$len  = $end - $first_ch_of_username_pos;
						
						//get the username 
						$user = substr($elem, $first_ch_of_username_pos,$len -1);
						//print "Date: ".$date."-User: ".$user."\n<br>";
						$to_now = 0;
						
						//the parser can handle multiple users
						if (array_key_exists($last_user,$users))
						{
							//how much does the user already have accumulated in
							//chat time for this chat
							$to_now 	= $users[$last_user];
							//print "TO NOW: ".$to_now."\n<br>";							
						}						
						//convert the date to a UNIX time stamp
						$timestamp 		= $this->convert_date($date);
						if ($first)
						{
							//the first chat time value for this user
							//then start at timestamp
							$previous_time = $timestamp;
							$first = false;							
						} else
						{
							//calculate time of chat line
							$seconds = $timestamp - $previous_time;
							//print "Seconds: " . $seconds."\n<br>";
							//the maximum chat time is 111 seconds
							//(deliberately defined by Zeronada)						 
							if ($seconds > 111)
							{
								$seconds = 111;
							}
							//add up the chat time to the user					
							$users[$last_user] = $to_now + $seconds ;
							//print "Accumulated for user ".$last_user.": " . $users[$last_user]."\n<br>";
							//print "<hr>";								
						}
						//who was the last user
						$last_user = $user;		
						//where we are in the time line
						$previous_time = $timestamp;
					} //foreach $elem
				} //foreach $match
			} //if preg_pregmatch
			
			$path  = "";
			$count = 1;
			
			foreach($users as $user=>$value)
			{	
				//the parser returns a url to the caller, constructing a key/value pair
				//sequence of user_XY=username&time_XY=chattime		
				$path = $path."user_".$count."=".$user."&time_".$count."=".$value."&";
				$count++;
				//print "Final chat time for user ".$user.": ".$value."\n<br>";
				//print $path."<br>";
			}
			$path = substr($path, 0, strlen($path) -1 ); //chop last "&"
			return $path;
		} catch (Exception $e) 
		{
			echo  'Caught exception: ',  $e->getMessage(), "\n";
		} //catch
	} //function
	
	//convert the pidgin text date into UNIX timestamp
	private function convert_date($date)
	{
		$parts 		= explode(" ",$date);
		$date_parts = explode(".", $parts[0]);
		$time_parts = explode(":", $parts[1]);
		$timestamp 	= mktime($time_parts[0],$time_parts[1],$time_parts[2],
							 $date_parts[1], $date_parts[2], $date_parts[0]);
		return $timestamp;
	}
		
} //class
?>
