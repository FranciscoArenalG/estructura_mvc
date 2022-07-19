<?php
class DashboardModel extends ModelBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /* Inicio Métodos de filtros */
    public function getClientes()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select id_c_cliente, razon_social_cliente
                from tb_c_cliente tcc
                where id_c_cliente in ($cliente)
                order by 1
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDeudores()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(tcd2.razon_social_deudor), tcd2.id_c_deudor
                from tb_c_documento tcd
                inner join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                order by 1
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getCapaAgin()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(
                (case
                when current_date - tcd.fecha_documento - tcd.dias_credito <0 then 'A (Current)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 0 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                else 'Desconocida'
                end)) as capa
                from tb_c_documento tcd
                inner join tb_c_cliente tcc
                on tcd.id_c_cliente = tcc.id_c_cliente
                where 1=1
                and tcd.status in (1, 2, 3, 4, 5)
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                order by 1
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getCapaAginCobranza()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(
                (case
                when tpd.fecha_aplicacion_pago  - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'MAS DE 120 DIAS'
                end)) as capa
                from tb_pago_documento tpd
                left join tb_c_documento tcd
                on tcd.id_c_documento = tpd.id_c_documento
                where 1=1
                and tpd.id_c_cliente in ($cliente)
                and tcd.id_c_documento is not null
                and tpd.fecha_aplicacion_pago is not null
                and tcd.fecha_documento is not null
                order by capa
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getTiposAnomalia()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(tcta.descripcion_tipo_anomalia), tcta.id_c_tipo_anomalia
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                where 1=1
                and tcd.status in (1, 2, 3, 4, 5)
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                order by 1
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getAnomalias()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select tca.id_c_anomalia,  tca.clave_anomalia , tca.descripcion_anomalia, tcta.descripcion_tipo_anomalia
                from tb_c_anomalia tca
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left join tb_c_documento tcd 
                on tcd.id_c_anomalia = tca.id_c_anomalia 
                where 1=1
                and tcd.id_c_cliente in ($cliente)
                group by tca.id_c_anomalia,  tca.clave_anomalia , tca.descripcion_anomalia, tcta.descripcion_tipo_anomalia
                order by descripcion_anomalia ASC
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
        }
    }
    public function getRegiones($clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = " tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT tcz.nombre_zona, tcz.id_c_zona
            FROM tb_c_documento tcd INNER JOIN tb_c_zona tcz ON tcz.id_c_zona = tcd.num_zona
            WHERE $whereClientes AND
            (case
                when current_date - tcd.fecha_documento - tcd.dias_credito <0 then 'A (Current)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 0 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                else 'Desconocida'
                end) IN ('A (Current)')
            GROUP BY tcz.nombre_zona, tcz.id_c_zona
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos de filtros */

    /* Inicio Métodos de targets Cartera */
    public function getMontoPorCobrar($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)/* Consulta actualizada */
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select COALESCE(sum(tcd.saldo_documento), 0, sum(tcd.saldo_documento)) as monto_por_cobrar
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                $whereClientes
                and tcd.status in (1, 5)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                $whereSapAnomalia
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: " . $e->getMessage();
            return false;
        }
    }
    public function getFacturasPorCobrar($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)/* Consulta actualizada */
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select count(*) as facturas_por_cobrar
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                $whereSapAnomalia
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ". $e->getMessage();
            return false;
        }
    }
    public function getTotalDeudores($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)/* Consulta actualizada */
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select count( distinct tcd.id_c_deudor) as total_deudores
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                $whereSapAnomalia
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos de targets Cartera */

    /* Inicio Métodos de targets Cobranza */
    public function getMontoCobrado($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
            $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select count(*) as facturas_cobradas, sum(tcp.monto_pago) as monto_cobrado
            from tb_pago_documento tpd
            left join tb_c_documento tcd
            on tcd.id_c_documento = tpd.id_c_documento
            left join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left join tb_cuenta_deudor tcd3
            on tcd3.id_c_deudor = tcd2.id_c_deudor
            left join tb_c_pago tcp
            on tpd.id_c_pago = tcp.id_c_pago
            left join tb_c_transferencia_cheque tctc
            on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
            left join tb_c_tipo_pago tctp
            on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
            left join tb_c_anomalia tca
            on tca.id_c_anomalia = tcd.id_c_anomalia
            left join tb_documento_nota tdn
            on tdn.id_c_documento = tcd.id_c_documento
            left join tb_c_documento tcd4
            on tcd4.id_c_documento = tdn.id_c_nota
            where $whereFecha
            $whereCapa
            $whereDeudores
            $whereClientes
            and tcd.id_c_documento is not null
            and tpd.fecha_aplicacion_pago is not null
            and tcd.fecha_documento is not null
            ");
            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getFacturacionCobradas($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
            $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select count(*) as facturas_cobradas, sum(tcp.monto_pago) as monto_cobrado
            from tb_pago_documento tpd
            left join tb_c_documento tcd
            on tcd.id_c_documento = tpd.id_c_documento
            left join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left join tb_cuenta_deudor tcd3
            on tcd3.id_c_deudor = tcd2.id_c_deudor
            left join tb_c_pago tcp
            on tpd.id_c_pago = tcp.id_c_pago
            left join tb_c_transferencia_cheque tctc
            on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
            left join tb_c_tipo_pago tctp
            on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
            left join tb_c_anomalia tca
            on tca.id_c_anomalia = tcd.id_c_anomalia
            left join tb_documento_nota tdn
            on tdn.id_c_documento = tcd.id_c_documento
            left join tb_c_documento tcd4
            on tcd4.id_c_documento = tdn.id_c_nota
            where $whereFecha
            $whereCapa
            $whereDeudores
            $whereClientes
            and tcd.id_c_documento is not null
            and tpd.fecha_aplicacion_pago is not null
            and tcd.fecha_documento is not null
            ");

            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos de targets Cobranza */

    /* Inicio Métodos Sección Cartera */
    public function getEstatusVencimiento($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select * from
                (select (case when sum(tcd.saldo_documento) is null then 0 else sum(tcd.saldo_documento) end)  as SaldoSinVencer
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                where 1=1
                $whereClientes
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                $whereSapAnomalia
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                and current_date - tcd.fecha_documento - tcd.dias_credito < 1
                and tcd.fecha_documento between ? and ?) as tbl1,
                (select (case when sum(tcd.saldo_documento) is null then 0 else sum(tcd.saldo_documento) end)  as SaldoVencido
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                where 1=1
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                $whereSapAnomalia
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                and current_date - tcd.fecha_documento - tcd.dias_credito > 0
                and tcd.fecha_documento between ? and ?) as tbl2
            ");
            $query->execute([$fechainicial, $fechafinal, $fechainicial, $fechafinal]);

            if ($query->rowCount() == 0) {
            // echo $query->rowCount();
            return $query->rowCount();
            }else {
            return $query->fetchAll();
            }
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getSaldoCapaAging($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                SELECT (CASE WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito <1 THEN 'A (Current)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 1 AND 30 THEN 'B (0-30)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 31 AND 60 THEN 'C (31-60)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 61 AND 90 THEN 'D (61-90)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 91 AND 120 THEN 'E (91-120)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 121 AND 150 THEN 'F (121-150)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito BETWEEN 151 AND 180 THEN 'G (151-180)'
                WHEN CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito > 180 THEN 'H (>180)'
                ELSE 'Desconocida'
                END) AS AsignStatus, sum(tcd.saldo_documento) as SaldoDocumento
                FROM tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                where 1=1
                $whereClientes
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                $whereSapAnomalia
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                and tcd.fecha_documento between ? and ?
                group by AsignStatus
                order by AsignStatus DESC
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaSaldoPorEstatus($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select (case when tcta.descripcion_tipo_anomalia is null then 'Otros' else tcta.descripcion_tipo_anomalia end) as anomalia, sum(tcd.saldo_documento) as saldo
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                $whereSapAnomalia
                and tcd.fecha_documento between ? and ?
                group by anomalia
                order by 1
            ");

            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getSaldoPorRegion($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                SELECT tcz.nombre_zona AS Zona,SUM(tcd.saldo_documento) AS Saldo
                FROM tb_c_documento tcd
                INNER JOIN tb_c_zona tcz ON tcz.id_c_zona = tcd.num_zona
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                WHERE tcd.id_c_tipo_documento = 1
                $whereClientes
                AND tcd.status in (1, 5)
                $whereSapAnomalia
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.fecha_documento between ? and ?
                GROUP BY tcz.nombre_zona
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataTablaTop20DeudoresSaldoVencido($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select tcd2.razon_social_deudor, sum(tcd.importe_documento) as saldo, count(*) as facturas,
                    ((sum(tcd.importe_documento) / (select COALESCE(sum(tcd.importe_documento), 0, sum(tcd.importe_documento)) as monto_por_cobrar
                        from tb_c_documento tcd
                        left join tb_c_anomalia tca
                        on tcd.id_c_anomalia = tca.id_c_anomalia
                        left outer join tb_c_tipo_cliente tctc
                        on tcd.id_division = tctc.id_c_tipo_cliente
                        where 1=1
                        and tcd.status in (1, 2, 3, 4, 5)
                        $whereClientes
                        $whereDeudores
                        $whereTipoAnomlia
                        $whereAnomlia
                        $whereCapa
                        and tcd.id_c_tipo_documento = 1
                        and tcd.fecha_documento between ? and ?
                    )) * 100) as porcentaje
                from tb_c_documento tcd
                left outer join tb_c_deudor tcd2
                on tcd.id_c_deudor = tcd2.id_c_deudor
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join app_chck_doctos acd
                on tcd.id_c_documento = acd.id_c_documento
                and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                and (current_date - tcd.fecha_documento - tcd.dias_credito) > 0
                $whereSapAnomalia
                group by tcd2.razon_social_deudor
                order by 2 desc
                limit 20
            ");

            $query->execute([$fechainicial, $fechafinal, $fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getKPI($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select (saldo_capa_current/saldo_total) as KPI
                    from
                    (
                    select sum(tcd.saldo_documento) saldo_capa_current
                    from tb_c_documento tcd
                    left join tb_c_anomalia tca
                    on tcd.id_c_anomalia = tca.id_c_anomalia
                    left join app_chck_doctos acd
                    on tcd.id_c_documento = acd.id_c_documento
                    and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                    where 1=1
                    and tcd.status IN (1,5)
                    $whereClientes
                    AND CURRENT_DATE - tcd.fecha_documento - tcd.dias_credito <1
                    $whereSapAnomalia
                    $whereDeudores
                    $whereTipoAnomlia
                    $whereAnomlia
                    $whereCapa
                    AND tcd.fecha_documento BETWEEN ? and ?
                    ) as a,
                    (
                    select sum(tcd.saldo_documento) saldo_total
                    from tb_c_documento tcd
                    left join tb_c_anomalia tca
                    on tcd.id_c_anomalia = tca.id_c_anomalia
                    left join app_chck_doctos acd
                    on tcd.id_c_documento = acd.id_c_documento
                    and acd.id_app_chck_doctos in (select max(acd2.id_app_chck_doctos) from app_chck_doctos acd2 where acd2.numero_documento = acd.numero_documento)
                    where 1=1
                    and tcd.status in (1,5)
                    $whereClientes
                    $whereSapAnomalia
                    $whereDeudores
                    $whereTipoAnomlia
                    $whereAnomlia
                    $whereCapa
                    AND tcd.fecha_documento BETWEEN ? and ?
                    ) as b
            ");
            $query->execute([$fechainicial, $fechafinal, $fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDSO($clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select (cartera_activa/cartera_total)*dias as DSO
                from
                (
                select sum(saldo_documento) cartera_activa
                from tb_c_documento tcd
                where 1=1
                and tcd.status in (1,5)
                and tcd.id_c_tipo_documento = 1
                $whereClientes
                and date_trunc('month',fecha_recibida_aeesa) = DATE_TRUNC('month',now()- INTERVAL '1 months')
                ) as a,
                (
                select sum(importe_documento) cartera_total
                from tb_c_documento tcd
                where 1=1
                and tcd.status in (1,2,3,4,5)
                and tcd.id_c_tipo_documento = 1
                $whereClientes
                and date_trunc('month',fecha_recibida_aeesa) = DATE_TRUNC('month',now()- INTERVAL '1 months')
                ) as b,
                (
                SELECT
                    DATE_PART('days',
                        DATE_TRUNC('month',now()- INTERVAL '1 months')
                        + '1 MONTH'::INTERVAL
                        - '1 DAY'::INTERVAL
                    ) as dias
                ) as c
            ");
            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Cartera */

    /* Inicio Métodos Sección Cobranza */
    public function getDataGraficaPagoCapaAging($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
            $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                    end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select
            sum(tcp.monto_pago) as monto,
            (case
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
            when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
            end) as capa
            from tb_pago_documento tpd
            left join tb_c_documento tcd
            on tcd.id_c_documento = tpd.id_c_documento
            left join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left join tb_cuenta_deudor tcd3
            on tcd3.id_c_deudor = tcd2.id_c_deudor
            left join tb_c_pago tcp
            on tpd.id_c_pago = tcp.id_c_pago
            left join tb_c_transferencia_cheque tctc
            on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
            left join tb_c_tipo_pago tctp
            on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
            left join tb_c_anomalia tca
            on tca.id_c_anomalia = tcd.id_c_anomalia
            left join tb_documento_nota tdn
            on tdn.id_c_documento = tcd.id_c_documento
            left join tb_c_documento tcd4
            on tcd4.id_c_documento = tdn.id_c_nota
            left join tb_c_zona tcz
            on tcd.num_zona = tcz.id_c_zona
            where $whereFecha
            $whereDeudores
            $whereCapa
            $whereClientes
            and tcd.id_c_documento is not null
            and tpd.fecha_aplicacion_pago is not null
            and tcd.fecha_documento is not null
            group BY capa
            order by capa asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaPagoCapaAgingRegion($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
            $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select
            sum(tcp.monto_pago) as monto,
            (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end) as region
            from tb_pago_documento tpd
            left join tb_c_documento tcd
            on tcd.id_c_documento = tpd.id_c_documento
            left join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left join tb_cuenta_deudor tcd3
            on tcd3.id_c_deudor = tcd2.id_c_deudor
            left join tb_c_pago tcp
            on tpd.id_c_pago = tcp.id_c_pago
            left join tb_c_transferencia_cheque tctc
            on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
            left join tb_c_tipo_pago tctp
            on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
            left join tb_c_anomalia tca
            on tca.id_c_anomalia = tcd.id_c_anomalia
            left join tb_documento_nota tdn
            on tdn.id_c_documento = tcd.id_c_documento
            left join tb_c_documento tcd4
            on tcd4.id_c_documento = tdn.id_c_nota
            left join tb_c_zona tcz
            on tcd.num_zona = tcz.id_c_zona
            where $whereFecha
            $whereCapa
            $whereDeudores
            $whereClientes
            and tcd.id_c_documento is not null
            and tpd.fecha_aplicacion_pago is not null
            and tcd.fecha_documento is not null
            group by region
            order by region asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaMontoRecuperadoDeudor($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
            $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                    end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select deudor, pago
            from
            (
            select tcd2.razon_social_deudor as deudor, sum(tcp.monto_pago) as pago
            from tb_pago_documento tpd
            left join tb_c_documento tcd
            on tcd.id_c_documento = tpd.id_c_documento
            left join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left join tb_cuenta_deudor tcd3
            on tcd3.id_c_deudor = tcd2.id_c_deudor
            left join tb_c_pago tcp
            on tpd.id_c_pago = tcp.id_c_pago
            left join tb_c_transferencia_cheque tctc
            on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
            left join tb_c_tipo_pago tctp
            on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
            left join tb_c_anomalia tca
            on tca.id_c_anomalia = tcd.id_c_anomalia
            left join tb_documento_nota tdn
            on tdn.id_c_documento = tcd.id_c_documento
            left join tb_c_documento tcd4
            on tcd4.id_c_documento = tdn.id_c_nota
            where $whereFecha
            $whereDeudores
            $whereCapa
            $whereClientes
            and tcd.id_c_documento is not null
            and tpd.fecha_aplicacion_pago is not null
            and tcd.fecha_documento is not null
            group by tcd2.razon_social_deudor
            order by 2 ASC
            ) as foo
            where pago > 0
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaRecuentoFactura($fechainicial,$fechafinal,$deudores,$capas,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
              $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
              $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
              SELECT
              (CASE WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 1 THEN 'enero'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 2 THEN 'febrero'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 3 THEN 'marzo'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 4 THEN 'abril'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 5 THEN 'mayo'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 6 THEN 'junio'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 7 THEN 'julio'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 8 THEN 'agosto'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 9 THEN 'septiembre'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 10 THEN 'octubre'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 11 THEN 'noviembre'
              WHEN date_part('month'::TEXT, tpd.fecha_aplicacion_pago) = 12 THEN 'diciembre'
              ELSE 'Sin mes'
              END) AS mes,
              date_part('month'::TEXT, tpd.fecha_aplicacion_pago) AS numMes,
              date_part('year'::TEXT, tpd.fecha_aplicacion_pago) AS Anio, sum(tcp.monto_pago) as monto_cobrado,
              COUNT(tcd.numero_documento) AS ConteoFacturas
              from tb_pago_documento tpd
              left join tb_c_documento tcd
              on tcd.id_c_documento = tpd.id_c_documento
              left join tb_c_deudor tcd2
              on tcd2.id_c_deudor = tcd.id_c_deudor
              left join tb_cuenta_deudor tcd3
              on tcd3.id_c_deudor = tcd2.id_c_deudor
              left join tb_c_pago tcp
              on tpd.id_c_pago = tcp.id_c_pago
              left join tb_c_transferencia_cheque tctc
              on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
              left join tb_c_tipo_pago tctp
              on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
              left join tb_c_anomalia tca
              on tca.id_c_anomalia = tcd.id_c_anomalia
              left join tb_documento_nota tdn
              on tdn.id_c_documento = tcd.id_c_documento
              left join tb_c_documento tcd4
              on tcd4.id_c_documento = tdn.id_c_nota
              where $whereFecha
              $whereDeudores
              $whereCapa
              $whereClientes
              and tcd.id_c_documento is not null
              and tpd.fecha_aplicacion_pago is not null
              and tcd.fecha_documento is not null
              group by mes, anio, numMes
              order by anio asc, numMes ASC
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Cobranza */

    /* Inicio Métodos Sección Kpi */
    public function getRegionesConteo($region,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereRegion = "";
            if ($region != "" && $region != null && $region != 'null') {
            $whereRegion = " and tcz.id_c_zona in ($region)";
            }
            $whereClientes = " and tck.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tck.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select tck.id_c_cliente as cliente, tcz.id_c_zona, tcz.nombre_zona
            from tb_c_kpi tck
            left join tb_c_zona tcz
            on tck.id_c_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereRegion
            group by cliente, tcz.id_c_zona, tcz.nombre_zona
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getRegionesKpiDso($region,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereRegion = "";
            if ($region != "" && $region != null && $region != 'null') {
            $whereRegion = " and tcz.id_c_zona in ($region)";
            }
            $whereClientes = " and tck.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tck.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select tck.id_c_cliente as cliente, tcz.id_c_zona, tcz.nombre_zona, tck.saldo_total, tck.saldo_corriente, tck.saldo_vencido,tck.dso,
            (CASE WHEN date_part('month'::TEXT, tck.fecha_kpi) = 1 THEN 'enero'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 2 THEN 'febrero'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 3 THEN 'marzo'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 4 THEN 'abril'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 5 THEN 'mayo'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 6 THEN 'junio'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 7 THEN 'julio'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 8 THEN 'agosto'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 9 THEN 'septiembre'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 10 THEN 'octubre'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 11 THEN 'noviembre'
            WHEN date_part('month'::TEXT, tck.fecha_kpi) = 12 THEN 'diciembre'
            END) AS mes, date_part('year'::TEXT, tck.fecha_kpi) AS Anio
            from tb_c_kpi tck
            left join tb_c_zona tcz
            on tck.id_c_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereRegion
            order by Anio ASC
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Kpi */

    /* Inicio Métodos Sección Gestoría */
    public function getTargetMontoFacturasGestionadas($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT SUM(acd.importe_documento)
            from app_chck_doctos acd
            inner join tb_user tu
            on acd.id_user = tu.id_user
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getTargetFacturasGestionadas($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT COUNT(acd.numero_documento) as factura
            from app_chck_doctos acd
            inner join tb_user tu
            on acd.id_user = tu.id_user
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getTargetVisitasRealizadas($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT  COUNT(distinct tcd.id_c_deudor) as visitas
            from app_chck_doctos acd
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left join tb_c_documento tcd
            on tcd.id_c_documento = acd.id_c_documento
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getVisitasPorRegion($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT COUNT(distinct tcd.id_c_deudor) AS Conteo, tcz.nombre_zona AS region
            from app_chck_doctos acd
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left join tb_c_documento tcd
            on tcd.id_c_documento = acd.id_c_documento
            inner join tb_c_cliente tcc
            ON tcd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd2
            ON tcd.id_c_deudor = tcd2.id_c_deudor
            LEFT JOIN tb_cuenta_deudor tcd4
            ON tcd.id_c_deudor = tcd4.id_c_deudor
            LEFT JOIN tb_cuenta_deudor_cliente tcdc
            ON tcd4.id_cuenta_deudor = tcdc.id_cuenta_deudor
            LEFT JOIN tb_c_zona tcz
            ON tcdc.id_c_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            GROUP BY region
            order by region asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getVisitasPorRegionTabla3($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end), COUNT(distinct acd.id_c_deudor) as visitas, COUNT(distinct tcd.id_c_deudor) as deudores, COUNT(acd.numero_documento) as factura
            from app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            group by tcz.nombre_zona
            order by tcz.nombre_zona asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getVisitasPorRegionTabla3Localidad($fechainicial,$fechafinal,$region,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereRegion =  " and (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end) in ('$region')";
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select (case when trlg.localidad is null then 'NO IDENTIFICADA' else trlg.localidad  end)as Localidad, (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end), COUNT(distinct acd.id_c_deudor) as visitas, COUNT(distinct acd.id_c_deudor) as deudores, COUNT(acd.numero_documento) as factura
            from app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            left join tb_region_localidad_gestor trlg
            on trlg.gestor = tu.name_user
            where 1=1
            $whereClientes
            $whereRegion
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            group by Localidad, tcz.nombre_zona
            order by Localidad asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getVisitasPorRegionTabla3Conteo($fechainicial,$fechafinal,$region,$localidad,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereRegion =  " and (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end) in ('$region')";
            $whereLocalidad = " and (case when trlg.localidad is null then 'NO IDENTIFICADA' else trlg.localidad  end) in ('$localidad')";
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select (case when trlg.localidad is null then 'NO IDENTIFICADA' else trlg.localidad  end) as Localidad, (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end), tcd.razon_social_deudor, COUNT(distinct acd.id_c_deudor) as visitas, COUNT(acd.numero_documento) as factura
            from app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            left join tb_region_localidad_gestor trlg
            on trlg.gestor = tu.name_user
            where 1=1
            $whereClientes
            $whereRegion
            $whereLocalidad
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            group by Localidad, tcz.nombre_zona, tcd.razon_social_deudor
            order by Localidad asc
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getVisitasPorRegionTabla3DetalleVisita($fechainicial,$fechafinal,$region,$localidad,$deudor,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereRegion =  " and (case when tcz.nombre_zona is null then 'NO IDENTIFICADA' else tcz.nombre_zona end) in ('$region')";
            $whereLocalidad = " and (case when trlg.localidad is null then 'NO IDENTIFICADA' else trlg.localidad  end) in ('$localidad')";
            $whereDeudor = " and tcd.razon_social_deudor in ('$deudor')";
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select acd.fecha_inserta ,acd.numero_documento as factura, acd.saldo_documento as SaldoDocumento, tca.descripcion_anomalia, acd.observacion as Ultimocomentario
            from app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            left join tb_region_localidad_gestor trlg
            on trlg.gestor = tu.name_user
            where 1=1
            $whereClientes
            $whereRegion
            $whereLocalidad
            $whereDeudor
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getGraficaRecuentoFacturaImporteDocumentoAnioMes($fechainicial,$fechafinal,$clientes)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            SELECT COUNT(acd.numero_documento) as factura,
            (CASE WHEN date_part('month'::TEXT, acd.fecha_inserta) = 1 THEN 'enero'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 2 THEN 'febrero'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 3 THEN 'marzo'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 4 THEN 'abril'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 5 THEN 'mayo'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 6 THEN 'junio'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 7 THEN 'julio'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 8 THEN 'agosto'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 9 THEN 'septiembre'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 10 THEN 'octubre'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 11 THEN 'noviembre'
            WHEN date_part('month'::TEXT, acd.fecha_inserta) = 12 THEN 'diciembre'
            END) AS mes,
            date_part('month'::TEXT, acd.fecha_inserta) AS numeroMes,
            date_part('year'::TEXT, acd.fecha_inserta) AS anio,
            SUM(acd.importe_documento) AS ImporteDocumento
            FROM app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and acd.status_chckd = 2
            GROUP BY mes, anio, numeroMes
            ORDER BY anio ASC, numeroMes ASC
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Gestoría */

    /* Inicio Método para exportar a excel */
    public function getFiltrosGestoria($fechainicial,$fechafinal,$clientes)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
                $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
                $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            // $query = $this->con->query("SELECT tcz.nombre_zona AS Zona,SUM(tcd.saldo_documento) AS Saldo FROM tb_c_documento tcd INNER JOIN tb_c_zona tcz ON tcz.id_c_zona = tcd.num_zona WHERE tcd.id_c_cliente = 269 GROUP BY tcz.nombre_zona");
            $query = $this->db->connect()->prepare("
                SELECT acd.fecha_inserta, acd.fechagestion as fecha_gestion, tu.name_user as gestor, tcr.ruta, tcc.razon_social_cliente as cliente,
                tcd.razon_social_deudor as deudor, acd.numero_documento as documento, acd.importe_documento as importe, acd.saldo_documento as saldo,
                tca.clave_anomalia||'-'||tca.descripcion_anomalia as anomalia, acd.contacto, acd.puesto_contacto, acd.fecha_reprogramacion,
                acd.contrarecibo, acd.observacion, acd.tipo, adc.data_source as medio,
                (case
                when acd.insercion_manual=1 then 'manual'
                when acd.insercion_manual=0 then 'sistema'
                end) as insercion, tcz.nombre_zona as zona
                from app_chck_doctos acd
                inner join tb_c_cliente as tcc
                on acd.id_c_cliente = tcc.id_c_cliente
                inner join tb_c_deudor tcd
                on acd.id_c_deudor = tcd.id_c_deudor
                inner join tb_user tu
                on acd.id_user = tu.id_user
                left outer join tb_c_ruta tcr
                on acd.id_c_ruta = tcr.id_c_ruta
                left outer join tb_c_anomalia as tca
                on acd.id_c_anomalia = tca.id_c_anomalia
                left outer join app_data_source as adc
                on acd.id_data_source = adc.id_data_source
                left outer join tb_c_documento tcd2
                on acd.id_c_documento = tcd2.id_c_documento
                left outer join tb_c_zona tcz
                on tcd2.num_zona = tcz.id_c_zona
                where 1=1
                $whereClientes
                $whereFecha
                and tu.id_user != 1450
                and status_chckd = 2
                order by 1,2,5,6,7
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getColumnasGestoria($fechainicial,$fechafinal,$clientes)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
            $whereFecha = " and acd.fecha_inserta = '$fechafinal'";
            }else {
            $whereFecha = " and acd.fecha_inserta between '$fechainicial' and '$fechafinal'";
            }
            $whereClientes = " and acd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and acd.id_c_cliente in ($clientes)";
            }
            // $query = $this->con->query("SELECT tcz.nombre_zona AS Zona,SUM(tcd.saldo_documento) AS Saldo FROM tb_c_documento tcd INNER JOIN tb_c_zona tcz ON tcz.id_c_zona = tcd.num_zona WHERE tcd.id_c_cliente = 269 GROUP BY tcz.nombre_zona");
            $query = $this->db->connect()->prepare("
            SELECT acd.fecha_inserta, acd.fechagestion as fecha_gestion, tu.name_user as gestor, tcr.ruta, tcc.razon_social_cliente as cliente,
            tcd.razon_social_deudor as deudor, acd.numero_documento as documento, acd.importe_documento as importe, acd.saldo_documento as saldo,
            tca.clave_anomalia||'-'||tca.descripcion_anomalia as anomalia, acd.contacto, acd.puesto_contacto, acd.fecha_reprogramacion,
            acd.contrarecibo, acd.observacion, acd.tipo, adc.data_source as medio,
            (case
            when acd.insercion_manual=1 then 'manual'
            when acd.insercion_manual=0 then 'sistema'
            end) as insercion, tcz.nombre_zona as zona
            from app_chck_doctos acd
            inner join tb_c_cliente as tcc
            on acd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd
            on acd.id_c_deudor = tcd.id_c_deudor
            inner join tb_user tu
            on acd.id_user = tu.id_user
            left outer join tb_c_ruta tcr
            on acd.id_c_ruta = tcr.id_c_ruta
            left outer join tb_c_anomalia as tca
            on acd.id_c_anomalia = tca.id_c_anomalia
            left outer join app_data_source as adc
            on acd.id_data_source = adc.id_data_source
            left outer join tb_c_documento tcd2
            on acd.id_c_documento = tcd2.id_c_documento
            left outer join tb_c_zona tcz
            on tcd2.num_zona = tcz.id_c_zona
            where 1=1
            $whereClientes
            $whereFecha
            and tu.id_user != 1450
            and status_chckd = 2
            order by 1,2,5,6,7
            ");
            $query->execute();
            $columns = [];
            for ($i = 0; $i < $query->columnCount(); $i++) {
            $columns[] = $query->getColumnMeta($i)['name'];
            }
            return $columns;
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getFiltros($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes)/* Consulta actualizada */
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereStatus = " and tcd.status in (1, 5)";
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <0 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 0 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }else {
            $whereCapa = "and (case
                when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ('A (Current)', 'B (0-30)', 'C (31-60)', 'D (61-90)', 'Desconocida', 'E (91-120)', 'F (121-150)', 'G (151-180)', 'H (>180)')";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select tcc.razon_social_cliente as cliente, tcd4.cuenta, tcd.id_c_proyecto as proyecto,
                tcd.numero_documento as FACTURA, tcd2.razon_social_deudor as deudor,
                tcd.pedido, to_char(tcd.fecha_documento, 'dd-mm-yyyy') as fecha_documento, tcd.importe_documento,
                tcd.dias_credito, tcd.saldo_documento,
                tcd.fecha_anomalia,
                (case when tcr.ruta is null then 'Sin ruta'
                else tcr.ruta
                end) as ruta,
                fecha_recibida_aeesa as fecha_recibida, tcd.fecha_reprogramacion,
                (case when tcd.status=1 then 'ACTIVA'
                when tcd.status=2 then 'CONCILIADA'
                when tcd.status=3 then 'PAGADA'
                when tcd.status=4 then 'CANCELADA'
                when tcd.status=5 then 'GESTIONADA'
                end) as estatus, tcd.folio_fiscal, tcc2.contra_recibo, tcc2.importe as importe_contrarecibo, tcc2.fecha_emision,
                (case when tca.descripcion_anomalia is null then 'Sin anomalía'
                else tca.descripcion_anomalia
                end) as anomalia, tca.clave_anomalia, tcd.observacion_documento, tco.descripcion_observacion as UltimoComentario,-- acd.observacion,
                tctd.nombre_tipo_documento as tipo_documento, tcd.clave_sap,
                (case
                when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                else 'Desconocida'
                end) AS CapaAging
                from tb_c_documento tcd
                inner join tb_c_cliente tcc
                on tcd.id_c_cliente = tcc.id_c_cliente
                inner join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_ruta tcr
                on tcd.id_c_ruta = tcr.id_c_ruta
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_cuenta_deudor tcd4
                on tcd.id_c_deudor = tcd4.id_c_deudor
                left join tb_c_contrarecibo tcc2
                on tcd.id_c_contrarecibo = tcc2.id_c_contrarecibo
                left join tb_c_tipo_documento tctd
                on tcd.id_c_tipo_documento = tctd.id_c_tipo_documento
                left join tb_c_observacion tco
                on tcd.id_c_observacion = tco.id_c_observacion
                where 1=1
                $whereClientes
                and tcd.id_c_tipo_documento = 1
                $whereSapAnomalia
                and tcd.status in (1, 5)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getColumnas($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias, $clientes)/* COnsulta actualizada */
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            $whereSapAnomalia = "";
            if (!in_array("263", $clientes_asignados)) {
                $whereSapAnomalia = " and tcd.clave_sap in ('RV') and tca.clave_anomalia not in ('GDC')";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
            select tcc.razon_social_cliente as cliente, tcd4.cuenta, tcd.id_c_proyecto as proyecto,
            tcd.numero_documento as FACTURA, tcd2.razon_social_deudor as deudor,
            tcd.pedido, to_char(tcd.fecha_documento, 'dd-mm-yyyy') as fecha_documento, tcd.importe_documento,
            tcd.dias_credito, tcd.saldo_documento,
            tcd.fecha_anomalia,
            (case when tcr.ruta is null then 'Sin ruta'
            else tcr.ruta
            end) as ruta,
            fecha_recibida_aeesa as fecha_recibida, tcd.fecha_reprogramacion,
            (case when tcd.status=1 then 'ACTIVA'
            when tcd.status=2 then 'CONCILIADA'
            when tcd.status=3 then 'PAGADA'
            when tcd.status=4 then 'CANCELADA'
            when tcd.status=5 then 'GESTIONADA'
            end) as estatus, tcd.folio_fiscal, tcc2.contra_recibo, tcc2.importe as importe_contrarecibo, tcc2.fecha_emision,
            (case when tca.descripcion_anomalia is null then 'Sin anomalía'
            else tca.descripcion_anomalia
            end) as anomalia, tca.clave_anomalia, tcd.observacion_documento, tco.descripcion_observacion as UltimoComentario,-- acd.observacion,
            tctd.nombre_tipo_documento as tipo_documento, tcd.clave_sap,
            (case
            when current_date - tcd.fecha_documento - tcd.dias_credito <1 then 'A (Current)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 1 and 30 then 'B (0-30)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 31 and 60 then 'C (31-60)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 61 and 90 then 'D (61-90)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 91 and 120 then 'E (91-120)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 121 and 150 then 'F (121-150)'
            when current_date - tcd.fecha_documento - tcd.dias_credito between 151 and 180 then 'G (151-180)'
            when current_date - tcd.fecha_documento - tcd.dias_credito > 180 then 'H (>180)'
            else 'Desconocida'
            end) AS CapaAging
            from tb_c_documento tcd
            inner join tb_c_cliente tcc
            on tcd.id_c_cliente = tcc.id_c_cliente
            inner join tb_c_deudor tcd2
            on tcd2.id_c_deudor = tcd.id_c_deudor
            left outer join tb_c_tipo_cliente tctc
            on tcd.id_division = tctc.id_c_tipo_cliente
            left join tb_c_ruta tcr
            on tcd.id_c_ruta = tcr.id_c_ruta
            left join tb_c_anomalia tca
            on tcd.id_c_anomalia = tca.id_c_anomalia
            left join tb_cuenta_deudor tcd4
            on tcd.id_c_deudor = tcd4.id_c_deudor
            left join tb_c_contrarecibo tcc2
            on tcd.id_c_contrarecibo = tcc2.id_c_contrarecibo
            left join tb_c_tipo_documento tctd
            on tcd.id_c_tipo_documento = tctd.id_c_tipo_documento
            left join tb_c_observacion tco
            on tcd.id_c_observacion = tco.id_c_observacion
            where 1=1
            $whereClientes
            and tcd.id_c_tipo_documento = 1
            $whereSapAnomalia
            and tcd.status in (1, 5)
            $whereDeudores
            $whereTipoAnomlia
            $whereAnomlia
            $whereCapa
            and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            $columns = [];
            for ($i = 0; $i < $query->columnCount(); $i++) {
            $columns[] = $query->getColumnMeta($i)['name'];
            }
            return $columns;
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getFiltrosCobranza($fechainicial, $fechafinal, $deudores, $capas, $clientes)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
                $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
                $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                    end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select tcd.id_c_documento ,tcd3.cuenta as CUENTA, tcd2.razon_social_deudor DEUDOR ,tcd.numero_documento as FACTURA,
                tcd.pedido as DOCUMENTO_SAP, tctc.cheque as NO_LINEA_BANCARIA,tcd.fecha_documento as FECHA_FACTURA, tcd.importe_documento as FACTURA_IMPORTE,
                tcd.saldo_documento as FACTURA_SALDO, tctc.cheque as CHEQUE, tctc.importe as IMPORTE_CHEQUE,
                tpd.fecha_aplicacion_pago as FECHA_APLICACION, tpd.fecha_deposito_pago as FECHA_DEPOSITO, tcp.monto_pago as MONTO_APLICADO, tctp.nombre_tipo_pago as TIPO_PAGO,
                (case
                when tcd.dias_credito is null then 0
                else tcd.dias_credito
                end) as DIAS_CREDITO,
                (tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end)) as DIAS_ANTIGUEDAD,
                (case
                when tpd.fecha_aplicacion_pago  - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 120 then 'F MAS DE 120 DIAS'
                end) as CAPA_AGING,
                tcd4.numero_documento as NOTA_DE_CREDITO_APLICADA, tcd4.importe_documento as IMPORTE_NC
                from tb_pago_documento tpd
                left join tb_c_documento tcd
                on tcd.id_c_documento = tpd.id_c_documento
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_cuenta_deudor tcd3
                on tcd3.id_c_deudor = tcd2.id_c_deudor
                left join tb_c_pago tcp
                on tpd.id_c_pago = tcp.id_c_pago
                left join tb_c_transferencia_cheque tctc
                on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
                left join tb_c_tipo_pago tctp
                on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
                left join tb_c_anomalia tca
                on tca.id_c_anomalia = tcd.id_c_anomalia
                left join tb_documento_nota tdn
                on tdn.id_c_documento = tcd.id_c_documento
                left join tb_c_documento tcd4
                on tcd4.id_c_documento = tdn.id_c_nota
                where $whereFecha
                $whereDeudores
                $whereCapa
                and tcd.status in (1,3)
                $whereClientes
                and tcd.id_c_documento is not null
                and tpd.fecha_aplicacion_pago is not null
                and tcd.fecha_documento is not null
                ");
                $query->execute();
                // [$fecha_inicial, $fecha_final]
                return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getColumnasCobranza($fechainicial, $fechafinal, $deudores, $capas, $clientes)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
                $whereFecha = " tpd.fecha_aplicacion_pago = '$fechafinal'";
            }else {
                $whereFecha = " tpd.fecha_aplicacion_pago between '$fechainicial' and '$fechafinal'";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null) {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null) {
                $whereCapa = "and (case
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) >120 then 'F MAS DE 120 DIAS'
                    end) in ($capas)";
            }
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            }
            $query = $this->db->connect()->prepare("
                select tcd.id_c_documento ,tcd3.cuenta as CUENTA, tcd2.razon_social_deudor DEUDOR ,tcd.numero_documento as FACTURA,
                tcd.pedido as DOCUMENTO_SAP, tctc.cheque as NO_LINEA_BANCARIA,tcd.fecha_documento as FECHA_FACTURA, tcd.importe_documento as FACTURA_IMPORTE,
                tcd.saldo_documento as FACTURA_SALDO, tctc.cheque as CHEQUE, tctc.importe as IMPORTE_CHEQUE,
                tpd.fecha_aplicacion_pago as FECHA_APLICACION, tpd.fecha_deposito_pago as FECHA_DEPOSITO, tcp.monto_pago as MONTO_APLICADO, tctp.nombre_tipo_pago as TIPO_PAGO,
                (case
                when tcd.dias_credito is null then 0
                else tcd.dias_credito
                end) as DIAS_CREDITO,
                (tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end)) as DIAS_ANTIGUEDAD,
                (case
                when tpd.fecha_aplicacion_pago  - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tpd.fecha_aplicacion_pago - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 120 then 'F MAS DE 120 DIAS'
                end) as CAPA_AGING,
                tcd4.numero_documento as NOTA_DE_CREDITO_APLICADA, tcd4.importe_documento as IMPORTE_NC
                from tb_pago_documento tpd
                left join tb_c_documento tcd
                on tcd.id_c_documento = tpd.id_c_documento
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_cuenta_deudor tcd3
                on tcd3.id_c_deudor = tcd2.id_c_deudor
                left join tb_c_pago tcp
                on tpd.id_c_pago = tcp.id_c_pago
                left join tb_c_transferencia_cheque tctc
                on tpd.id_c_transferencia_cheque = tctc.id_c_transferencia_cheque
                left join tb_c_tipo_pago tctp
                on tctp.id_c_tipo_pago = tctc.id_c_tipo_pago
                left join tb_c_anomalia tca
                on tca.id_c_anomalia = tcd.id_c_anomalia
                left join tb_documento_nota tdn
                on tdn.id_c_documento = tcd.id_c_documento
                left join tb_c_documento tcd4
                on tcd4.id_c_documento = tdn.id_c_nota
                where $whereFecha
                $whereDeudores
                $whereCapa
                and tcd.status in (1,3)
                $whereClientes
                and tcd.id_c_documento is not null
                and tpd.fecha_aplicacion_pago is not null
                and tcd.fecha_documento is not null
            ");
            $query->execute();
            // [$fecha_inicial, $fecha_final]
            $columns = [];
            for ($i = 0; $i < $query->columnCount(); $i++) {
                $columns[] = $query->getColumnMeta($i)['name'];
            }
            return $columns;
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Lista de bloqueos */
    public function getFiltrosBloqueos($fecha_buscar)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select
                tcz.nombre_zona as region,
                tcd.id_c_deudor as cliente,
                tcd2.razon_social_deudor as nombreCliente,
                count (*) as facturasvencidas,
                sum(current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end)) as saldovencido,
                tca.fecha_alta as fechaSolicitudBloqueo,
                tcd.contrato_documento as motivoBloqueo
                from tb_c_documento tcd
                left join tb_c_zona tcz
                on tcz.id_c_zona = tcd.num_zona
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_c_alta tca
                on tca.id_c_documento = tcd.id_c_documento
                where 1=1
                and tcd.id_c_cliente in (269)
                and tca.fecha_alta = ?
                group by tcz.nombre_zona,tcd.id_c_deudor,tcd2.razon_social_deudor,tca.fecha_alta,tcd.contrato_documento
            ");
            $query->execute([$fecha_buscar]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getColumnasBloqueos($fecha_buscar)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select
                tcz.nombre_zona as region,
                tcd.id_c_deudor as cliente,
                tcd2.razon_social_deudor as nombreCliente,
                count (*) as facturasvencidas,
                sum(current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end)) as saldovencido,
                tca.fecha_alta as fechaSolicitudBloqueo,
                tcd.contrato_documento as motivoBloqueo
                from tb_c_documento tcd
                left join tb_c_zona tcz
                on tcz.id_c_zona = tcd.num_zona
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_c_alta tca
                on tca.id_c_documento = tcd.id_c_documento
                where 1=1
                and tcd.id_c_cliente in (269)
                and tca.fecha_alta = ?
                group by tcz.nombre_zona,tcd.id_c_deudor,tcd2.razon_social_deudor,tca.fecha_alta,tcd.contrato_documento
            ");
            $query->execute([$fecha_buscar]);
            $columns = [];
            for ($i = 0; $i < $query->columnCount(); $i++) {
            $columns[] = $query->getColumnMeta($i)['name'];
            }
            return $columns;
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Método para exportar a excel */
}
