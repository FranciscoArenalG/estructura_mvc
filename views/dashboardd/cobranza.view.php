<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Cobranza</title>
</head>

<body>
    <?php require "views/header.view.php";?>
    <?php
        $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
        $resp_sanofi = (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados))?"true":"false";
        $grafica = "d-none";
        if ($resp_sanofi == "true") {
            $grafica = "";
        }
    ?>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFiltros">
        Filtros
    </button>
    <button class="btn btn-primary" id="exportarDatos">
        Exportar datos
    </button>
    <!-- CARDS -->
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card card-body" id="labelFiltroFechas">

            </div>
        </div>
        <div class="col-lg-6 col-6">
            <!-- small box -->
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
                    <h3 id="facturacion_cobradas">0</h3>
                    <p>Facturación cobradas</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <!-- GRAFICA MONTO RECUPERADO POR DIVISIÓN -->
        <div class="col-12 col-md-12 <?=$grafica?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monto recuperado por división (K)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Loader -->
                    <div class="row" id="loaderGraficaMontoRecuperadoDivision">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaMontoRecuperadoDivision">
                        <div class="col-12 col-md-12 text-center">
                            <p>No existe información con los filtros seleccionados</p>
                        </div>
                    </div>
                    <!-- Grafica -->
                    <div class="row" id="divGraficaMontoRecuperadoDivision">
                        <div class="col-md-12" style="overflow: scroll; height: 400px;">
                            <div id="graficaMontoRecuperadoDivision" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- GRAFICA MONTO RECUPERADO POR DEUDOR -->
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monto recuperado por deudor (K)</h3>

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
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No data -->
                    <div class="row" id="noDataGraficaMontoRecuperadoDeudor">
                        <div class="col-12 col-md-12 text-center">
                            <p>No existe información con los filtros seleccionados</p>
                        </div>
                    </div>

                    <!-- Grafica -->
                    <div class="row" id="divGraficaMontoRecuperadoDeudor">
                        <div class="col-md-12" style="overflow: scroll; height: 400px;">

                            <div id="graficaMontoRecuperadoDeudor" style="width: 100%;height:400px;"></div>

                        </div>

                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- GRAFICA PAGO POR CAPA AGING -->
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pago por capa aging (K)</h3>

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
                            <p>No existe información con los filtros seleccionados</p>
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
        <!-- GRAFICA RECUENTO DE FACTURA Y MONTO PAGO POR FECHA DE APLICACION -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recuento de factura y monto de pago por fecha de aplicación (K)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaRecuentoFactura">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaRecuentoFactura">
                        <div class="col-12 col-md-12 text-center">
                            <p>No existe información con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaRecuentoFactura">
                        <div class="col-12 col-md-12">
                            <div id="graficaRecuentoFactura" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- GRAFICA MORATORIA DE PAGO -->
        <div class="col-12 col-md-6 d-none">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Moratoria de pagos</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaMoratoriaPago">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaMoratoriaPago">
                        <div class="col-12 col-md-12 text-center">
                            <p>No existe información con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaMoratoriaPago">
                        <div class="col-12 col-md-12" style="overflow: scroll; height: 400px;">
                            <div id="graficaMoratoriaPago" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <?php require "views/footer.view.php";?>
    <script src="<?php echo constant("URL");?>public/js/paginas/dashboardd/cobranza.js"></script>
    <script>
        var servidor = '<?php echo constant("URL")?>';
        var resp_sanofi = <?=$resp_sanofi?>;
    </script>
</body>
<!-- Modal -->
<div class="modal fade" id="modalFiltros" tabindex="-1" role="dialog" aria-labelledby="modalFiltrosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFiltrosLabel">FILTROS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Cliente:</label>
                            <select id="selectCliente" class="select2" multiple="multiple" data-placeholder="Seleccionar clientes" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Deudor:</label>
                            <select id="selectDeudor" class="select2" multiple="multiple" data-placeholder="Seleccionar deudores" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Capa Aging:</label>
                            <select id="selectCapaAgin" class="select2" multiple="multiple" data-placeholder="Seleccionar capa aging" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
                            <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>División:</label>
                            <select id="selectDivision" class="select2" multiple="multiple" data-placeholder="Seleccionar división" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mt-2">
                        <div class="form-group">
                            <button id="btnEliminarFiltros" class="btn btn-danger form-control mt-4">
                                Eliminar filtros
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mt-2">
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
