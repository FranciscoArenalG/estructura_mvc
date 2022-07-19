<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Gestoría</title>
  <style media="screen">
  td.details-control, td.child-control {
    background: url(https://www.datatables.net/examples/resources/details_open.png) no-repeat center center;
    cursor: pointer;
    width: 30px;
    transition: .5s;
  }
  tr.shown td.details-control, tr.shown td.child-control {
    background: url(https://www.datatables.net/examples/resources/details_close.png) no-repeat center center;
    width: 30px;
    transition: .5s;
  }
  table.dataTable td table.dataTable,
  table.dataTable td table.dataTable * {
    border: none;
  }
  </style>
</head>

<body>
    <?php require "views/header.view.php";?>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModal">
        Filtros
    </button>
    <button class="btn btn-primary mb-3" id="exportarDatos">
        Exportar datos
    </button>
    <!-- Inicio Tarjetas -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card card-body" id="labelFiltroFechas">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="targetMontoFacturasGestionadas">$0</h3><p>Monto facturas gestionadas</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
        </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="targetFacturasGestionadas">0</h3><p>Facturas gestionadas</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="targetVisitasRealizadas">0</h3><p>Visitas realizadas</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
        </div>
        </div>
    <!-- Fin Tarjetas -->
    <!-- Inicio Gráficas -->
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
        <!--Md=Tablet, Lg=Portatil, Sm=movil-->
        <div class="card">
            <div class="card-header">
            <h3 class="card-title">Visitas por región</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <!-- Loader -->
            <div class="row" id="loaderGraficaVisitasPorRegion">
                <div class="col-12 col-md-12">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                    </div>
                </div>
                </div>
            </div>
            <!-- Sin información Grafica --->
            <div class="row" id="noDataGraficaVisitasPorRegion">
                <div class="col-12 col-md-12">
                <p>No existe información con los filtros seleccionados</p>
                </div>
            </div>
            <!-- Grafica --->
            <div class="row" id="divGraficaVisitasPorRegion">
                <div class="col-12 col-md-12">
                <div id="graficaVisitasPorRegion" style="width: 100%;height:400px;"></div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- INICIO Gráfica Recuento Factura Importe Documento Anio Mes -->
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
        <!--Md=Tablet, Lg=Portatil, Sm=movil-->
        <div class="card">
            <div class="card-header">
            <h3 class="card-title">Recuento de Factura e Importe Documento por Año y Mes</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <!-- Loader -->
            <div class="row" id="loaderGraficaRecuentoFacturaImporteDocumentoAnioMes">
                <div class="col-12 col-md-12">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                    </div>
                </div>
                </div>
            </div>
            <!-- Sin información Grafica --->
            <div class="row" id="noDataGraficaRecuentoFacturaImporteDocumentoAnioMes">
                <div class="col-12 col-md-12">
                <p>No existe información con los filtros seleccionados</p>
                </div>
            </div>
            <!-- Grafica --->
            <div class="row" id="divGraficaRecuentoFacturaImporteDocumentoAnioMes">
                <div class="col-12 col-md-12">
                <div id="graficaRecuentoFacturaImporteDocumentoAnioMes" style="width: 100%;height:400px;"></div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- INICIO Tabla2 Visitas por región -->
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
        <!--Md=Tablet, Lg=Portatil, Sm=movil-->
        <div class="card">
            <div class="card-header">
            <h3 class="card-title">Visitas Por Región (Table)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <!-- Loader -->
            <div class="row" id="loaderTablaVisitasPorRegion2">
                <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                    </div>
                </div>
                </div>
            </div>
            <!-- Sin información Grafica --->
            <div class="row" id="noDataTablaVisitasPorRegion2">
                <div class="col-lg-12 col-md-12 col-sm-12">
                <p>No existe información con los filtros seleccionados</p>
                </div>
            </div>
            <!-- Grafica --->
            <div class="row" id="divTablaVisitasPorRegion2" style="overflow: scroll; height: 500px;">
                <!-- <div class="col-12 col-md-12"> -->
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id='accordion'>
                    <table class="table table-bordered" style="width:100%;">
                        <tbody id="tblvistasporregion">

                        </tbody>
                    </table>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            </div>
        </div>
        </div>
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
        $tamanio = "4";
    }
    ?>
    <script src="<?php echo constant("URL");?>public/js/paginas/dashboard/gestoria.js"></script>
    <script>
        var servidor = '<?php echo constant("URL")?>';
        var count_clientes = <?=count($clientes_asignados)?>;
    </script>
      <!-- Fin Gráficas -->
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
                <select id="selectCliente" class="select2" multiple="multiple" data-placeholder="Seleccionar regiones" style="width: 100%;">
                </select>
            </div>
            </div>
            <!-- <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
            <div class="form-group">
                <label>Regiones:</label>
                <select id="selectRegiones" class="select2" multiple="multiple" data-placeholder="Seleccionar regiones" style="width: 100%;">
                </select>
            </div>
            </div> -->
            <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
                <div class="form-group">
                    <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
                    <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
                </div>
            </div>
            <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
            <div class="form-group">
                <button id="btnEliminarFiltros" class="btn btn-danger form-control mt-4">
                Eliminar filtros
                </button>
            </div>
            </div>
            <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
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
