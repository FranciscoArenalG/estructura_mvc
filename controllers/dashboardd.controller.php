<?php
/**
 *
 */
require_once 'public/vendor/autoload.php';
require_once 'public/vendor/box/spout/src/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Row;
class Dashboardd extends ControllerBase{

  function __construct(){
    parent::__construct();
    // Estructura 2 - Aplica para Aplica a BSM, GM, INNN, LBRM, NF, Sandoz, Sanofi, SMC y General
    if (isset($_SESSION['id_usuario-'.constant('Sistema')]) && $_SESSION['fk_estructura-'.constant('Sistema')] != 2) {
      header("location:".constant('URL')."Errores");
    }
  }
  /* Inicio Métodos de vistas */
  function render(){
    $this->view->render('dashboardd/index');
  }
  function antiguedad(){
    $this->view->render('dashboardd/antiguedad');
  }
  function anomalias(){
    $this->view->render('dashboardd/anomalias');
  }
  function kpi(){
    $this->view->render('dashboardd/kpi');
  }
  function cobranza(){
    $this->view->render('dashboardd/cobranza');
  }
  /* Fin de Métodos de vistas */

  /* Inicio de Métodos de filtros */
  function getClientes(){
    $data = $this->model->getClientes();
    echo json_encode($data);
    return 0;
  }
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
  function getSelectDivisiones(){
    $data = $this->model->getSelectDivisiones();
    echo json_encode($data);
    return 0;
  }
  function getResponsables(){
    $data = $this->model->getResponsables();
    echo json_encode($data);
    return 0;
  }
  /* Filtro de divisiones por Zona */
  function getSelectDivisionesZonas(){
    $data = $this->model->getSelectDivisionesZonas();
    echo json_encode($data);
    return 0;
  }
  /* Fin de Métodos de filtros */

