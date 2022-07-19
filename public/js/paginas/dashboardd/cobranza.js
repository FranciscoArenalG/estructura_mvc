$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartMontoRecuperadoDeudor = null;
    let myChartPagoCapaAging = null;
    let myChartRecuentoFactura = null;
    let myChartMoratoriaPago = null;

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

        console.log('Eliminando filtros');

        $("#selectCliente").val("");
        $("#selectCliente").trigger("change");

        $("#selectDeudor").val("");
        $("#selectDeudor").trigger("change");

        $("#selectCapaAgin").val("");
        $("#selectCapaAgin").trigger("change");

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
        let divisiones = $("#selectDivision").val().toString();
        let tipos_anomalia = "";
        let anomalias = "";
        let peticion = url + `dashboardd/getexportarcobranza/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
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


    async function updateMontoCobrado(clientes, deudores, capas, divisiones) {
        let data = `dashboardd/getMontoCobrado/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        $("#monto_cobrado").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.monto_cobrado) / 1000);
        let currencyString = myNumeral.format('$0,0.00');
        $("#monto_cobrado").text(currencyString + ' K')
    }

    async function updateFacturacionCobradas(clientes, deudores, capas, divisiones) {
        let data = `dashboardd/getFacturacionCobradas/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        $("#facturacion_cobradas").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.facturas_cobradas));
        let currencyString = myNumeral.format('0,0');
        $("#facturacion_cobradas").text(currencyString)
    }

    async function renderGraficaMontoRecuperadoDeudor(clientes, deudores, capas, divisiones) {
        let data_ = `dashboardd/getDataGraficaMontoRecuperadoDeudor/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaMontoRecuperadoDeudor").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaMontoRecuperadoDeudor").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaMontoRecuperadoDeudor").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaMontoRecuperadoDeudor").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaMontoRecuperadoDeudor").addClass("d-none");
            return false;
        }
        let altura = response.length * 35;
        // Modificamos la altura del grafico
        $("#graficaMontoRecuperadoDeudor").css('height', `${altura}px`)
        // Ocultamos el loader
        $("#loaderGraficaMontoRecuperadoDeudor").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaMontoRecuperadoDeudor").removeClass("d-none");
        let labels = [];
        let data = [];
        for (let item of response) {
            data.push(Number(item.pago/ 1000).toFixed(2) );
            labels.push(item.deudor)
        }
        let chartDom = document.getElementById('graficaMontoRecuperadoDeudor');
        myChartMontoRecuperadoDeudor = echarts.init(chartDom);
        let option;
        option = {
            legend: {},
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'shadow'
                }
            },
            grid: {
                left: '3%',
                right: '2%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: labels
            },
            series: [{
                name: 'Monto recuperado por deudor',
                type: 'bar',
                stack: 'Total',
                label: {
                    show: true,
                    position: 'inside'
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                data: data
            }]
        };
        option && myChartMontoRecuperadoDeudor.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaPagoCapaAging(clientes_, deudores, capas__, divisiones) {
        let data_ = `dashboardd/getDataGraficaPagoCapaAging/${fecha_inicia}/${fecha_final}/${(clientes_=="")?null:clientes_}/${(deudores=="")?null:deudores}/${(capas__=="")?null:capas__}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaPagoCapaAging").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaPagoCapaAging").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaPagoCapaAging").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaPagoCapaAging").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaPagoCapaAging").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaPagoCapaAging").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaPagoCapaAging").removeClass("d-none");
        let data = [];
        let clientes = [];
        let capas = [];
        for (let item of response) {
            capas.push(item.capa);
            clientes.push(item.cliente);
        }
        const dataClientes = new Set(clientes);
        clientes = [...dataClientes];
        const dataCapas = new Set(capas);
        capas = [...dataCapas]
        for (let cliente of clientes) {
            let values = []; // valores de la division por cada capa
            for (let capa_ of capas) {
                let resultado = response.filter(item => item.capa == capa_ && item.cliente == cliente);
                if (resultado.length == 0) {
                    values.push(0);
                } else {
                    values.push(Number(resultado[0].sum / 1000).toFixed(2) )
                }
            }
            data.push({
                name: cliente,
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
        let chartDom = document.getElementById('graficaPagoCapaAging');
        myChartPagoCapaAging = echarts.init(chartDom);
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
                type: 'category',
                data: capas
            },
            yAxis: {

                type: 'value'
            },
            series: data
        };
        option && myChartPagoCapaAging.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaRecuentoFactura(clientes, deudores, capas, divisiones) {
        let data_ = `dashboardd/getDataGraficaRecuentoFactura/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaRecuentoFactura").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecuentoFactura").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecuentoFactura").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecuentoFactura").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecuentoFactura").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecuentoFactura").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecuentoFactura").removeClass("d-none");
        let data = [];
        let labels = [];
        let i = 0;
        for (let item of response) {
            data.push(Number(item.monto_cobrado / 1000).toFixed(2));
            labels.push(item.fecha_aplicacion);
        }
        let chartDom = document.getElementById('graficaRecuentoFactura');
        myChartRecuentoFactura = echarts.init(chartDom);
        let option;
        option = {
            legend: {},
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'shadow'
                }
            },
            xAxis: {
                type: 'category',
                data: labels
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                name: 'Recuento de facturas',
                type: 'line',
                stack: 'Total',
                //radius: '50%',
                label: {
                    show: false,
                    position: 'inside'
                },
                emphasis: {
                    //scale: true,
                    //scaleSize: 20,
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                data: data
            }]
        };
        option && myChartRecuentoFactura.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaMoratoriaPago(clientes, deudores, capas, divisiones) {
        let data_ = `dashboardd/getDataGraficaMoratoriaPago/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaMoratoriaPago").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaMoratoriaPago").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaMoratoriaPago").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaMoratoriaPago").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaMoratoriaPago").addClass("d-none");
            return false;
        }
        let altura = response.length * 35;
        // Modificamos la altura del grafico
        $("#graficaMoratoriaPago").css('height', `${altura}px`)
        // Ocultamos el loader
        $("#loaderGraficaMoratoriaPago").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaMoratoriaPago").removeClass("d-none");
        let data = [];
        let labels = [];
        let i = 0;
        for (let item of response) {
            data.push(Number(item.dias_credito).toFixed());
            labels.push(item.razon_social_deudor);
        }
        let chartDom = document.getElementById('graficaMoratoriaPago');
        myChartMoratoriaPago = echarts.init(chartDom);
        let option;
        option = {
            legend: {},
            tooltip: {},
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: labels
            },
            series: [{
                name: 'Moratoria de pagos',
                type: 'bar',
                stack: 'Total',
                label: {
                    show: true,
                    position: 'inside'
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                data: data
            }]
        };
        option && myChartMoratoriaPago.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaMontoRecuperadoDivision(clientes, deudores, capas, divisiones){
        let data_ = `dashboardd/getDataGraficaMontoRecuperadoDivision/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaMontoRecuperadoDivision").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaMontoRecuperadoDivision").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaMontoRecuperadoDivision").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaMontoRecuperadoDivision").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaMontoRecuperadoDivision").addClass("d-none");
            return false;
        }
        /* let altura = response.length * 35;
        // Modificamos la altura del grafico
        $("#graficaMontoRecuperadoDivision").css('height', `${altura}px`) */
        // Ocultamos el loader
        $("#loaderGraficaMontoRecuperadoDivision").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaMontoRecuperadoDivision").removeClass("d-none");
        let labels = [];
        let data = [];
        for (let item of response) {
            data.push(Number(item.pago/ 1000).toFixed(2) );
            labels.push(item.deudor)
        }
        let chartDom = document.getElementById('graficaMontoRecuperadoDivision');
        myChartMontoRecuperadoDeudor = echarts.init(chartDom);
        let option;
        option = {
            legend: {},
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'shadow'
                }
            },
            grid: {
                left: '3%',
                right: '2%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value'
            },
            yAxis: {
                type: 'category',
                data: labels
            },
            series: [{
                name: 'Monto recuperado por división',
                type: 'bar',
                stack: 'Total',
                label: {
                    show: true,
                    position: 'inside'
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                data: data
            }]
        };
        option && myChartMontoRecuperadoDeudor.setOption(option, {
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
        let divisiones = $("#selectDivision").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        updateMontoCobrado(clientes, deudores, capas, divisiones);
        updateFacturacionCobradas(clientes, deudores, capas, divisiones);

        renderGraficaMontoRecuperadoDeudor(clientes, deudores, capas, divisiones);
        renderGraficaPagoCapaAging(clientes, deudores, capas, divisiones)
        renderGraficaRecuentoFactura(clientes, deudores, capas, divisiones)
        renderGraficaMoratoriaPago(clientes, deudores, capas, divisiones)
        if (resp_sanofi) {
            renderGraficaMontoRecuperadoDivision(clientes, deudores, capas, divisiones);
        }

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

    aplicarFiltros();

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartMontoRecuperadoDeudor.resize();
        myChartPagoCapaAging.resize();
        myChartRecuentoFactura.resize();
        myChartMoratoriaPago.resize();
    }
    /** Inicializacion de plugin select2 */

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })

});