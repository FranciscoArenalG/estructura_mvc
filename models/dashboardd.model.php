<?php
class DashboarddModel extends ModelBase
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
    public function getSelectDivisiones()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct(tctc.descripcion_cliente)
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                where 1=1
                and tcd.status in (1, 2, 3, 4, 5)
                and tcd.id_c_cliente in ($cliente)
                and tcd.id_c_tipo_documento = 1
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilación: ".$e->getMessage();
        }
    }
    public function getResponsables()/* Sección Anomalias */
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select distinct((case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end)) as responsable
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                where 1=1
                and tcd.status in (1, 5)
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
    /* Filtro de divisiones por Zona */
    public function getSelectDivisionesZonas()
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
            select nombre_zona from tb_c_documento tcd
            left join tb_c_zona tcz
            on tcz.id_c_zona = tcd.num_zona
            where tcd.id_c_cliente in ($cliente)
            group by nombre_zona
            ");
            $query->execute();
            /* select nombre_zona  from tb_c_zona tcz
            where tcz.id_c_cliente in ($cliente)
            group by nombre_zona */
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilación: ".$e->getMessage();
        }
    }
    /* Fin Métodos de filtros */

    /* Inicio Métodos de targets Cartera, Antigüedad de saldos y Anomalía */
    public function getMontoPorCobrar($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                    $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }else{
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
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
            
            $query = $this->db->connect()->prepare("
                select COALESCE(sum(tcd.saldo_documento), 0, sum(tcd.saldo_documento)) as monto_por_cobrar
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd2
                on tcd.id_c_deudor=tcd2.id_c_deudor
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: " . $e->getMessage();
            return false;
        }
    }
    public function getFacturasPorCobrar($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                    $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }else{
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
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
            $query = $this->db->connect()->prepare("
                select count(*) as facturas_por_cobrar
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd2
                on tcd.id_c_deudor=tcd2.id_c_deudor 
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_documento tctd
                on tcd.id_c_tipo_documento = tctd.id_c_tipo_documento
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ". $e->getMessage();
            return false;
        }
    }
    public function getTotalDeudores($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
        $whereClientes = "";
        if ($clientes != "" && $clientes != null && $clientes != 'null') {
            $whereClientes = " and tcd.id_c_cliente in ($clientes)";
        } else {
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
        }
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
        $whereDivision = "";
        $left_join = "";
        $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
        if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }else{
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            }
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
        $query = $this->db->connect()->prepare("
            select count( distinct tcd.id_c_deudor) as total_deudores
            from tb_c_documento tcd
            left join tb_cuenta_deudor tcd2
            on tcd.id_c_deudor=tcd2.id_c_deudor 
            left outer join tb_c_tipo_cliente tctc
            on tcd.id_division = tctc.id_c_tipo_cliente
            left join tb_c_anomalia tca
            on tcd.id_c_anomalia = tca.id_c_anomalia
            $left_join
            where 1=1
            and tcd.status in (1, 5)
            $whereClientes
            $whereDeudores
            $whereTipoAnomlia
            $whereAnomlia
            $whereCapa
            $whereDivision
            and tcd.id_c_tipo_documento = 1
            and tcd.fecha_documento between ? and ?
        ");
        $query->execute([$fechainicial, $fechafinal]);
        return $query->fetch();
    }
    public function getDivisiones($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
        $whereClientes = "";
        if ($clientes != "" && $clientes != null && $clientes != 'null') {
            $whereClientes = " and tcd.id_c_cliente in ($clientes)";
        } else {
            $whereClientes = " and tcd.id_c_cliente in ($cliente)";
        }
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
        $whereDivision = "";
        $left_join = "";
        $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
        if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }else{
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            }
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
        }
        $query = $this->db->connect()->prepare("
            select count( distinct(tctc.descripcion_cliente) ) as total_division
            from tb_c_documento tcd
            left join tb_cuenta_deudor tcd2
            on tcd.id_c_deudor=tcd2.id_c_deudor 
            left outer join tb_c_tipo_cliente tctc
            on tcd.id_division = tctc.id_c_tipo_cliente
            left join tb_c_anomalia tca
            on tcd.id_c_anomalia = tca.id_c_anomalia
            $left_join
            where 1=1
            and tcd.status in (1, 5)
            $whereClientes
            $whereDeudores
            $whereTipoAnomlia
            $whereAnomlia
            $whereCapa
            $whereDivision
            and tcd.id_c_tipo_documento = 1
            and tcd.fecha_documento between ? and ?
        ");
        $query->execute([$fechainicial, $fechafinal]);
        return $query->fetch();
    }
    /* Fin Métodos de targets Cartera, Antigüedad de saldos y Anomalía */

    /* Inicio Métodos Sección Cartera */
    public function getDataGraficaSaldoPorDivision($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
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
                select distinct(case when $campo is null then 'Sin division' else $campo end) as division, sum(tcd.saldo_documento) as saldo
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd2
                on tcd.id_c_deudor=tcd2.id_c_deudor 
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                group by $campo
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
        }
    }
    public function getDataGraficaSaldoPorEstatus($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
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
                select tcta.descripcion_tipo_anomalia as anomalia, sum(tcd.saldo_documento) as saldo
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd2
                on tcd.id_c_deudor=tcd2.id_c_deudor 
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                group by tcta.descripcion_tipo_anomalia
                order by 1
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataTablaTop20DeudoresSaldoVencido($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
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
                select tcd2.razon_social_deudor, sum(saldo_documento) as saldo, count(*) as facturas,
                    ((sum(saldo_documento) / (select COALESCE(sum(saldo_documento), 0, sum(saldo_documento)) as monto_por_cobrar
                        from tb_c_documento tcd
                        left join tb_cuenta_deudor tcd2
                        on tcd.id_c_deudor=tcd2.id_c_deudor 
                        left join tb_c_anomalia tca
                        on tcd.id_c_anomalia = tca.id_c_anomalia
                        left outer join tb_c_tipo_cliente tctc
                        on tcd.id_division = tctc.id_c_tipo_cliente
                        $left_join
                        where 1=1
                        and tcd.status in (1,2,3,4,5)
                        $whereClientes
                        $whereDeudores
                        $whereTipoAnomlia
                        $whereAnomlia
                        $whereCapa
                        $whereDivision
                        and tcd.id_c_tipo_documento = 1
                        and tcd.fecha_documento between ? and ?
                    )) * 100) as porcentaje
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd3
                on tcd.id_c_deudor=tcd3.id_c_deudor 
                left join tb_c_deudor tcd2
                on tcd.id_c_deudor = tcd2.id_c_deudor
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                --and (current_date - tcd.fecha_documento - tcd.dias_credito) > 0
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
    public function getDSO($clientes,$deudores,$capas, $tipos_anomalia, $anomalias)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $query = $this->db->connect()->prepare("
                select (cartera_activa/cartera_total)*dias as DSO
                from
                (
                select sum(saldo_documento) cartera_activa
                from tb_c_documento tcd
                where 1=1
                and tcd.status in (1)
                and tcd.id_c_tipo_documento = 1
                and tcd.id_c_cliente in ($cliente)
                and date_trunc('month',fecha_recibida_aeesa) = DATE_TRUNC('month',now()- INTERVAL '1 months')
                ) as a,
                (
                select sum(saldo_documento) cartera_total
                from tb_c_documento tcd
                where 1=1
                and tcd.status in (1,2,3,4,5)
                and tcd.id_c_tipo_documento = 1
                and tcd.id_c_cliente in ($cliente)
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

    /* Inicio Métodos Sección Antigüedad de saldos */
    public function getDataGraficaFechaDocumento($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " where foo.division in ($divisiones)";
            } */
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
                select foo.capa, foo.division, sum(foo.saldo_documento)
                from (
                    select
                    (case
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end)
                    as capa, (case when $campo is null then 'Sin division' else $campo end) as division, saldo_documento
                    from tb_c_documento tcd
                    left outer join tb_c_tipo_cliente tctc
                    on tcd.id_division = tctc.id_c_tipo_cliente
                    left join tb_c_anomalia tca
                    on tcd.id_c_anomalia = tca.id_c_anomalia
                    $left_join
                    where 1=1
                    and tcd.status in (1, 5)
                    $whereClientes
                    $whereDeudores
                    $whereTipoAnomlia
                    $whereAnomlia
                    $whereCapa
                    and tcd.id_c_tipo_documento = 1
                    and tcd.fecha_documento between ? and ?
                    )
                as foo
                $whereDivision
                group by foo.capa, foo.division
                order by 1 DESC, 2 ASC
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
        }
    }
    public function getDataGraficaFechaRecibida($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " where foo.division in ($divisiones)";
            } */
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                select foo.capa, foo.division, sum(foo.saldo_documento)
                from (
                select
                (case
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_recibida_aeesa - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                end) as capa, (case when $campo is null then 'Sin division' else $campo end) as division, saldo_documento
                from tb_c_documento tcd
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                ) as foo
                $whereDivision
                group by foo.capa, foo.division
                order by 1 DESC, 2 ASC
            ");

            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaFechaContrarecibo($fechainicial,$fechafinal,$clientes,$deudores,$capas, $tipos_anomalia, $anomalias, $divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " where foo.division in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " where foo.division in ($divisiones)";
            } */
            $whereCapa = "";
            if ($capas != "" && $capas != null && $capas != 'null') {
                $whereCapa = "and (case
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) in ($capas)";
            }
            $query = $this->db->connect()->prepare("
                    select foo.capa, foo.division, sum(foo.saldo_documento)
                    from (
                    select
                    (case
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                    when current_date - tcc2.fecha_emision - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                    else 'Desconocida'
                    end) as capa, (case when $campo is null then 'Sin division' else $campo end) as division, saldo_documento
                    from tb_c_documento tcd
                    left outer join tb_c_tipo_cliente tctc
                    on tcd.id_division = tctc.id_c_tipo_cliente
                    left join tb_c_contrarecibo tcc2
                    on tcd.id_c_contrarecibo = tcc2.id_c_contrarecibo
                    left join tb_c_anomalia tca
                    on tcd.id_c_anomalia = tca.id_c_anomalia
                    $left_join
                    where 1=1
                    and tcd.status in (1, 5)
                    $whereClientes
                    $whereDeudores
                    $whereTipoAnomlia
                    $whereAnomlia
                    $whereCapa
                    and tcd.id_c_tipo_documento = 1
                    and tcd.fecha_documento between ? and ?
                    ) as foo
                    $whereDivision
                    group by foo.capa, foo.division
                    order by 1 DESC, 2 ASC
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Antigüedad de saldos */

    /* Inicio Métodos Sección Anomalía */
    public function getDataGraficaRecuentoFacturaAnomalia2($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$responsables)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
            $whereResponsable = "";
            if ($responsables != "" && $responsables != null && $responsables != 'null') {
                $whereResponsable = " and (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) in ($responsables)";
            }
            $query = $this->db->connect()->prepare("
            select (case when tcta.descripcion_tipo_anomalia is null then 'Sin Clasificacion' else tcta.descripcion_tipo_anomalia end) as TipoAnomalia, tca.descripcion_anomalia as Anomalia, count(tca.descripcion_anomalia) as conteo
            from tb_c_documento tcd
            left join tb_c_anomalia tca
            on tcd.id_c_anomalia = tca.id_c_anomalia
            left join tb_c_tipo_anomalia tcta
            on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
            left outer join tb_c_tipo_cliente tctc
            on tcd.id_division = tctc.id_c_tipo_cliente
            $left_join
            where 1=1
            and tcd.status in (1, 5)
            and tcd.id_c_tipo_documento = 1
            $whereDeudores
            $whereTipoAnomlia
            $whereAnomlia
            $whereDivision
            $whereClientes
            $whereCapa
            $whereResponsable
            and tcd.fecha_documento between ? and ?
            group by TipoAnomalia,Anomalia
            order by TipoAnomalia
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaRecuentoFacturaAnomalia($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$responsables)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
            $whereResponsable = "";
            if ($responsables != "" && $responsables != null && $responsables != 'null') {
                $whereResponsable = " and (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) in ($responsables)";
            }
            $query = $this->db->connect()->prepare("
                select tcta.descripcion_tipo_anomalia, count(*)
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_tipo_documento = 1
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereDivision
                $whereCapa
                $whereResponsable
                and tcd.fecha_documento between ? and ?
                group by tcta.descripcion_tipo_anomalia
                order by 1
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaRecuentoAnomaliasResponsable($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$responsables)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
            $whereResponsable = "";
            if ($responsables != "" && $responsables != null && $responsables != 'null') {
                $whereResponsable = " and (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) in ($responsables)";
            }
            $query = $this->db->connect()->prepare("
                select
                (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) as responsable , count(*) as total
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereDivision
                $whereCapa
                $whereResponsable
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                group by tca.id_c_responsable
                order by 2
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaSaldoPorAnomalia($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$responsables)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereTipoAnomlia = "";
            if ($tipos_anomalia != "" && $tipos_anomalia != null && $tipos_anomalia != 'null') {
                $whereTipoAnomlia = " and tca.id_c_tipo_anomalia in ($tipos_anomalia)";
            }
            $whereAnomlia = "";
            if ($anomalias != "" && $anomalias != 'null' && $anomalias != null) {
                $whereAnomlia = " and tca.id_c_anomalia in ($anomalias)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
            $whereResponsable = "";
            if ($responsables != "" && $responsables != null && $responsables != 'null') {
                $whereResponsable = " and (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) in ($responsables)";
            }
            $query = $this->db->connect()->prepare("
                select tca.descripcion_anomalia, sum(tcd.importe_documento) as total
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereDivision
                $whereCapa
                $whereResponsable
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                group by tca.descripcion_anomalia
                order by 2
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaAnomaliaFrecuente($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$responsables)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
            $whereResponsable = "";
            if ($responsables != "" && $responsables != null && $responsables != 'null') {
                $whereResponsable = " and (case
                when tca.id_c_responsable = 0 then 'Cliente'
                when tca.id_c_responsable = 1 then 'Collecta'
                when tca.id_c_responsable = 2 then 'Pendiente'
                else 'Otro'
                end) in ($responsables)";
            }
            $query = $this->db->connect()->prepare("
                select tca.descripcion_anomalia, count(*) as total
                from tb_c_documento tcd
                left join tb_c_anomalia tca
                on tcd.id_c_anomalia = tca.id_c_anomalia
                left join tb_c_tipo_anomalia tcta
                on tca.id_c_tipo_anomalia = tcta.id_c_tipo_anomalia
                left outer join tb_c_tipo_cliente tctc
                on tcd.id_division = tctc.id_c_tipo_cliente
                $left_join
                where 1=1
                and tcd.status in (1, 5)
                and tcd.id_c_tipo_documento = 1
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereDivision
                $whereCapa
                $whereResponsable
                and tcd.fecha_documento between ? and ?
                group by tca.descripcion_anomalia
                order by 2
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Anomalía */

    /* Inicio Métodos Sección KPI Sanofi */
    public function getDataGraficaKpiRecepcionEvidencias($fechainicial,$fechafinal,$clientes,$deudores,$divisiones,$fecha_aplicar){
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaFactura') {
                $whereFechas = " tcks.fecha_factura between ? and ? ";
               }else{
                $whereFechas = " tcks.fecha_collecta between ? and ? ";
               }
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcks.fk_id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcks.fk_id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcks.fk_id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }
            $query = $this->db->connect()->prepare("
                select dias,meses,numeroMes,anio, total_registros, (conteodias*100)/total_registros::numeric  as promedio
                from
                (
                select count(((case when fecha_remision is null then current_date else fecha_remision end) - fecha_factura)) as conteodias,
                ((case when fecha_remision is null then current_date else fecha_remision end) - fecha_factura) as dias,
                (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS meses,
                date_part('month'::TEXT, fecha_factura) AS numeroMes,
                date_part('year'::TEXT, fecha_factura) AS anio
                from tb_c_kpi_sanofi tcks 
                left join tb_c_documento tcd 
                on tcd.numero_documento = tcks.numero_factura 
                left join tb_c_zona tcz 
                on tcz.id_c_zona = tcd.num_zona 
                where $whereFechas
                $whereClientes
                $whereDeudores
                $whereDivision
                group by dias, meses,anio,numeroMes
                ) as a,
                (
                select (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS mes, count(*) as total_registros from tb_c_kpi_sanofi tcks  where $whereFechas Group By mes
                ) as b
                group by meses,dias, anio,numeroMes, conteodias,numeroMes, total_registros
                ORDER BY anio ASC, numeroMes asc
            ");
            $query->execute([$fechainicial, $fechafinal,$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaKpiRecepcionContratos($fechainicial,$fechafinal,$clientes,$deudores,$divisiones,$fecha_aplicar){
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaFactura') {
                $whereFechas = " tcks.fecha_factura between ? and ? ";
               }else{
                $whereFechas = " tcks.fecha_collecta between ? and ? ";
               }
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcks.fk_id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcks.fk_id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcks.fk_id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }
            $query = $this->db->connect()->prepare("
                select dias,meses,numeroMes,anio, total_registros, (conteodias*100)/total_registros::numeric  as promedio
                from
                (
                select count(((case when fecha_contrato is null then current_date else fecha_contrato end) - fecha_factura)) as conteodias,
                ((case when fecha_contrato is null then current_date else fecha_contrato end) - fecha_factura) as dias,
                (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS meses,
                date_part('month'::TEXT, fecha_factura) AS numeroMes,
                date_part('year'::TEXT, fecha_factura) AS anio
                from tb_c_kpi_sanofi tcks 
                left join tb_c_documento tcd 
                on tcd.numero_documento = tcks.numero_factura 
                left join tb_c_zona tcz 
                on tcz.id_c_zona = tcd.num_zona 
                where $whereFechas
                $whereClientes
                $whereDeudores
                $whereDivision
                group by dias, meses,anio,numeroMes
                ) as a,
                (
                select (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS mes, count(*) as total_registros from tb_c_kpi_sanofi tcks  where $whereFechas Group By mes
                ) as b
                group by meses,dias, anio,numeroMes, conteodias,numeroMes, total_registros
                ORDER BY anio ASC, numeroMes asc
            ");
            $query->execute([$fechainicial, $fechafinal,$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaKpiRecepcionFianza($fechainicial,$fechafinal,$clientes,$deudores,$divisiones,$fecha_aplicar){
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaFactura') {
                $whereFechas = " tcks.fecha_factura between ? and ? ";
               }else{
                $whereFechas = " tcks.fecha_collecta between ? and ? ";
               }
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcks.fk_id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcks.fk_id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcks.fk_id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }
            $query = $this->db->connect()->prepare("
                select dias,meses,numeroMes,anio, total_registros, (conteodias*100)/total_registros::numeric  as promedio
                from
                (
                select count(((case when fecha_fianza is null then current_date else fecha_fianza end) - fecha_factura)) as conteodias,
                ((case when fecha_fianza is null then current_date else fecha_fianza end) - fecha_factura) as dias,
                (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS meses,
                date_part('month'::TEXT, fecha_factura) AS numeroMes,
                date_part('year'::TEXT, fecha_factura) AS anio
                from tb_c_kpi_sanofi tcks 
                left join tb_c_documento tcd 
                on tcd.numero_documento = tcks.numero_factura 
                left join tb_c_zona tcz 
                on tcz.id_c_zona = tcd.num_zona 
                where $whereFechas
                $whereClientes
                $whereDeudores
                $whereDivision
                group by dias, meses,anio,numeroMes
                ) as a,
                (
                select (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS mes, count(*) as total_registros from tb_c_kpi_sanofi tcks  where $whereFechas Group By mes
                ) as b
                group by meses,dias, anio,numeroMes, conteodias,numeroMes, total_registros
                ORDER BY anio ASC, numeroMes asc
            ");
            $query->execute([$fechainicial, $fechafinal,$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaKpiRecepcionConvenios($fechainicial,$fechafinal,$clientes,$deudores,$divisiones,$fecha_aplicar){
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereFechas = "";
            if ($fecha_aplicar != "" && $fecha_aplicar != null && $fecha_aplicar != 'null') {
               if ($fecha_aplicar == 'fechaFactura') {
                $whereFechas = " tcks.fecha_factura between ? and ? ";
               }else{
                $whereFechas = " tcks.fecha_collecta between ? and ? ";
               }
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcks.fk_id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcks.fk_id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcks.fk_id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }
            $query = $this->db->connect()->prepare("
                select dias,meses,numeroMes,anio, total_registros, (conteodias*100)/total_registros::numeric  as promedio
                from
                (
                select count(((case when fecha_convenio is null then current_date else fecha_convenio end) - fecha_factura)) as conteodias,
                ((case when fecha_convenio is null then current_date else fecha_convenio end) - fecha_factura) as dias,
                (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS meses,
                date_part('month'::TEXT, fecha_factura) AS numeroMes,
                date_part('year'::TEXT, fecha_factura) AS anio
                from tb_c_kpi_sanofi tcks 
                left join tb_c_documento tcd 
                on tcd.numero_documento = tcks.numero_factura 
                left join tb_c_zona tcz 
                on tcz.id_c_zona = tcd.num_zona 
                where $whereFechas
                $whereClientes
                $whereDeudores
                $whereDivision
                group by dias, meses,anio,numeroMes
                ) as a,
                (
                select (CASE WHEN date_part('month'::TEXT, fecha_factura) = 1 THEN 'enero'
                WHEN date_part('month'::TEXT, fecha_factura) = 2 THEN 'febrero'
                WHEN date_part('month'::TEXT, fecha_factura) = 3 THEN 'marzo'
                WHEN date_part('month'::TEXT, fecha_factura) = 4 THEN 'abril'
                WHEN date_part('month'::TEXT, fecha_factura) = 5 THEN 'mayo'
                WHEN date_part('month'::TEXT, fecha_factura) = 6 THEN 'junio'
                WHEN date_part('month'::TEXT, fecha_factura) = 7 THEN 'julio'
                WHEN date_part('month'::TEXT, fecha_factura) = 8 THEN 'agosto'
                WHEN date_part('month'::TEXT, fecha_factura) = 9 THEN 'septiembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 10 THEN 'octubre'
                WHEN date_part('month'::TEXT, fecha_factura) = 11 THEN 'noviembre'
                WHEN date_part('month'::TEXT, fecha_factura) = 12 THEN 'diciembre'
                END) AS mes, count(*) as total_registros from tb_c_kpi_sanofi tcks  where $whereFechas Group By mes
                ) as b
                group by meses,dias, anio,numeroMes, conteodias,numeroMes, total_registros
                ORDER BY anio ASC, numeroMes asc
            ");
            $query->execute([$fechainicial, $fechafinal,$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección KPI Sanofi */

    /* Inicio Métodos Sección Cobranza */
    public function getMontoCobrado($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                $left_join
                where 1=1
                and tcd.status in (3)
                $whereClientes
                $whereDeudores
                $whereDivision
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tctc2.fecha_aplicacion between ? and ?
                and tcp.monto_pago is not null
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getFacturacionCobradas($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                $left_join
                where 1=1
                and tcd.status in (3)
                $whereClientes
                $whereDeudores
                $whereDivision
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tctc2.fecha_aplicacion between ? and ?
                and tcp.monto_pago is not null
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetch();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaMontoRecuperadoDivision($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
            }
            $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
            $campo = "tcz.nombre_zona";
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                    select $campo as deudor, sum(tcp.monto_pago) as pago
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
                    $left_join
                    where 1=1
                    and tcd.status in (3)
                    $whereClientes
                    $whereDeudores
                    $whereDivision
                    $whereCapa
                    and tcd.id_c_tipo_documento = 1
                    and tctc2.fecha_aplicacion between ? and ?
                    group by $campo
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
    public function getDataGraficaMontoRecuperadoDeudor($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                    $left_join
                    where 1=1
                    and tcd.status in (3)
                    $whereClientes
                    $whereDeudores
                    $whereDivision
                    $whereCapa
                    and tcd.id_c_tipo_documento = 1
                    and tctc2.fecha_aplicacion between ? and ?
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
    public function getDataGraficaPagoCapaAging($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";

            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when tctc2.fecha_aplicacion - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
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
                $left_join
                where 1=1
                and tcd.status in (3)
                $whereClientes
                $whereDeudores
                $whereDivision
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tctc2.fecha_aplicacion between ? and ?
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
    public function getDataGraficaRecuentoFactura($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                select tctc2.fecha_aplicacion, sum(tcp.monto_pago) as monto_cobrado
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
                $left_join
                where 1=1
                and tcd.status in (3)
                $whereClientes
                $whereDeudores
                $whereDivision
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tctc2.fecha_aplicacion between ? and ?
                and tcp.monto_pago is not null
                group by tctc2.fecha_aplicacion
                order by 1
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getDataGraficaMoratoriaPago($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                select tcd2.razon_social_deudor, tcd.dias_credito
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
                $left_join
                where 1=1
                and tcd.status in (3)
                $whereClientes
                $whereDeudores
                $whereDivision
                $whereCapa
                and tcd.id_c_tipo_documento = 1
                and tctc2.fecha_aplicacion between ? and ?
                group by tcd2.razon_social_deudor, tcd.dias_credito
                order by 2 asc
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    /* Fin Métodos Sección Cobranza */

    /* Inicio Método para exportar a excel */
    public function getExport($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$isCobranza)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereStatus = "and tcd.status in (1, 5)";
            if($isCobranza != "" && $isCobranza != null && $isCobranza != 'null') {
                $whereStatus = "and tcd.status in (3)";
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                select max(distinct tcd.id_c_documento) as IdDocumento, tcc.razon_social_cliente as cliente, 
                (case when $campo is null then 'Sin division' else $campo end) as division, 
                tcd.id_c_cliente, tcd4.cuenta, tcd.id_c_proyecto as proyecto,
                tcd.numero_documento as FACTURA, tcd.id_c_deudor, tcd2.razon_social_deudor as deudor,
                tcd.pedido, to_char(tcd.fecha_documento, 'dd-mm-yyyy') as fecha_documento, tcd.importe_documento,
                tcd.dias_credito, tcd.saldo_documento,
                tcd.fecha_anomalia, tctc2.fecha_aplicacion,
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
                end) as anomalia, tca.clave_anomalia, tcd.observacion_documento, tco.descripcion_observacion  as UltimoComentario,-- acd.observacion,
                tctd.nombre_tipo_documento as tipo_documento,
                (case
                    when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
                else 'Desconocida'
                    end) AS CapaAging
                from tb_c_documento tcd
                left join tb_cuenta_deudor tcd3
                on tcd.id_c_deudor=tcd3.id_c_deudor 
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
                left join tb_cuenta_deudor_cliente tcdc
                on tcd4.id_cuenta_deudor = tcdc.id_cuenta_deudor
                left join tb_c_observacion tco
                on tcd.id_c_observacion = tco.id_c_observacion
                $left_join
                where 1=1
                $whereStatus
                $whereClientes
                $whereDeudores
                $whereTipoAnomlia
                $whereAnomlia
                $whereCapa
                $whereDivision
                and tcd.id_c_tipo_documento = 1
                and tcd.fecha_documento between ? and ?
                group by cliente, tcd.id_c_cliente,division, tcd4.cuenta, proyecto,
                FACTURA, tcd.id_c_deudor, deudor,
                tcd.pedido, fecha_documento, tcd.importe_documento,
                tcd.dias_credito, tcd.saldo_documento,
                tcd.fecha_anomalia, tctc2.fecha_aplicacion,
                ruta,
                fecha_recibida, tcd.fecha_reprogramacion,
                estatus, tcd.folio_fiscal, tcc2.contra_recibo, importe_contrarecibo, tcc2.fecha_emision,
                anomalia, tca.clave_anomalia, tcd.observacion_documento, Ultimocomentario,-- acd.observacion,
                tipo_documento,
                CapaAging
            ");
            $query->execute([$fechainicial, $fechafinal]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo "Error recopilado: ".$e->getMessage();
            return false;
        }
    }
    public function getColumnas($fechainicial,$fechafinal,$clientes,$deudores,$capas,$tipos_anomalia,$anomalias,$divisiones,$isCobranza)
    {
        try {
            $cliente = $_SESSION['id_cliente-' . constant('Sistema')];
            $whereStatus = "and tcd.status in (1, 5)";
            if($isCobranza != "" && $isCobranza != null && $isCobranza != 'null') {
                $whereStatus = "and tcd.status in (3)";
            }
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
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
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
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
            select max(distinct tcd.id_c_documento) as IdDocumento, tcc.razon_social_cliente as cliente, (CASE WHEN(tcd.id_division=3) THEN('Imss')
            WHEN(tcd.id_division=2) THEN('Gobierno')
            ELSE('Privado') END) as division,tcd.id_c_cliente, tcd4.cuenta, tcd.id_c_proyecto as proyecto,
            tcd.numero_documento as FACTURA, tcd.id_c_deudor, tcd2.razon_social_deudor as deudor,
            tcd.pedido, to_char(tcd.fecha_documento, 'dd-mm-yyyy') as fecha_documento, tcd.importe_documento,
            tcd.dias_credito, tcd.saldo_documento,
            tcd.fecha_anomalia, tctc2.fecha_aplicacion,
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
            end) as anomalia, tca.clave_anomalia, tcd.observacion_documento, tco.descripcion_observacion  as UltimoComentario,-- acd.observacion,
            tctd.nombre_tipo_documento as tipo_documento,
            (case
                when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) <1 then 'A (Current)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 1 and 30 then 'B (0-30)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 31 and 60 then 'C (31-60)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 61 and 90 then 'D (61-90)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 91 and 120 then 'E (91-120)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 121 and 150 then 'F (121-150)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) between 151 and 180 then 'G (151-180)'
            when current_date - tcd.fecha_documento - (case when tcd.dias_credito is null then 30 else tcd.dias_credito end) > 180 then 'H (>180)'
            else 'Desconocida'
                end) AS CapaAging
            from tb_c_documento tcd
            left join tb_cuenta_deudor tcd3
            on tcd.id_c_deudor=tcd3.id_c_deudor 
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
            left join tb_cuenta_deudor_cliente tcdc
            on tcd4.id_cuenta_deudor = tcdc.id_cuenta_deudor
            left join tb_c_observacion tco
            on tcd.id_c_observacion = tco.id_c_observacion
            where 1=1
            $whereStatus
            $whereClientes
            $whereDeudores
            $whereTipoAnomlia
            $whereAnomlia
            $whereCapa
            $whereDivision
            and tcd.id_c_tipo_documento = 1
            and tcd.fecha_documento between ? and ?
            group by cliente, tcd.id_c_cliente, division, tcd4.cuenta, proyecto,
            FACTURA, tcd.id_c_deudor, deudor,
            tcd.pedido, fecha_documento, tcd.importe_documento,
            tcd.dias_credito, tcd.saldo_documento,
            tcd.fecha_anomalia, tctc2.fecha_aplicacion,
            ruta,
            fecha_recibida, tcd.fecha_reprogramacion,
            estatus, tcd.folio_fiscal, tcc2.contra_recibo, importe_contrarecibo, tcc2.fecha_emision,
            anomalia, tca.clave_anomalia, tcd.observacion_documento, Ultimocomentario,-- acd.observacion,
            tipo_documento,
            CapaAging
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

    public function getFiltrosCobranza($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
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
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null && $deudores != 'null') {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            $left_join = "";
            $campo = "tctc.descripcion_cliente";
            $clientes_asignados = explode(",",$_SESSION['id_cliente-'.constant('Sistema')]);
            if (in_array("205", $clientes_asignados) || in_array("206", $clientes_asignados) || in_array("262", $clientes_asignados)){
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tcz.nombre_zona in ($divisiones) ";
                }
                $left_join = " left join tb_c_zona tcz on tcz.id_c_zona = tcd.num_zona ";
                $campo = "tcz.nombre_zona";
            }else{
                if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                    $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
                }
            }
            /* $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
            } */
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
                $left_join
                where $whereFecha
                $whereClientes
                $whereDeudores
                $whereCapa
                $whereDivision
                and tcd.status in (3)
                --and tpd.id_c_cliente = $cliente
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
    public function getColumnasCobranza($fechainicial,$fechafinal,$clientes,$deudores,$capas,$divisiones)
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
            $whereClientes = "";
            if ($clientes != "" && $clientes != null && $clientes != 'null') {
                $whereClientes = " and tcd.id_c_cliente in ($clientes)";
            } else {
                $whereClientes = " and tcd.id_c_cliente in ($cliente)";
            }
            $whereDeudores = "";
            if ($deudores != "" && $deudores != null) {
                $whereDeudores = " and tcd.id_c_deudor in ($deudores)";
            }
            $whereDivision = "";
            if ($divisiones != "" && $divisiones != null && $divisiones != 'null') {
                $whereDivision = " and tctc.descripcion_cliente in ($divisiones)";
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
                $whereClientes
                $whereDeudores
                $whereCapa
                $whereDivision
                and tcd.status in (3)
                --and tpd.id_c_cliente = $cliente
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
    /* Fin Método para exportar a excel */
}
