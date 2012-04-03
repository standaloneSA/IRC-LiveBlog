<?php
// config
$exchangeName = 'amq.fanout';
$queueName = "queue1"; 

// connect
$connection = new AMQPConnection();
$connection->connect() or die ("Error connecting\n");


// setup our queue
$chan = new AMQPChannel($connection); 
$q = new AMQPQueue($chan);
$q->setName($queueName); 
$q->declare();

$q->bind($exchangeName, $queueName);

$envelope = $q->get(AMQP_AUTOACK);
if ( ! $envelope ) { 
	print "It appears that the queue is empty\n"; 
} else { 
	print "We may have found something: \n"; 
	print $envelope->getBody() . "\n";  
}
$q->unbind($exchangeName, $queueName);

$connection->disconnect();

?>
