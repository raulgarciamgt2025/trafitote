<?php
// Include security configuration first to get session settings function
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('tools.php');

// Validate session
SecurityManager::validateSession();

try {
    // Initialize database helper
    $dbHelper = new DatabaseHelper();
    
    // Sanitize input
    $codigo_barra = SecurityManager::sanitizeInput($_REQUEST['codigo_barra'] ?? '');
    
    if (empty($codigo_barra)) {
        throw new Exception("Missing codigo_barra parameter");
    }

    // Get partidas arancelarias
    $str_sql = "SELECT a.* ";
    $str_sql .= "FROM dbo.VW_PARTIDA_ARANCELARIA_MAS_USADA a WITH(NOLOCK) ";
    $str_sql .= "ORDER BY a.partida_arancelaria ";
    
    $partidas = $dbHelper->query($str_sql);
    $cmbPartida = "";
    foreach ($partidas as $registro) {
        $cmbPartida .= "<option value='".htmlspecialchars($registro["id_partida"])."' >".htmlspecialchars($registro["partida_arancelaria"])."</option>";
    }

    // Get package details
    $str_sql = "SET DATEFORMAT DMY; ";
    $str_sql .= "SELECT a.* ";
    $str_sql .= "FROM dbo.VW_PAQUETERIA_MIAMIV2 a WITH(NOLOCK) ";
    $str_sql .= "WHERE a.codigo_barra = ? AND a.awb <> 'd' ";
    
    $params = [$codigo_barra];
    $paquetes = $dbHelper->query($str_sql, $params);
    
    $factura_cargada = '';
    $factura_miami = '';
    $paquete_data = [];
    
    if (!empty($paquetes)) {
        $paquete_data = $paquetes[0];
        $factura_cargada = $paquete_data["factura_cargada"] ?? '';
        $factura_miami = $paquete_data["factura_miami"] ?? '';
    }
    
} catch (Exception $e) {
    error_log("Error in validar_paquete.php: " . $e->getMessage());
    $cmbPartida = "";
    $factura_cargada = '';
    $factura_miami = '';
}
?>

<script language="javascript">
<!--
	function volver()
	{
        window.location = "index2.php?component=paquetes_todos";

    }
    function validar_paquete()
	{
        if ( document.getElementById("slPartida").value == "0"  )
        {
            alert("Selecciono una partida arancelaria");
            document.getElementById("slPartida").focus();
            return;
        }
		$.post("db/validar_documento_todos.php",
			   { codigo_barra: "<?php echo htmlspecialchars($codigo_barra); ?>",
                 partida_arancelaria: $("#slPartida").val()
			   },
			   function (respuesta)
			   {
					if ( respuesta != "Ok" )
						alert(respuesta);
					else
					{
						alert("Paquete Confirmado");
                        window.location = "index2.php?component=paquetes_todos";
                    }
			   }
			  )
		
	}		
	
//-->
</script>
<form>
<table width="50%" border="0" align="center" class="table table-sm" id="tblFiltros" style="width:50%">
     <tr>
      <th colspan="2" class="msgconfig">VALIDACI&Oacute;N DE PAQUETES</th>
         <input type="hidden" value="<?php echo $factura_cargada ?>" id="hfactura_cargada">
         <input type="hidden" value="<?php echo $factura_miami ?>" id="hfactura_miami">
     </tr>
     <tr>     </tr>
     <tr>
     <td width="20%"><div align="left">Código Barra:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['codigo_barra'] ?? ''); ?></div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Fecha:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['fecha_importacion'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Remitente:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['Remitente'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Consignatario:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['Consignatario'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Tracking:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['Tracking'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Piezas:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['piezas'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Peso:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['peso'] ?? ''); ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Valor Declarado(Miami):</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['valordeclarado'] ?? ''); ?></div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Contenido(Miami):</div></td>
        <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['contenido'] ?? ''); ?></div></td>
    </tr>
     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['seccion'] ?? ''); ?></div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Valor Declarado(Cliente):</div></td>
        <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['valor_declarado'] ?? ''); ?></div></td>
    </tr>

    <tr>
        <td width="20%"><div align="left">Contenido(Cliente):</div></td>
        <td width="80%"><div align="left"><?php echo htmlspecialchars($paquete_data['contenido_cliente'] ?? ''); ?></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Enlace documento:</div></td>
        <td width="80%"><div align="left"><a href="<?php echo htmlspecialchars($paquete_data['archivo_alerta'] ?? ''); ?>" target="_blank"><?php echo htmlspecialchars($paquete_data['archivo_alerta'] ?? ''); ?></a></div></td>
    </tr>
     <tr>
         <td width="20%"><div align="left">Partida Arancelar&iacute;a:</div></td>
         <td width="80%">
             <select id="slPartida" name="slPartida" style="width: 600px">
                 <option value="0">Otra Partida</option>
             <?php
                    echo $cmbPartida;
             ?>
             </select>
             <br/>
             <input id="txtBusqueda" name="txtBusqueda" maxlength="50" style="width: 400px"><button id="btnBuscarPartida" name="btnBuscarPartida">Buscar</button>
         </td>
     </tr>
     <tr>
       <td colspan="2"><div align="center">
         <input type="button" name="cmdCancelar" id="cmdCancelar" value="Grabar Validaci&oacute;n" onClick="javascript:validar_paquete();" style="width:350px"> &nbsp;&nbsp;&nbsp;
         <input type="button" name="cmdSalir" id="cmdSalir" value="Salir sin cambios" onclick="javascript:volver();" style="width:200px"/>
       </div></td>
     </tr>
     </table>
</form>
<script>
    $( "#btnBuscarPartida" ).click(function() {
        event.preventDefault();
        $.post("data/jsonPartidas.php",
            {
                filtro: $( "#txtBusqueda" ).val()
            },
            function (data)
            {
                var rows = JSON.parse(data);
                $('#slPartida').empty();
                $('#slPartida').append('<option value="0">Otra Partida</option>');
                rows.forEach(function(el){
                    $('#slPartida').append('<option value="'+ el.id_partida + '">' + el.partida_arancelaria + ' </option>');
                });

            }
        );

    });
</script>
