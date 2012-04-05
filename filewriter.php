#!/usr/bin/php
<?php

$outFile = "/home/bandman/public_html/ajax/output.json"; 
$fileRefresh = 3; // in seconds

$messageCount = 0;
$arrMessages = array(); 
$prevTimeStamp = ""; 

// config
$exchangeName = 'IRClog';
$queueName = "test_parallel"; 

// connect
$connection = new AMQPConnection();
$connection->connect() or die ("Error connecting\n");


// setup our queue
$chan = new AMQPChannel($connection); 
$chan->qos(0,5); 
$q = new AMQPQueue($chan);
//$q->setName($queueName); 
//$q->setFlags(AMQP_DURABLE); 

$q->declare();

$q->bind($exchangeName, $queueName);


function consumeQueue($envelope, $queue) { 
	global $messageCount; 
	global $arrMessages;
	global $prevTimeStamp; 
	global $fileRefresh; 

	$messageCount++; 
	$timestamp = time(); 

	$arrMessages[] = array(
		$messageCount, 
		$timestamp, 
		$envelope->getBody()
	); 

	$secondsPassed = $timestamp - $prevTimeStamp; 
	if ( $secondsPassed >= $fileRefresh ) { 
		writeFile($arrMessages); 
		$prevTimeStamp = $timestamp; 
	}

	print $envelope->getBody() . "\n";

	$queue->ack($envelope->getDeliveryTag()); 
} // end function consumeQueue()

function writeFile($arrMessage) { 
	global $outFile;

	$strJSON = json_encode($arrMessage) or 
		die ("Error: " . json_last_error()); 
	
	print "**** Saving to $outFile\n"; 
	file_put_contents($outFile, $strJSON); 

} // end function writeFile()

print "Waiting. Press ctrl-c to cancel\n"; 
$q->consume("consumeQueue"); 

$q->unbind($exchangeName, $queueName);
$connection->disconnect();

?>
