$(function() {

    let url = servidor;

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    let myChartMontoRecuperadoDeudor = null;
    let myChartPagoCapaAging = null;
    let myChartRecuentoFactura = null;
    let myChartMoratoriaPago = null;
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

        $('#fechaDeposito').prop("checked", false)
        $('#fechaAplicacion').prop("checked", true)

    })

    $("#btnAplicarFiltros").on('click', function() {

        aplicarFiltros();

    })

    $("#exportarDatos").on('click', function() {
        exportarDatos();
    });

    //Btn exportar datos
    function exportarDatos() {
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let fechaFiltrar = $('input[name=fechaFiltrar]:checked').val();
        let peticion = url + `dashboardt/getexportarcobranza/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`
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
                link.download = "Cobranza-" + fecha_inicia + " al " + fecha_final;
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


    async function updateMontoCobrado(deudores, capas, fechaFiltrar) {
        let data = `dashboardt/getMontoCobrado/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#monto_cobrado").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.monto_cobrado) / 1000);
        let currencyString = myNumeral.format('$0,0.00');
        $("#monto_cobrado").text(currencyString + ' K')
    }

    async function updateFacturacionCobradas(deudores, capas, fechaFiltrar) {
        let data = `dashboardt/getFacturacionCobradas/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`
        $("#facturacion_cobradas").text("Cargando ...")
        let peticion = await fetch(url + data);
        let response = await peticion.json();
        let myNumeral = numeral(Number(response.facturas_cobradas));
        let currencyString = myNumeral.format('0,0');
        $("#facturacion_cobradas").text(currencyString)
    }

    async function renderGraficaMontoRecuperadoDeudor(deudores, capas, fechaFiltrar) {
        let data_ = `dashboardt/getDataGraficaMontoRecuperadoDeudor/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`
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
                    type: 'shadow' // 'shadow' as default; can also be 'line' or 'shadow'
                }
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
                tooltip: {
                    valueFormatter: value => formatoMoneda(value)
                },
                stack: 'Total',
                label: {
                    show: false,
                    position: 'right'
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

    async function renderGraficaPagoCapaAging(deudores, capas_, fechaFiltrar) {
        let data_ = `dashboardt/getDataGraficaPagoCapaAging/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas_=="")?null:capas_}/${(fechaFiltrar=="")?null:fechaFiltrar}`
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
                tooltip: {
                    valueFormatter: value => formatoMoneda(value)
                },
                stack: 'total',
                label: {
                    show: false,
                    position: 'inside'
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

                type: 'value',
                axisLabel: {
                    formatter: '${value}'
                  }
            },
            series: data
        };
        option && myChartPagoCapaAging.setOption(option, {
            notMerge: true
        });
    }

    async function renderGraficaRecuentoFactura(deudores, capas, fechaFiltrar) {
        try {
            let data_ = `dashboardt/getDataGraficaRecuentoFactura/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`
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
                            show: false,
                            position: 'inside'
                        },
                        data: data
                    },
                    {
                        name: 'Monto cobrado $',
                        type: 'line',
                        tooltip: {
                            valueFormatter: value => formatoMoneda(value)
                        },
                        label: {
                            show: false,
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

    async function tablaMontoCobradoPorDeudor() {
        try {
            // console.log("Cargando tabla...");
            deudores = $("#selectDeudor").val().toString();
            capas = $("#selectCapaAgin").val().toString();
            let fechaFiltrar = $('input[name=fechaFiltrar]:checked').val();
            let peticion = await fetch(url + `dashboardt/getDataGraficaMontoRecuperadoDeudor/${fecha_inicia}/${fecha_final}/${(deudores=="")?null:deudores}/${(capas=="")?null:capas}/${(fechaFiltrar=="")?null:fechaFiltrar}`)
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

    async function cargarCapaAgin() {
        $("#selectCapaAgin").empty();
        let peticion = await fetch(url + "dashboardt/getCapaAgin");
        let response = await peticion.json();
        for (let item of response) {
            let option = document.createElement("option")
            option.value = `'${item.capa}'`;
            option.text = item.capa
            $("#selectCapaAgin").append(option)
        }
        console.log('cargando capa aging ...');
    }

    /** Aplicar filtros */

    function aplicarFiltros() {
        let deudores = $("#selectDeudor").val().toString();
        let capas = $("#selectCapaAgin").val().toString();
        let fechaFiltrar = $('input[name=fechaFiltrar]:checked').val();
        console.log(fechaFiltrar);
        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))

        updateMontoCobrado(deudores, capas, fechaFiltrar);
        updateFacturacionCobradas(deudores, capas, fechaFiltrar);

        renderGraficaMontoRecuperadoDeudor(deudores, capas, fechaFiltrar);
        renderGraficaPagoCapaAging(deudores, capas, fechaFiltrar);
        renderGraficaRecuentoFactura(deudores, capas, fechaFiltrar);
        tablaMontoCobradoPorDeudor();

    }
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