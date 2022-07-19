$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartSaldoPorEstatus = null;
    let myChartSaldoPorDivision = null;
    let myChartDSO = null;


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
        let myNumeral = numeral(Number(response.monto_por_cobrar) / 1000);
        let currencyString = myNumeral.format('$0,0.00');
        $("#monto_por_cobrar").text(currencyString + ' K')
    }

    async function updateFacturasPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getFacturasPorCobrar/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#facturas_por_cobrar").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.facturas_por_cobrar));
        let currencyString = myNumeral.format('0,0');
        $("#facturas_por_cobrar").text(currencyString)
    }

    async function updateTotalDeudores(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getTotalDeudores/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#deudores").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.total_deudores));
        let currencyString = myNumeral.format('0,0');
        $("#deudores").text(currencyString)
    }

    async function updateTotalDivisiones(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data = `dashboardd/getDivisiones/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        $("#divisiones").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.total_division));
        let currencyString = myNumeral.format('0,0');
        $("#divisiones").text(currencyString)
    }

    async function renderGraficaSaldoPorDivision(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data_ = `dashboardd/getDataGraficaSaldoPorDivision/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaSaldoPorDivision").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaSaldoPorDivision").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaSaldoPorDivision").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
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
        //let colors = ['#f56954', '#00a65a', '#f39c12', 'rgb(255, 99, 132)', 'rgb(75, 192, 192)', 'rgb(255, 205, 86)', 'rgba(60,141,188,0.9)'];
        for (let item of response) {
            let myNumeral = numeral(Number(item.saldo).toFixed());
            let currencyString = myNumeral.format('$0,0.00');
            data.push({
                value: Number(item.saldo / 1000).toFixed(2),
                name: item.division
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
                name: 'División',
                type: 'pie',
                radius: '70%',
                data: data,
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };
        option && myChartSaldoPorDivision.setOption(option);
    }

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartSaldoPorDivision.resize();
        myChartSaldoPorEstatus.resize();
        myChartDSO.resize();

    }

    async function renderGraficaSaldoPorEstatus(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
        let data_ = `dashboardd/getDataGraficaSaldoPorEstatus/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        // Mostramos el loader
        $("#loaderGraficaSaldoPorEstatus").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaSaldoPorEstatus").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaSaldoPorEstatus").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
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
        console.log(data);
        let chartDom = document.getElementById('graficaSaldoPorEstatus');
        myChartSaldoPorEstatus = echarts.init(chartDom);
        let option;
        option = {
            legend: {},
            tooltip: {},
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
                name: 'Tipo de Anomalía',
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
        option && myChartSaldoPorEstatus.setOption(option);
    }

    async function renderTablaTop20DeudoresConSaldoVencido(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones) {
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
        let data_ = `dashboardd/getDataTablaTop20DeudoresSaldoVencido/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}`
        let peticion = await fetch(url + data_);
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
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${Number(item.porcentaje).toFixed()}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                ${Number(item.porcentaje).toFixed()}%
                            </div>
                        </div>
                    </td>
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
    }

    async function renderDSO(clientes, deudores, capas, tipos_anomalia, anomalias) {
        // Mostramos el loader
        $("#loaderGraficaDSO").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaDSO").addClass("d-none");
        let data_ = `dashboardd/getDSO/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}`
        let peticion = await fetch(url + data_);
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
                    offsetCenter: [0, '-15%'],
                    fontSize: 60,
                    fontWeight: 'bolder',
                    formatter: '{value}',
                    color: 'auto'
                },
                data: [{
                    value: Number(response.dso).toFixed()
                }]
            }, ]
        };
        option && myChartDSO.setOption(option);
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

        renderGraficaSaldoPorDivision(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        renderGraficaSaldoPorEstatus(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones)

        renderTablaTop20DeudoresConSaldoVencido(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones)
        renderDSO(clientes, deudores, capas, tipos_anomalia, anomalias)

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