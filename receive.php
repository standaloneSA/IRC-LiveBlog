<?php
// config
$exchangeName = 'new_topic1';
$routingKey = 'routing.key';
$queueName = 'myqueue';

// connect
$connection = new AMQPConnection();
$connection->connect() or die ("Error connecting\n");

$chan = new AMQPChannel($connection); 

// setup our queue
$q = new AMQPQueue($chan);
$q->setName = $queueName; 
$q->declare();

// Bind it on the exchange to routing.key
$q->bind($exchangeName, $routingKey);

// show the message
print_r($q->get());

// disconnect
$connection->disconnect();

?>
