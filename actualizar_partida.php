<?php
   session_start();
    if ( !isset($_SESSION['id_usuario']))
    {
        ?><script>alert("Su sesión vencio");self.location ="logout.php";</script><?php
        return;
    }
   include('tools.php');
   $enlace =  mssql_connect($host , $usuario, $contrasena); 
   mssql_select_db($database,$enlace );

    $str_sql = "";
    $str_sql .= "SELECT a.* ";
    $str_sql .= "FROM dbo.VW_PARTIDA_ARANCELARIA_MAS_USADA a WITH(NOLOCK) ";
    $str_sql .= "ORDER BY a.partida_arancelaria ";
    $rs =  mssql_query($str_sql,$enlace);
    $cmbPartida =  "";
    while ( $registro = mssql_fetch_array($rs) )
    {
        $cmbPartida .= "<option value='".$registro["id_partida"]."' >".$registro["partida_arancelaria"]."</option>";
    }

    $str_sql = "";
    $str_sql .= "SET DATEFORMAT DMY; ";
    $str_sql .= "SELECT a.* ";
    $str_sql .= "FROM dbo.VW_PAQUETERIA_MIAMIV2 a WITH(NOLOCK) ";
    $str_sql .= "WHERE a.codigo_barra = '".$_REQUEST['codigo_barra']."' AND a.awb <> 'd' ";
    $rs =  mssql_query($str_sql,$enlace);
    $registro = mssql_fetch_array($rs);
    $factura_cargada = $registro["factura_cargada"];
    $factura_miami = $registro["factura_miami"];
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
		$.post("db/actualizar_partida_arancelaria.php",
			   { codigo_barra: '<?php echo $_REQUEST['codigo_barra'] ?>',
                 partida_arancelaria: $("#slPartida").val()
			   },
			   function (respuesta)
			   {
					if ( respuesta != "Ok" )
						alert(respuesta);
					else
					{
						alert("Partida Grabada");
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
      <th colspan="2" class="msgconfig">CARGAR PARTIDA ARANCELARIA</th>
         <input type="hidden" value="<?php echo $factura_cargada ?>" id="hfactura_cargada">
         <input type="hidden" value="<?php echo $factura_miami ?>" id="hfactura_miami">
     </tr>
     <tr>     </tr>
     <tr>
     <td width="20%"><div align="left">Código Barra:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['codigo_barra'] ?></div></td>
     </tr>
     <tr>
     <td width="20%"><div align="left">Fecha:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['fecha_importacion'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Remitente:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Remitente'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Consignatario:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Consignatario'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Tracking:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['Tracking'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Piezas:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['piezas'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Peso:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['peso'] ?></div></td>
     </tr>

     <tr>
     <td width="20%"><div align="left">Valor Declarado(Miami):</div></td>
     <td width="80%"><div align="left"><?php echo $registro['valordeclarado'] ?></div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Contenido(Miami):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['contenido'] ?></div></td>
    </tr>
     <tr>
     <td width="20%"><div align="left">Seccion:</div></td>
     <td width="80%"><div align="left"><?php echo $registro['seccion'] ?></div></td>
     </tr>
    <tr>
        <td width="20%"><div align="left">Valor Declarado(Cliente):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['valor_declarado'] ?></div></td>
    </tr>

    <tr>
        <td width="20%"><div align="left">Contenido(Cliente):</div></td>
        <td width="80%"><div align="left"><?php echo $registro['contenido_cliente'] ?></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Enlace documento:</div></td>
        <td width="80%"><div align="left"><a href="<?php echo $registro['archivo_alerta'] ?>" target="_blank"><?php echo $registro['archivo_alerta'] ?></a></div></td>
    </tr>
    <tr>
        <td width="20%"><div align="left">Partida Grabada:</div></td>
        <td width="80%"><div align="left"><?php echo $registro['partida_grabada'] ?></div></td>
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
         <input type="button" name="cmdCancelar" id="cmdCancelar" value="Grabar Partida Arancelaria" onClick="javascript:validar_paquete();" style="width:350px"> &nbsp;&nbsp;&nbsp;
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
<?php
    mssql_close($enlace);
?>
