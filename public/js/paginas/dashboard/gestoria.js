$(function () {
    let url = servidor;

    let controller = new AbortController();

    let fecha_inicia = moment().format('YYYY-01-01');
    let fecha_final = moment().format('YYYY-MM-DD');

    // INICIO DEL INPUT RANGO DE FECHA
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
                "Do", "Lu", "Mar", "Mie", "Jue", "Vie", "Sab"
            ],
            "monthNames": [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ],
            "firstDay": 1,
        },
        startDate: fecha_inicia,
        endDate: fecha_final
    }, function (start, end, label) {
        fecha_inicia = start.format('YYYY-MM-DD');
        fecha_final = end.format('YYYY-MM-DD');
    });
    // FIN DEL INPUT RANGO DE FECHA

    // INICIO Botones
    $("#btnEliminarFiltros").on('click', function () {
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
    // FIN Botones

    // Exportar datos
    function exportarDatos() {
        let clientes = $("#selectCliente").val().toString();
        let peticion = url + `dashboard/exportarGestoria/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
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
                link.download = "Gestoria-" + fecha_inicia + " al " + fecha_final;
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

    // INICIO Filtros
    function aplicarFiltros() {
        // let deudores = $("#selectDeudor").val().toString();
        let clientes = $("#selectCliente").val().toString();

        $("#labelFiltroFechas").text('Fechas: ' + moment(fecha_inicia).format('DD-MM-YYYY') + " al " + moment(fecha_final).format('DD-MM-YYYY'))
        targetMontoFacturasGestionadas(clientes);
        targetFacturasGestionadas(clientes);
        targetVisitasRealizadas(clientes);

        renderTablaVisitasPorRegion2(clientes);
        renderGraficaVisitasPorRegion(clientes);
        // renderTablaVisitasPorRegion();

        renderGraficaRecuentoFacturaImporteDocumentoAnioMes(clientes);

    }
    // FIN Filtros

    // INICIO Tarjetas
    async function targetMontoFacturasGestionadas(clientes) {
        const signal = controller.signal;
        try {
            let api = `dashboard/getTargetMontoFacturasGestionadas/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            $("#targetMontoFacturasGestionadas").text("Cargando ...")
            let peticion = await fetch(url + api, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response[0]['sum']) / 1000);
            let currencyString = myNumeral.format('$0,0.00');
            $("#targetMontoFacturasGestionadas").text(currencyString + ' K')
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function targetFacturasGestionadas(clientes) {
        const signal = controller.signal;
        try {
            let api = `dashboard/getTargetFacturasGestionadas/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            $("#targetFacturasGestionadas").text("Cargando ...")
            let peticion = await fetch(url + api, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response[0]['factura']));
            let currencyString = myNumeral.format('0,0');
            $("#targetFacturasGestionadas").text(currencyString)
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function targetVisitasRealizadas(clientes) {
        const signal = controller.signal;
        try {
            let api = `dashboard/getTargetVisitasRealizadas/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            $("#targetVisitasRealizadas").text("Cargando ...")
            let peticion = await fetch(url + api, { signal });
            let response = await peticion.json();
            let myNumeral = numeral(Number(response[0]['visitas']));
            let currencyString = myNumeral.format('0,0');
            $("#targetVisitasRealizadas").text(currencyString)
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }
    // FIN Tarjetas

    // INICIO Gráficas
    async function renderGraficaVisitasPorRegion(clientes) {
        const signal = controller.signal;
        try {
            let api = `dashboard/getVisitasPorRegion/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaVisitasPorRegion").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaVisitasPorRegion").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaVisitasPorRegion").addClass('d-none');
            //Realizamos la petición
            let peticion = await fetch(url + api, { signal });
            //Convertimos a formato Json la respuesta de la peticion
            let response = await peticion.json();
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaVisitasPorRegion").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaVisitasPorRegion").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaVisitasPorRegion").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaVisitasPorRegion").removeClass("d-none");
            let data = [];
            let labels = [];
            for (let item of response) {
                data.push(Number(item.conteo).toFixed());
                labels.push(item.region);
            }
            let chartDom = document.getElementById('graficaVisitasPorRegion');
            myChartVisitasPorRegion = echarts.init(chartDom);
            let option;
            option = {
                tooltip: { trigger: 'item' },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'value' },
                yAxis: { type: 'category', data: labels },
                series: [{
                    name: 'Región',
                    type: 'bar',
                    label: {
                        show: true,
                        position: 'inside',
                        // position: 'top',
                        formatter: '{b} ' + "-" + ' {c}'
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
            option && myChartVisitasPorRegion.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function renderTablaVisitasPorRegion2(clientes) {
        $("#tblvistasporregion").empty();
        const signal = controller.signal;
        try {
            let api = `dashboard/getVisitasPorRegionTabla3/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderTablaVisitasPorRegion2").removeClass("d-none");
            // Ocultamos la grafica
            $("#divTablaVisitasPorRegion2").addClass("d-none");
            // Ocultamos el no data
            $("#noDataTablaVisitasPorRegion2").addClass('d-none');
            //Realizamos la petición
            let peticion = await fetch(url + api, { signal });
            //Convertimos a formato Json la respuesta de la peticion
            let response = await peticion.json();
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataTablaVisitasPorRegion2").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderTablaVisitasPorRegion2").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderTablaVisitasPorRegion2").addClass("d-none");
            // Mostramos la grafica
            $("#divTablaVisitasPorRegion2").removeClass("d-none");

            for (let item of response) {
                jQuery("<tr style='background:#6fd2f0;'><th>Región</th><th>No.Visitas</th><th>No.Deudores</th><th>No.Facturas</th></tr>" +
                    "<tr><td>" + item.nombre_zona + "</td><td>" + item.visitas + "</td><td>" + item.deudores + "</td><td>" + item.factura + "</td></tr>" +
                    "<tr>" +
                    "<td colspan='4'>" +

                    "<div class='card'>" +
                    "<div class='card-header' id='headingOne'>" +
                    "<h5 class='mb-0'>" +
                    "<button class='btn btn-link' data-toggle='collapse' data-target='#" + item.nombre_zona.split(" ").join("_") + "' aria-expanded='true' aria-controls='" + item.nombre_zona.split(" ").join("_") + "'>" +
                    item.nombre_zona +
                    "</button>" +
                    "</h5>" +
                    "</div>" +
                    "<div id='" + item.nombre_zona.split(" ").join("_") + "' class='collapse' aria-labelledby='headingOne' data-parent='#accordion'>" +
                    "<div class='card-body'>" +
                    "<div id='accordion2'>" +
                    "<table class='table table-bordered' style='width:100%'>" +
                    "<tbody id='tblvistasporlocalidad" + item.nombre_zona.split(" ").join("_") + "'>" +

                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +

                    "</td>" +
                    "</tr>").filter(".seccion").end().appendTo("#tblvistasporregion");
                tablaLocalidad(item.nombre_zona.split(" ").join("_"), item.nombre_zona);
            }
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function tablaLocalidad(tbId, region) {
        let clientes = $("#selectCliente").val().toString();
        const signal = controller.signal;
        try {
            jQuery("<div class='row' id='loadertblvistasporregionLocalidad" + tbId + "'>" +
                "<div class='col-12 col-md-12'>" +
                "<div class='d-flex justify-content-center'>" +
                "<div class='spinner-border' role='status'>" +
                "<span class='sr-only'>Loading...</span>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>").filter(".seccion").end().appendTo("#tblvistasporlocalidad" + tbId);

            let api2 = `dashboard/getVisitasPorRegionTabla3Localidad/${fecha_inicia}/${fecha_final}/${(region=="")?null:region}/${(clientes=="")?null:clientes}`
            let peticion2 = await fetch(url + api2, { signal });
            let response2 = await peticion2.json();
            // Open this row
            for (let item2 of response2) {
                jQuery("<tr style='background:#F8B56A;'><th>Localidad</th><th>No.Visitas</th><th>No.Deudores</th><th>No.Facturas</th></tr>" +
                    "<tr><td>" + item2.localidad + "</td><td>" + item2.visitas + "</td><td>" + item2.deudores + "</td><td>" + item2.factura + "</td></tr>" +
                    "<tr>" +
                    "<td colspan='4'>" +

                    "<div class='card'>" +
                    "<div class='card-header' id='headingOne'>" +
                    "<h5 class='mb-0'>" +
                    "<button class='btn btn-link' data-toggle='collapse' data-target='#" + item2.localidad.split(" ").join("_") + "' aria-expanded='true' aria-controls='" + item2.localidad.split(" ").join("_") + "'>" +
                    item2.localidad +
                    "</button>" +
                    "</h5>" +
                    "</div>" +
                    "<div id='" + item2.localidad.split(" ").join("_") + "' class='collapse' aria-labelledby='headingOne' data-parent='#accordion3'>" +
                    "<div class='card-body'>" +
                    "<div id='accordion3'>" +
                    "<table class='table table-bordered' style='width:100%'>" +
                    "<tbody id='tblvistaspordeudores" + item2.nombre_zona.split(" ").join("_") + item2.localidad.split(" ").join("_") + "'>" +

                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +

                    "</td>" +
                    "</tr>").filter(".seccion").end().appendTo("#tblvistasporlocalidad" + tbId);
                tablaConteo(item2.nombre_zona.split(" ").join("_") + item2.localidad.split(" ").join("_"), item2.localidad, tbId.split("_").join(" "));
                $("#loadertblvistasporregionLocalidad" + tbId).remove();
            }
            $("#loadertblvistasporregionLocalidad" + tbId).remove();
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function tablaConteo(tbId, localidad, region) {
        let clientes = $("#selectCliente").val().toString();
        const signal = controller.signal;
        try {
            jQuery("<div class='row' id='loadertblvistasporregionDeudor" + tbId + "'>" +
                "<div class='col-12 col-md-12'>" +
                "<div class='d-flex justify-content-center'>" +
                "<div class='spinner-border' role='status'>" +
                "<span class='sr-only'>Loading...</span>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>").filter(".seccion").end().appendTo("#tblvistaspordeudores" + tbId);

            let api3 = `dashboard/getVisitasPorRegionTabla3Conteo/${fecha_inicia}/${fecha_final}/${(region=="")?null:region}/${(localidad=="")?null:localidad}/${(clientes=="")?null:clientes}`
            let peticion3 = await fetch(url + api3, { signal });
            let response3 = await peticion3.json();
            const generateRandomString = (num) => {
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let result1 = Math.random().toString(36).substring(0, num);

                return result1;
            }
            // Open this row
            for (var i = 0; i < response3.length; i++) {
                jQuery("<tr style='background:#F8806A;'><th>Deudor</th><th>No.Visitas</th><th>No.Facturas</th></tr>" +
                    "<tr><td>" + response3[i].razon_social_deudor + "</td><td>" + response3[i].visitas + "</td><td>" + response3[i].factura + "</td></tr>" +
                    "<tr>" +
                    "<td colspan='3'>" +

                    "<div class='card'>" +
                    "<div class='card-header' id='headingOne'>" +
                    "<h5 class='mb-0'>" +
                    "<button class='btn btn-link' data-toggle='collapse' data-target='#" + response3[i].nombre_zona.split(" ").join("_") + response3[i].localidad.split(" ").join("_") + i + "' aria-expanded='true' aria-controls='" + response3[i].nombre_zona.split(" ").join("_") + response3[i].localidad.split(" ").join("_") + i + "'>" +
                    response3[i].razon_social_deudor +
                    "</button>" +
                    "</h5>" +
                    "</div>" +
                    "<div id='" + response3[i].nombre_zona.split(" ").join("_") + response3[i].localidad.split(" ").join("_") + i + "' class='collapse' aria-labelledby='headingOne' data-parent='#accordion4'>" +
                    "<div class='card-body'>" +
                    "<div id='accordion4'>" +
                    "<table class='table table-bordered' style='width:100%'>" +
                    "<tbody id='tblvistaspordetalles" + response3[i].nombre_zona.split(" ").join("_") + response3[i].localidad.split(" ").join("_") + i + "'>" +
                    "<thead><tr style='background:#fdb9ad;'><td>#</td><th>Fecha gestión</th><th>Factura</th><th>Saldo Documento</th><th>Anomalía</th><th>Ultimo comentario</th></tr></thead>" +
                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +

                    "</td>" +
                    "</tr>").filter(".seccion").end().appendTo("#tblvistaspordeudores" + tbId);
                tablaDetalle(response3[i].nombre_zona.split(" ").join("_") + response3[i].localidad.split(" ").join("_") + i, response3[i].localidad, response3[i].nombre_zona, response3[i].razon_social_deudor);

            }
            $("#loadertblvistasporregionDeudor" + tbId).remove();
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function tablaDetalle(tbId, localidad, region, deudor) {
        let clientes = $("#selectCliente").val().toString();
        const signal = controller.signal;
        try {
            jQuery("<div class='row' id='loadertblvistasporregiondetalles" + tbId + "'>" +
                "<div class='col-12 col-md-12'>" +
                "<div class='d-flex justify-content-center'>" +
                "<div class='spinner-border' role='status'>" +
                "<span class='sr-only'>Loading...</span>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>").filter(".seccion").end().appendTo("#tblvistaspordetalles" + tbId);

            let api4 = `dashboard/getVisitasPorRegionTabla3DetalleVisita/${fecha_inicia}/${fecha_final}/${(region=="")?null:region}/${(localidad=="")?null:localidad}/${(deudor==""?null:deudor)}/${(clientes=="")?null:clientes}`
            let peticion4 = await fetch(url + api4, { signal });
            let response4 = await peticion4.json();
            // Open this row
            for (var i = 0; i < response4.length; i++) {
                jQuery("<tr><td>" + (i + 1) + "</td><td>" + response4[i].fecha_inserta + "</td><td>" + response4[i].factura + "</td><td>" + response4[i].saldodocumento + "</td>" +
                    "<td>" + response4[i].descripcion_anomalia + "</td>" +
                    "<td>" + response4[i].ultimocomentario + "</td></tr>").filter(".seccion").end().appendTo("#tblvistaspordetalles" + tbId);
            }
            // for(let item3 of response4){
            // jQuery("<tr><td>"+item3.fechagestion+"</td><td>"+item3.factura+"</td><td>"+item3.saldodocumento+"</td>"+
            //   "<td>"+item3.descripcion_anomalia+"</td>"+
            //   "<td>"+item3.ultimocomentario+"</td></tr>").filter(".seccion").end().appendTo("#tblvistaspordetalles"+tbId);
            // // tablaLocalidad(item.nombre_zona.split(" ").join("_"),item.nombre_zona);
            // }
            $("#loadertblvistasporregiondetalles" + tbId).remove();
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }

    async function renderGraficaRecuentoFacturaImporteDocumentoAnioMes(clientes) {
        const signal = controller.signal;
        try {
            let api = `dashboard/getGraficaRecuentoFacturaImporteDocumentoAnioMes/${fecha_inicia}/${fecha_final}/${(clientes=="")?null:clientes}`
            // Mostramos el loader
            $("#loaderGraficaRecuentoFacturaImporteDocumentoAnioMes").removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaRecuentoFacturaImporteDocumentoAnioMes").addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaRecuentoFacturaImporteDocumentoAnioMes").addClass('d-none');
            //Realizamos la petición
            let peticion = await fetch(url + api, { signal });
            //Convertimos a formato Json la respuesta de la peticion
            let response = await peticion.json();
            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaRecuentoFacturaImporteDocumentoAnioMes").removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaRecuentoFacturaImporteDocumentoAnioMes").addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaRecuentoFacturaImporteDocumentoAnioMes").addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaRecuentoFacturaImporteDocumentoAnioMes").removeClass("d-none");

            let data = [];
            let data2 = [];
            let labels = [];
            for (let item of response) {
                data.push(Number(item.factura).toFixed());
                labels.push(item.mes + " " + item.anio);
                data2.push(Number(item.importedocumento).toFixed(2))
            }
            let chartDom = document.getElementById('graficaRecuentoFacturaImporteDocumentoAnioMes');
            myChartGraficaRecuentoFacturaImporteDocumentoAnioMes = echarts.init(chartDom);
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
                        name: 'Importe Documento $',
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

            option && myChartGraficaRecuentoFacturaImporteDocumentoAnioMes.setOption(option);
        } catch (err) {
            if (err.name == 'AbortError') { // se maneja el abort()
                // alert("Aborted!");
            } else {
                throw err;
            }
        }
    }
    // FIN Gráficas

    /* Cargar Filtros */
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

    // INICIO llamadas de métodos al iniciar la aplicación
    if (count_clientes > 1) {
        cargarClientes();
    }
    aplicarFiltros();
    // FIN llamadas de métodos al iniciar la aplicación

    // INICIO establecemos la versión de bootstrap a los selects
    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })
    // FIn establecemos la versión de bootstrap a los selects
});