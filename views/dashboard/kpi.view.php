<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - KPI</title>
</head>
<body>
    <?php require "views/header.view.php";?>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModal">
        Filtros
    </button>
        <div id="graficasAutomatizadas" class="row"></div>
    </div>
    <?php require "views/footer.view.php";?>
    <?php
    $ocultar = "d-none";
    $tamanio = "6";
    $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
    if (count($clientes_asignados) > 1) {
        $ocultar = "";
        $tamanio = "6";
    }else{
        $ocultar = "d-none";
        $tamanio = "12";
    }
    ?>
    <script src="<?php echo constant("URL");?>public/js/paginas/dashboard/kpi.js"></script>
    <script>
        var servidor = '<?php echo constant("URL")?>';
        var count_clientes = <?=count($clientes_asignados)?>;
    </script>
</body>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">FILTROS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12 <?=$ocultar?>">
                        <div class="form-group">
                        <label>Cliente:</label>
                        <select id="selectCliente" class="select2" multiple="multiple" data-placeholder="Seleccionar cliente" style="width: 100%;">
                        </select>
                        </div>
                    </div>
                    <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
                        <div class="form-group">
                            <label>Regiones:</label>
                            <select id="selectRegiones" class="select2" multiple="multiple" data-placeholder="Seleccionar regiones" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-group">
                            <button id="btnEliminarFiltros" class="btn btn-danger form-control mt-4">
                                Eliminar filtros
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-group">
                            <button id="btnAplicarFiltros" class="btn btn-success form-control mt-4">
                                Aplicar filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <!--<button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
</html>
