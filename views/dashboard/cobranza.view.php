<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Cobranza</title>
</head>
<body>
    <?php require "views/header.view.php";?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Filtros
    </button>
    <button class="btn btn-primary" id="exportarDatos">
        Exportar datos
    </button>
    <div class="row">
        <div class="col-12 col-md-12 mt-3">
            <div class="card card-body" id="labelFiltroFechas">

            </div>
        </div>
        <div class="col-lg-6 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="monto_cobrado">$0 K</h3>
                    <p>Monto cobrado</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="facturacion_cobradas">$0 K</h3>

                    <p>Facturas cobradas</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <!-- GRAFICA PAGO POR CAPA AGING -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pago por capa aging (Miles de pesos)</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Loader-->
                    <div class="row" id="loaderGraficaPagoCapaAging">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No data -->
                    <div class="row" id="noDataGraficaPagoCapaAging">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay resultados con los filtros seleccionados</p>
                        </div>
                    </div>

                    <div class="row" id="divGraficaPagoCapaAging">
                        <div class="col-12 col-md-12">
                            <div id="graficaPagoCapaAging" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- GRAFICA PAGO POR CAPA AGING REGIÓN-->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pago por región (Miles de pesos)</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Loader-->
                    <div class="row" id="loaderGraficaPagoCapaAgingRegion">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No data -->
                    <div class="row" id="noDataGraficaPagoCapaAgingRegion">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay resultados con los filtros seleccionados</p>
                        </div>
                    </div>

                    <div class="row" id="divGraficaPagoCapaAgingRegion">
                        <div class="col-12 col-md-12">
                            <div id="graficaPagoCapaAgingRegion" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- GRAFICA MONTO RECUPERADO POR FACTURA -->
        <div class="col-lg-6 col-md-6 col-sm-12"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monto recuperado por deudor</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="reponsive" id="cajaTblMonto">
                <table id="tblMontoRecuperadoDeudor" class="display" style="width:100%;">
                    <thead><tr><th>Deudor</th><th>Pago</th></tr></thead>
                    <tfoot><tr><th>Deudor</th><th>Pago</th></tr></tfoot>
                </table>
                </div>
            </div>
            </div>
        </div>
        <!-- GRAFICA MONTO RECUPERADO POR FACTURA -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monto recuperado por deudor (Miles de pesos)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Loader -->
                    <div class="row" id="loaderGraficaMontoRecuperadoDeudor">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaMontoRecuperadoDeudor">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                            <p>No hay deudores con los filtros seleccionados</p>
                        </div>
                    </div>
                    <!-- Grafica -->
                    <div class="row" id="divGraficaMontoRecuperadoDeudor">
                        <div class="col-lg-12 col-md-12s col-sm-12" style="overflow: scroll; height: 400px;">
                            <div id="graficaMontoRecuperadoDeudor" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- GRAFICA RECUENTO DE FACTURA Y MONTO PAGO POR FECHA DE APLICACION -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monto recuperado por mes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaRecuentoFactura">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaRecuentoFactura">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                            <p>No hay resultados con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaRecuentoFactura">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div id="graficaRecuentoFactura" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <?php require "views/footer.view.php";?>
    <?php
    $ocultar = "d-none";
    $tamanio = "6";
    $tamanio2 = "3";
    $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
    if (count($clientes_asignados) > 1) {
        $ocultar = "";
        $tamanio2 = "6";
        $tamanio = "6";
    }else{
        $ocultar = "d-none";
        $tamanio2 = "3";
        $tamanio = "6";
    }
    ?>
    <script src="<?php echo constant("URL");?>public/js/paginas/dashboard/cobranza.js"></script>
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

                    <div class="col-12 col-md-<?=$tamanio?> <?=$ocultar?>">
                        <div class="form-group">
                            <label>Cliente:</label>
                            <select id="selectCliente" class="select2" multiple="multiple" data-placeholder="Seleccionar clientes" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-<?=$tamanio?>">
                        <div class="form-group">
                            <label>Deudor:</label>
                            <select id="selectDeudor" class="select2" multiple="multiple" data-placeholder="Seleccionar deudores" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-<?=$tamanio2?>">
                        <div class="form-group">
                            <label>Capa Aging:</label>
                            <select id="selectCapaAgin" class="select2" multiple="multiple" data-placeholder="Seleccionar capa aging" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-<?=$tamanio2?>">
                        <div class="form-group">
                            <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
                            <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
                        </div>
                    </div>
                    <div class="col-12 col-md-<?=$tamanio2?> d-none">
                        <div class="form-group">
                            <label>División:</label>
                            <select id="selectDivision" class="select2" multiple="multiple" data-placeholder="Seleccionar división" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-2">
                        <div class="form-group">
                            <button id="btnEliminarFiltros" class="btn btn-danger form-control mt-4">
                                Eliminar filtros
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-2">
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
