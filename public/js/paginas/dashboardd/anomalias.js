$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartRecuentoFacturaAnomalia = null;
    let myChartRecuentoAnomaliasResponsable = null;
    let myChartSaldoPorAnomalia = null;
    let myChartAnomaliaFrecuente = null;

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

    async function renderGraficaRecuentoFacturaAnomalia(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables) {
        let data_ = `dashboardd/getDataGraficaRecuentoFacturaAnomalia/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}/${(responsables=="")?null:responsables}`
        // Mostramos el loader
        $("#loaderGraficaRecuentoFacturaAnomalia").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaRecuentoFacturaAnomalia").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecuentoFacturaAnomalia").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecuentoFacturaAnomalia").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecuentoFacturaAnomalia").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecuentoFacturaAnomalia").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaRecuentoFacturaAnomalia").removeClass("d-none");
        let labels = [];
        let data = [];
        for (let item of response) {
            data.push({
                value: Number(item.count).toFixed(),
                name: item.descripcion_tipo_anomalia
            });
        }
        let chartDom = document.getElementById('graficaRecuentoFacturaAnomalia');
        myChartRecuentoFacturaAnomalia = echarts.init(chartDom);
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
                name: 'Tipo de anomalía',
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
        option && myChartRecuentoFacturaAnomalia.setOption(option);
    }

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartRecuentoFacturaAnomalia.resize();
        myChartRecuentoAnomaliasResponsable.resize();
        myChartSaldoPorAnomalia.resize();
        myChartAnomaliaFrecuente.resize();

    }

    async function renderGraficaRecuentoAnomaliasResponsable(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables) {
        let data_ = `dashboardd/getDataGraficaRecuentoAnomaliasResponsable/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}/${(responsables=="")?null:responsables}`
        // Mostramos el loader
        $("#loaderGraficaRecuentoAnomaliasResponsable").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaSaldoRecuentoAnomaliasResponsable").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaRecuentoAnomaliasResponsable").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaRecuentoAnomaliasResponsable").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaRecuentoAnomaliasResponsable").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaRecuentoAnomaliasResponsable").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaSaldoRecuentoAnomaliasResponsable").removeClass("d-none");
        let data = [];
        let labels = [];
        let i = 0;
        for (let item of response) {
            data.push(Number(item.total).toFixed());
            labels.push(item.responsable);
        }
        console.log(data);
        let chartDom = document.getElementById('graficaRecuentoAnomaliasResponsable');
        myChartRecuentoAnomaliasResponsable = echarts.init(chartDom);
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
                name: 'Responsables',
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
        option && myChartRecuentoAnomaliasResponsable.setOption(option);
    }

    async function renderGraficaSaldoPorAnomalia(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables) {
        let data_ = `dashboardd/getDataGraficaSaldoPorAnomalia/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}/${(responsables=="")?null:responsables}`
        // Mostramos el loader
        $("#loaderGraficaSaldoPorAnomalia").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaSaldoPorAnomalia").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaSaldoPorAnomalia").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaSaldoPorAnomalia").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaSaldoPorAnomalia").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaSaldoPorAnomalia").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaSaldoPorAnomalia").removeClass("d-none");
        let data = [];
        let labels = [];
        let i = 0;
        for (let item of response) {
            data.push(Number(item.total / 1000).toFixed());
            labels.push(item.descripcion_anomalia);
        }
        console.log(data);
        let chartDom = document.getElementById('graficaSaldoPorAnomalia');
        myChartSaldoPorAnomalia = echarts.init(chartDom);
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
                left: '1%',
                right: '1%',
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
                name: 'Anomalías',
                type: 'bar',
                stack: 'Total',
                //radius: '50%',
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
        option && myChartSaldoPorAnomalia.setOption(option);
    }

    async function renderGraficaAnomaliaFrecuente(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables) {
        let data_ = `dashboardd/getDataGraficaAnomaliaFrecuente/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(tipos_anomalia=="")?null:tipos_anomalia}/${(anomalias=="")?null:anomalias}/${(divisiones=="")?null:divisiones}/${(responsables=="")?null:responsables}`
        // Mostramos el loader
        $("#loaderGraficaAnomaliaFrecuente").removeClass("d-none");
        // Ocultamos la grafica
        $("#divGraficaAnomaliaFrecuente").addClass("d-none");
        // Ocultamos el no data
        $("#noDataGraficaAnomaliaFrecuente").addClass('d-none');
        let peticion = await fetch(url + data_);
        let response = await peticion.json();
        if (response.length == 0) {
            // Mostramos el no data
            $("#noDataGraficaAnomaliaFrecuente").removeClass('d-none');
            // Ocultamos el loader
            $("#loaderGraficaAnomaliaFrecuente").addClass("d-none");
            return false;
        }
        // Ocultamos el loader
        $("#loaderGraficaAnomaliaFrecuente").addClass("d-none");
        // Mostramos la grafica
        $("#divGraficaAnomaliaFrecuente").removeClass("d-none");
        let data = [];
        let labels = [];
        let i = 0;
        for (let item of response) {
            data.push(Number(item.total).toFixed());
            labels.push(item.descripcion_anomalia);
        }
        let chartDom = document.getElementById('graficaAnomaliaFrecuente');
        myChartAnomaliaFrecuente = echarts.init(chartDom);
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
                name: 'Anomalías',
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
        option && myChartAnomaliaFrecuente.setOption(option);
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

    async function cargarResponsables() {
        $("#selectResponsable").empty();
        let peticion = await fetch(url + "dashboardd/getResponsables");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = `'${item.responsable}'`;
            option.text = item.responsable
            $("#selectResponsable").append(option)
        }
        console.log('cargando responsables ...');
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
        let responsables = $("#selectResponsable").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        updateMontoPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateFacturasPorCobrar(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateTotalDeudores(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);
        updateTotalDivisiones(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones);

        renderGraficaRecuentoFacturaAnomalia(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables);
        renderGraficaRecuentoAnomaliasResponsable(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables);
        renderGraficaSaldoPorAnomalia(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables)
        renderGraficaAnomaliaFrecuente(clientes, deudores, capas, tipos_anomalia, anomalias, divisiones, responsables)

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
    cargarResponsables();

    aplicarFiltros();

    /** Inicializacion de plugin select2 */

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })

});