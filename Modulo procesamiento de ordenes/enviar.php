<?php

//$json = file_get_contents("prueba.json");

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();

$channel->queue_declare('cola_administracion_inventario', false, true, false, false); 

$msg = new AMQPMessage(json_encode($_SESSION));
//$msg = new AMQPMessage($json); //json_encode($_SESSION)
$channel->basic_publish($msg, 'exchange_procesosnegocio', 'key_cola_administracion_inventario'); 

//echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();

//echo " -> Mensaje enviado!\n";

echo    '<div class="alert alert-primary" style="text-align:center; margin-top:10px;">
            <em>Confirmando orden</em>
            <br>
            <div class="spinner-grow text-primary"></div>
            <div class="spinner-grow text-primary"></div>
            <div class="spinner-grow text-primary"></div>
        </div>';
        include "templates/pie.php";

//$enviado = true;
//include 'recibir.php';
//print_r(json_encode($_SESSION['CARRITO']));
?> 