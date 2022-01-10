<?php
    include 'global/config.php';
    include 'global/Carrito.php';
    include 'templates/cabecera.php';
?>

    <h3>Lista de artículos</h3>
    <?php if(!empty($_SESSION['detalles'])){ ?>
    <table class="table table-light table-bordered">
        <tbody>
            <tr>
                <th width="10%" class="text-center">Código</th>
                <th width="40%" class="text-center">Nombre</th>
                <th width="10%" class="text-center">Cantidad</th>
                <th width="20%" class="text-center">Precio</th>
                <th width="20%" class="text-center">Total</th>
                
            </tr>
            <?php $total=0; ?>
            <?php foreach($_SESSION['detalles'] as $indice=>$articulo){ ?>
            <tr>
                <td width="10%" class="text-center"><?php echo $articulo['ID']?></td>
                <td width="40%" class="text-center"><?php echo $articulo['NOMBRE']?></td>
                <td width="10%" class="text-center"><?php echo $articulo['CANTIDAD']?></td>
                <td width="20%" class="text-center"><?php echo $articulo['PRECIO']?></td>
                <td width="20%" class="text-center"><?php echo number_format($articulo['PRECIO']*$articulo['CANTIDAD'],2)?></td>
            </tr>
            <?php $total=$total+($articulo['PRECIO']*$articulo['CANTIDAD']); ?>
            <?php } ?>
            <tr>
                <td colspan="4" align="right"><h3>Total</h3></td>
                <td align="right"><h3>S/<?php echo number_format($total,2); ?></h3></td>
            </tr>
        </tbody>
    </table>
    <!--<button class="btn btn-success" name="btnOrdenar" value="Ordenar" type="submit">ORDENAR <i class="fas fa-check"></i></button>-->
    <a href="ordenar.php" class="btn btn-success" role="button">ORDENAR <i class="fas fa-check"></i></a>
    <?php }else{ ?>
        <div class="alert alert-success">
            No hay artículos en el carrito!
        </div>
    <?php } ?>
<?php
    include 'templates/pie.php';
    print_r(json_encode($_SESSION['detalles']));
    //include 'enviar.php';
    
?>