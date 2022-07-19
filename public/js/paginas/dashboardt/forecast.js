$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartForecast = null;
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
    }, function(start, end, label) {

        fecha_inicia = start.format('YYYY-MM-DD');
        fecha_final = end.format('YYYY-MM-DD');

    });

    $("#btnEliminarFiltros").on('click', function() {
        console.log('Eliminando filtros');
        $("#selectDeudor").val("");
        $("#selectDeudor").trigger("change");
        fecha_inicia = moment().format('YYYY-MM-01');
        fecha_final = moment().format('YYYY-MM-DD');
        $('input[name="filtroFechas"]').data('daterangepicker').setStartDate(fecha_inicia);
        $('input[name="filtroFechas"]').data('daterangepicker').setEndDate(fecha_final);
    })

    $("#btnAplicarFiltros").on('click', function() {

        aplicarFiltros();

    })

    /** Carga de estadisticas (tarjetas) */

    async function renderGraficaForecast(deudores) {
        try {
            let data_ = `dashboardt/getDataGraficaForecast/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}`
            // Mostramos el loader
            $("#loaderGraficaForecast").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaForecast").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaForecast").addClass('d-none');
            let peticion = await fetch(url + data_);
            let response = await peticion.json();
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaForecast").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaForecast").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaForecast").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaForecast").removeClass("d-none");
            let data = [];
            /* let data2 = []; */
            let labels = [];
            let meses = ['enero','febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            for (let item of response) {
                data.push(Number(item.sum).toFixed());
                labels.push(item.mes + " " + item.anio + "-"+item.numsemana);
                /* data2.push(Number(item.monto_cobrado).toFixed(2)) */
            }
            let chartDom = document.getElementById('graficaForecast');
            myChartForecast = echarts.init(chartDom);
            let option;
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
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
                        axisLabel: {
                            rotate: 30
                        },
                        data: labels
                    }
                ],
                yAxis: [
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
                        name: 'Importe pagado',
                        type: 'bar',
                        tooltip: {
                            valueFormatter: value => formatoMoneda(value)
                        },
                        data: data
                    }
                ]
            };
            option && myChartForecast.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function tablaForecast() {
        try {
            // console.log("Cargando tabla...");
            deudores = $("#selectDeudor").val().toString();
            let peticion = await fetch(url + `dashboardt/getTablaForecast/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}`)
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
                    { "data": "fecha" },
                    {"data": "deudor"},
                    {
                        "data": "saldo",
                        render: function (data, type) {
                            var number = $.fn.dataTable.render.number(',', '.', 2, '$').display(data);
                            return number;
                        }
                    },
                    {"data": "factura"}
                ]
            });
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    /** Carga de selects */
    async function cargarDeudores() {
        $("#selectDeudor").empty();
        let peticion = await fetch(url + "dashboardt/getDeudores");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = item.id_c_deudor;
            option.text = item.razon_social_deudor
            $("#selectDeudor").append(option)
        }
        console.log('cargando deudores ...');
    }

    /** Aplicar filtros */

    function aplicarFiltros() {
        let deudores = $("#selectDeudor").val().toString();
        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))
        renderGraficaForecast(deudores);
        tablaForecast();
    }
    cargarDeudores();
    aplicarFiltros();

    $(window).on('resize', resize);

    // Resize function
    function resize() {

        // Resize chart
        myChartForecast.resize();
    }
    /** Inicializacion de plugin select2 */

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })

});