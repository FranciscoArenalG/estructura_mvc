<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Cartera</title>
</head>
<body>
<?php require "views/header.view.php";?>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Filtros
  </button>
  <button class="btn btn-primary" id="exportarDatos">
      Exportar datos
  </button>
  <div class="row row-cols-auto">
    <div class="col-12 col-md-12 mt-3">
      <div class="card card-body" id="labelFiltroFechas">
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
      <div class="small-box bg-info">
        <div class="inner">
          <p><strong><span id="monto_por_cobrar" style="font-size:2.0rem">$0 </span></strong> <span id="milespesos" style="font-size:1.5em"></span></p>
          <label>Monto por cobrar</label>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="facturas_por_cobrar">787</h3>
          <label>Facturas por cobrar</label>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="deudores">90</h3>
          <label>Deudores</label>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
      </div>
    </div>
    <!-- GRAFICA SALDO POR DIVISION -->
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo Documento por Status Vencimiento (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Loader -->
          <div class="row" id="loaderGraficaSaldoPorDivision">
            <div class="col-12 col-md-12">
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <!-- No data -->
          <div class="row" id="noDataGraficaSaldoPorDivision">
            <div class="col-12 col-md-12 text-center">
              <p>No hay divisiones con los filtros seleccionados</p>
            </div>
          </div>
          <!-- Grafica-->
          <div class="row" id="divGraficaSaldoPorDivision">
              <div class="col-12 col-md-12">
                  <!-- <canvas id="pieChart" height="222"></canvas>-->
                  <div id="graficaSaldoPorDivision" style="width: 100%;height:400px;"></div>
                  <!-- ./chart-responsive -->
              </div>
          </div>
        </div>
      </div>
    </div>
    <!-- SALDO POR CAPA AGING -->
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por Capa Aging (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Loader-->
          <div class="row" id="loaderGraficaSaldoPorCapaAging">
            <div class="col-12 col-md-12">
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <!-- No data -->
          <div class="row" id="noDataGraficaSaldoPorCapaAging">
            <div class="col-12 col-md-12 text-center">
              <p>No hay estatus con los filtros seleccionados</p>
            </div>
          </div>
          <div class="row" id="divGraficaSaldoPorCapaAging">
            <div class="col-12 col-md-12">
              <div id="graficaSaldoPorCapaAging" style="width: 100%;height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- SALDO POR ESTATUS -->
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por estatus (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Loader-->
          <div class="row" id="loaderGraficaSaldoPorEstatus">
            <div class="col-12 col-md-12">
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <!-- No data -->
          <div class="row" id="noDataGraficaSaldoPorEstatus">
            <div class="col-12 col-md-12 text-center">
              <p>No hay estatus con los filtros seleccionados</p>
            </div>
          </div>
          <div class="row" id="divGraficaSaldoPorEstatus">
            <div class="col-12 col-md-12">
              <div id="graficaSaldoPorEstatus" style="width: 100%;height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- SALDO POR REGIÓN -->
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por Región (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Loader-->
          <div class="row" id="loaderGraficaSaldoPorRegion">
            <div class="col-12 col-md-12">
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <!-- No data -->
          <div class="row" id="noDataGraficaSaldoPorRegion">
            <div class="col-12 col-md-12 text-center">
              <p>No hay estatus con los filtros seleccionados</p>
            </div>
          </div>
          <div class="row" id="divGraficaSaldoPorRegion">
            <div class="col-12 col-md-12">
              <div id="graficaSaldoPorRegion" style="width: 100%;height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- TOP 20 DEUDORES CON SALDO VENCIDO -->
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Top 20 deudores saldo vencido (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Deudor</th>
                  <th>Saldo vencido</th>
                  <th style="width: 40px">Facturas</th>
                </tr>
              </thead>
              <tbody id="bodyTopDeudores"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Gráficas KPI y DSO -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="row">
        <!-- KPI Current -->
        <div class="col-lg-12 col-md-12 col-sm-12 text-center"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">KPI Current %</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <!-- Loader -->
              <div class="row" id="loaderGraficaKPI">
                <div class="col-12 col-md-12">
                  <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Grafica -->
              <div class="row" id="divGraficaKPI">
                <div class="col-12 col-md-12">
                  <div id="graficaKPI" style="width: 100%;height:400px;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- DSO -->
        <div class="col-lg-12 col-md-12 col-sm-12 text-center"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">DSO %</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <!-- Loader -->
              <div class="row" id="loaderGraficaDSO">
                <div class="col-12 col-md-12">
                  <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Grafica DSO--->
              <div class="row" id="divGraficaDSO">
                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                  <div id="graficaDSO" style="width: 100%;height:400px;"></div>
                </div>
              </div>
            </div>
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
    $tamanio = "4";
  }else{
    $ocultar = "d-none";
    $tamanio = "6";
  }
  ?>
  <script src="<?php echo constant("URL");?>public/js/paginas/dashboard/cartera.js"></script>
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
              <label>Deudor:</label>
              <select id="selectDeudor" class="select2" multiple="multiple" data-placeholder="Seleccionar deudores" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-<?=$tamanio?> col-md-<?=$tamanio?> col-sm-12">
            <div class="form-group">
              <label>Capa Aging:</label>
              <select id="selectCapaAgin" class="select2" multiple="multiple" data-placeholder="Seleccionar capa aging" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Tipo de anomalía:</label>
              <select id="selectTipoAnomalia" class="select2" multiple="multiple" data-placeholder="Seleccionar tipo de anomalía" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Anomalía:</label>
              <select id="selectAnomalias" class="select2" multiple="multiple" data-placeholder="Seleccionar anomalía" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 d-none">
            <div class="form-group">
              <label>División:</label>
              <select id="selectDivision" class="select2" multiple="multiple" data-placeholder="Seleccionar división" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
              <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
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
