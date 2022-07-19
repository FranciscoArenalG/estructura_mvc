$(function () {

    let url = servidor;
    let controller = new AbortController();

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartRecepcionEvidencias = null;
    let myChartRecepcionContratos = null;
    let myChartRecepcionFianza = null;
    let myChartRecepcionConvenios = null;
    // INICIO Métodos para cargar la información a los campos de filtros
    $('input[name="filtroFechas"]').daterangepicker({
        opens: 'right',
        "locale": {
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "De",
            "toLabel": "A",
            "customRangeLabel": "Custom",
            "weekLabel": "W",
            "format": "YYYY-MM-DD",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Mar",
                "Mie",
                "Jue",
                "Vie",
                "Sab"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 1,
        },
        startDate: fecha_inicia,
        endDate: fecha_final
    }, function (start, end, label) {

        fecha_inicia = start.format('YYYY-MM-DD');
        fecha_final = end.format('YYYY-MM-DD');

    });
    async function cargarClientes() {
        const signal = controller.signal;
        try {
            $("#selectCliente").empty();
            let peticion = await fetch(url + `dashboardd/getClientes`, { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_cliente;
                option.text = item.razon_social_cliente
                $("#selectCliente").append(option)
            }
            console.log('cargando deudores ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    async function cargarDeudores() {
        $("#selectDeudor").empty();
        let peticion = await fetch(url + "dashboardd/getDeudores");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = item.id_c_deudor;
            option.text = item.razon_social_deudor
            $("#selectDeudor").append(option)
        }
        console.log('cargando deudores ...');
    }
    async function cargarDivisionesZonas() {
        $("#selectDivision").empty();
        let peticion = await fetch(url + "dashboardd/getSelectDivisionesZonas");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = `'${item.nombre_zona}'`;
            option.text = item.nombre_zona
            $("#selectDivision").append(option)
        }
        console.log('cargando divisiones sanofi...');
    }
    // FIN Métodos para cargar la información a los campos de filtros

    // INICIO Eventos de botones
    $("#btnEliminarFiltros").on('click', function () {
        console.log('Eliminando filtros...');
        $("#selectCliente").val("");
        $("#selectCliente").trigger("change");

        $("#selectDeudor").val("");
        $("#selectDeudor").trigger("change");

        $("#selectDivision").val("");
        $("#selectDivision").trigger("change");

        fecha_inicia = moment().format('YYYY-MM-01');
        fecha_final = moment().format('YYYY-MM-DD');

        $('input[name="filtroFechas"]').data('daterangepicker').setStartDate(fecha_inicia);
        $('input[name="filtroFechas"]').data('daterangepicker').setEndDate(fecha_final);

        $('#fechaDeposito').prop("checked", false)
        $('#fechaAplicacion').prop("checked", true)
    });
    $("#btnAplicarFiltros").on('click', function () {
        controller.abort();
        controller = new AbortController();
        console.log('Aplicando filtros...');
        aplicarFiltros();
    });

    $(".redireccionMenu").on('click', function () {
        controller.abort();
    });

    async function renderGraficaKpiRecepcionEvidencias(clientes,deudores,divisiones,fechaFiltrar){
        let data_ = `dashboardd/getDataGraficaKpiRecepcionEvidencias/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(divisiones=="")?null:divisiones}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#loaderGraficaRecepcionEvidencias").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecepcionEvidencias").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecepcionEvidencias").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();

        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecepcionEvidencias").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecepcionEvidencias").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecepcionEvidencias").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecepcionEvidencias").removeClass("d-none");

        let data = [];
        let labels = [];
        let labelPorcentaje = [];
        let conteoTotal = [];
        // let i = 0;
        for (let item of response) {
            data.push(Number(item.promedio).toFixed(2));
            labels.push(item.meses.slice(0, 3) + "-" + item.anio);
            labelPorcentaje.push(Number(item.dias).toFixed());
            conteoTotal.push(Number(item.total_registros).toFixed());
        }
        console.log(data);
        console.log(labels);
        let grafica = 'graficaRecepcionEvidencias';
        let chartDom = document.getElementById(grafica);
        myChartRecepcionEvidencias = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: labels
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                // boundaryGap: false,
                axisLabel: {
                    rotate: 90
                },
                data: labels
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Días',
                    type: 'line',
                    stack: 'Total',
                    data: labelPorcentaje
                }, {
                    name: 'Porcentaje %',
                    type: 'line',
                    data: data
                },
                {
                    name: 'Recuento de Factura',
                    type: 'bar',
                    // barWidth: '60%',
                    label: {
                        show: false,
                        position: 'inside'
                    },
                    data: conteoTotal
                },
            ]
        };

        option && myChartRecepcionEvidencias.setOption(option);
    }
    async function renderGraficaKpiRecepcionContratos(clientes,deudores,divisiones,fechaFiltrar){
        let data_ = `dashboardd/getDataGraficaKpiRecepcionContratos/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(divisiones=="")?null:divisiones}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#loaderGraficaRecepcionContratos").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecepcionContratos").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecepcionContratos").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();

        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecepcionContratos").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecepcionContratos").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecepcionContratos").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecepcionContratos").removeClass("d-none");

        let data = [];
        let labels = [];
        let labelPorcentaje = [];
        let conteoTotal = [];
        // let i = 0;
        for (let item of response) {
            data.push(Number(item.promedio).toFixed(2));
            labels.push(item.meses.slice(0, 3) + "-" + item.anio);
            labelPorcentaje.push(Number(item.dias).toFixed());
            conteoTotal.push(Number(item.total_registros).toFixed());
        }
        console.log(data);
        console.log(labels);
        let grafica = 'graficaRecepcionContratos';
        let chartDom = document.getElementById(grafica);
        myChartRecepcionContratos = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: labels
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                // boundaryGap: false,
                axisLabel: {
                    rotate: 90
                },
                data: labels
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Días',
                    type: 'line',
                    stack: 'Total',
                    data: labelPorcentaje
                }, {
                    name: 'Porcentaje %',
                    type: 'line',
                    data: data
                },
                {
                    name: 'Recuento de Factura',
                    type: 'bar',
                    // barWidth: '60%',
                    label: {
                        show: false,
                        position: 'inside'
                    },
                    data: conteoTotal
                },
            ]
        };

        option && myChartRecepcionContratos.setOption(option);
    }
    async function renderGraficaKpiRecepcionFianza(clientes,deudores,divisiones,fechaFiltrar){
        let data_ = `dashboardd/getDataGraficaKpiRecepcionFianza/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(divisiones=="")?null:divisiones}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#loaderGraficaRecepcionFianza").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecepcionFianza").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecepcionFianza").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();

        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecepcionFianza").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecepcionFianza").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecepcionFianza").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecepcionFianza").removeClass("d-none");

        let data = [];
        let labels = [];
        let labelPorcentaje = [];
        let conteoTotal = [];
        // let i = 0;
        for (let item of response) {
            data.push(Number(item.promedio).toFixed(2));
            labels.push(item.meses.slice(0, 3) + "-" + item.anio);
            labelPorcentaje.push(Number(item.dias).toFixed());
            conteoTotal.push(Number(item.total_registros).toFixed());
        }
        console.log(data);
        console.log(labels);
        let grafica = 'graficaRecepcionFianza';
        let chartDom = document.getElementById(grafica);
        myChartRecepcionFianza = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: labels
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                // boundaryGap: false,
                axisLabel: {
                    rotate: 90
                },
                data: labels
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Días',
                    type: 'line',
                    stack: 'Total',
                    data: labelPorcentaje
                }, {
                    name: 'Porcentaje %',
                    type: 'line',
                    data: data
                },
                {
                    name: 'Recuento de Factura',
                    type: 'bar',
                    // barWidth: '60%',
                    label: {
                        show: false,
                        position: 'inside'
                    },
                    data: conteoTotal
                },
            ]
        };

        option && myChartRecepcionFianza.setOption(option);
    }
    async function renderGraficaKpiRecepcionConvenios(clientes,deudores,divisiones,fechaFiltrar){
        let data_ = `dashboardd/getDataGraficaKpiRecepcionConvenios/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(divisiones=="")?null:divisiones}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#loaderGraficaRecepcionConvenios").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecepcionConvenios").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecepcionConvenios").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();

        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecepcionConvenios").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecepcionConvenios").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecepcionConvenios").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecepcionConvenios").removeClass("d-none");

        let data = [];
        let labels = [];
        let labelPorcentaje = [];
        let conteoTotal = [];
        // let i = 0;
        for (let item of response) {
            data.push(Number(item.promedio).toFixed(2));
            labels.push(item.meses.slice(0, 3) + "-" + item.anio);
            labelPorcentaje.push(Number(item.dias).toFixed());
            conteoTotal.push(Number(item.total_registros).toFixed());
        }
        console.log(data);
        console.log(labels);
        let grafica = 'graficaRecepcionConvenios';
        let chartDom = document.getElementById(grafica);
        myChartRecepcionConvenios = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: labels
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                // boundaryGap: false,
                axisLabel: {
                    rotate: 90
                },
                data: labels
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Días',
                    type: 'line',
                    stack: 'Total',
                    data: labelPorcentaje
                }, {
                    name: 'Porcentaje %',
                    type: 'line',
                    data: data
                },
                {
                    name: 'Recuento de Factura',
                    type: 'bar',
                    // barWidth: '60%',
                    label: {
                        show: false,
                        position: 'inside'
                    },
                    data: conteoTotal
                },
            ]
        };

        option && myChartRecepcionConvenios.setOption(option);
    }

    function aplicarFiltros() {
        let clientes = $("#selectCliente").val().toString();
        let deudores = $("#selectDeudor").val().toString();
        let divisiones = $("#selectDivision").val().toString();
        let fechaFiltrar = $('input[name=fechaFiltrar]:checked').val();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        renderGraficaKpiRecepcionEvidencias(clientes,deudores,divisiones,fechaFiltrar);
        renderGraficaKpiRecepcionContratos(clientes,deudores,divisiones,fechaFiltrar);
        renderGraficaKpiRecepcionFianza(clientes,deudores,divisiones,fechaFiltrar);
        renderGraficaKpiRecepcionConvenios(clientes,deudores,divisiones,fechaFiltrar);
    }
    // FIN Eventos de botones

    //INICIO Métodos de Gráficas

    //FIN Métodos de Gráficas
    cargarClientes();
    cargarDeudores();
    cargarDivisionesZonas();
    aplicarFiltros();

    $(window).on('resize', resize);
    function resize() {
        // Resize chart
        myChartRecepcionEvidencias.resize();
        myChartRecepcionContratos.resize();
        myChartRecepcionFianza.resize();
        myChartRecepcionConvenios.resize();
    }

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })
});