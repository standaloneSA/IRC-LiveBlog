<?php

// config
$routingKey = 'routing.key';
$message = $argv[1];

// connect
$connection = new AMQPConnection();
$connection->connect();

$chan = new AMQPChannel($connection); 
$ex = new AMQPExchange($chan); 
$ex->setName('new_topic1'); 
if ( ! $ex->getType() ) { 
	$ex->setType('direct'); 
	$ex->declare(); 
}

$chan->startTransaction(); 
$ex->publish($message, $routingKey); 
$chan->commitTransaction(); 

?>
