<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dashboard - Forecast Cobranza</title>
</head>

<body>
  <!-- <h1>Esta es la vista de Main</h1> -->
  <?php require "views/header.view.php";?>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFiltros">
      Filtros
  </button>
  <div class="row">
    <div class="col-12 col-md-12 mt-3">
      <div class="card card-body" id="labelFiltroFechas">
      </div>
    </div>
    <!-- GRAFICA FORECAST DE PAGO -->
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="card">
        <div class="card-header">
            <h3 class="card-title">Forecast de pago</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
        </div>
        <div class="card-body">
          <!-- Loader-->
          <div class="row" id="loaderGraficaForecast">
            <div class="col-12 col-md-12">
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
            </div>
          </div>
          <!-- No data -->
          <div class="row" id="noDataGraficaForecast">
            <div class="col-12 col-md-12 text-center">
              <p>No hay resultados con los filtros seleccionados</p>
            </div>
          </div>
          <div class="row" id="divGraficaForecast">
            <div class="col-12 col-md-12">
              <div id="graficaForecast" style="width: 100%;height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- TABLA FORECAST DE PAGO -->
    <div class="col-lg-12 col-md-12 col-sm-12"><!--Md=Tablet, Lg=Portatil, Sm=movil-->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Forecast de pago (tabla)</h3>
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
              <thead><tr><th>Fecha forecast</th><th>Deudor</th><th>Saldo documento</th><th>Recuento de factura</th></tr></thead>
              <tfoot><tr><th>Fecha forecast</th><th>Deudor</th><th>Saldo documento</th><th>Recuento de factura</th></tr></tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require "views/footer.view.php";?>
  <script src="<?php echo constant("URL");?>public/js/paginas/dashboardt/forecast.js"></script>
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