<?php

include_once("Net/SmartIRC.php"); 

class ircServer { 

	private $server = ""; 
	private $channel = ""; 
	private $nick	= ""; 
	private $port = "6667"; 
	
	function __construct($server, $channel, $nick) { 
		$this->server = $server; 
		$this->channel = $channel; 
		$this->user = $user; 
	} 
	
	private function connect($server) { 
	
	} // end connect()

	private function disconnect() { 
		
	} // end disconnect()
	
	private function joinChannel($channel) { 
	
	} // end joinChannel()
	
	private function partChannel() { 
	
	} // end partChannel()

} 

class mybot { 

	function handleUpdate(&$irc, &$data) { 
		$type = $data->type; 
		$outputMessage = ""; 

		switch($type) { 
			case SMARTIRC_TYPE_JOIN: 
				$outputMessage = $data->nick . " joined " . $data->channel; 
				break; 
			case SMARTIRC_TYPE_PART:
				$outputMessage = $data->nick . " left " . $data->channel; 
				break;
			case SMARTIRC_TYPE_CHANNEL:
				$outputMessage = "<" . $data->nick . "> " . $data->message; 
				break;
			case SMARTIRC_TYPE_TOPICCHANGE:
				$outputMessage = "The topic on " . $data->channel . " is now " . $data->message; 
				break;
			case SMARTIRC_TYPE_NICKCHANGE:
				$outputMessage = $data->nick . " is now known as " . $data->message; 
				break;
			case SMARTIRC_TYPE_KICK:
				$outputMessage = $data->nick . " got kicked from " . $data->channel; 
				break; 
			case SMARTIRC_TYPE_QUIT:
				$outputMessage = $data->nick . " quit"; 
				break;
			case SMARTIRC_TYPE_MODECHANGE:
			case SMARTIRC_TYPE_CHANNELMODE:
				$outputMessage = "Mode change: " . $data->rawmessage;
				break; 
			}

			print $outputMessage . "\n"; 

	} // end function handleUpdate()
} // end class mybot()
		
	
$bot = &new mybot(); 
$irc = &new Net_SmartIRC(); 

//$irc->setDebug(SMARTIRC_DEBUG_ALL); 
$irc->setUseSockets(TRUE); 

print "\nConnecting to irc.freenode.net\n"; 
$irc->connect('irc.freenode.net', 6667); 

$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example.php)', 0, 'Net_SmartIRC'); 

print "Joining #smartirc-test\n"; 
$irc->join(array('#smartirc-test')); 

//$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '.', $bot, 'channelMessage');
//$irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '.*', $bot, 'channelMemberChange'); 
//$irc->registerActionhandler(SMARTIRC_TYPE_PART, '.*', $bot, 'channelMemberChange'); 
$irc->registerActionhandler(SMARTIRC_TYPE_ALL, '.*', $bot, 'handleUpdate'); 

$irc->listen(); 



?>
