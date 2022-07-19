$(function () {

    let url = servidor;
    let controller = new AbortController();

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
    }, function (start, end, label) {

        fecha_inicia = start.format('YYYY-MM-DD');
        fecha_final = end.format('YYYY-MM-DD');

    });

    $("#btnEliminarFiltros").on('click', function () {

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

    $("#btnAplicarFiltros").on('click', function () {
        controller.abort();
        controller = new AbortController();
        aplicarFiltros();
    })

    $("#exportarDatos").on('click', function () {
        exportarDatos();
    })
    $(".redireccionMenu").on('click', function () {
        controller.abort();
    });

    //Btn exportar datos
    function exportarDatos() {
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let clientes = $("#selectCliente").val().toString();

        let peticion = url + `dashboard/exportarCobranza/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`
        var req = new XMLHttpRequest();
        var titulo, texto, icono;
        req.open('GET', peticion, true);
        req.responseType = 'blob';
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        swal({
            icon: 'info',
            title: 'Procesando',
            text: 'Se esta generando el archivo, espere un momento...',
            closeOnClickOutside: false,
            closeOnEsc: false,
            allowOutsideClick: false,
            buttons: false
        });
        req.onload = function (e) {
            if (req.readyState === 4 && req.status === 200) {
                var contenidoEnBlob = req.response;
                var link = document.createElement('a');
                link.href = (window.URL || window.webkitURL).createObjectURL(contenidoEnBlob);
                link.download = "Cobranza-" + fecha_inicia + " al " + fecha_final;
                var clicEvent = new MouseEvent('click', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': true
                });
                link.dispatchEvent(clicEvent);
                titulo = "Éxito"; texto = "Se genero correctamente el archivo!"; icono = "success";
            } else if (req.readyState === 4 && req.status === 500) {
                titulo = "Error de servidor!"; texto = "No se pudo generar el archivo, vuelva a intentarlo!, sí el problema persiste comuniquese con sistemas"; icono = "error";
            } else {
                // alert(" No es posible acceder al archivo, probablemente no existe.");
                titulo = "Error al recopilar la información"; texto = "No es posible acceder al archivo, probablemente no existe"; icono = "info";
            }
            swal({
                icon: icono,
                title: titulo,
                text: texto,
                closeOnClickOutside: false,
                closeOnEsc: false,
                allowOutsideClick: false,
                buttons: false,
                timer: 3000
            });
        };
        req.send(null);

    }

    /** Carga de estadisticas (tarjetas) */


    async function updateMontoCobrado(deudores, capas, clientes) {
        const signal = controller.signal;
        try {
            let data = `dashboard/getMontoCobrado/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`
            $("#monto_cobrado").text("Cargando ...")
            let peticion = await fetch(url + data, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response.monto_cobrado) / 1000);
            let currencyString = myNumeral.format('$0,0.00');
            $("#monto_cobrado").text(currencyString + ' (Miles de pesos)')
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function updateFacturacionCobradas(deudores, capas, clientes) {
        const signal = controller.signal;
        try {
            let data = `dashboard/getFacturacionCobradas/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`
            $("#facturacion_cobradas").text("Cargando ...")
            let peticion = await fetch(url + data, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response.facturas_cobradas));
            let currencyString = myNumeral.format('0,0');
            $("#facturacion_cobradas").text(currencyString)
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartMontoRecuperadoDeudor.resize();
        myChartPagoCapaAging.resize();
        myChartRecuentoFactura.resize();
        myChartMoratoriaPago.resize();

    }

    async function renderGraficaPagoCapaAging(deudores, capas__, clientes) {
        const signal = controller.signal;
        try {
            let data_ = `dashboard/getDataGraficaPagoCapaAging/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas__=="")?null:capas__}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaPagoCapaAging").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaPagoCapaAging").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaPagoCapaAging").addClass('d-none');
            let peticion = await fetch(url + data_, { signal });
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

            // let altura = $("#tblMontoRecuperadoDeudor_wrapper").height();
            // $("#divGraficaPagoCapaAging").css('height', `${altura}px`)
            // // $("#graficaPagoCapaAging").css('height', `${altura}px`)
            let data = [];
            let labels = [];
            for (let item of response) {
                data.push(Number(item.monto / 1000).toFixed(2));
                labels.push(item.capa)
            }
            let chartDom = document.getElementById('graficaPagoCapaAging');
            myChartPagoCapaAging = echarts.init(chartDom);
            let option;
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        // Use axis to trigger tooltip
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
                    axisLabel: {
                        rotate: 30
                    },
                    data: labels
                },
                yAxis: {

                    type: 'value'

                },
                series: [{
                    name: 'Monto',
                    type: 'bar',
                    stack: 'Total',
                    //radius: '50%',
                    label: {
                        show: true,
                        position: 'top'
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
            option && myChartPagoCapaAging.setOption(option, {
                notMerge: true
            });
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function renderGraficaPagoCapaAgingRegion(deudores, capas__, clientes) {
        const signal = controller.signal;
        try {
            let data_ = `dashboard/getDataGraficaPagoCapaAgingRegion/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas__=="")?null:capas__}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaPagoCapaAgingRegion").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaPagoCapaAgingRegion").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaPagoCapaAgingRegion").addClass('d-none');
            let peticion = await fetch(url + data_, { signal });
            let response = await peticion.json();
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaPagoCapaAgingRegion").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaPagoCapaAgingRegion").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaPagoCapaAgingRegion").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaPagoCapaAgingRegion").removeClass("d-none");
            // let altura = $("#tblMontoRecuperadoDeudor_wrapper").height();
            // $("#divGraficaPagoCapaAgingRegion").css('height', `${altura}px`)
            // $("#graficaPagoCapaAgingRegion").css('height', `${altura}px`)
            let data = [];
            let labels = [];
            for (let item of response) {
                data.push(Number(item.monto / 1000).toFixed(2));
                labels.push(item.region)
            }
            let chartDom = document.getElementById('graficaPagoCapaAgingRegion');
            myChartPagoCapaAging = echarts.init(chartDom);
            let option;
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        // Use axis to trigger tooltip
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
                    axisLabel: {
                        rotate: 30
                    },
                    data: labels
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Monto',
                    type: 'bar',
                    stack: 'Total',
                    //radius: '50%',
                    label: {
                        show: true,
                        position: 'top'
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
            option && myChartPagoCapaAging.setOption(option, {
                notMerge: true
            });
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function renderGraficaMontoRecuperadoDeudor(deudores, capas, clientes) {
        const signal = controller.signal;
        try {
            let data_ = `dashboard/getDataGraficaMontoRecuperadoDeudor/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaMontoRecuperadoDeudor").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaMontoRecuperadoDeudor").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaMontoRecuperadoDeudor").addClass('d-none');
            let peticion = await fetch(url + data_, { signal });
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
                data.push(Number(item.pago / 1000).toFixed(2));
                labels.push(item.deudor)
            }
            let chartDom = document.getElementById('graficaMontoRecuperadoDeudor');
            myChartMontoRecuperadoDeudor = echarts.init(chartDom);
            let option;
            option = {
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: labels
                },
                // Declare several bar series, each will be mapped
                // to a column of dataset.source by default.
                series: [{
                    name: 'Monto recuperado por deudor',
                    type: 'bar',
                    stack: 'Total',
                    //radius: '50%',
                    label: {
                        show: true,
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
            option && myChartMontoRecuperadoDeudor.setOption(option, {
                notMerge: true
            });
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function tablaMontoCobradoPorDeudor() {
        const signal = controller.signal;
        try {
            // console.log("Cargando tabla...");
            deudores = $("#selectDeudor").val().toString();
            capas = $("#selectCapaAgin").val().toString();
            clientes = $("#selectCliente").val().toString();
            let peticion = await fetch(url + `dashboard/getDataGraficaMontoRecuperadoDeudor/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`, { signal })
            let response = await peticion.json();
            $('#tblMontoRecuperadoDeudor').DataTable().destroy();
            $('#tblMontoRecuperadoDeudor').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                "pageLength": 5,
                "lengthMenu": [[5, 10, -1], [5, 10, "All"]],
                data: response,
                "columns": [
                    { "data": "deudor" },
                    {
                        "data": "pago",
                        render: function (data, type) {
                            var number = $.fn.dataTable.render.number(',', '.', 2, '$').display(data);
                            return number;
                        }
                    }
                ]
            });
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function renderGraficaRecuentoFactura(deudores, capas, clientes) {
        const signal = controller.signal;
        try {
            let data_ = `dashboard/getDataGraficaRecuentoFactura/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaRecuentoFactura").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaRecuentoFactura").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaRecuentoFactura").addClass('d-none');
            let peticion = await fetch(url + data_, { signal });
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
            let data2 = [];
            let labels = [];
            for (let item of response) {
                data.push(Number(item.conteofacturas).toFixed());
                labels.push(item.mes + " " + item.anio);
                data2.push(Number(item.monto_cobrado).toFixed(2))
            }
            let chartDom = document.getElementById('graficaRecuentoFactura');
            myChartRecuentoFactura = echarts.init(chartDom);
            let option;
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: labels
                    }
                ],
                yAxis: [
                    { type: 'value' },
                    {
                        type: 'value',
                        nombre: 'Importe Documento',
                        axisLabel: {
                            formatter: '${value}'
                        }
                    }
                ],
                series: [
                    {
                        name: 'Recuento de Factura',
                        type: 'bar',
                        // barWidth: '60%',
                        label: {
                            show: true,
                            position: 'inside'
                        },
                        data: data
                    },
                    {
                        name: 'Monto cobrado $',
                        type: 'line',
                        label: {
                            show: true,
                            position: 'top',
                            formatter: '${c}'
                        },
                        yAxisIndex: 1,
                        data: data2
                    }
                ]
            };
            option && myChartRecuentoFactura.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    /** Carga de selects */

    async function cargarClientes() {
        const signal = controller.signal;
        try {
            $("#selectCliente").empty();
            let peticion = await fetch(url + "dashboard/getClientes", { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_cliente;
                option.text = item.razon_social_cliente
                $("#selectCliente").append(option)
            }
            console.log('cargando clientes ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function cargarDeudores() {
        const signal = controller.signal;
        try {
            $("#selectDeudor").empty();
            let peticion = await fetch(url + "dashboard/getDeudores", { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_deudor;
                option.text = item.razon_social_deudor
                $("#selectDeudor").append(option)
            }
            console.log('cargando deudores ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function cargarCapaAgin() {
        const signal = controller.signal;
        try {
            $("#selectCapaAgin").empty();
            let peticion = await fetch(url + "dashboard/getCapaAginCobranza", { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = `'${item.capa}'`;
                option.text = item.capa
                $("#selectCapaAgin").append(option)
            }
            console.log('cargando capa aging ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    /** Aplicar filtros */

    function aplicarFiltros() {

        // let clientes = $("#selectCliente").val().toString();
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let clientes = $("#selectCliente").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        updateMontoCobrado(deudores, capas, clientes);
        updateFacturacionCobradas(deudores, capas, clientes);

        renderGraficaMontoRecuperadoDeudor(deudores, capas, clientes);
        renderGraficaPagoCapaAging(deudores, capas, clientes);
        renderGraficaPagoCapaAgingRegion(deudores, capas, clientes);
        renderGraficaRecuentoFactura(deudores, capas, clientes);
        tablaMontoCobradoPorDeudor();

    }

    $("#exportarDatosListaBloqueos").on('click', function () {
        exportarListaBloqueos();
    });
    
    function exportarListaBloqueos() {
    let peticion = url + `dashboard/listabloqueos`
    var req = new XMLHttpRequest();
    var titulo, texto, icono;
    req.open('GET', peticion, true);
    req.responseType = 'blob';
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    swal({
        icon: 'info',
        title: 'Procesando',
        text: 'Se esta generando el archivo, espere un momento...',
        closeOnClickOutside: false,
        closeOnEsc: false,
        allowOutsideClick: false,
        buttons: false
    });
    req.onload = function (e) {
        if (req.readyState === 4 && req.status === 200) {
            var contenidoEnBlob = req.response;
            var link = document.createElement('a');
            link.href = (window.URL || window.webkitURL).createObjectURL(contenidoEnBlob);
            link.download = "Lista de bloqueo Peñafiel";
            var clicEvent = new MouseEvent('click', {
                'view': window,
                'bubbles': true,
                'cancelable': true
            });
            link.dispatchEvent(clicEvent);
            titulo = "Éxito"; texto = "Se genero correctamente el archivo!"; icono = "success";
        } else if (req.readyState === 4 && req.status === 500) {
            titulo = "Error de servidor!"; texto = "No se pudo generar el archivo, vuelva a intentarlo!, sí el problema persiste comuniquese con sistemas"; icono = "error";
        } else {
            // alert(" No es posible acceder al archivo, probablemente no existe.");
            titulo = "Error al recopilar la información"; texto = "No es posible acceder al archivo, probablemente no existe"; icono = "info";
        }
        swal({
            icon: icono,
            title: titulo,
            text: texto,
            closeOnClickOutside: false,
            closeOnEsc: false,
            allowOutsideClick: false,
            buttons: false,
            timer: 3000
        });
    };
    req.send(null);

    }

    // tablaMontoCobradoPorDeudor();
    if (count_clientes > 1) {
        cargarClientes();
    }
    cargarDeudores();
    cargarCapaAgin();
    aplicarFiltros();

    /** Inicializacion de plugin select2 */

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })

});