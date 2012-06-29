<?php

// config
$routingKey = 'test_parallel';
$message = $argv[1];

// connect
$connection = new AMQPConnection();
$connection->connect();

$chan = new AMQPChannel($connection); 
if ( $chan->isConnected() ) { 
	$ex = new AMQPExchange($chan); 
} else { 
	print "Error connecting to channel\n"; 
	exit;
}

$ex->setName("IRClog1"); 
$ex->setType("fanout"); 
$ex->declare(); 


$chan->startTransaction(); 
//$ex->publish($message, $routingKey, AMQP_NOPARAM, array("delivery_mode", "2")); 
$ex->publish($message, $routingKey); 
$chan->commitTransaction(); 
print "Sent: " . $argv[1] . "\n"; 

?>