  /* Inicio de Métodos de peticiones */
  /* Inicio Métodos Targets Cartera, Antigüedad de saldos y Anomalía */
  function getMontoPorCobrar($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getMontoPorCobrar($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getFacturasPorCobrar($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getFacturasPorCobrar($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getTotalDeudores($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getTotalDeudores($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDivisiones($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDivisiones($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos Targets Cartera, Antigüedad de saldos y Anomalía */

  /* Inicio de Métodos de Sección Cartera */
  function getDataGraficaSaldoPorDivision($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataGraficaSaldoPorDivision($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaSaldoPorEstatus($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataGraficaSaldoPorEstatus($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataTablaTop20DeudoresSaldoVencido($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataTablaTop20DeudoresSaldoVencido($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDSO($param = null){
    $clientes = (empty($param[0])? null : $param[0]);
    $deudores = (empty($param[1])? null : $param[1]);
    $capas = (empty($param[2])? null : $param[2]);
    $tipos_anomalia = (empty($param[3])? null : $param[3]);
    $anomalias = (empty($param[4])? null : $param[4]);
    $data = $this->model->getDSO($clientes,$deudores,$capas, $tipos_anomalia, $anomalias);
    echo json_encode($data);
    return 0;
  }
  /* Fin de Métodos de Sección Cartera */

  /* Inicio de Métodos de Sección Antigüedad de saldos */
  function getDataGraficaFechaDocumento($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataGraficaFechaDocumento($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaFechaRecibida($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataGraficaFechaRecibida($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaFechaContrarecibo($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $data = $this->model->getDataGraficaFechaContrarecibo($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones);
    echo json_encode($data);
    return 0;
  }
  /* Fin de Métodos de Sección Antigüedad de saldos */

  /* Inicio Métodos de Sección Anomalías */
  function getDataGraficaRecuentoFacturaAnomalia($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $responsables = (empty($param[8])? null : $param[8]);
    $data = $this->model->getDataGraficaRecuentoFacturaAnomalia($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $responsables);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaRecuentoAnomaliasResponsable($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $responsables = (empty($param[8])? null : $param[8]);
    $data = $this->model->getDataGraficaRecuentoAnomaliasResponsable($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $responsables);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaSaldoPorAnomalia($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $responsables = (empty($param[8])? null : $param[8]);
    $data = $this->model->getDataGraficaSaldoPorAnomalia($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $responsables);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaAnomaliaFrecuente($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $responsables = (empty($param[8])? null : $param[8]);
    $data = $this->model->getDataGraficaAnomaliaFrecuente($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $responsables);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos de Sección Anomalías */

  /* Inicio Métodos de Sección KPI Sanofi */
  function getDataGraficaKpiRecepcionEvidencias($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $divisiones = (empty($param[4])? null : $param[4]);
    $fechaFiltrar = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaKpiRecepcionEvidencias($fechainicial,$fechafinal,$clientes,$deudores, $divisiones,$fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaKpiRecepcionContratos($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $divisiones = (empty($param[4])? null : $param[4]);
    $fechaFiltrar = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaKpiRecepcionContratos($fechainicial,$fechafinal,$clientes,$deudores, $divisiones, $fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaKpiRecepcionFianza($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $divisiones = (empty($param[4])? null : $param[4]);
    $fechaFiltrar = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaKpiRecepcionFianza($fechainicial,$fechafinal,$clientes,$deudores, $divisiones, $fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaKpiRecepcionConvenios($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $divisiones = (empty($param[4])? null : $param[4]);
    $fechaFiltrar = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaKpiRecepcionConvenios($fechainicial,$fechafinal,$clientes,$deudores, $divisiones, $fechaFiltrar);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos de Sección KPI Sanofi */

  /* Inicio Métodos de Sección Cobranza */
  function getMontoCobrado($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getMontoCobrado($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getFacturacionCobradas($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getFacturacionCobradas($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaMontoRecuperadoDivision($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaMontoRecuperadoDivision($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaMontoRecuperadoDeudor($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaMontoRecuperadoDeudor($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaPagoCapaAging($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaPagoCapaAging($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaRecuentoFactura($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaRecuentoFactura($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  function getDataGraficaMoratoriaPago($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getDataGraficaMoratoriaPago($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones);
    echo json_encode($data);
    return 0;
  }
  /* Fin Métodos de Sección Cobranza */

  /* Inicio Método para exportar a excel */
  function getexportar($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $tipos_anomalia = (empty($param[5])? null : $param[5]);
    $anomalias = (empty($param[6])? null : $param[6]);
    $divisiones = (empty($param[7])? null : $param[7]);
    $isCobranza = (empty($param[8])? null : $param[8]);
    $data = $this->model->getExport($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $isCobranza);
    $data2 = $this->model->getColumnas($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones, $isCobranza);
    $this->exportar($data, $data2, $fechainicial, $fechafinal);
  }
  function getexportarcobranza($param = null){
    $fechainicial = $param[0];
    $fechafinal = $param[1];
    $clientes = (empty($param[2])? null : $param[2]);
    $deudores = (empty($param[3])? null : $param[3]);
    $capas = (empty($param[4])? null : $param[4]);
    $divisiones = (empty($param[5])? null : $param[5]);
    $data = $this->model->getFiltrosCobranza($fechainicial,$fechafinal,$clientes,$deudores,$capas, $divisiones);
    $data2 = $this->model->getColumnasCobranza($fechainicial,$fechafinal,$clientes,$deudores,$capas,  $divisiones);
    $this->exportar($data, $data2, $fechainicial, $fechafinal);
  }
  function exportar($datos, $columnas, $fechainicial, $fechafinal){
    $writer = WriterEntityFactory::createXLSXWriter();
    $filePath = "Dashboard-".$_SESSION['nombre_usuario-'.constant('Sistema')]."-" . $fechainicial . " al " . $fechafinal . ".xlsx";
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
  /* Fin Método para exportar a excel */

  /* Fin de Métodos de peticiones */
}

 ?>
