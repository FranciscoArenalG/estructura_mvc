<?php
class DashboardtModel extends ModelBase
{
    function __construct(){
        parent::__construct();
    }
    /* Inicio Métodos de filtros */
    public function getDeudores()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(tcd2.razon_social_deudor), tcd2.id_c_deudor
                from tb_c_documento tcd
                inner join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
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
    public function getCapaAgin()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(
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
    /* Fin Métodos de filtros */

    /* Inicio Métodos de targets sección Cartera */
    public function getMontoPorCobrar($fechainicial,$fechafinal,$deudores,$capas, $tipos_anomalia, $anomalias)
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select COALESCE(sum(tcd.saldo_documento), 0, sum(tcd.saldo_documento)) as monto_por_cobrar
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                where 1=1
                and tcd.status in (1,5)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc.descripcion_cliente not in ('Imss')
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: " . $e->getMessage();
            return false;
        }
    }
    public function getFacturasPorCobrar($fechainicial,$fechafinal,$deudores,$capas, $tipos_anomalia, $anomalias)
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select count(*) as facturas_por_cobrar
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_documento tctd
                on tcd.id_c_tipo_documento = tctd.id_c_tipo_documento
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc.descripcion_cliente not in ('Imss')
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ". $e->getMessage();
            return false;
        }
    }
    public function getTotalDeudores($fechainicial,$fechafinal,$deudores,$capas, $tipos_anomalia, $anomalias)
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select count( distinct tcd.id_c_deudor) as total_deudores
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc.descripcion_cliente not in ('Imss')
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos de targets sección cartera */

    /* Inicio Métodos de Sección Cartera */
    public function getEstatusVencimiento($fechainicial,$fechafinal,$deudores,$capas, $tipos_anomalia, $anomalias)/* Consulta actualizada */
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $query = $this->db->connect()->prepare("
                select * from
                (select (case when sum(tcd.saldo_documento) is null then 0 else sum(tcd.saldo_documento) end)  as SaldoSinVencer
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                --and tctc.descripcion_cliente not in ('Imss')
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                and current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) < 1
                and tcd.fecha_documento between ? and ?) as tbl1,
                (select (case when sum(tcd.saldo_documento) is null then 0 else sum(tcd.saldo_documento) end)  as SaldoVencido
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                --and tctc.descripcion_cliente not in ('Imss')
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                and current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 0
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
    public function getSaldoCapaAging($fechainicial,$fechafinal,$deudores,$capas, $tipos_anomalia, $anomalias)/* Consulta actualizada */
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != null && $anomalias != 'null') {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $query = $this->db->connect()->prepare("
                SELECT (CASE WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 THEN 'A (Current)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 1 AND 30 THEN 'B (0-30)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 31 AND 60 THEN 'C (31-60)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 61 AND 90 THEN 'D (61-90)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 91 AND 120 THEN 'E (91-120)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 121 AND 150 THEN 'F (121-150)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) BETWEEN 151 AND 180 THEN 'G (151-180)'
                WHEN CURRENT_DATE - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 180 THEN 'H (>180)'
                ELSE 'Desconocida'
                END) AS AsignStatus, sum(tcd.saldo_documento) as SaldoDocumento
                FROM tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
                and tcd.status in (1, 5)
                $whereDeudores
                $whereTipoAnomlia
                $whereCapa
                $whereAnomlia
                --and tctc.descripcion_cliente not in ('Imss')
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
    public function getDataGraficaSaldoPorEstatus($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias)/* Consulta actualizada */
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
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
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc.descripcion_cliente not in ('Imss')
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
    public function getDSO()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select (cartera_activa/cartera_total)*dias as DSO
                from
                (
                select sum(saldo_documento) cartera_activa
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                where 1=1
                and tcd.status in (1,5)
                and tcd.id_c_tipo_documento = 1
                and tcd.id_c_cliente in ($cliente)
                --and tctc.descripcion_cliente not in ('Imss')
                and date_trunc('month',fecha_recibida_aeesa) = DATE_TRUNC('month',now()- INTERVAL '1 months')
                ) as a,
                (
                select sum(importe_documento) cartera_total
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                where 1=1
                and tcd.status in (1,2,3,4,5)
                and tcd.id_c_tipo_documento = 1
                and tcd.id_c_cliente in ($cliente)
                --and tctc.descripcion_cliente not in ('Imss')
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
    public function getDataTablaTop20DeudoresSaldoVencido($fechainicial,$fechafinal,$deudores,$capas,$tipos_anomalia,$anomalias)/* Consulta actualizada */
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
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select tcd2.id_c_deudor,tcd2.razon_social_deudor, sum(tcd.saldo_documento) as saldo, count(*) as facturas,
                ((sum(tcd.saldo_documento) / (select COALESCE(sum(tcd.saldo_documento), 0, sum(tcd.saldo_documento)) as monto_por_cobrar
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.status in (1, 2, 3, 4, 5)
                and tcd.id_c_cliente in ($cliente)
                --and tctc.descripcion_cliente not in ('Imss')
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                and current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end)>0
                )) * 100) as porcentaje
                from tb_c_documento tcd
                left outer join tb_c_deudor tcd2
                on tcd.id_c_deudor = tcd2.id_c_deudor
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_cliente in ($cliente)
                --and tctc.descripcion_cliente not in ('Imss')
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                and current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end)>0
                --and (current_date - tcd.fecha_documento - tcd.dias_credito) > 0
                group by tcd2.id_c_deudor, tcd2.razon_social_deudor
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
    /* Fin Métodos de Sección Cartera */

    /* Inicio Métodos de Sección Cobranza */
    public function getMontoCobrado($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaAplicacion') {
                $whereFechas = " and tctc2.fecha_aplicacion between ? and ?";
               }else{
                $whereFechas = " and tctc2.fecha_deposito between ? and ?";
               }
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select count(*) as facturas_cobradas, sum(tcp.monto_pago) as monto_cobrado
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
                left join tb_pago_documento tpd
                on tcd.id_c_documento = tpd.id_c_documento
                left join tb_c_pago tcp
                on tpd.id_c_pago = tcp.id_c_pago
                left join tb_c_transferencia_cheque tctc2
                on tpd.id_c_transferencia_cheque = tctc2.id_c_transferencia_cheque
                where 1=1
                and tcd.status in (3)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc2.fecha_aplicacion between ? and ?
                $whereFechas
                and tcp.monto_pago is not null
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getFacturacionCobradas($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaAplicacion') {
                $whereFechas = " and tctc2.fecha_aplicacion between ? and ?";
               }else{
                $whereFechas = " and tctc2.fecha_deposito between ? and ?";
               }
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select count(*) as facturas_cobradas, sum(tcp.monto_pago) as monto_cobrado
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
                left join tb_pago_documento tpd
                on tcd.id_c_documento = tpd.id_c_documento
                left join tb_c_pago tcp
                on tpd.id_c_pago = tcp.id_c_pago
                left join tb_c_transferencia_cheque tctc2
                on tpd.id_c_transferencia_cheque = tctc2.id_c_transferencia_cheque
                where 1=1
                and tcd.status in (3)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc2.fecha_aplicacion between ? and ?
                $whereFechas
                and tcp.monto_pago is not null
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaMontoRecuperadoDeudor($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaAplicacion') {
                $whereFechas = " and tctc2.fecha_aplicacion between ? and ?";
               }else{
                $whereFechas = " and tctc2.fecha_deposito between ? and ?";
               }
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select deudor, pago
                from
                (
                    select tcd2.razon_social_deudor as deudor, sum(tcp.monto_pago) as pago
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
                    left join tb_pago_documento tpd
                    on tcd.id_c_documento = tpd.id_c_documento
                    left join tb_c_pago tcp
                    on tpd.id_c_pago = tcp.id_c_pago
                    left join tb_c_transferencia_cheque tctc2
                    on tpd.id_c_transferencia_cheque = tctc2.id_c_transferencia_cheque
                    where 1=1
                    and tcd.status in (3)
                    and tcd.id_c_cliente in ($cliente)
                    $whereDeudores
                    $whereCapa
                    and tcd.id_c_tipo_documento = 1
                    --and tctc2.fecha_aplicacion between ? and ?
                    $whereFechas
                    group by tcd2.razon_social_deudor
                    order by 2 ASC
                ) as foo
                where pago > 0
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaPagoCapaAging($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $fecha = "tctc2.fecha_aplicacion";
            if ($fecha_aplicar != 'fechaAplicacion') {
                $fecha = "tctc2.fecha_deposito";
            }
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaAplicacion') {
                $whereFechas = " and tctc2.fecha_aplicacion between ? and ?";
               }else{
                $whereFechas = " and tctc2.fecha_deposito between ? and ?";
               }
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select cliente, capa, sum(monto)
                from (
                select tcc.razon_social_cliente as cliente,
                (case
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when $fecha - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) as capa, tcp.monto_pago as monto
                --count(*) as facturas_cobradas, sum(tcp.monto_pago) as monto_cobrado
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
                left join tb_pago_documento tpd
                on tcd.id_c_documento = tpd.id_c_documento
                left join tb_c_pago tcp
                on tpd.id_c_pago = tcp.id_c_pago
                left join tb_c_transferencia_cheque tctc2
                on tpd.id_c_transferencia_cheque = tctc2.id_c_transferencia_cheque
                where 1=1
                and tcd.status in (3)
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                --and tctc2.fecha_aplicacion between ? and ?
                $whereFechas
                and tcp.monto_pago is not null
                ) as foo
                group by cliente, capa
                order by 1, 2
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaRecuentoFactura($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            $whereFecha = '';
            $fecha = "tctc.fecha_aplicacion";
            if ($fecha_aplicar != 'fechaAplicacion') {
                $fecha = "tctc.fecha_deposito";
            }
            if ($fechainicial == $fechafinal) {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                     $whereFechas = " tctc.fecha_aplicacion  = '$fechafinal'";
                    }else{
                     $whereFechas = " tctc.fecha_deposito  = '$fechafinal'";
                    }
                 }
              $whereFecha = $whereFechas;
            }else {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                     $whereFechas = " tctc.fecha_aplicacion between '$fechainicial' and '$fechafinal'";
                    }else{
                     $whereFechas = " tctc.fecha_deposito between '$fechainicial' and '$fechafinal'";
                    }
                 }
              $whereFecha = $whereFechas;
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
            $query = $this->db->connect()->prepare("
              SELECT
              (CASE WHEN date_part('month'::TEXT, $fecha) = 1 THEN 'enero'
              WHEN date_part('month'::TEXT, $fecha) = 2 THEN 'febrero'
              WHEN date_part('month'::TEXT, $fecha) = 3 THEN 'marzo'
              WHEN date_part('month'::TEXT, $fecha) = 4 THEN 'abril'
              WHEN date_part('month'::TEXT, $fecha) = 5 THEN 'mayo'
              WHEN date_part('month'::TEXT, $fecha) = 6 THEN 'junio'
              WHEN date_part('month'::TEXT, $fecha) = 7 THEN 'julio'
              WHEN date_part('month'::TEXT, $fecha) = 8 THEN 'agosto'
              WHEN date_part('month'::TEXT, $fecha) = 9 THEN 'septiembre'
              WHEN date_part('month'::TEXT, $fecha) = 10 THEN 'octubre'
              WHEN date_part('month'::TEXT, $fecha) = 11 THEN 'noviembre'
              WHEN date_part('month'::TEXT, $fecha) = 12 THEN 'diciembre'
              ELSE 'Sin mes'
              END) AS mes,
              date_part('month'::TEXT, $fecha) AS numMes,
              date_part('year'::TEXT, $fecha) AS Anio, sum(tcp.monto_pago) as monto_cobrado,
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
              and tpd.id_c_cliente = $cliente
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
    /* Fin Métodos de Sección Cobranza */

    /* Inicio Métodos de Sección Forecast */
    public function getDataGraficaForecast($fechainicial,$fechafinal,$deudores)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $query = $this->db->connect()->prepare("
                select
                (CASE WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, tcc.fecha_vencimiento) = 12 THEN 'diciembre'
                ELSE 'Sin mes'
                END) AS mes,date_part('week'::TEXT, tcc.fecha_vencimiento) AS numSemana,
                date_part('year'::TEXT, tcc.fecha_vencimiento) AS Anio,sum(tcd.importe_documento)
                from tb_c_documento tcd
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_c_contrarecibo tcc
                on tcc.id_c_contrarecibo = tcd.id_c_contrarecibo
                where tcd.id_c_cliente in ($cliente)
                --and tcd.status in (1,5)
                and tcc.fecha_vencimiento between ? and ?
                $whereDeudores
                group by mes, anio, numSemana
                order by numSemana,mes desc,anio
            ");
            $query->execute([$fechainicial,$fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getTablaForecast($fechainicial,$fechafinal,$deudores)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $query = $this->db->connect()->prepare("
                select tcc.fecha_vencimiento as fecha,tcd2.razon_social_deudor as deudor, sum(tcd.importe_documento) as saldo, count(tcd.id_c_documento) as factura
                from tb_c_documento tcd
                left join tb_c_deudor tcd2
                on tcd2.id_c_deudor = tcd.id_c_deudor
                left join tb_c_contrarecibo tcc
                on tcc.id_c_contrarecibo = tcd.id_c_contrarecibo
                where tcd.id_c_cliente in ($cliente)
                $whereDeudores
                and tcc.fecha_vencimiento between ? and ?
                group by fecha, deudor
            ");
            $query->execute([$fechainicial,$fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos de Sección Forecast */

    /* Inicio Métodos para exportar excel */
    public function getFiltros($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias)/* Consulta actualizada */
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
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
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
    public function getColumnas($fechainicial, $fechafinal, $deudores, $capas, $tipos_anomalia, $anomalias)/* Consulta actualizada */
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
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
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
    public function getFiltrosCobranza($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                        $whereFechas = " tctc.fecha_aplicacion = '$fechafinal'";
                    }else{
                        $whereFechas = " tctc.fecha_deposito = '$fechafinal'";
                    }
                }
                $whereFecha = $whereFechas;
            }else {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                        $whereFechas = " tctc.fecha_aplicacion between '$fechainicial' and '$fechafinal'";
                    }else{
                        $whereFechas = " tctc.fecha_deposito between '$fechainicial' and '$fechafinal'";
                    }
                }
                $whereFecha = $whereFechas;
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
            $query = $this->db->connect()->prepare("
                select tcd.id_c_documento ,tcd3.cuenta as CUENTA, tcd2.razon_social_deudor DEUDOR ,tcd.numero_documento as FACTURA,
                tcd.pedido as DOCUMENTO_SAP, tctc.cheque as NO_LINEA_BANCARIA,tcd.fecha_documento as FECHA_FACTURA, tcd.importe_documento as FACTURA_IMPORTE,
                tcd.saldo_documento as FACTURA_SALDO, tctc.cheque as CHEQUE, tctc.importe as IMPORTE_CHEQUE,
                tctc.fecha_aplicacion as FECHA_APLICACION, tctc.fecha_deposito as FECHA_DEPOSITO, tcp.monto_pago as MONTO_APLICADO, tctp.nombre_tipo_pago as TIPO_PAGO,
                (case
                when tcd.dias_credito is null then 0
                else tcd.dias_credito
                end) as DIAS_CREDITO,
                (tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end)) as DIAS_ANTIGUEDAD,
                (case
                when tctc.fecha_aplicacion  - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 120 then 'F MAS DE 120 DIAS'
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
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereCapa
                and tcd.status in (1,3)
                and tcd.id_c_documento is not null
                and tctc.fecha_aplicacion is not null
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
    public function getColumnasCobranza($fechainicial,$fechafinal,$deudores,$capas,$fecha_aplicar)
    {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 300);
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            $whereFecha = '';
            if ($fechainicial == $fechafinal) {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                        $whereFechas = " tctc.fecha_aplicacion = '$fechafinal'";
                    }else{
                        $whereFechas = " tctc.fecha_deposito = '$fechafinal'";
                    }
                }
                $whereFecha = $whereFechas;
            }else {
                if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
                    if ($fecha_aplicar == 'fechaAplicacion') {
                        $whereFechas = " tctc.fecha_aplicacion between '$fechainicial' and '$fechafinal'";
                    }else{
                        $whereFechas = " tctc.fecha_deposito between '$fechainicial' and '$fechafinal'";
                    }
                }
                $whereFecha = $whereFechas;
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
            $query = $this->db->connect()->prepare("
                select tcd.id_c_documento ,tcd3.cuenta as CUENTA, tcd2.razon_social_deudor DEUDOR ,tcd.numero_documento as FACTURA,
                tcd.pedido as DOCUMENTO_SAP, tctc.cheque as NO_LINEA_BANCARIA,tcd.fecha_documento as FECHA_FACTURA, tcd.importe_documento as FACTURA_IMPORTE,
                tcd.saldo_documento as FACTURA_SALDO, tctc.cheque as CHEQUE, tctc.importe as IMPORTE_CHEQUE,
                tctc.fecha_aplicacion as FECHA_APLICACION, tctc.fecha_deposito as FECHA_DEPOSITO, tcp.monto_pago as MONTO_APLICADO, tctp.nombre_tipo_pago as TIPO_PAGO,
                (case
                when tcd.dias_credito is null then 0
                else tcd.dias_credito
                end) as DIAS_CREDITO,
                (tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end)) as DIAS_ANTIGUEDAD,
                (case
                when tctc.fecha_aplicacion  - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) <1 then 'A CURRENT'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 1 and 30 then 'B 1 A 30 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 31 and 60 then 'C 31 A 60 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 61 and 90 then 'D 61 A 91 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) between 91 and 120 then 'E 91 A 120 DIAS'
                when tctc.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 0 else tcd.dias_credito end) > 120 then 'F MAS DE 120 DIAS'
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
                and tcd.id_c_cliente in ($cliente)
                $whereDeudores
                $whereCapa
                and tcd.status in (1,3)
                and tcd.id_c_documento is not null
                and tctc.fecha_aplicacion is not null
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
    /* Fin Métodos para exportar excel */
}

?>