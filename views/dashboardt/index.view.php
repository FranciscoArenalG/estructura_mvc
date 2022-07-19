<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dashboard - Cartera</title>
</head>

<body>
  <!-- <h1>Esta es la vista de Main</h1> -->
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
    <div class="col-lg-4 col-md-4 col-sm-12">
      <!--Md=Tablet, Lg=Portatil, Sm=movil-->
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="monto_por_cobrar">$0</h3>
          <label>Monto por cobrar</label>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-4 col-sm-12">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="facturas_por_cobrar">0</h3>
          <label>Facturas por cobrar</label>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-4 col-sm-12">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="deudores">0</h3>
          <label>Deudores</label>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
      </div>
    </div>
    <!-- GRAFICA SALDO POR VENCIMIENTO -->
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo por Vencimiento (Miles de pesos)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
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
          <!-- /.row -->
        </div>
      </div>
      <!-- /.card -->
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
            <!-- /.card-body -->
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
          <!-- /.card-body -->
      </div>
    </div>
    <!-- DSO -->
    <!-- <div class="col-12 col-md-6 text-center"> -->
    <div class="col-lg-6 col-md-6 col-sm-12 text-center"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
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
                      <tbody id="bodyTopDeudores">

                          <!--<tr>
                          <td>4.</td>
                          <td>Delegacion Jalisco</td>
                          <td>
                              <div class="progress progress-xs progress-striped active">
                                  <div class="progress-bar bg-success" style="width: 90%"></div>
                              </div>
                          </td>
                          <td>90</td>
                      </tr>-->
                      </tbody>
                  </table>
              </div>
          </div>
          <!-- /.card-body -->
      </div>
    </div>
  </div>
  <?php require "views/footer.view.php";?>
  <script src="<?php echo constant("URL");?>public/js/paginas/dashboardt/cartera.js"></script>
  <script>var servidor = '<?php echo constant("URL")?>';</script>
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
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Deudor:</label>
              <select id="selectDeudor" class="select2" multiple="multiple" data-placeholder="Seleccionar deudores" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
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