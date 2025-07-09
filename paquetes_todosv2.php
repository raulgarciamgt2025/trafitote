<?php
   session_start();
    if (!isset($_SESSION['id_usuario']))
    {
        ?><script>alert("Su sesión vencio");self.location ="logout.php";</script><?php
        return;
    }

    $factura= "T";
    $estado = "T";
    $partida = "T";
?>
<style>
    div.dt-container {
        width: 100%;
        margin: 0 auto;
    }
</style>

<table id="tblPaquetes" class="display nowrap" style="width:100%">
  <thead>
    <th>No.</th>
    <th>Acciones</th>
    <th>PA</th>
    <th><Multi</th>
    <th>Docs</th>
    <th>Fec.Cargo</th>
    <th>FT</th>
    <th>Reemp</th>
    <th>Factura</th>
    <th>Código Barra</th>
    <th>Fecha</th>
    <th>Remitente</th>
    <th>Consignatario</th>
    <th>Tracking</th>
    <th>Retail</th>
    <th>Peso</th>
    <th>Contenido(Miami)</th>
    <th>Valor Dec.(Miami)</th>
    <th>Contenido(Cliente)</th>
    <th>Valor Dec.(Cliente)</th>
    <th>Documento</th>
    <th>Sección</th>
    <th>Emails</th>
    <th>Llamadas</th>
    <th>Usuario Ult. Acción</th>
    <th>Fec.Ultima Acción</th>
    <th>Ultima Acción</th>
    <th>Agente</th>
  </thead>
  <tbody>
  </tbody>
    <tfoot>
        <tr>
            <th>No.</th>
            <th>Acciones</th>
            <th>PA</th>
            <th><Multi</th>
            <th>Docs</th>
            <th>Fec.Cargo</th>
            <th>FT</th>
            <th>Reemp</th>
            <th>Factura</th>
            <th>Código Barra</th>
            <th>Fecha</th>
            <th>Remitente</th>
            <th>Consignatario</th>
            <th>Tracking</th>
            <th>Retail</th>
            <th>Peso</th>
            <th>Contenido(Miami)</th>
            <th>Valor Dec.(Miami)</th>
            <th>Contenido(Cliente)</th>
            <th>Valor Dec.(Cliente)</th>
            <th>Documento</th>
            <th>Sección</th>
            <th>Emails</th>
            <th>Llamadas</th>
            <th>Usuario Ult. Acción</th>
            <th>Fec.Ultima Acción</th>
            <th>Ultima Acción</th>
            <th>Agente</th>
        </tr>
    </tfoot>
</table>
<div id="dialog" title="Información"></div>
<script>
    $( document ).ready(function() {
        $('#tblPaquetes thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#tblPaquetes thead')
        $('#tblPaquetes').dataTable(
            {
                orderCellsTop: true,
                fixedHeader: true,
                "aProcessing": true,
                "aServerSide": true,
                dom: 'lfrtip',
                keys: true,
                initComplete: function () {
                    var api = this.api();
                    var cursorPosition;
                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" class="form-control" />');

                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('change', function (e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                    cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function (e) {
                                    e.stopPropagation();

                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
                "ajax":
                    {
                        url: 'data/json_paquetes.php',
                        type : "post",
                        dataType : "json",
                        error: function(e){
                            console.log(e.responseText);
                        }
                    },
                "scrollX": true,
                "destroy" : true,
                "bDestroy": true,
                "iDisplayLength": 50,
                "order": [[ 1, "desc" ]],
                "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay registros a desplegar",
                    "info":           " &nbsp;&nbsp;&nbsp;Mostrando _START_ de _END_ de un total de  _TOTAL_ registros",
                    "infoEmpty":      " &nbsp;&nbsp;&nbsp;Mostrando 0 de 0 de un total de 0 registros",
                    "infoFiltered":   "(Filtrados de _MAX_ un total de registros)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrando _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No hay registros que cumplan con el criterio de busqueda",
                    "paginate": {
                        "first":      "Primero",
                        "last":       "Ultimo",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            }).DataTable();
    })
</script>
