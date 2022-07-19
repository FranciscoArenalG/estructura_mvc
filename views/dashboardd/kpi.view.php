<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - KPI</title>
</head>
<body>
    <?php require "views/header.view.php";?>
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalFiltros">
        Filtros
    </button>
    <div class="row">
    <div class="col-12 col-md-12">
            <div class="card card-body" id="labelFiltroFechas">

            </div>
        </div>
        <!-- INICIO GRÁFICAS -->
        <!-- INICIO GRÁFICA KPI RECEPCIÓN DE EVIDENCIAS -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title">Kpi recepción de evidencias</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                    </button>
                </div>
                </div>
                <div class="card-body">
                <div class="row" id="loaderGraficaRecepcionEvidencias">
                    <div class="col-12 col-md-12">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- No data -->
                <div class="row d-none" id="noDataGraficaRecepcionEvidencias">
                    <div class="col-12 col-md-12 text-center">
                    <p>No hay información con los filtros seleccionados</p>
                    </div>
                </div>
                    <!-- Grafica-->
                <div class="row" id="divGraficaRecepcionEvidencias">
                    <div class="col-md-12">
                    <div id="graficaRecepcionEvidencias" style="width: 100%;height:400px;"></div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!-- INICIO GRÁFICA KPI RECEPCIÓN DE CONTRATOS -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title">Kpi recepción de contratos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                    </button>
                </div>
                </div>
                <div class="card-body">
                <div class="row" id="loaderGraficaRecepcionContratos">
                    <div class="col-12 col-md-12">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- No data -->
                <div class="row" id="noDataGraficaRecepcionContratos">
                    <div class="col-12 col-md-12 text-center">
                    <p>No hay información con los filtros seleccionados</p>
                    </div>
                </div>
                    <!-- Grafica-->
                <div class="row" id="divGraficaRecepcionContratos">
                    <div class="col-md-12">
                    <div id="graficaRecepcionContratos" style="width: 100%;height:400px;"></div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!-- INICIO GRÁFICA KPI RECEPCIÓN DE FIANZA -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title">Kpi recepción de fianza</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                    </button>
                </div>
                </div>
                <div class="card-body">
                <div class="row" id="loaderGraficaRecepcionFianza">
                    <div class="col-12 col-md-12">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- No data -->
                <div class="row" id="noDataGraficaRecepcionFianza">
                    <div class="col-12 col-md-12 text-center">
                    <p>No hay información con los filtros seleccionados</p>
                    </div>
                </div>
                    <!-- Grafica-->
                <div class="row" id="divGraficaRecepcionFianza">
                    <div class="col-md-12">
                    <div id="graficaRecepcionFianza" style="width: 100%;height:400px;"></div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!-- INICIO GRÁFICA KPI RECEPCIÓN DE CONVENIOS -->
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title">Kpi recepción de convenios</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                    </button>
                </div>
                </div>
                <div class="card-body">
                <div class="row" id="loaderGraficaRecepcionConvenios">
                    <div class="col-12 col-md-12">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- No data -->
                <div class="row" id="noDataGraficaRecepcionConvenios">
                    <div class="col-12 col-md-12 text-center">
                    <p>No hay información con los filtros seleccionados</p>
                    </div>
                </div>
                    <!-- Grafica-->
                <div class="row" id="divGraficaRecepcionConvenios">
                    <div class="col-md-12">
                    <div id="graficaRecepcionConvenios" style="width: 100%;height:400px;"></div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <?php require "views/footer.view.php";?>
    <script src="<?php echo constant("URL");?>public/js/paginas/dashboardd/kpi.js"></script>
    <script>
        var servidor = '<?php echo constant("URL")?>';
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
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label>Cliente:</label>
                <select id="selectCliente" class="select2" multiple="multiple" data-placeholder="Seleccionar clientes" style="width: 100%;">
                </select>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label>Deudor:</label>
              <select id="selectDeudor" class="select2" multiple="multiple" data-placeholder="Seleccionar deudores" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label for="inputPassword6">Periodo Inicial y Periodo Final:</label>
              <input type="text" name="filtroFechas" class="form-control" aria-describedby="passwordHelpInline;">
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label>Fecha a filtrar:</label>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="fechaFactura" value="fechaFactura" name="fechaFiltrar" class="custom-control-input form-control" checked>
                <label class="custom-control-label" for="fechaFactura">Fecha factura</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="fechaCollecta" value="fechaCollecta" name="fechaFiltrar" class="custom-control-input form-control">
                <label class="custom-control-label" for="fechaCollecta">Fecha collecta</label>
              </div>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
              <label>División:</label>
              <select id="selectDivision" class="select2" multiple="multiple" data-placeholder="Seleccionar división" style="width: 100%;">
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <button id="btnEliminarFiltros" class="btn btn-danger form-control mt-4">
                Eliminar filtros
              </button>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
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
