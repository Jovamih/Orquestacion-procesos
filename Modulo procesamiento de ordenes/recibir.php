<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();

$channel->queue_declare('cola_procesamientoordenes', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    $mensaje = json_decode($msg->body,true);
    //print_r($mensaje);
    $parte_a =  $mensaje['origen'];
    if($parte_a=="inventario"){
        $parte_b =  $mensaje['contenido'];
        echo $parte_a.' - '.$parte_b;
    }
    if($parte_a=="cuentas"){
        $parte_c =  $mensaje['documento_factura'];
        $parte_d =  $mensaje['fecha_cobro'];
        $parte_e =  $mensaje['estado_registro'];
        //echo $parte_a.' - '.$parte_c.' - '.$parte_d.' - '.$parte_e;
        //echo '<script type="text/javascript">'; 
    }
    echo '<script>alert("gaaa");</script>;';
    
};
  
$channel->basic_consume('cola_procesamientoordenes', 'exchange_procesosnegocio', false, true, false, false, $callback);
  
while ($channel->is_open) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>


   