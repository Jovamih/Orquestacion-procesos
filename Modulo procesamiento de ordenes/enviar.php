<?php

//Incluimos las bibliotecas y clases necesarias
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//Creamos la conexion con el servidor y el canal
$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();

//Declaramos la cola a la cual se va enviar
$channel->queue_declare('cola_administracion_inventario', false, true, false, false); 
//Preparamos en mensaje
$msg = new AMQPMessage(json_encode($_SESSION));
//Publicamos el mensaje
$channel->basic_publish($msg, 'exchange_procesosnegocio', 'key_cola_administracion_inventario'); 

//Cerramos el canal y la conexion
$channel->close();
$connection->close();

echo    '<div class="alert alert-primary" style="text-align:center; margin-top:10px;">
            <em>Confirmando orden</em>
            <br>
            <div class="spinner-grow text-primary"></div>
            <div class="spinner-grow text-primary"></div>
            <div class="spinner-grow text-primary"></div>
        </div>';
        include "templates/pie.php";
?>
<meta http-equiv="refresh" content="3; url=respuesta.php">
