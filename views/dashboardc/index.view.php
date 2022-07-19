<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dashboard - Cartera</title>
</head>

<body>
  <!-- <h1>Esta es la vista de Main</h1> -->
  <?php require "views/header.view.php";?>
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
          <h3 id="facturas_por_cobrar">$0</h3>
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

    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="card">
        <h5 class="card-header">Inicio</h5>
        <div class="card-body">
          <!-- <h5 class="card-title">Special title treatment</h5> -->
          <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
          <p class="card-text">Bienvenido</p>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Saldo Documento por Status Vencimiento (Miles de pesos)</h3>
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
  </div>
  </div>
  <?php require "views/footer.view.php";?>
</body>
</html>