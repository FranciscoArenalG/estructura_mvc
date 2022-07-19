<?php
/**
 *
 */
require_once 'public/vendor/autoload.php';
require_once 'public/vendor/box/spout/src/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class Dashboard extends ControllerBase
{

    public function __construct()
    {
        parent::__construct();
        // Estructura 1 - Aplica para Peñafiel y General
        if (isset($_SESSION['id_usuario-' . constant('Sistema')]) && $_SESSION['fk_estructura-' . constant('Sistema')] != 1) {
            header("location:" . constant('URL') . "Errores");
        }
    }

    /* Inicio Métodos de vistas */
    function render()
    {
        $this->view->render('dashboard/index');
    }
    function cobranza()
    {
        $this->view->render('dashboard/cobranza');
    }
    function kpi()
    {
        $this->view->render('dashboard/kpi');
    }
    function gestoria()
    {
        $this->view->render('dashboard/gestoria');
    }
    /* Fin Métodos de vistas */

    /* Inicio de Métodos de filtros */
    function getClientes()
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getClientes($clientes);
        echo json_encode($data);
        return 0;
    }
    function getDeudores($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getDeudores($clientes);
        echo json_encode($data);
        return 0;
    }
    function getCapaAgin($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getCapaAgin($clientes);
        echo json_encode($data);
        return 0;
    }
    function getCapaAginCobranza($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getCapaAginCobranza($clientes);
        echo json_encode($data);
        return 0;
    }
    function getTiposAnomalia($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getTiposAnomalia($clientes);
        echo json_encode($data);
        return 0;
    }
    function getAnomalias($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getAnomalias($clientes);
        echo json_encode($data);
        return 0;
    }
    function getRegiones($param = null)
    {
        $clientes = (empty($param[0]) ? null : $param[0]);
        $data = $this->model->getRegiones($clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin de Métodos de filtros */

    /* Inicio de Métodos de peticiones */
    /* Inicio de Métodos Targets */
    /* Inicio Targets Sección Cartera */
    function getMontoPorCobrar($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getMontoPorCobrar($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getFacturasPorCobrar($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getFacturasPorCobrar($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getTotalDeudores($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getTotalDeudores($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Targets Sección Cartera */
    /* Inicio Targets Sección Cobranza */
    function getMontoCobrado($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getMontoCobrado($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getFacturacionCobradas($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getFacturacionCobradas($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Targets Sección Cobranza */
    /* Fin de Métodos Targets */

    /* Inicio Métodos de Sección Cartera */
    function getEstatusVencimiento($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getEstatusVencimiento($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getSaldoCapaAging($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getSaldoCapaAging($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDataGraficaSaldoPorEstatus($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getDataGraficaSaldoPorEstatus($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getSaldoPorRegion($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getSaldoPorRegion($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDataTablaTop20DeudoresSaldoVencido($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getDataTablaTop20DeudoresSaldoVencido($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getKPI($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getKPI($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDSO($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getDSO($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Métodos de Sección Cartera */

    /* Inicio Métodos de Sección Cobranza */
    function getDataGraficaPagoCapaAging($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getDataGraficaPagoCapaAging($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDataGraficaPagoCapaAgingRegion($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getDataGraficaPagoCapaAgingRegion($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDataGraficaMontoRecuperadoDeudor($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getDataGraficaMontoRecuperadoDeudor($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getDataGraficaRecuentoFactura($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getDataGraficaRecuentoFactura($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Métodos de Sección Cobranza */

    /* Inicio Métodos de Sección Kpi */
    function getRegionesConteo($param = null)
    {
        $regiones = $param[0];
        $clientes = (empty($param[1]) ? null : $param[1]);
        $data = $this->model->getRegionesConteo($regiones,$clientes);
        echo json_encode($data);
        return 0;
    }
    function getRegionesKpiDso($param = null)
    {
        $regiones = $param[0];
        $clientes = (empty($param[1]) ? null : $param[1]);
        $data = $this->model->getRegionesKpiDso($regiones,$clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Métodos de Sección Kpi */

    /* Inicio Métodos de Sección Gestoría */
    function getTargetMontoFacturasGestionadas($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getTargetMontoFacturasGestionadas($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getTargetFacturasGestionadas($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getTargetFacturasGestionadas($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getTargetVisitasRealizadas($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getTargetVisitasRealizadas($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getVisitasPorRegion($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getVisitasPorRegion($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getVisitasPorRegionTabla3($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getVisitasPorRegionTabla3($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getVisitasPorRegionTabla3Localidad($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $region = (empty($param[2]) ? null : $param[2]);
        $clientes = (empty($param[3]) ? null : $param[3]);
        $data = $this->model->getVisitasPorRegionTabla3Localidad($fechainicial, $fechafinal, $region, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getVisitasPorRegionTabla3Conteo($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $region = (empty($param[2]) ? null : $param[2]);
        $localidad = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getVisitasPorRegionTabla3Conteo($fechainicial, $fechafinal, $region, $localidad, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getVisitasPorRegionTabla3DetalleVisita($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $region = (empty($param[2]) ? null : $param[2]);
        $localidad = (empty($param[3]) ? null : $param[3]);
        $deudor = (empty($param[4]) ? null : $param[4]);
        $clientes = (empty($param[5]) ? null : $param[5]);
        $data = $this->model->getVisitasPorRegionTabla3DetalleVisita($fechainicial, $fechafinal, $region, $localidad, $deudor, $clientes);
        echo json_encode($data);
        return 0;
    }
    function getGraficaRecuentoFacturaImporteDocumentoAnioMes($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getGraficaRecuentoFacturaImporteDocumentoAnioMes($fechainicial, $fechafinal, $clientes);
        echo json_encode($data);
        return 0;
    }
    /* Fin Métodos de Sección Gestoría */

    /* Inicio Métodos Exportación */
    function exportarGestoria($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $clientes = (empty($param[2]) ? null : $param[2]);
        $data = $this->model->getFiltrosGestoria($fechainicial, $fechafinal, $clientes);
        $data2 = $this->model->getColumnasGestoria($fechainicial, $fechafinal, $clientes);
        $this->exportar($data, $data2, $fechainicial, $fechafinal);
    }
    function getexportar($param = null)/* Cartera */
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $tipos_anomalia = (empty($param[4]) ? null : $param[4]);
        $anomalias = (empty($param[5]) ? null : $param[5]);
        $clientes = (empty($param[6]) ? null : $param[6]);
        $data = $this->model->getFiltros($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        $data2 = $this->model->getColumnas($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes);
        $this->exportar($data, $data2, $fechainicial, $fechafinal);
    }
    function exportarCobranza($param = null)
    {
        $fechainicial = $param[0];
        $fechafinal = $param[1];
        $deudores = (empty($param[2]) ? null : $param[2]);
        $capas = (empty($param[3]) ? null : $param[3]);
        $clientes = (empty($param[4]) ? null : $param[4]);
        $data = $this->model->getFiltrosCobranza($fechainicial, $fechafinal, $deudores, $capas, $clientes);
        $data2 = $this->model->getColumnasCobranza($fechainicial, $fechafinal, $deudores, $capas, $clientes);
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
    function listabloqueos(){
        $fecha_buscar="";
        $dia_actual = date("N");
        if ($dia_actual == 1) {
            $fecha_buscar = date("Y-m-d");
        }else {
            $proximo_lunes = time() + ( (7-($dia_actual-1)) * 24 * 60 * 60 );
            $proximo_lunes_fecha = date('Y-m-d', $proximo_lunes);
            $fecha_buscar = $proximo_lunes_fecha;
        }
        $data = $this->model->getFiltrosBloqueos($fecha_buscar);
        $data2 = $this->model->getColumnasBloqueos($fecha_buscar);
        $this->exportar($data, $data2, $fecha_buscar, $fecha_buscar);
    }
    /* Fin Métodos Exportación */

    /* Fin de Métodos de peticiones */
}
