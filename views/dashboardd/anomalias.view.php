<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Anomalias</title>
</head>
<body>
    <?php require "views/header.view.php";?>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Filtros
    </button>
    <button class="btn btn-primary" id="exportarDatos">
        Exportar datos
    </button>
    <!-- CARDS -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card card-body" id="labelFiltroFechas"></div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="monto_por_cobrar">$0</h3>
                    <p>Monto por cobrar</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="facturas_por_cobrar">787</h3>
                    <p>Facturas por cobrar</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="deudores">90</h3>
                    <p>Deudores</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="divisiones">3</h3>
                    <p>Divisiones</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
        <!-- GRAFICA RECUENTO DE FACTURA POR TIPO DE ANOMALIA -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recuento de factura por tipo de anomalía</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="loaderGraficaRecuentoFacturaAnomalia">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaRecuentoFacturaAnomalia">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay divisiones con los filtros seleccionados</p>
                        </div>
                    </div>
                    <!-- Grafica -->
                    <div class="row" id="divGraficaRecuentoFacturaAnomalia">
                        <div class="col-md-12">
                            <div id="graficaRecuentoFacturaAnomalia" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GRAFICA RECUENTO DE ANOMALIAS POR RESPONSABLE -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recuento de anomalías por responsable</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaRecuentoAnomaliasResponsable">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaRecuentoAnomaliasResponsable">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay estatus con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaSaldoRecuentoAnomaliasResponsable">
                        <div class="col-12 col-md-12">
                            <div id="graficaRecuentoAnomaliasResponsable" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GRAFICA SALDO POR ANOMALIA -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Saldo por anomalía (K)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaSaldoPorAnomalia">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaSaldoPorAnomalia">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay estatus con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaSaldoPorAnomalia">
                        <div class="col-12 col-md-12">
                            <div id="graficaSaldoPorAnomalia" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GRAFICA ANOMALIA MAS FRECUENTE -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Anomalía más frecuente</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loader-->
                    <div class="row" id="loaderGraficaAnomaliaFrecuente">
                        <div class="col-12 col-md-12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No data -->
                    <div class="row" id="noDataGraficaAnomaliaFrecuente">
                        <div class="col-12 col-md-12 text-center">
                            <p>No hay estatus con los filtros seleccionados</p>
                        </div>
                    </div>
                    <div class="row" id="divGraficaAnomaliaFrecuente">
                        <div class="col-12 col-md-12">
                            <div id="graficaAnomaliaFrecuente" style="width: 100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require "views/footer.view.php";?>
    <?php
        $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
        $resp_sanofi = (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados))?"true":"false";
    ?>
  <script src="<?php echo constant("URL"); ?>public/js/paginas/dashboardd/anomalias.js"></script>
  <script>
    var servidor = '<?php echo constant("URL") ?>';
    var resp_sanofi = <?=$resp_sanofi?>;
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
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>Capa Aging:</label>
                            <select id="selectCapaAgin" class="select2" multiple="multiple" data-placeholder="Seleccionar capa aging" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>Tipo de anomalía:</label>
                            <select id="selectTipoAnomalia" class="select2" multiple="multiple" data-placeholder="Seleccionar tipo de anomalía" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>Anomalía:</label>
                            <select id="selectAnomalias" class="select2" multiple="multiple" data-placeholder="Seleccionar anomalía" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
                            <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>División:</label>
                            <select id="selectDivision" class="select2" multiple="multiple" data-placeholder="Seleccionar división" style="width: 100%;">

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>Responsable:</label>
                            <select id="selectResponsable" class="select2" multiple="multiple" data-placeholder="Seleccionar responsable" style="width: 100%;">
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
