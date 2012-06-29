#!/usr/bin/php
<?php

include_once("Net/SmartIRC.php"); 

$channel = "#LOPSA-live"; 
$botName = "MPSwindow"; 
$botRealName = "SmartBot 2000 (ask standalone.sysadmin@gmail.com)"; 


class mybot { 

	// AMQP Variables
	private $messageQueue = "IRCoutput"; 
	private $connection = ""; 
	private $channel = ""; 
	private $exchange = ""; 

	function __construct() { 
		$this->AMQPinit(); 
	} // end constructor()

	function AMQPinit() { 
		$this->connection = new AMQPConnection(); 
		$this->connection->connect(); 

		$this->channel = new AMQPChannel($this->connection); 
		if ( $this->channel->isConnected() ) { 
			$this->exchange = new AMQPExchange($this->channel); 
		} else { 
			print "Error connecting to channel\n"; 
			exit; 
		}
	
		$this->exchange->setName("IRClog"); 
		$this->exchange->setType("fanout"); 
		$this->exchange->declare(); 

	} // end function AMQPinit()

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
			default: 
				$outputMessage = ""; 
			}

			if ( $outputMessage != "" ) { 
				$this->channel->startTransaction(); 
				$this->exchange->publish($outputMessage, $this->messageQueue); 
				$this->channel->commitTransaction(); 
				print $outputMessage . "\n"; 
			}

	} // end function handleUpdate()
} // end class mybot()
		
	
$bot = &new mybot(); 
$irc = &new Net_SmartIRC(); 

//$irc->setDebug(SMARTIRC_DEBUG_ALL); 
$irc->setUseSockets(TRUE); 

print "\nConnecting to irc.lopsa.org\n"; 
$irc->connect('irc.lopsa.org', 6667); 

$irc->login("$botName", "$botRealName", 0, 'Net_SmartIRC'); 

print "Joining $channel\n"; 
$irc->join(array("$channel")); 

//$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '.', $bot, 'channelMessage');
//$irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '.*', $bot, 'channelMemberChange'); 
//$irc->registerActionhandler(SMARTIRC_TYPE_PART, '.*', $bot, 'channelMemberChange'); 
$irc->registerActionhandler(SMARTIRC_TYPE_ALL, '.*', $bot, 'handleUpdate'); 

$irc->listen(); 



?>
