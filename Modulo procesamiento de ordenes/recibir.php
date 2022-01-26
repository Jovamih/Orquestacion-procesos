<?php
    include 'global/Carrito.php';
    include 'templates/cabecera.php';

    ini_set('max_execution_time', 3);
    error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title>Tienda de Útiles FISI</title>
</head>
<body>  
    <!--<script>swal("Pedido en Proceso!", "Esperando confirmación de pedido...", "info");</script>
    <a href="recibir.php" class="btn btn-info" role="button" style="color:#ffff;">
        <span class="spinner-grow spinner-grow-sm"></span>
            Actualizar respuesta
    </a>-->




<?php

//Incluimos las bibliotecas y clases necesarias
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
$mensajeFinal = "";

//Creamos la conexion con el servidor y el canal
$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();

//Declaramos la cola de la que vamos a consumir
$channel->queue_declare('cola_procesamientoordenes', false, true, false, false);

//echo " [*] Esperando mensaje!","\n";
//Callback para recibir los mensajes enviados por el servidor
$callback = function ($msg) {
    
    $mensaje = json_decode($msg->body,true);
    $parte_a =  $mensaje['origen'];

    if($parte_a=="inventario"){
        $parte_a = "Módulo Inventario";
        $parte_b =  $mensaje['contenido'];
        $mensajeFinal = $parte_b; ?>

        <div class="container">
            <div class="alert alert-danger">
                <h3>PEDIDO NO PROCESADO</h3>
                Algo salió mal -<strong> <?php echo $parte_a ?> </strong> 
                <br>
                <p><strong>Error: </strong><?php echo $mensajeFinal ?> </p>
                <a href="index.php" class="alert-link">Volver a inicio!</a>
            </div>
        </div>
        <?php
    }    
    if($parte_a=="cuentasporcobrar"){
        $parte_a = "Módulo Cuentas por cobrar";
        $parte_d =  $mensaje['fecha_cobro'];
        $parte_e =  $mensaje['estado_registro'];
        $parte_f =  $mensaje['fecha_entrega']; 
        $mensajeFinal = "Pedido ha sido ejecutado satisfactoriamente!"; ?>
        
        <div class="container">
            <div class="alert alert-success">
                <h3>PEDIDO PROCESADO</h3>
                <?php echo $mensajeFinal.' -' ?><strong> <?php echo $parte_a ?> </strong> 
                <br>
                <p><strong>Fecha de pedido: </strong><?php echo $parte_d ?> </p>
                <p><strong>Fecha de entrega: </strong><?php echo $parte_f ?> </p>
                <p><strong>Estado de pedido: </strong><?php echo $parte_e ?> </p>
                <?php
                    session_start(); //to ensure you are using same session
                    session_destroy(); //destroy the session
                ?>
                <a href="index.php" class="alert-link">Volver a inicio!</a>
            </div>
        </div>
        <?php
    }   
};

//Consumimos el mensaje
$channel->basic_consume('cola_procesamientoordenes', 'exchange_procesosnegocio', false, true, false, false, $callback);

while ($channel->is_open) {
    $channel->wait();
}

//Cerramos el canal y la conexion
$channel->close();
$connection->close();

?>
</body>
</html>