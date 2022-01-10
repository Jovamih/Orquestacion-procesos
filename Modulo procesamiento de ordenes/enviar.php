<?php

$json = file_get_contents("prueba.json");

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();

$channel->queue_declare('cola_procesamientoordenes', false, true, false, false); //cola_facturacion

//$msg = new AMQPMessage(json_encode($_SESSION));
$msg = new AMQPMessage($json); //json_encode($_SESSION)
$channel->basic_publish($msg, '', 'cola_procesamientoordenes'); //cola_facturacion

//echo " [x] Sent 'Hello World!'\n";

/*$channel->close();
$connection->close();*/

echo " -> Mensaje enviado!\n";

//$enviado = true;
//include 'recibir.php';
//print_r(json_encode($_SESSION['CARRITO']));
?> 