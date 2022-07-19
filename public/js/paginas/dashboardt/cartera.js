$(function () {

    let url = servidor;
    let controller = new AbortController();

    let fecha_inicia = moment().format('2018-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartSaldoPorDivision = null;
    let myChartSaldoPorEstatus = null;
    let myChartSaldoPorCapaAging = null;
    let myChartGraficaSaldoPorRegion = null;
    let myChartKPI = null;
    let myChartDSO = null;
    function formatoMoneda(value) {
        return new Intl.NumberFormat("es-MX", {style: "currency", currency: "MXN"}).format(value);
    }
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

        $("#selectDeudor").val("");
        $("#selectDeudor").trigger("change");

        $("#selectCapaAgin").val("");
        $("#selectCapaAgin").trigger("change");

        $("#selectTipoAnomalia").val("");
        $("#selectTipoAnomalia").trigger("change");

        $("#selectAnomalias").val("");
        $("#selectAnomalias").trigger("change");

        fecha_inicia = moment().format('2018-01-01');
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
    });
    $(".redireccionMenu").on('click', function () {
        controller.abort();
    });

    /* Cargar select */
    async function cargarDeudores() {
        const signal = controller.signal;
        try {
            $("#selectDeudor").empty();
            let peticion = await fetch(url + "dashboardt/getDeudores", { signal });
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
            let peticion = await fetch(url + "dashboardt/getCapaAgin", { signal });
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

    async function cargarTipoAnomalias() {
        const signal = controller.signal;
        try {
            $("#selectTipoAnomalia").empty();
            let peticion = await fetch(url + "dashboardt/getTiposAnomalia", { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_tipo_anomalia;
                option.text = item.descripcion_tipo_anomalia
                $("#selectTipoAnomalia").append(option)
            }
            console.log('cargando tipos de anomalia ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function cargarAnomalias() {
        const signal = controller.signal;
        try {
            $("#selectAnomalias").empty();
            let peticion = await fetch(url + "dashboardt/getAnomalias", { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_anomalia;
                option.text = item.descripcion_anomalia
                $("#selectAnomalias").append(option)
            }
            console.log('cargando anomalias ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    /* Inicio targets */
    async function updateMontoPorCobrar(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            let data = `dashboardt/getMontoPorCobrar/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            $("#monto_por_cobrar").text("Cargando ...")
            let peticion = await fetch(url + data, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response.monto_por_cobrar) / 1000);
            let currencyString = myNumeral.format('$0,0.0');
            $("#monto_por_cobrar").text(currencyString);
            $("#milespesos").text("(Miles de pesos)");
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    async function updateFacturasPorCobrar(deudores, capas, tipos_anomalia, anomalias) {
        let data = `dashboardt/getFacturasPorCobrar/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
        $("#facturas_por_cobrar").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.facturas_por_cobrar));
        let currencyString = myNumeral.format('0,0');
        $("#facturas_por_cobrar").text(currencyString)
    }
    async function updateTotalDeudores(deudores, capas, tipos_anomalia, anomalias) {
        let data = `dashboardt/getTotalDeudores/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
        $("#deudores").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.total_deudores));
        let currencyString = myNumeral.format('0,0');
        $("#deudores").text(currencyString)
    }
    /* Fin targets */

    /* Exportar datos */
    function exportarDatos() {
        deudores = $("#selectDeudor").val().toString();
        capas = $("#selectCapaAgin").val().toString();
        tipos_anomalia = $("#selectTipoAnomalia").val().toString();
        anomalias = $("#selectAnomalias").val().toString();
        divisiones = $("#selectDivision").val().toString();
        let peticion = url + `dashboardt/getexportar/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
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
                link.download = "Cartera-" + fecha_inicia + " al " + fecha_final;
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

    /* Inicio Gráficas */
    // Saldo Documento por Status Vencimiento LISTO
    async function renderGraficaSaldoPorDivision(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            let data_ = `dashboardt/getEstatusVencimiento/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            // Mostramos el loader
            $("#loaderGraficaSaldoPorDivision").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaSaldoPorDivision").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaSaldoPorDivision").addClass('d-none');
            let peticion = await fetch(url + data_, { signal });
            let response = await peticion.json();
            // console.log(response);
            if (response.length == 0 || response[0]['saldosinvencer'] == null && response[0]['saldovencido'] == null) {
                // Mostramos el no data
                $("#noDataGraficaSaldoPorDivision").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaSaldoPorDivision").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaSaldoPorDivision").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaSaldoPorDivision").removeClass("d-none");
            let labels = [];
            let data = [];
            //let colors = ['#f50254', '#00a65a', '#f39c12', 'rgb(255, 99, 132)', 'rgb(75, 192, 192)', 'rgb(255, 205, 86)', 'rgba(60,141,188,0.9)'];
            //let colors = ['#58B5FB', '#052240', '#7A65F2', '#FF625B'];
            for (let item of response) {
                let suma = parseFloat(item.saldosinvencer) + parseFloat(item.saldovencido);
                let porcentajeSaldoSinVencer = numeral(Number((item.saldosinvencer / suma) * 100).toFixed(2)).format('0,0.00');
                let porcentajeSaldoVencido = numeral(Number((item.saldovencido / suma) * 100).toFixed(2)).format('0,0.00');
                data.push({
                    value: Number(item.saldosinvencer / 1000).toFixed(2),
                    name: 'Sin vencer - ' + porcentajeSaldoSinVencer + '%'
                }, {
                    value: Number(item.saldovencido / 1000).toFixed(2),
                    name: 'Vencido - ' + porcentajeSaldoVencido + '%'
                });
            }
            let chartDom = document.getElementById('graficaSaldoPorDivision');
            myChartSaldoPorDivision = echarts.init(chartDom);
            let option;
            option = {
                title: {
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [{
                    name: 'Vencimiento',
                    type: 'pie',
                    tooltip: {
                        valueFormatter: value => formatoMoneda(value)
                    },
                    radius: '80%',
                    data: data,
                    emphasis: {
                        //scale: true,
                        //scaleSize: 20,
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            };
            myChartSaldoPorDivision.clear();
            option && myChartSaldoPorDivision.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    async function renderGraficaSaldoPorCapaAging(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            let data_ = `dashboardt/getSaldoCapaAging/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            // Mostramos el loader
            $("#loaderGraficaSaldoPorCapaAging").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaSaldoPorCapaAging").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaSaldoPorCapaAging").addClass('d-none');
            //Realizamos la petición
            let peticion = await fetch(url + data_, { signal });
            //Convertimos a formato Json la respuesta de la peticion
            let response = await peticion.json();
            // console.log(response);
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaSaldoPorCapaAging").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaSaldoPorCapaAging").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaSaldoPorCapaAging").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaSaldoPorCapaAging").removeClass("d-none");
            let data = [];
            let labels = [];

            for (let item of response) {
                data.push(Number(item.saldodocumento / 1000).toFixed(2));
                labels.push(item.asignstatus);
            }
            // console.log(data);
            let chartDom = document.getElementById('graficaSaldoPorCapaAging');
            myChartSaldoPorCapaAging = echarts.init(chartDom);
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
                    type: 'value',
                    axisLabel: {
                        formatter: '${value}'
                    }
                    //boundaryGap: [0, 0.01],
                    // data: labels
                },
                yAxis: {
                    type: 'category',
                    data: labels
                },
                series: [{
                    name: 'Capa Aging',
                    type: 'bar',
                    tooltip: {
                        valueFormatter: value => formatoMoneda(value)
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
            myChartSaldoPorCapaAging.clear();
            option && myChartSaldoPorCapaAging.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    // Saldo por estatus
    async function renderGraficaSaldoPorEstatus(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            let data_ = `dashboardt/getDataGraficaSaldoPorEstatus/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            // Mostramos el loader
            $("#loaderGraficaSaldoPorEstatus").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaSaldoPorEstatus").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaSaldoPorEstatus").addClass('d-none');
            //Realizamos la petición
            let peticion = await fetch(url + data_, { signal });
            //Convertimos a formato Json la respuesta de la peticion
            let response = await peticion.json();
            // console.log(response);
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaSaldoPorEstatus").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaSaldoPorEstatus").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaSaldoPorEstatus").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaSaldoPorEstatus").removeClass("d-none");
            let data = [];
            let labels = [];
            let i = 0;
            for (let item of response) {
                data.push(Number(item.saldo / 1000).toFixed(2));
                labels.push(item.anomalia);
            }
            // console.log(data);
            let chartDom = document.getElementById('graficaSaldoPorEstatus');
            myChartSaldoPorEstatus = echarts.init(chartDom);
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
                    type: 'value',
                    axisLabel: {
                        formatter: '${value}'
                    }
                    //boundaryGap: [0, 0.01],
                    // data: labels
                },
                yAxis: {
                    type: 'category',
                    data: labels
                },
                series: [{
                    name: 'Tipo de anomalía',
                    type: 'bar',
                    tooltip: {
                        valueFormatter: value => formatoMoneda(value)
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
            myChartSaldoPorEstatus.clear();
            option && myChartSaldoPorEstatus.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    // DSO %
    async function renderDSO(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            // Mostramos el loader
            $("#loaderGraficaDSO").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaDSO").addClass("d-none");
            let data_ = `dashboardt/getDSO/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            let peticion = await fetch(url + data_, { signal });
            let response = await peticion.json();
            // Ocultamos el loader
            $("#loaderGraficaDSO").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaDSO").removeClass("d-none");
            let chartDom = document.getElementById('graficaDSO');
            myChartDSO = echarts.init(chartDom);
            let option;
            option = {
                series: [{
                    type: 'gauge',
                    center: ['50%', '60%'],
                    startAngle: 200,
                    endAngle: -20,
                    min: 0,
                    max: 100,
                    splitNumber: 10,
                    itemStyle: {
                        color: '#FFAB91'
                    },
                    progress: {
                        show: true,
                        width: 30
                    },
                    pointer: {
                        show: false
                    },
                    axisLine: {
                        lineStyle: {
                            width: 30
                        }
                    },
                    axisTick: {
                        distance: -45,
                        splitNumber: 5,
                        lineStyle: {
                            width: 2,
                            color: '#999'
                        }
                    },
                    splitLine: {
                        distance: -52,
                        length: 14,
                        lineStyle: {
                            width: 3,
                            color: '#999'
                        }
                    },
                    axisLabel: {
                        distance: -20,
                        color: '#999',
                        fontSize: 20
                    },
                    anchor: {
                        show: false
                    },
                    title: {
                        show: false
                    },
                    detail: {
                        valueAnimation: true,
                        width: '60%',
                        lineHeight: 40,
                        borderRadius: 8,
                        offsetCenter: [0, '-10%'],
                        fontSize: 60,
                        fontWeight: 'bolder',
                        formatter: '{value}',
                        color: 'auto'
                    },
                    data: [{
                        value: Number(response.dso).toFixed()
                    }]
                },]
            };
            myChartDSO.clear();
            option && myChartDSO.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    // Top 20 deudores saldo vencido
    async function renderTablaTop20DeudoresConSaldoVencido(deudores, capas, tipos_anomalia, anomalias) {
        const signal = controller.signal;
        try {
            $("#bodyTopDeudores").html("");
            // Mostramos loading
            $("#bodyTopDeudores").append(`
            <tr>
                <td colspan="5">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </td>
            </tr>
        `);
            let data_ = `dashboardt/getDataTablaTop20DeudoresSaldoVencido/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
            let peticion = await fetch(url + data_, { signal });
            let response = await peticion.json();
            $("#bodyTopDeudores").html("");
            let i = 1;
            for (let item of response) {
                let myNumeral = numeral(Number(item.saldo / 1000).toFixed(2));
                let currencyString = myNumeral.format('$0,0.00');
                $("#bodyTopDeudores").append(`
                <tr>
                    <td>${i}</td>
                    <td>${item.razon_social_deudor}</td>
                    <td>${currencyString}</td>
                    <td>${item.facturas}</td>
                </tr>
            `);
                i++;
            }
            if (i == 1) {
                $("#bodyTopDeudores").append(`
                <tr class="text-center">
                    <td colspan="5">No hay deudores con saldo vencido</td>
                </tr>
            `);
                i++;
            }
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    /* Fin Gráficas */
    function aplicarFiltros() {
        // let clientes = $("#selectCliente").val().toString();
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let tipos_anomalia = $("#selectTipoAnomalia").val().toString();
        let anomalias = $("#selectAnomalias").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))
        // Targets
        updateMontoPorCobrar(deudores, capas, tipos_anomalia, anomalias);
        updateFacturasPorCobrar(deudores, capas, tipos_anomalia, anomalias);
        updateTotalDeudores(deudores, capas, tipos_anomalia, anomalias);
        /* Gráficas */
        renderGraficaSaldoPorDivision(deudores, capas, tipos_anomalia, anomalias);
        renderGraficaSaldoPorCapaAging(deudores, capas, tipos_anomalia, anomalias);
        renderGraficaSaldoPorEstatus(deudores, capas, tipos_anomalia, anomalias);
        renderDSO(deudores, capas, tipos_anomalia, anomalias);
        renderTablaTop20DeudoresConSaldoVencido(deudores, capas, tipos_anomalia, anomalias);
    }

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