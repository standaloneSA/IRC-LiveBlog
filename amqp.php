<?php

$cnn = new AMQPConnection(); 

$cnn->setLogin('guest'); 
$cnn->setPassword('guest'); 

if ($cnn->connect()) { 
	echo "Established connnection\n"; 
} else { 
	echo "No connection"; 
}

$message = "Hello World"; 

$channel = new AMQPChannel($cnn); 

$exchange = new AMQPExchange($channel); 

$exchange->setName('exchange_name'); 
$exchange->publish($message, 'your_routing_key'); 

$channel->startTransaction(); 

$exchange->publish($message, 'your_queue_name'); 

$channel->commitTransaction(); 

$cnn->disconnect(); 

?>

