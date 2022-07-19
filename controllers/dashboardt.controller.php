<?php
/**
 *
 */
require_once 'public/vendor/autoload.php';
require_once 'public/vendor/box/spout/src/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
class Dashboardt extends ControllerBase{

  function __construct(){
    parent::__construct();
    // Estructura 3 - Aplica para ... y General
    if (isset($_SESSION['id_usuario-'.constant('Sistema')]) && $_SESSION['fk_estructura-'.constant('Sistema')] != 3) {
      header("location:".constant('URL')."Errores");
    }
  }
  /* Inicio Métodos de vistas */
  function render(){
    $this->view->render('dashboardt/index');
  }

  function cobranza(){
    $this->view->render('dashboardt/cobranza');
  }

  function forecastcobranza(){
    $this->view->render('dashboardt/forecastcobranza');
  }
  /* Fin Métodos de vistas */

  /* Inicio Métodos filtros */
  function getDeudores(){
    $data = $this->model->getDeudores();
    echo json_encode($data);
    return 0;
  }
  function getCapaAgin(){
    $data = $this->model->getCapaAgin();
    echo json_encode($data);
    return 0;
  }
  function getTiposAnomalia(){
    $data = $this->model->getTiposAnomalia();
    echo json_encode($data);
    return 0;
  }
  function getAnomalias(){
    $data = $this->model->getAnomalias();
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos filtros */

  /* Inicio Métodos de peticiones */
  /* Inicio Métodos Targets Cartera */
  function getMontoPorCobrar($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getMontoPorCobrar($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getFacturasPorCobrar($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getFacturasPorCobrar($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getTotalDeudores($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getTotalDeudores($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos Targets Cartera */

  /* Inicio Métodos Gráficas Cartera */
  function getEstatusVencimiento($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getEstatusVencimiento($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getSaldoCapaAging($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getSaldoCapaAging($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaSaldoPorEstatus($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaSaldoPorEstatus($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getDSO($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDSO($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  function getDataTablaTop20DeudoresSaldoVencido($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $tipo_anomalia = (empty($param[4])? null : $param[4]);
    $anomalias = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataTablaTop20DeudoresSaldoVencido($fechainicial,$fechafinal,$deudor,$capas,$tipo_anomalia,$anomalias);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métofos Gráficas Cartera */

  /* Inicio Métodos targets Cobranza */
  function getMontoCobrado($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $fechaFiltrar = (empty($param[4])? null : $param[4]);
    $data = $this->model->getMontoCobrado($fechainicial,$fechafinal,$deudor,$capas,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getFacturacionCobradas($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $fechaFiltrar = (empty($param[4])? null : $param[4]);
    $data = $this->model->getFacturacionCobradas($fechainicial,$fechafinal,$deudor,$capas,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaMontoRecuperadoDeudor($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $fechaFiltrar = (empty($param[4])? null : $param[4]);
    $data = $this->model->getDataGraficaMontoRecuperadoDeudor($fechainicial,$fechafinal,$deudor,$capas,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaPagoCapaAging($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $fechaFiltrar = (empty($param[4])? null : $param[4]);
    $data = $this->model->getDataGraficaPagoCapaAging($fechainicial,$fechafinal,$deudor,$capas,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaRecuentoFactura($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $capas = (empty($param[3])? null : $param[3]);
    $fechaFiltrar = (empty($param[4])? null : $param[4]);
    $data = $this->model->getDataGraficaRecuentoFactura($fechainicial,$fechafinal,$deudor,$capas,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos targets Cobranza */

  /* Inicio Métodos Gráficas Cobranza */

  /* Fin Métodos Gráficas Cobranza */

  /* Inicio Métodos Gráficas Forecast */
  function getDataGraficaForecast($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = (empty($param[2])? null : $param[2]);
    $data = $this->model->getDataGraficaForecast($fechainicial,$fechafinal,$deudor);
    echo json_encode($data);
    return 0;
  }
  function getTablaForecast($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $deudor = $param[2];
    $data = $this->model->getTablaForecast($fechainicial,$fechafinal,$deudor);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos Gráficas Forecast */
  /* Fin Métodos de peticiones */

  /* Inicio Métodos para exportar */
  function getexportar($param = null)/* Cartera */
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $data = $this->model->getFiltros($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias);
        $data2 = $this->model->getColumnas($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias);
        $this->exportar($data, $data2, $fechainicial, $fechafinal);
    }
    function getexportarcobranza($param = null){
      $fechainicial = $param[0];
      $fechafinal = $param[1];
      $deudores = (empty($param[2])? null : $param[2]);
      $capas = (empty($param[3])? null : $param[3]);
      $fechaFiltrar = (empty($param[4])? null : $param[4]);
      $data = $this->model->getFiltrosCobranza($fechainicial,$fechafinal,$deudores,$capas,$fechaFiltrar);
      $data2 = $this->model->getColumnasCobranza($fechainicial,$fechafinal,$deudores,$capas,$fechaFiltrar);
      $this->exportar($data, $data2, $fechainicial, $fechafinal);
    }
    function exportar($datos, $columnas, $fechainicial, $fechafinal)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = $_SESSION['nombre_usuario-' . constant('Sistema')] . "-" . $fechainicial . " al " . $fechafinal . ".xlsx";
        $writer->openToBrowser($filePath);
        $cells = [];
        foreach ($columnas as $columna) {
            $cells[] = WriterEntityFactory::createCell($columna);
        }
        $singleRow = WriterEntityFactory::createRow($cells);
        $writer->addRow($singleRow);
        $styleDecimal = (new StyleBuilder())
            ->setFormat('0.00')
            ->build();
        foreach ($datos as $dato) {
            $cells2 = [];
            for ($i = 0; $i < count($columnas); $i++) {
                if (gettype($dato[$columnas[$i]]) == "double") {
                    $cells2[] = WriterEntityFactory::createCell($dato[$columnas[$i]], $styleDecimal);
                } else {
                    $cells2[] = WriterEntityFactory::createCell($dato[$columnas[$i]]);
                }
            }
            $singleRow2 = WriterEntityFactory::createRow($cells2);
            $writer->addRow($singleRow2);
        }
        $writer->close();
        exit;
    }
  /* Fin Métodos para exportar */
}

 ?>
