$(function () {

    let url = servidor;
    let controller = new AbortController();
    // INICIO Métodos para cargar la información a los campos de filtros
    // Carga las regiones al Select "selectRegiones"
    async function cargarRegiones(clientes) {
        const signal = controller.signal;
        try {
            $("#selectRegiones").empty();
            let peticion = await fetch(url + `dashboard/getRegiones/${(clientes=="")?null:clientes}`, { signal });
            let response = await peticion.json();
            for (let item of response) {
                let option = document.createElement("option")
                option.value = item.id_c_zona;
                option.text = item.nombre_zona;
                $("#selectRegiones").append(option);
            }
            console.log('cargando regiones ...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }
    async function cargarClientes() {
        const signal = controller.signal;
        try {
            $("#selectCliente").empty();
            let peticion = await fetch(url + `dashboard/getClientes`, { signal });
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
    // FIN Métodos para cargar la información a los campos de filtros

    // INICIO Eventos de botones
    $("#btnEliminarFiltros").on('click', function () {
        console.log('Eliminando filtros...');
        $("#selectRegiones").val("");
        $("#selectRegiones").trigger("change");
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

    function aplicarFiltros() {
        let regiones = $("#selectRegiones").val().toString();
        let clientes = $("#selectCliente").val().toString();
        /* console.log("conteo:" + regiones.length); */
        
            cargarRegiones(clientes);
        
        // renderGraficaSaldoCurrentGlobal();
        // renderGraficaDSOGlobal();
        //renderGraficaSaldoCurrentRegion(regiones);
        renderGraficasSaldosCurrentsRegiones(regiones,clientes);
        jQuery("<div class='col-lg-12 col-md-12 col-sm-12 text-center' id='loaderGraficaGeneral'>" +
            "<div class='row' >" +
            "<div class='col-12 col-md-12'>" +
            "<div class='d-flex justify-content-center'>" +
            "<div class='spinner-border' role='status'>" +
            "<span class='sr-only'>Loading...</span>" +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div></div>").filter(".loader").end().appendTo("#graficasAutomatizadas");
    }
    // FIN Eventos de botones

    //INICIO Métodos de Gráficas
    async function renderGraficasSaldosCurrentsRegiones(regiones,clientes) {
        const signal = controller.signal;
        try {
            let peticionConteo, responseConteo;
            // console.log("Contador de regiones: " + regiones.length);
            $("#graficasAutomatizadas").empty();
            if (regiones.length == 0) {
                console.log("No hay regiones seleccionadas...");
                peticionConteo = await fetch(url + `dashboard/getRegionesConteo/${(regiones=="")?null:regiones}/${(clientes=="")?null:clientes}`, { signal });
                responseConteo = await peticionConteo.json();
            } else {
                console.log("Hay regiones seleccionadas...");
                peticionConteo = await fetch(url + `dashboard/getRegionesConteo/${(regiones=="")?null:regiones}/${(clientes=="")?null:clientes}`);
                responseConteo = await peticionConteo.json();
            }
            if (responseConteo.length == 0) {
                $("#loaderGraficaGeneral").remove();
                jQuery("<div class='col-lg-12 col-md-12 col-sm-12 text-center' id='loaderGraficaGeneral'>" +
                "<div class='row' >" +
                "<div class='col-12 col-md-12'>" +
                "<div class='d-flex justify-content-center'>" +
                "<p>No existe información</p>"+
                "</div>" +
                "</div>" +
                "</div></div>").filter(".loader").end().appendTo("#graficasAutomatizadas");
            }else{
                $("#loaderGraficaGeneral").remove();
            }
            
            for (var i = 0; i < responseConteo.length; i++) {
                // console.log(responseConteo[i]['nombre_zona']);
                // console.log(responseConteo[i]['id_c_zona']);
                //Pendiente de desarrollo
                jQuery("<div class='col-lg-6 col-md-6 col-sm-12 text-center'><!--Md=Tablet, Lg=Portatil, Sm=movil-->" +
                    "<div class='card'> " +
                    "  <div class='card-header'> " +
                    "      <h3 class='card-title'>Saldo Current (objetivo y real) % - " + responseConteo[i]['nombre_zona'] + "</h3> " +
                    "    <div class='card-tools'> " +
                    "      <button type='button' class='btn btn-tool' data-card-widget='collapse'> " +
                    "          <i class='fas fa-minus'></i> " +
                    "      </button> " +
                    "    </div> " +
                    "  </div> " +
                    "  <!-- /.card-header --> " +
                    "<div class='card-body'> " +
                    "  <!-- Loader --> " +
                    "  <div class='row' id='loaderGraficaSaldoCurrentRegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'> " +
                    "    <div class='col-12 col-md-12'> " +
                    "        <div class='d-flex justify-content-center'> " +
                    "            <div class='spinner-border' role='status'> " +
                    "                <span class='sr-only'>Loading...</span> " +
                    "            </div> " +
                    "        </div> " +
                    "    </div> " +
                    "  </div> " +
                    "  <!-- Sin información Grafica ---> " +
                    "  <div class='row' id='noDataGraficaSaldoCurrentRegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'> " +
                    "      <div class='col-12 col-md-12'> " +
                    "          <p>No existe información con los filtros seleccionados</p> " +
                    "      </div> " +
                    "  </div> " +
                    "  <!-- Grafica ---> " +
                    "  <div class='row' id='divGraficaSaldoCurrentRegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'> " +
                    "      <div class='col-12 col-md-12'> " +
                    "          <div id='graficaSaldoCurrentRegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "' style='width: 100%;height:400px;'></div> " +
                    "      </div> " +
                    "  </div> " +
                    "  </div> " +
                    "</div> " +
                    "</div>").filter(".seccion").end().appendTo("#graficasAutomatizadas");
                    if (responseConteo[i]['cliente'] != 263) {
                    jQuery(
                        // Card para DSO por cada región
                        "<div class='col-lg-6 col-md-6 col-sm-12 text-center'><!--Md=Tablet, Lg=Portatil, Sm=movil-->" +
                        "<div class='card'>" +
                        "<div class='card-header'>" +
                        "<h3 class='card-title'>DSO (objetivo y real) % - " + responseConteo[i]['nombre_zona'] + "</h3>" +
                        "<div class='card-tools'>" +
                        "<button type='button' class='btn btn-tool' data-card-widget='collapse'>" +
                        "<i class='fas fa-minus'></i>" +
                        "</button>" +
                        "</div>" +
                        "</div>" +
                        "<div class='card-body'>" +
                        "<!-- Loader -->" +
                        "<div class='row' id='loaderGraficaDSORegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'>" +
                        "<div class='col-12 col-md-12'>" +
                        "<div class='d-flex justify-content-center'>" +
                        "<div class='spinner-border' role='status'>" +
                        "<span class='sr-only'>Loading...</span>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "<!-- Sin información Grafica --->" +
                        "<div class='row' id='noDataGraficaDSORegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'>" +
                        "<div class='col-12 col-md-12'>" +
                        "<p>No existe información con los filtros seleccionados</p>" +
                        "</div>" +
                        "</div>" +
                        "<!-- Grafica --->" +
                        "<div class='row' id='divGraficaDSORegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "'>" +
                        "<div class='col-12 col-md-12'>" +
                        "<div id='graficaDSORegion" + responseConteo[i]['nombre_zona'].split(" ").join("_") + "' style='width: 100%;height:400px;'></div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>"
                    ).filter(".seccion").end().appendTo("#graficasAutomatizadas");
                    }
                renderGraficaSaldoCurrenPorRegion(responseConteo[i]['id_c_zona'], responseConteo[i]['nombre_zona'].split(" ").join("_"),(clientes=="")?null:clientes);
                if (responseConteo[i]['cliente'] != 263) {
                    renderGraficaDSOPorRegion(responseConteo[i]['id_c_zona'], responseConteo[i]['nombre_zona'].split(" ").join("_"),(clientes=="")?null:clientes);
                }
                
            }
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }

    }

    async function renderGraficaSaldoCurrenPorRegion(regiones, contenedores,clientes) {
        const signal = controller.signal;
        try {
            //API a consultar
            let api = `dashboard/getRegionesKpiDso/${(regiones=="")?null:regiones}/${(clientes=="")?null:clientes}`
            // peticion = await fetch(url + `ajax.dashboard.php?getRegionesKpiDso=1&regiones=${regiones}`);
            // response = await peticion.json();
            // Mostramos el loader
            $("#loaderGraficaSaldoCurrentRegion" + contenedores).removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaSaldoCurrentRegion" + contenedores).addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaSaldoCurrentRegion" + contenedores).addClass('d-none');

            let peticion = await fetch(url + api, { signal });//Petición
            let response = await peticion.json();//Respuesta de la petición

            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaSaldoCurrentRegion" + contenedores).removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaSaldoCurrentRegion" + contenedores).addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaSaldoCurrentRegion" + contenedores).addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaSaldoCurrentRegion" + contenedores).removeClass("d-none");

            let data = [];
            let labels = [];
            let labelPorcentaje = [];
            // let i = 0;
            for (let item of response) {
                data.push(Number(item.saldo_corriente).toFixed(2));
                labels.push(item.mes.slice(0, 3) + "-" + item.anio);
            }
            for (var i = 0; i < labels.length; i++) {
                labelPorcentaje.push(Number(95).toFixed());
            }
            console.log(data);
            console.log(labels);
            let grafica = 'graficaSaldoCurrentRegion' + contenedores;
            let chartDom = document.getElementById(grafica);
            myChart = echarts.init(chartDom);
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
                        name: 'Target Current %',
                        type: 'line',
                        stack: 'Total',
                        data: labelPorcentaje
                    }, {
                        name: 'Porcentaje %',
                        type: 'line',
                        data: data
                    }
                ]
            };

            option && myChart.setOption(option);
            console.log('Cargando información de GraficaSaldoCurrent' + contenedores + '...');
        } catch (err) {
            if (err.name == 'AbortError') { } else { throw err; }
        }
    }

    async function renderGraficaDSOPorRegion(regiones, contenedores,clientes) {
        const signal = controller.signal;
        try {
            //API a consultar
            let api = `dashboard/getRegionesKpiDso/${(regiones=="")?null:regiones}/${(clientes=="")?null:clientes}`
            // peticion = await fetch(url + `ajax.dashboard.php?getRegionesKpiDso=1&regiones=${regiones}`);
            // response = await peticion.json();
            // Mostramos el loader
            $("#loaderGraficaDSORegion" + contenedores).removeClass("d-none");
            // Ocultamos la grafica
            $("#divGraficaDSORegion" + contenedores).addClass("d-none");
            // Ocultamos el no data
            $("#noDataGraficaDSORegion" + contenedores).addClass('d-none');

            let peticion = await fetch(url + api, { signal });//Petición
            let response = await peticion.json();//Respuesta de la petición

            if (response.length == 0) {
                // Mostramos el no data
                $("#noDataGraficaDSORegion" + contenedores).removeClass('d-none');
                // Ocultamos el loader
                $("#loaderGraficaDSORegion" + contenedores).addClass("d-none");
                return false;
            }
            // Ocultamos el loader
            $("#loaderGraficaDSORegion" + contenedores).addClass("d-none");
            // Mostramos la grafica
            $("#divGraficaDSORegion" + contenedores).removeClass("d-none");

            let data = [];
            let labels = [];
            let labelPorcentaje = [];
            // let i = 0;
            for (let item of response) {
                data.push(Number(item.dso).toFixed());
                labels.push(item.mes.slice(0, 3) + "-" + item.anio);
            }
            for (var i = 0; i < labels.length; i++) {
                labelPorcentaje.push(Number(28).toFixed());
            }
            console.log(data);
            console.log(labels);
            let grafica = 'graficaDSORegion' + contenedores;
            let chartDom = document.getElementById(grafica);
            myChart = echarts.init(chartDom);
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
                        name: 'Target Current',
                        type: 'line',
                        stack: 'Total',
                        data: labelPorcentaje
                    }, {
                        name: 'DSO',
                        type: 'line',
                        data: data,

                    }
                ]
            };

            option && myChart.setOption(option);
            console.log('Cargando información de GraficaDSORegion' + contenedores + '...');
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
    //FIN Métodos de Gráficas
    if (count_clientes > 1) {
        cargarClientes();
    }
    /* cargarRegiones(clientes); */
    aplicarFiltros();

    $('.select2').select2({
        language: 'es',
        theme: "bootstrap4"
    })
});