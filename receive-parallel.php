#!/usr/bin/php
<?php
// config
$exchangeName = 'IRClog1';
$queueName = "test_parallel"; 

// connect
$connection = new AMQPConnection();
$connection->connect() or die ("Error connecting\n");


// setup our queue
$chan = new AMQPChannel($connection); 
$chan->qos(0,5); 
$q = new AMQPQueue($chan);
$q->setName($queueName); 
$q->setFlags(AMQP_DURABLE); 

$q->declare();

$q->bind($exchangeName, $queueName);


function consumeQueue($envelope, $queue) { 
	print $envelope->getBody() . "\n";
	sleep(rand(2,7));
	//sleep(1); 
	$queue->ack($envelope->getDeliveryTag()); 
	//print "completed.\n"; 
} // end function consumeQueue()



print "Waiting. Press ctrl-c to cancel\n"; 
$q->consume("consumeQueue"); 

$q->unbind($exchangeName, $queueName);
$connection->disconnect();

?>
