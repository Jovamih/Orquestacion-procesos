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
    
    <script>swal("Pedido en Proceso!", "Esperando confirmación de pedido...", "info");</script>
    <?php
        include 'global/Carrito.php';
        include 'templates/cabecera.php';
    ?>
    <a href="recibir.php" class="btn btn-info" role="button" style="color:#ffff;">
        <span class="spinner-grow spinner-grow-sm"></span>
            Actualizar respuesta
    </a>
</body>
</html>
<!--<a href="recibir.php" class="btn btn-primary" role="button">Recibir respuesta <i class="fas fa-check"></i></a>-->
