<?php
    session_start();

    $mensaje="";
       

    if(isset($_POST['btnAction'])){
        
        switch($_POST['btnAction']){
            case 'Agregar':
                if(is_numeric($_POST['id'])){
                    $ID=intval($_POST['id']);
                    $mensaje="OK";
                }else{
                    $mensaje="Ups...algo salio mal!";
                }

                if(is_string($_POST['nombre'])){
                    $NOMBRE=$_POST['nombre'];
                    $mensaje="OK";
                }

                if(is_numeric($_POST['precio'])){
                    $PRECIO=floatval($_POST['precio']);
                    $mensaje="OK";
                }

                if(is_numeric($_POST['cantidad_entrada'])){
                    $CANTIDAD=intval($_POST['cantidad_entrada']);
                    $mensaje="OK";
                }
                
                /******/
                $_SESSION['cod_cliente']=1;
                $_SESSION['nombre_cliente']="Shaylynn Guilliatt";
                $_SESSION['ruc_cliente']="413-49-645";

                if(!isset($_SESSION['detalles'])){
                    $articulo=array(
                        'ID'=>$ID,
                        'NOMBRE'=>$NOMBRE,
                        'PRECIO'=>$PRECIO,
                        'CANTIDAD'=>$CANTIDAD
                    );
                    $_SESSION['detalles'][0]=$articulo;
                    $mensaje="Producto agregado al carrito!";
                }else{
                    $idArticulos=array_column($_SESSION['detalles'],"ID");
                    if(in_array($ID,$idArticulos)){

                        $mensaje="Ups el producto ya ha sido seleccionado!";
                    }else{
                    $num_articulos=count($_SESSION['detalles']);
                    $articulo=array(
                        'ID'=>$ID,
                        'NOMBRE'=>$NOMBRE,
                        'PRECIO'=>$PRECIO,
                        'CANTIDAD'=>$CANTIDAD
                    );
                    $_SESSION['detalles'][$num_articulos]=$articulo;
                    $mensaje="Producto agregado al carrito!";
                    }
                }
                //$mensaje=print_r($_SESSION['detalles'],true);
                
            break;
        }
    }
    //session_destroy();
?>