$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartGraficaFechaDocumento = null;
    let myChartGraficaFechaRecibido = null;
    let myChartGraficaFechaContrarecibo = null;


    /** INICIALIZACION DEL INPUT RANO DE FECHA */
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
    }, function(start, end, label) {

        fecha_inicia = start.format('YYYY-MM-DD');
        fecha_final = end.format('YYYY-MM-DD');

    });

    $("#btnEliminarFiltros").on('click', function() {

        $("#selectCliente").val("");
        $("#selectCliente").trigger("change");

        $("#selectDeudor").val("");
        $("#selectDeudor").trigger("change");

        $("#selectCapaAgin").val("");
        $("#selectCapaAgin").trigger("change");

        $("#selectTipoAnomalia").val("");
        $("#selectTipoAnomalia").trigger("change");

        $("#selectAnomalias").val("");
        $("#selectAnomalias").trigger("change");

        $("#selectDivision").val("");
        $("#selectDivision").trigger("change");

        fecha_inicia = moment().format('YYYY-MM-01');
        fecha_final = moment().format('YYYY-MM-DD');

        $('input[name="filtroFechas"]').data('daterangepicker').setStartDate(fecha_inicia);
        $('input[name="filtroFechas"]').data('daterangepicker').setEndDate(fecha_final);

    })

    $("#btnAplicarFiltros").on('click', function() {

        aplicarFiltros();

    })

    $("#exportarDatos").on('click', function() {
        exportarDatos();
    });

    //Btn exportar datos
    function exportarDatos() {
        let clientes = $("#selectCliente").val().toString();
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let tipos_anomalia = $("#selectTipoAnomalia").val().toString();
        let anomalias = $("#selectAnomalias").val().toString();
        let divisiones = $("#selectDivision").val().toString();
        let peticion = url + `dashboardd/getexportar/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}/null`
        $.ajax({
            url: peticion,
            data: {},
            method: 'get',
            responseType: 'blob',
            beforeSend: function() {
                swal({
                    icon: 'info',
                    title: 'Procesando información',
                    text: 'Se está generando el archivo, por favor espere...',
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    allowOutsideClick: false,
                    buttons: false
                });
                var link;
                link = document.createElement('a');
                link.id = "descargar";
                link.textContent = 'Aquí';
                link.href = peticion;
                var clicEvent = new MouseEvent('click', {
                    'view': window,
                    'bubbles': false,
                    'cancelable': true
                });
                link.dispatchEvent(clicEvent);
            },
            success: function(returnHtml) {
                swal({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Se genero correctamente el archivo!',
                    buttons: false,
                    timer: 2000
                });
            }
        });
    }


    /** Carga de estadisticas (tarjetas) */

    async function updateMontoPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getMontoPorCobrar/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#monto_por_cobrar").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.monto_por_cobrar / 1000));
        let currencyString = myNumeral.format('$0,0.00');
        $("#monto_por_cobrar").text(currencyString + ' K')
    }

    async function updateFacturasPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getFacturasPorCobrar/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#facturas_por_cobrar").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.facturas_por_cobrar).toFixed());
        let currencyString = myNumeral.format('0,0');
        $("#facturas_por_cobrar").text(currencyString)
    }

    async function updateTotalDeudores(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getTotalDeudores/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#deudores").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.total_deudores).toFixed());
        let currencyString = myNumeral.format('0,0');
        $("#deudores").text(currencyString)
    }

    async function updateTotalDivisiones(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getDivisiones/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#divisiones").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.total_division).toFixed());
        let currencyString = myNumeral.format('0,0');
        $("#divisiones").text(currencyString)
    }

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartGraficaFechaDocumento.resize();
        myChartGraficaFechaRecibido.resize();
        myChartGraficaFechaContrarecibo.resize();

    }

    async function renderGraficaFechaDocumento(clientes, deudores, capas_aging, tipos_anomalia, anomalias, divisiones_) {
        let data_ = `dashboardd/getDataGraficaFechaDocumento/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas_aging=="")?null:capas_aging}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones_=="")?null:divisiones_}`
        // Mostramos el loader
        $("#loaderGraficaFechaDocumento").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaFechaDocumento").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaFechaDocumento").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaFechaDocumento").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaFechaDocumento").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaFechaDocumento").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaFechaDocumento").removeClass("d-none");
        let data = [];
        let capas = [];
        let divisiones = [];
        for (let item of response) {
            capas.push(item.capa);
            divisiones.push(item.division)
        }
        const dataCapas = new Set(capas);
        capas = [...dataCapas];
        const dataDivisiones = new Set(divisiones);
        divisiones = [...dataDivisiones]
        for (let division of divisiones) {
            let values = []; // valores de la division por cada capa
            for (let capa_ of capas) {
                let resultado = response.filter(item => item.capa == capa_ && item.division == division);
                if (resultado.length == 0) {
                    values.push(0);
                } else {
                    values.push(Number(resultado[0].sum).toFixed() / 1000)
                }
            }
            data.push({
                name: division,
                type: 'bar',
                stack: 'total',
                label: {
                    show: false
                },
                emphasis: {
                    focus: 'series'
                },
                data: values
            })
        }
        let chartDom = document.getElementById('graficaFechaDocumento');
        myChartGraficaFechaDocumento = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
                }
            },
            legend: {},
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: capas
            },
            series: data
        };
        option && myChartGraficaFechaDocumento.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaFechaRecibido(clientes, deudores, capas_aging, tipos_anomalia, anomalias, divisiones_) {
        let data_ = `dashboardd/getDataGraficaFechaRecibida/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas_aging=="")?null:capas_aging}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones_=="")?null:divisiones_}`
        // Mostramos el loader
        $("#loaderGraficaFechaRecibido").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaFechaRecibido").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaFechaRecibido").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaFechaRecibido").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaFechaRecibido").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaFechaRecibido").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaFechaRecibido").removeClass("d-none");
        let data = [];
        let capas = [];
        let divisiones = [];
        for (let item of response) {
            capas.push(item.capa);
            divisiones.push(item.division)
        }
        const dataCapas = new Set(capas);
        capas = [...dataCapas];
        const dataDivisiones = new Set(divisiones);
        divisiones = [...dataDivisiones]
        for (let division of divisiones) {
            let values = []; // valores de la division por cada capa
            for (let capa_ of capas) {
                let resultado = response.filter(item => item.capa == capa_ && item.division == division);
                if (resultado.length == 0) {
                    values.push(0);
                } else {
                    values.push(Number(resultado[0].sum).toFixed() / 1000)
                }
            }
            data.push({
                name: division,
                type: 'bar',
                stack: 'total',
                label: {
                    show: false
                },
                emphasis: {
                    focus: 'series'
                },
                data: values
            })
        }
        let chartDom = document.getElementById('graficaFechaRecibido');
        myChartGraficaFechaRecibido = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
                }
            },
            legend: {},
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: capas
            },
            series: data
        };
        option && myChartGraficaFechaRecibido.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaFechaContrarecibo(clientes, deudores, capas_aging, tipos_anomalia, anomalias, divisiones_) {
        let data_ = `dashboardd/getDataGraficaFechaContrarecibo/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas_aging=="")?null:capas_aging}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones_=="")?null:divisiones_}`
        // Mostramos el loader
        $("#loaderGraficaFechaContrarecibo").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaFechaContrarecibo").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaFechaContrarecibo").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaFechaContrarecibo").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaFechaContrarecibo").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaFechaContrarecibo").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaFechaContrarecibo").removeClass("d-none");
        let data = [];
        let capas = [];
        let divisiones = [];
        for (let item of response) {
            capas.push(item.capa);
            divisiones.push(item.division)
        }
        const dataCapas = new Set(capas);
        capas = [...dataCapas];
        const dataDivisiones = new Set(divisiones);
        divisiones = [...dataDivisiones]
        for (let division of divisiones) {
            let values = []; // valores de la division por cada capa
            for (let capa_ of capas) {
                let resultado = response.filter(item => item.capa == capa_ && item.division == division);
                if (resultado.length == 0) {
                    values.push(0);
                } else {
                    values.push(Number(resultado[0].sum).toFixed() / 1000)
                }
            }
            data.push({
                name: division,
                type: 'bar',
                stack: 'total',
                label: {
                    show: false
                },
                emphasis: {
                    focus: 'series'
                },
                data: values
            })
        }
        let chartDom = document.getElementById('graficaFechaContrarecibo');
        myChartGraficaFechaContrarecibo = echarts.init(chartDom);
        let option;
        option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
                }
            },
            legend: {},
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: capas
            },
            series: data
        };
        option && myChartGraficaFechaContrarecibo.setOption(option, {
            notMerge: true
        });
    }




    /** Carga de selects */

    async function cargarClientes() {
        $("#selectCliente").empty();
        let peticion = await fetch(url + "dashboardd/getClientes");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = item.id_c_cliente;
            option.text = item.razon_social_cliente
            $("#selectCliente").append(option)
        }
        console.log('cargando clientes ...');
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

    async function cargarCapaAgin() {
        $("#selectCapaAgin").empty();
        let peticion = await fetch(url + "dashboardd/getCapaAgin");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = `'${item.capa}'`;
            option.text = item.capa
            $("#selectCapaAgin").append(option)
        }
        console.log('cargando capa aging ...');
    }

    async function cargarTipoAnomalias() {
        $("#selectTipoAnomalia").empty();
        let peticion = await fetch(url + "dashboardd/getTiposAnomalia");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = item.id_c_tipo_anomalia;
            option.text = item.descripcion_tipo_anomalia
            $("#selectTipoAnomalia").append(option)
        }
        console.log('cargando tipos de anomalia ...');
    }

    async function cargarAnomalias() {
        $("#selectAnomalias").empty();
        let peticion = await fetch(url + "dashboardd/getAnomalias");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = item.id_c_anomalia;
            option.text = item.descripcion_anomalia
            $("#selectAnomalias").append(option)
        }
        console.log('cargando anomalias ...');
    }

    async function cargarDivisiones() {
        $("#selectDivision").empty();
        let peticion = await fetch(url + "dashboardd/getSelectDivisiones");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = `'${item.descripcion_cliente}'`;
            option.text = item.descripcion_cliente
            $("#selectDivision").append(option)
        }
        console.log('cargando divisiones ...');
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

    /** Aplicar filtros */

    function aplicarFiltros() {

        let clientes = $("#selectCliente").val().toString();
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let tipos_anomalia = $("#selectTipoAnomalia").val().toString();
        let anomalias = $("#selectAnomalias").val().toString();
        let divisiones = $("#selectDivision").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        updateMontoPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateFacturasPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateTotalDeudores(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateTotalDivisiones(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);

        renderGraficaFechaDocumento(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        renderGraficaFechaRecibido(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        renderGraficaFechaContrarecibo(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);

    }

    cargarClientes();
    if (resp_sanofi) {
        cargarDivisionesZonas()
    }else{
        cargarDivisiones();
    }
    /* cargarDivisiones(); */
    cargarDeudores();
    cargarCapaAgin();
    cargarTipoAnomalias();
    cargarAnomalias();


    aplicarFiltros();

    /** Inicializacion de plugin select2 */

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })
});