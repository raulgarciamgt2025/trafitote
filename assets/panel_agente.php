<?php
    include_once("../include/conexion.php");
    if (session_status() == PHP_SESSION_NONE)
    {
        session_start();
    }
    if ( !isset($_SESSION['usuario']) )
    {
        header("Location: ../include/logout.php");
        return;
    }

    if (!isCsrfTokenValid($_REQUEST["tokenid"]))
    {
        echo "Token incorrecto... sesión no valida..";
        return;
    }

$serverName = $host;
$connectionOptions = array("Database" => $db ,"Uid" => $usuario,"PWD" => $contrasena);

$conexion = sqlsrv_connect($serverName, $connectionOptions);

$consulta = "";
$consulta .="SELECT a.* ";
$consulta .="FROM partida_arancelaria_mas_usada a ";

$stmt = sqlsrv_query( $conexion, $consulta,array() );
$cmbPartidas="";
$cmbPartidas .="<option value=\"\"></option>";
while( $registro = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) )
{
    $cmbPartidas .="<option value=\"".$registro["id_partida"]."\">".$registro["partida"]." - ".$registro["descripcion"]." - ".$registro["dai"]."</option>";
}

sqlsrv_close($conexion);
?>
<script>
    function accion(codigo_barra,objeto)
    {


        var rowId =$("#grid-table").jqGrid('getGridParam','selrow');
        var rowData = jQuery("#grid-table").getRowData(rowId);
        var indice = objeto.value;
        switch (indice)
        {
            case "1": // Revisar documentos cargados;
                $.post("data/json_documentos_cargados.php",
                    {
                        codigo_barra: rowData['codigo_barra'],
                        tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                    },
                    function(data,status){
                        if ( status == "success")
                        {
                            var rows = JSON.parse(data);
                            var tblBody = document.getElementById("table_coincidencias").tBodies[0];
                            tblBody.innerHTML = "";
                            var newBody = "";
                            rows.forEach(function(el){
                                newBody = newBody + "<tr>" ;
                                newBody = newBody + "<td>"+ el.seccion + "</td>" ;
                                newBody = newBody + "<td>"+ el.fecha_grabo_formato + "</td>" ;
                                newBody = newBody + "<td>"+ el.tienda + "</td>" ;
                                newBody = newBody + "<td>"+ el.tracking + "</td>" ;
                                newBody = newBody + "<td>"+ el.descripcion + "</td>" ;
                                newBody = newBody + "<td>"+ el.valor_declarado + "</td>" ;
                                newBody = newBody + "<td><a href='"  + el.email_body_url + "' target='_blank'><img src='imagenes/documento.png' border='0'></a></td>" ;
                                newBody = newBody + "</tr>";
                            });
                            tblBody.innerHTML = newBody;
                            $( "#coincidencias" ).dialog({
                                resizable: true,
                                width:800,
                                height:600,
                                modal: true,
                                open: function ()
                                {

                                },
                                buttons: {
                                    Salir: function() {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
                        }
                        else
                        {
                            alert("No se pudo cargar la informacion");
                        }

                    });
                break;
            case "2": // Ver coincidencias
                var coincidencias_seccion = rowData['coincidencias_seccion'];
                var coincidencias_5100 = rowData['coincidencias_5100'];

                var coincidencias = 1;
                var coincidencias_desconocido = 1;
                if (coincidencias_seccion.includes("rojo"))
                    coincidencias = 0;
                if (coincidencias_5100.includes("rojo"))
                    coincidencias_desconocido = 0;
                if ( coincidencias ==0 && coincidencias_desconocido== 0 )
                {
                    bootbox.alert("No hay conincidencias de prealertas de la sección ni de desconocidos");
                    return;
                }

                $.post("data/json_coincidencias.php",
                    {
                        codigo_barra: rowData['codigo_barra'],
                        seccion: rowData['seccion'],
                        fecha: rowData['fecha_descarga'],
                        tracking: rowData['tracking'],
                        tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                    },
                    function(data,status){
                        if ( status == "success")
                        {
                            var rows = JSON.parse(data);
                            var tblBody = document.getElementById("table_coincidencias").tBodies[0];
                            tblBody.innerHTML = "";
                            var newBody = "";
                            rows.forEach(function(el){
                                newBody = newBody + "<tr>" ;
                                newBody = newBody + "<td>"+ el.seccion + "</td>" ;
                                newBody = newBody + "<td>"+ el.fecha_grabo_formato + "</td>" ;
                                newBody = newBody + "<td>"+ el.tienda + "</td>" ;
                                newBody = newBody + "<td>"+ el.tracking + "</td>" ;
                                newBody = newBody + "<td>"+ el.descripcion + "</td>" ;
                                newBody = newBody + "<td>"+ el.valor_declarado + "</td>" ;
                                newBody = newBody + "<td><a href='"  + el.email_body_url + "' target='_blank'><img src='imagenes/documento.png' border='0'></a></td>" ;
                                newBody = newBody + "</tr>";
                            });
                            tblBody.innerHTML = newBody;
                            $( "#coincidencias" ).dialog({
                                resizable: true,
                                width:800,
                                height:600,
                                modal: true,
                                open: function ()
                                {

                                },
                                buttons: {
                                    Salir: function() {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
                        }
                        else
                        {
                            alert("No se pudo cargar la informacion");
                        }

                    });
                break;
            case "3": // Bitacora Cliente
                break;
            case "4": // Enviar Link carga archivo

                $("#codigo_barra_enlace").html(rowData['codigo_barra']);
                $("#seccion_enlace").html(rowData['seccion']);
                $("#remitente_enlace").html(rowData['remitente']);
                $("#consignatario_enlace").html(rowData['consignatario']);
                $("#tracking_enlace").html(rowData['tracking']);
                $( "#enviar_link" ).dialog({
                    resizable: true,
                    width:800,
                    height:500,
                    modal: true,
                    open: function ()
                    {
                    },
                    buttons: {
                        "Enviar Enlace": function() {
                            if ($("#email_enlace").val() == "" )
                            {
                                bootbox.alert("Correo electronico en blanco");
                                return;
                            }

                            $.post("edit/enviar_enlace_correo.php",
                                {
                                    codigo_barra: rowData['codigo_barra'],
                                    email: $("#email_enlace").val(),
                                    tipo_correo: $("#tipo_enlace").val(),
                                    seccion: rowData['seccion'],
                                    tracking: rowData['tracking'],
                                    remitente: rowData['remitente'],
                                    fecha_recibido: rowData['fecha_importacion'],
                                    consignatario: rowData['consignatario'],
                                    contenido: rowData['contenido'],
                                    peso: rowData['peso'],
                                    valor_declarado: rowData['valor_declarado'],
                                    tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                                },
                                function (respuesta)
                                {
                                    if ( respuesta.trim() == "" )
                                    {
                                        bootbox.alert("Correo enviado correctamente");

                                    }
                                    else
                                        bootbox.alert(respuesta);
                                }
                            );
                            $( this ).dialog( "close" );
                        },
                        "Salir": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                break;
            case "5": // Cargar Factura
                alert("5");
                break;
            case "6": // Cancelar paquete
                $("#codigo_barra_cancelar").html(rowData['codigo_barra']);
                $("#seccion_cancelar").html(rowData['seccion']);
                $("#remitente_cancelar").html(rowData['remitente']);
                $("#consignatario_cancelar").html(rowData['consignatario']);
                $("#tracking_cancelar").html(rowData['tracking']);
                $( "#cancelar_paquete" ).dialog({
                    resizable: true,
                    width:800,
                    height:500,
                    modal: true,
                    open: function ()
                    {
                    },
                    buttons: {
                        "Cancelar Paquete": function() {
                            if ($("#txtMotivo").val() == "" )
                            {
                                bootbox.alert("Motivo en blanco");
                                return;
                            }

                            $.post("edit/cancelar_paquete.php",
                                {
                                    codigo_barra: rowData['codigo_barra'],
                                    motivo: $("#txtMotivo").val(),
                                    tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                                },
                                function (respuesta)
                                {
                                    if ( respuesta.trim() == "" )
                                    {
                                        bootbox.alert("Paquete cancelado correctamente");

                                    }
                                    else
                                        bootbox.alert(respuesta);
                                }
                            );
                            $( this ).dialog( "close" );
                        },
                        "Salir": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                break;
            case "7": // Modificar valor y contenido
                $("#codigo_barra_modificar").html(rowData['codigo_barra']);
                $("#seccion_modificar").html(rowData['seccion']);
                $("#remitente_modificar").html(rowData['remitente']);
                $("#consignatario_modificar").html(rowData['consignatario']);
                $("#tracking_modificar").html(rowData['tracking']);
                $( "#modificar_datos" ).dialog({
                    resizable: true,
                    width:800,
                    height:500,
                    modal: true,
                    open: function ()
                    {
                    },
                    buttons: {
                        "Enviar Enlace": function() {
                            if ($("#email_enlace").val() == "" )
                            {
                                bootbox.alert("Correo electronico en blanco");
                                return;
                            }

                            $.post("edit/enviar_enlace_correo.php",
                                {
                                    codigo_barra: rowData['codigo_barra'],
                                    email: $("#email_enlace").val(),
                                    tipo_correo: $("#tipo_enlace").val(),
                                    seccion: rowData['seccion'],
                                    tracking: rowData['tracking'],
                                    remitente: rowData['remitente'],
                                    fecha_recibido: rowData['fecha_importacion'],
                                    consignatario: rowData['consignatario'],
                                    contenido: rowData['contenido'],
                                    peso: rowData['peso'],
                                    valor_declarado: rowData['valor_declarado'],
                                    tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                                },
                                function (respuesta)
                                {
                                    if ( respuesta.trim() == "" )
                                    {
                                        bootbox.alert("Correo enviado correctamente");

                                    }
                                    else
                                        bootbox.alert(respuesta);
                                }
                            );
                            $( this ).dialog( "close" );
                        },
                        "Salir": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                break;
            case "8": // Reemplazar
                bootbox.confirm({
                    message: "Esta seguro de reemplaza  la factura sección " + rowData['seccion'] + " Código Barra " + rowData['codigo_barra'] + " ?",
                    buttons: {
                        confirm: {
                            label: 'Si',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if ( result )
                        {
                            $.post("edit/reemplazar_factura.php",
                                {
                                    codigo_barra: rowData['codigo_barra'],
                                    tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                                },
                                function (respuesta)
                                {
                                    if ( respuesta.trim() == "" )
                                    {
                                        bootbox.alert("Correo enviado correctamente");

                                    }
                                    else
                                        bootbox.alert(respuesta);
                                }
                            );
                        }
                    }
                });
                break;
            case "9": // Contactos

                $.post("data/json_contactos_seccion.php",
                    {
                        seccion: rowData['seccion'],
                        tokenid: '<?php echo $_REQUEST["tokenid"] ?>'
                    },
                    function(data,status){
                        if ( status == "success")
                        {
                            var rows = JSON.parse(data);
                            var tblBody = document.getElementById("table_contactos").tBodies[0];
                            tblBody.innerHTML = "";
                            var newBody = "";
                            rows.forEach(function(el){
                                newBody = newBody + "<tr>" ;
                                newBody = newBody + "<td>"+ el.nombre + "</td>" ;
                                newBody = newBody + "<td>"+ el.apellido + "</td>" ;
                                newBody = newBody + "<td>"+ el.direccion + "</td>" ;
                                newBody = newBody + "<td>"+ el.telefono + "</td>" ;
                                newBody = newBody + "<td>"+ el.celular + "</td>" ;
                                newBody = newBody + "<td>"+ el.email + "</td>" ;
                                newBody = newBody + "<td>"+ el.email2 + "</td>" ;
                                newBody = newBody + "</tr>";
                            });
                            tblBody.innerHTML = newBody;
                            $( "#contactos" ).dialog({
                                resizable: true,
                                width:800,
                                height:600,
                                modal: true,
                                open: function ()
                                {

                                },
                                buttons: {
                                    Salir: function() {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
                        }
                        else
                        {
                            alert("No se pudo cargar la informacion");
                        }

                    });
                break;
        }

    }
</script>
<div id="modificar_datos" title="MODIFICAR VALOR/CONTENIDO/PARTIDA">
    <form name="forma_modificar" id="forma_modificar" method="post" autocomplete="off">
        <div class="row">
            <div class="col-12 bg-primary text-white">
                Datos del Paquete:
                <br/>
                C&oacute;digo Barra: <span id="codigo_barra_modificar"></span>
                <br/>
                Secci&oacute;n: <span id="seccion_modificar"></span>
                <br/>
                Remitente: <span id="remitente_modificar"></span>
                <br/>
                Consignatario: <span id="consignatario_modificar"></span>
                <br/>
                Tracking: <span id="tracking_modificar"></span>
                <br/>
                <br/>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-3">
                Valor Declarado:
            </div>
            <div class="col-2">
                <input type="text" id="valor_declarado" name="valor_declarado" maxlength="12">
            </div>
            <div class="col-7">
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                Contenido:
            </div>
            <div class="col-9">
                <input type="text" id="contenido_cliente" name="contenido_cliente" maxlength="500" style="width: 450px">
            </div>

        </div>
        <div class="row">
            <div class="col-3">
                Partida Arancelaria:
            </div>
            <div class="col-7">
                <select id="slPartidas" class="chosen-select" style="width: 450px">
                    <?php echo $cmbPartidas ?>
                </select>
            </div>
            <div class="col-2">
            </div>
        </div>
    </form>
</div>
<div id="cancelar_paquete" title="CANCELAR PAQUETE"  style="display:none">
    <form name="forma_enlace" id="forma_enlace" method="post" autocomplete="off">
        <div class="row">
            <div class="col-12 bg-primary text-white">
                Datos del Paquete:
                <br/>
                C&oacute;digo Barra: <span id="codigo_barra_cancelar"></span>
                <br/>
                Secci&oacute;n: <span id="seccion_cancelar"></span>
                <br/>
                Remitente: <span id="remitente_cancelar"></span>
                <br/>
                Consignatario: <span id="consignatario_cancelar"></span>
                <br/>
                Tracking: <span id="tracking_cancelar"></span>
                <br/>
                <br/>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-2">
                Motivo:
            </div>
            <div class="col-10">
                <textarea name="txtMotivo" cols="75" rows="5" id="txtMotivo" style="width: 450px" ></textarea>
            </div>
        </div>
    </form>
</div>
<div id="enviar_link" title="ENVIAR LINK DE CARGA DE FACTURA"  style="display:none">
    <form name="forma_enlace" id="forma_enlace" method="post" autocomplete="off">
        <div class="row">
            <div class="col-12 bg-primary text-white">
                Datos del Paquete:
                <br/>
                C&oacute;digo Barra: <span id="codigo_barra_enlace"></span>
                <br/>
                Secci&oacute;n: <span id="seccion_enlace"></span>
                <br/>
                Remitente: <span id="remitente_enlace"></span>
                <br/>
                Consignatario: <span id="consignatario_enlace"></span>
                <br/>
                Tracking: <span id="tracking_enlace"></span>
                <br/>
                <br/>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-2">
                Tipo:
            </div>
            <div class="col-10">
                <select style="width: 450px" class="ace-select text-dark-m1 bgc-default-l5 bgc-h-warning-l3  brc-default-m3 brc-h-warning-m1" id ="tipo_enlace" name="tipo_enlace">
                    <option value="1" selected="selected">Solicitud Contenido/Valor/Documento</option>
                    <option value="2">Confirmación Contenido/Valor</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                E-mail:
            </div>
            <div class="col-10">
                <input type="text" id="email_enlace" name="email_enlace" maxlength="250" style="width: 475px">
            </div>
        </div>
    </form>
</div>
<div id="contactos"  style="display:none">
    <div class="row">
        <div class="col-2">
            CONTACTOS
        </div>
        <div class="col-10">
        </div>
    </div>
    <table class="table table-striped" id="table_contactos">
        <thead>
        <tr>
            <th class="h6 small">NOMBRE</th>
            <th class="h6 small">APELLIDO</th>
            <th class="h6 small">DIRECCI&Oacute;N</th>
            <th class="h6 small">TEL&Eacute;FONO</th>
            <th class="h6 small">CELULAR</th>
            <th class="h6 small">EMAIL</th>
            <th class="h6 small">EMAIL 2</th>
        </tr>
        </thead>
        <tbody id="tbody_contactos">
        </tbody>
    </table>
</div>
<div id="coincidencias"  title="INCIDENCIAS" style="display:none">
    <table class="table table-striped" id="table_coincidencias">
        <thead>
        <tr>
            <th class="h6 small">SECCION</th>
            <th class="h6 small">FECHA</th>
            <th class="h6 small">REMITENTE</th>
            <th class="h6 small">TRACKING</th>
            <th class="h6 small">CONTENIDO</th>
            <th class="h6 small">VALOR</th>
            <th class="h6 small">DOCTO.</th>
        </tr>
        </thead>
        <tbody id="tbody_coincidencias">
        </tbody>
    </table>
</div>
<div id="documentos_cargados" title="DOCUMENTOS CARGADOS"  style="display:none">
    <table class="table table-striped" id="table_documentos">
        <thead>
        <tr>
            <th class="h6 small">FECHA CARGO</th>
            <th class="h6 small">USUARIO CARGO</th>
            <th class="h6 small">CONTENIDO</th>
            <th class="h6 small">VALOR DECLARADO</th>
            <th class="h6 small">DOCTO.</th>
        </tr>
        </thead>
        <tbody id="tbody_documentos">
        </tbody>
    </table>
</div>
<div class="page-header">
    <h1 class="page-title text-primary-d2">
        Tr&aacute;fico
        <small class="page-info text-secondary-d2">
            <i class="fa fa-angle-double-right text-80"></i>
            Panel de Agentes. Agente: <?php echo $_SESSION["agente"] ?>
        </small>
    </h1>
</div>
<form id="frmData" autocomplete="off">
    <div class="row">
        <div class="col-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" class="table-active" colspan="6">Filtros: </th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" id="seccion" placeholder="SECCION" maxlength="50" >
                        </td>
                        <td>
                            <input type="text" id="codigo_barra" placeholder="CODIGO BARRA" maxlength="50" >
                        </td>
                        <td>
                            <input type="text" PLACEHOLDER="TRACKING" id="tracking" maxlength="50" >
                        </td>
                        <td>
                            <button type="button" id="btnFiltrar" class="btn btn-success  radius-1 border-b-2 d-inline-flex align-items-center pl-2px py-2px btn-bold">
                                <span class="bgc-white-tp9 shadow-sm radius-2px h-4 px-25 pt-1 mr-25 border-1">
					                <i class="fa fa-check text-white-tp2 text-110 mt-3px"></i>
				                </span>
                                Filtrar
                            </button>
                        </td>
                        <td>
                            <button id="btnLimpiar" type="button" class="btn btn-danger radius-2 border-b-2 pl-2">
                                <i class="w-4 h-4 bgc-black-tp9 radius-round fa fa-times text-white-tp2 mr-1 align-middle pt-2"></i>
                                <span class="text-105 align-middle">Limpiar</span>
                            </button>
                        </td>
                        <td>


                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</form>
<div class="row" id="grid_datos">
    <div class="col-12">
        <table id="grid-table" class="table table-bordered table-striped"></table>
        <div id="grid-pager"></div>
    </div>
</div>

<script>
    jQuery(function($) {

        var grid_selector = "#grid-table";
        var pager_selector = "#grid-pager";
        var grid_box = '#gbox_grid-table';


        //resize to fit page size
        var parent_column = $(grid_selector).closest('.col-12');
        $(window).on('resize.jqGrid', function () {
            $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
        });
        //resize on sidebar collapse/expand
        $('.sidebar').on('expand.ace.sidebar' , function() {
            $(grid_box).hide();
        });
        $('.sidebar').on('collapsed.ace.sidebar expanded.ace.sidebar' , function() {
            $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
            $(grid_box).show();
        });


        $.extend($.jgrid.jqModal, {zIndex: 1100});

        //custom class names
        $.jgrid.guiStyles.bootstrap4ace = {
            baseGuiStyle: "bootstrap4",

            gBox: "",
            gView: "",

            hDiv: "border-y-1 brc-grey-l2 mt-n1px bgc-grey-l4",
            hTable: "text-uppercase text-80 text-grey-m1",
            colHeaders: "pl-2 pr-1 text-left",


            gridTitle: "bgc-primary-d1 text-white text-125 p-25",
            grid: "table table-hover table-bordered text-dark-m1 text-95 border-0 brc-grey-l2",
            titleButton: "btn btn-primary border-0 mr-2 px-2 w-auto radius-1",

            gridRow: "bgc-h-info-l3",

            actionsButton: "action-btn mx-1 px-2px float-none border-0",

            states: {
                select: "bgc-success-l2 bgc-h-success-l1",
                th: "bgc-yellow-l1 text-blue-d2",
                //hoverTh: "bgc-default-l2 text-default-d3",
                hoverTh: "bgc-yellow-m4 text-dark-m3",

                error: "alert bgc-danger-l3",
                //active: "active",
                //textOfClickable: ""
            },


            //dialogs
            overlay: "modal-backdrop",
            dialog: {
                header: "modal-header bgc-default-l4 text-blue-m1 py-2 px-3",
                window: "modal mw-100",
                document: "modal-dialog mw-none",

                content: "modal-content p-0",
                body: "modal-body px-2 py-25 text-130",
                footer: "modal-footer",

                closeButton: "mr-1 mt-n25 px-2 py-1 w-auto h-auto border-1 brc-h-warning-m1 bgc-h-warning-l1 text-danger radius-round",
                fmButton: "btn btn-sm btn-default",

                viewLabel: "control-label py-2",
                dataField: "form-control my-2 ml-1 w-auto",
                viewCellLabel: "text-right w-4 pr-2",
                viewData: "text-left text-secondary-d2 d-block border-1 brc-grey-l2 p-2 radius-1 my-2 ml-1"
            },

            searchDialog: {
                elem: "form-control w-95",
                operator: "form-control w-95",
                label: "form-control w-95",

                addRuleButton: "btn btn-xs btn-outline-primary radius-round btn-bold px-2 mx-1 text-110",
                addGroupButton: "btn btn-xs btn-primary mx-1 text-110",
                deleteRuleButton: "h-4 px-2 pt-0 text-150 mr-1 btn btn-xs btn-outline-danger border-0",
                deleteGroupButton: "h-4 px-2 pt-0 text-150 mr-1 btn btn-xs btn-outline-danger border-0",
            },

            navButton: "action-btn border-0 text-110 mx-1",
            pager: {
                pager: "py-3 px-1 px-md-2 bgc-primary-l4 border-y-1 brc-secondary-l1 mt-n1px",
                pagerInput: "form-control form-control-sm text-center d-inline-block",
                pagerSelect: "form-control w-6 px-1",
                pagerButton: "p-0 m-0 border-0 radius-round text-110",
            },

            subgrid: {
                button: "",//don't remove
                row: "bgc-default-l3",
            },

            loading: "alert bgc-primary-l3 brc-primary-m2 text-dark-tp3 text-120 px-4 py-3",
        };

        //use the following icons
        var _pageBtn = "btn w-4 h-4 px-0 mx-2px btn-outline-lightgrey btn-h-outline-primary btn-a-outline-primary radius-round bgc-white";
        $.jgrid.icons.icons4ace = {
            baseIconSet: "fontAwesome5",
            common: "fas",
            actions: {
                edit: "fa-pencil-alt text-blue",
                del: "fa-trash-alt text-danger-m1",
                save: "fa-check text-success",
                cancel: "fa-times text-orange-d2"
            },

            pager: {
                first: "fa-angle-double-left " + _pageBtn,
                prev: "fa-angle-left " + _pageBtn,
                next: "fa-angle-right " + _pageBtn,
                last: "fa-angle-double-right " + _pageBtn
            },

            gridMinimize: {
                visible: "fa-chevron-up",
                hidden: "fa-chevron-down"
            },

            sort: {
                common: "far",
                asc: "fa-caret-up",
                desc: "fa-caret-down"
            },

            form: {
                close: "fa-times my-2px",
                prev: "fa-chevron-left",
                next: "fa-chevron-right",
                save: "fa-check",
                undo: "fa-times",
                del: "fa-trash-alt",
                cancel: "fa-times",

                resizableLtr: "fa-rss fa-rotate-270 text-orange-d1 text-105"
            },
        };


        $(grid_selector).jqGrid({
            //direction: "rtl",

            iconSet: "icons4ace",
            guiStyle: "bootstrap4ace",

            multiselectWidth: 36,

            url: 'data/json_panel_agente.php',
            datatype: "json",
            mtype: 'GET',
            height: 400,//optional
            postData: {
                tokenid: function() { return '<?php echo $_REQUEST["tokenid"] ?>'; },
                seccion: function() { return $("#seccion").val() },
                codigo_barra: function() {  return $("#codigo_barra").val() },
                tracking: function() { return $("#tracking").val() }


            },

            //sortable: true,// requires jQuery UI Sortable

            colNames:['Acciones','Código Barra','Servicio','Reemp.','Hora','PRE','5100','Multi','Seccion','Fec. Impo.','Fec. Descarga', 'Remitente','Consignatario','Tracking','Retail','Peso','Contenido','Valor Declarado'],
            colModel:[
                {name:'acciones',index:'acciones',width:300,sorttype:"text",formatter:combo_acciones,align:"center"},
                {resizable: true,name:'codigo_barra',index:'codigo_barra', width:125,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'servicio',index:'servicio', width:125,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: false,name:'reemplazar_factura',index:'reemplazar_factura',width:50,sorttype:"text",formatter:reemplaza_factura,align:"center"},
                {resizable: false,name:'hora',index:'hora',width:50,sorttype:"text",formatter:enhorario,align:"center"},
                {resizable: false,name:'coincidencias_seccion',index:'coincidencias_seccion',width:50,sorttype:"text",formatter:coincidencias,align:"center"},
                {resizable: false,name:'coincidencias_5100',index:'coincidencias_5100',width:50,sorttype:"text",formatter:coincidencias_5100,align:"center"},
                {resizable: false,name:'multipieza',index:'multipieza',width:50,sorttype:"text",formatter:multipiezas,align:"center"},
                {resizable: true,name:'seccion',index:'seccion', width:150,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'fecha_importacion',index:'fecha_importacion', width:150,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'fecha_descarga',index:'fecha_descarga', width:150,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'remitente',index:'remitente', width:350,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'consignatario',index:'consignatario', width:350,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'tracking',index:'tracking', width:250,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'retail',index:'retail', width:100,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'peso',index:'peso', width:150,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'contenido',index:'contenido', width:450,editable: true,editoptions:{size:"10",maxlength:"10"}},
                {resizable: true,name:'valor_declarado',index:'valor_declarado', width:250,editable: true,editoptions:{size:"10",maxlength:"10"}}

            ],


            altRows: true,
            altclass: 'bgc-default-l4',

            viewrecords : true,
            rowNum: 100,
            rowList:[100,200,300,400,500,1000],

            pager : pager_selector,
            //toppager: true,

            multiselect: true,
            multiboxonly: true,
            //multikey: "ctrlKey",

            loadComplete : function() {
                var table = this;
                setTimeout(function() {
                    $(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
                    enableTooltips(table);
                }, 0);
            },

            editurl: null,//nothing is saved
            caption: "Paquetes activos sin manifestar",

            autowidth: true, shrinkToFit: false,
            //autowidth: true,
            //shrinkToFit: $(window).width() > 600,
            //forceFit: true,

            grouping: false,
            groupingView : {
                groupField : ['seccion'],
                groupDataSorted : true,
                plusicon : 'fa fa-chevron-down px-2 w-auto text-primary-m3 bgc-h-primary-l2 py-1 mx-1 radius-1',
                minusicon : 'fa fa-chevron-up px-2 w-auto text-primary-m3 bgc-h-primary-l2 py-1 mx-1 radius-1'
            },


            //subgrid options
            subGridWidth: 36,
            subGrid : false,
            subGridOptions : {
                plusicon : "fas fa-angle-double-down text-secondary-m2 text-90",
                minusicon  : "fas fa-angle-double-up text-info-m1 text-95",
                openicon : "fas fa-fw fa-reply fa-rotate-180 text-orange-d1"
            },

            //for this example we are using local data
            subGridRowExpanded: function (subgridDivId, rowId) {
                var subgridTableId = subgridDivId + "_t";
                $("#" + subgridDivId).html("<table id='" + subgridTableId + "'></table>");
                $("#" + subgridTableId).jqGrid({
                    datatype: 'local',
                    guiStyle: "bootstrap4ace",
                    data: subgrid_data,
                    colNames: ['No','Item Name','Qty'],
                    colModel: [
                        { name: 'id', width: 50 },
                        { name: 'name', width: 150 },
                        { name: 'qty', width: 50 }
                    ]
                });
            },

            //resize grid after pagination
            onPaging : function(pgButton){
                setTimeout(function() {
                    $(grid_box).hide();
                    $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
                    $(grid_box).show();
                }, 0);
            },

        });


        //enable search/filter toolbar
        //jQuery(grid_selector).jqGrid('filterToolbar',{defaultSearch:true,stringResult:true})
        //jQuery(grid_selector).filterToolbar({});


        //navButtons
        $(grid_selector).jqGrid('navGrid', pager_selector,
            {	//navbar options
                add: false,
                addicon : 'fa fa-plus-circle text-purple-m1 text-100',

                edit: false,
                editicon : 'fa fa-edit text-blue-m1 text-100',

                del: false,
                delicon : 'fa fa-trash text-danger-m2 text-100',

                search: false,
                searchicon : 'fa fa-search text-orange-d1 text-100',

                refresh: true,
                refreshicon : 'fa fa-sync text-success-m1 text-100',

                view: true,
                viewicon : 'fa fa-search-plus text-grey-d1 text-100',

            },
            {
                //edit record form
                //closeAfterEdit: true,
                width: 600,
                recreateForm: true,
                beforeShowForm : function(e) {
                    style_edit_form(e[0]);
                }
            },
            {
                //new record form
                width: 600,
                closeAfterAdd: true,
                recreateForm: true,
                viewPagerButtons: false,
                beforeShowForm : function(e) {
                    style_edit_form(e[0]);
                }
            },
            {
                //delete record form
                recreateForm: true,
                beforeShowForm : function(e) {
                    style_delete_form(e[0]);
                },
                onClick : function(e) {
                }
            },
            {
                //search form
                recreateForm: true,
                afterShowSearch: function(e){
                    style_search_form(e[0]);
                },
                afterRedraw: function(e){
                },
                multipleSearch: true,

                //multipleGroup:true,
                //showQuery: true
            },
            {
                //view record form
                recreateForm: true,
                width: 600,
                beforeShowForm: function(e){
                    style_edit_form(e[0]);
                    e[0].querySelector('tr[data-rowpos="1"]').classList.add('d-none');
                }
            }
        );

        //navGrid buttons don't work on touch devices, so trigger them on 'touch'
        $(pager_selector).find('.navtable .action-btn').on('touchend' , function() {
            $(this).triggerHandler('click')
        });

        ////////////////////////////////
        function  multipiezas(cellvalue, options, rowObject)
        {
            let resultado = "";
            if ( rowObject[7] > 1 )
                resultado = "<img src=\"imagenes/verde.png\"border=\"0\" />";
            else
                resultado = "<img src=\"imagenes/rojo.png\" border=\"0\" />";

            return resultado;
        }
        function  coincidencias_5100(cellvalue, options, rowObject)
        {
            let resultado = "";
            if ( rowObject[6] == 1 )
                resultado = "<img src=\"imagenes/verde.png\"border=\"0\" />";
            else
                resultado = "<img src=\"imagenes/rojo.png\" border=\"0\" />";

            return resultado;
        }
        function  coincidencias(cellvalue, options, rowObject)
        {
            let resultado = "";
            if ( rowObject[5] == 1 )
                resultado = "<img src=\"imagenes/verde.png\"border=\"0\" />";
            else
                resultado = "<img src=\"imagenes/rojo.png\" border=\"0\" />";

            return resultado;
        }
        function  enhorario(cellvalue, options, rowObject)
        {
            let resultado = "";
            if ( rowObject[4] == 1 )
                resultado = "<img src=\"imagenes/verde.png\"border=\"0\" />";
            else
                resultado = "<img src=\"imagenes/rojo.png\" border=\"0\" />";

            return resultado;
        }

        function  reemplaza_factura(cellvalue, options, rowObject)
        {
            let resultado = "";
            if ( rowObject[3] == 1 )
               resultado = "<img src=\"imagenes/verde.png\"border=\"0\" />";
            else
                resultado = "<img src=\"imagenes/rojo.png\" border=\"0\" />";

            return resultado;
        }
        function combo_acciones(cellvalue, options, rowObject)
        {
            return "<select onchange=\"javascript:accion('" + rowObject[0] + "',this)\" style=\"width: 250px\" class=\"ace-select text-dark-m1 bgc-default-l5 bgc-h-warning-l3  brc-default-m3 brc-h-warning-m1\" id =\"acciones_" + rowObject[0] + "\"> " +
                    "<option value=\"0\">Seleccione acción</option> " +
                    "<option value=\"1\">Revisar Docs. Cargados</option> " +
                    "<option value=\"2\">Ver coincidencias</option> " +
                    "<option value=\"3\">Llamada al cliente</option> " +
                    "<option value=\"4\">Enviar link carga archivo</option> " +
                    "<option value=\"5\">Cargar Factura</option> " +
                    "<option value=\"6\">Cancelar Paquete</option> " +
                    "<option value=\"7\">Modif. Info. C.B.</option> " +
                    "<option value=\"8\">Reemplazar Factura</option> " +
                    "<option value=\"9\">Ver contactos</option> " +
                    "</select>";
        }

        //change buttons colors in dialogs
        function style_edit_form(form) {
            form = $(form);
            //enable datepicker on "sdate" field and switches for "stock" field
            form.find('input[name=sdate]').attr('type', 'date');
            form.find('input[name=stock]').attr('style', 'width: 1.25rem !important;');

            //update buttons classes
            var buttons = form.parent().next().find('.EditButton .fm-button').attr('href', '#');//to disable for Bootstrap's "a:not([href])" style
            buttons.eq(0).removeClass('btn-default').addClass('btn-light-success border-2 text-600');
            buttons.eq(1).removeClass('btn-default').addClass('btn-light-grey border-2');

            //update next / prev buttons
            buttons = form.parent().next().find('.navButton .fm-button').removeClass('btn-default').addClass('px-25 mx-2px btn-outline-secondary btn-h-outline-primary btn-a-outline-primary radius-round');
        }

        function style_delete_form(form) {
            form = $(form);
            var buttons = form.parent().next().find('.EditButton .fm-button').attr('href', '#');
            buttons.eq(0).removeClass('btn-default').addClass('btn-light-danger border-2 text-600');
            buttons.eq(1).removeClass('btn-default').addClass('btn-light-grey border-2');
        }

        function style_search_form(form) {
            form = $(form);

            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable').addClass('text-white');

            buttons.find('.EditButton a').removeClass('btn-default');
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn-default');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn-grey');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn-primary');
        }


        //enable tooltips
        function enableTooltips(table) {
            $('.navtable .ui-pg-button').tooltip({container:'body', trigger: 'hover'});
            $(table).find('.ui-pg-div').tooltip({container:'body', trigger: 'hover'});
        }


        //var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');

    });
    jQuery(function($) {
        $(".chosen-select").chosen({allow_single_deselect:true});
    });
    $( "#btnFiltrar" ).click(function() {

        $('#grid-table').trigger( 'reloadGrid' );
    });

    $( "#btnLimpiar" ).click(function() {
        $('#seccion').val("");
        $('#codigo_barra').val("");
        $('#tracking').val("");
        $('#grid-table').trigger( 'reloadGrid' );
    });
    $( "#modificar_datos" ).hide();


</script>

