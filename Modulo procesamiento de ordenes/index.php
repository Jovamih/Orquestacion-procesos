<?php
    include 'global/config.php';
    include 'global/conexion.php';
    include 'global/Carrito.php';
    include 'templates/cabecera.php';
    
?>
        
        <div class="alert alert-success">
            
            <?php
                echo $mensaje;
            ?>
            <!--<a href="" class="badge badge-success">ver carrito</a>-->
        </div>
    </div>
    
    <a href="global/vaciar_carro.php" class="btn btn-info" role="button">Vaciar carrito</a>
    
    <br>
    <br>
    <div class="row">
        <?php
            $sentencia=$pdo->prepare("SELECT * FROM Articulo");
            $sentencia->execute();
            $listaArticulos=$sentencia->fetchAll(PDO::FETCH_ASSOC);
            //print_r($listaArticulos);
        ?>
        <?php foreach($listaArticulos as $Articulo){ ?>

            <div class="col-3">
                <div class="card" style="text-align: center;">
                    <h5 class="card-title"><?php echo $Articulo['nombre_articulo'].' - '.'S/'.$Articulo['precio_unitario'];?></h5>
                    <img title="" class="card-img-top" src="<?php echo $Articulo['Imagen'];?>" alt="" height="250px;">
                    <div class="card-body">
                            
                    <form action="" method="post"> 
                        <input type="number" id="cantidad_entrada" name="cantidad_entrada" min=0 placeholder="Ingrese cantidad" style="font-style: italic;">
                        <div style="height: 10px;"></div>
                        
                        <input type="hidden" name="id" id="id" value="<?php echo $Articulo['cod_articulo'];?>">
                        <input type="hidden" name="nombre" id="nombre" value="<?php echo $Articulo['nombre_articulo'];?>">
                        <input type="hidden" name="precio" id="precio" value="<?php echo $Articulo['precio_unitario'];?>">
                        
                        <button class="btn btn-primary" name="btnAction" value="Agregar" type="submit">Añadir al carrito <i class="fas fa-shopping-cart"></i></button>
                    </form>
   
                    </div>
                </div> <br>
            </div>
            

        <?php } ?>
        
        
    </div>

<?php
    include 'templates/pie.php';
?>

