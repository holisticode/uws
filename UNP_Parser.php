<?php
class UNP_Parser 
{
	private $date_len = 17;
	private $user_tag = "<span class=\"";	
	
	private $trmatch = "/^[0-9].*<\/span /ismxU";
	
	function parse($htmlfile) 
	{
		try
		{
		
			$users = array();
			
			if (preg_match_all($this->trmatch,$htmlfile, $matches))
			{				
				$previous_time = 0;
				$last_user = "";
				foreach ($matches as $match)
				{	
					$first = true;
					foreach($match as $elem)
					{
						//print $elem . "\n<br>"; //an $elem is a line
						
						$date = substr($elem,0,$this->date_len);
						$end  = strpos($elem, ">");					
						$first_ch_of_username_pos = $this->date_len + 1 +
									strlen($this->user_tag);
						$len  = $end - $first_ch_of_username_pos; 
						$user = substr($elem, $first_ch_of_username_pos,$len -1);
						//print "Date: ".$date."-User: ".$user."\n<br>";
						$to_now = 0;
						if (array_key_exists($last_user,$users))
						{
							$to_now 	= $users[$last_user];
							//print "TO NOW: ".$to_now."\n<br>";							
						}						
						$timestamp 		= $this->convert_date($date);
						if ($first)
						{
							$previous_time = $timestamp;
							$first = false;							
						} else
						{
							$seconds = $timestamp - $previous_time;
							//print "Seconds: " . $seconds."\n<br>";						 
							if ($seconds > 111)
							{
								$seconds = 111;
							}					
							$users[$last_user] = $to_now + $seconds ;
							//print "Accumulated for user ".$last_user.": " . $users[$last_user]."\n<br>";
							//print "<hr>";								
						}
						$last_user = $user;		
						$previous_time = $timestamp;
					} //foreach $elem
				} //foreach $match
			} //if preg_pregmatch
			
			$path  = "";
			$count = 1;
			
			foreach($users as $user=>$value)
			{			
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
