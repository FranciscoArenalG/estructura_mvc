<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dashboard - Cartera</title>
</head>

<body>
  <?php require "views/header.view.php";?>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFiltros">
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
    <!-- INICIO DE TARGETS -->
    <div class="col-lg-3 col-md-3 col-sm-12"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
      <!-- INICIO TARGET MONTO POR COBRAR -->
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
    <!-- INICIO TARGET FACTURAS POR COBRAR -->
    <div class="col-lg-3 col-md-3 col-sm-12">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="facturas_por_cobrar">0</h3>
          <p>Facturas por cobrar</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
      </div>
    </div>
    <!-- INICIO TARGET DEUDORES -->
    <div class="col-lg-3 col-md-3 col-sm-12">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="deudores">0</h3>
          <p>Deudores</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
      </div>
    </div>
    <!-- INICIO TARGET DIVISIONES -->
    <div class="col-lg-3 col-md-3 col-sm-12">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="divisiones">0</h3>
          <p>Divisiones</p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
      </div>
    </div>
    <!-- FIN DE TARGETS -->

    <!-- INICIO GRÁFICAS -->
    <!-- INICIO GRÁFICA SALDO POR DIVISIÓN -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por división (K)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
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
            <div class="col-md-12">
              <div id="graficaSaldoPorDivision" style="width: 100%;height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- INICIO GRÁFICA POR ESTATUS -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por estatus (K)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
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
    <!-- INICIO TABLA DEUDORES CON SALDO VENCIDO -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Top 20 deudores saldo vencido (K)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Deudor</th>
                  <th>Saldo vencido</th>
                  <th>Porcentaje</th>
                  <th style="width: 40px">Facturas</th>
                </tr>
              </thead>
              <tbody id="bodyTopDeudores"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- INICIO GRÁFICA DSO -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">DSO</h3>
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
            <div class="col-12 col-md-12">
              <div id="graficaDSO" style="width: 100%;height:400px;"></div>
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
  <script src="<?php echo constant("URL");?>public/js/paginas/dashboardd/cartera.js"></script>
  <script>
    var servidor = '<?php echo constant("URL")?>';
    var resp_sanofi = <?=$resp_sanofi?>;
  </script>
</body>
<!-- INICIO MODAL FILTROS -->
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
          <div class="col-12 col-md-3 mt-2">
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