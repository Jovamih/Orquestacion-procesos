<?php

//conexion de la base de dato
include ("conexion.php");

//conexion para recibir el mensaje 
require_once __DIR__ . '/vendor/autoload.php';
//require_once 'public.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
$channel = $connection->channel();
// recepcion del mensaje
$channel->queue_declare('cola_administracion_inventario', false,true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";
// consultar a la base de datos
$callback = function ($msg) {
    //echo ' [x] Received ', $msg->body, "\n";
    $mensaje = json_decode($msg->body,true);
    //json de procesamiento
    $parte = $mensaje['detalles'];
    $respuesta=true;
    //Verificacion a la base de datos sobre stock
        for ($i = 0;$i < count($parte);$i++) {
            $conn=conectar();
            $cod_producto =$parte[$i]['ID'];
            $cantidad =$parte[$i]['CANTIDAD'];
            $sql="SELECT cantidad FROM Articulo  WHERE cod_articulo ='$cod_producto'";
            $query = mysqli_query($conn,$sql);
            $row=mysqli_fetch_array($query);  
            if($row['cantidad']<=$cantidad)	{
                $respuesta=false;
                //echo " procesos:No Disponible'\n"; 
                break;     
            }    
        } 
        //Enviar mensaje a cola segun respuesta de consulta de Stock
        if($respuesta==true){
            //Enviar json a modulo de reserva
            $json_reservas["cod_cliente"] = $mensaje['cod_cliente'];
            $json_reservas["nombre_cliente"]= $mensaje['nombre_cliente'];
            $json_reservas["ruc_cliente"]= $mensaje['ruc_cliente'];
            $json= $mensaje['detalles'];
           
            for($i = 0;$i < count($json);$i ++) {
                $json_reservas["detalles"][$i]["cod_articulo"]=$json[$i]['ID'];
                $json_reservas["detalles"][$i]["nombre_articulo"]=$json[$i]['NOMBRE'];
                $json_reservas["detalles"][$i]["cantidad"]=$json[$i]['CANTIDAD'];
                $json_reservas["detalles"][$i]["precio_unitario"] =$json[$i]['PRECIO'];
            }

            $jsonreserva1 = json_encode($json_reservas);
            
            //echo $json;
            $connection2 = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
            $channel1= $connection2->channel();
            $channel1->queue_declare('cola_administracion_inventario', false, true, false, false);

            $msg1 = new AMQPMessage($jsonreserva1);
            $channel1->basic_publish($msg1, 'exchange_procesosnegocio', 'key_cola_reserva'); 
            echo " [x] 'Se esta enviando a Modulo de Reserva!'\n";
            echo $jsonreserva1;
            echo " [x] 'Se envio correctamente a Modulo de Reserva!'\n"; 
            //echo 'reserva';
            $channel1->close();
            $connection2->close();
        }else{
            //Enviar json a modulo de procesamientoordenes
            $miArray = array("origen"=>"inventario", "contenido"=>"Falta de Inventario");
            $json_procesamiento = json_encode($miArray);
            
            $connection3 = new AMQPStreamConnection('tiger.rmq.cloudamqp.com', 5672, 'apfwqrdk', 'QfWRMKJpECkqHzz43MdFveLcQG3_YWFX','apfwqrdk');
            $channel2= $connection3->channel(); 
            $channel2->queue_declare('cola_administracion_inventario', false, true, false, false);

            $msg2 = new AMQPMessage($json_procesamiento);
            $channel2->basic_publish($msg2, 'exchange_procesosnegocio', 'key_cola_procesamientoordenes');
            echo " [x] 'Se esta enviando a Modulo de Procesamiento!'\n";
            echo $json_procesamiento ;
            echo " [x] 'Se envio correctamente a Modulo de Procesamiento!'\n";
            //echo 'no disponible';
            $channel2->close();
            $connection3->close();
        }
};

$channel->basic_consume('cola_administracion_inventario', 'exchange_procesosnegocio', false, true, false, false, $callback);
while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>