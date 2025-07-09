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
    
    // Sanitize inputs
    $seccion = SecurityManager::sanitizeInput($_REQUEST['seccion'] ?? '');
    $codigo_barra = SecurityManager::sanitizeInput($_REQUEST['codigo_barra'] ?? '');
    
    if (empty($seccion) || empty($codigo_barra)) {
        throw new Exception("Missing required parameters");
    }

    // Get contacts
    $str_sql = "SET DATEFORMAT DMY; ";
    $str_sql .= "EXECUTE SP_DOCUMENTO_CLIENTE_CONTACTOS ? ";
    $params = [$seccion];
    $contactos = $dbHelper->query($str_sql, $params);

    // Get documents
    $str_sql = "SET DATEFORMAT DMY; ";
    $str_sql .= "SELECT a.*  ";
    $str_sql .= "FROM PAQUETERIA_MIAMI_DOCUMENTOS a WITH(NOLOCK) ";
    $str_sql .= "WHERE a.codigo_barra= ? ";
    $str_sql .= "UNION ALL ";
    $str_sql .= "SELECT	id,
		                    nrogui  codigo_barra,
		                    '' as 'url',
		                    fecha_valido fecha_cargo,
		                    usuario_valido usuario_cargo,
		                    '0.00' valor_declarado,
		                    'VALIDO ALERTA - ' + contenido_cliente + ' => ' + contenido contenido,
		                     NULL nit,
		                     NULL nombre,
		                     NULL apellido,
		                     NULL direccion,
		                     NULL email,
		                     NULL cambio,
		                     NULL permanente
		            FROM     PAQUETERIA_MIAMI
		            WHERE    nrogui = ? AND usuario_valido IS NOT NULL";

    $params2 = [$codigo_barra, $codigo_barra];
    $documentos = $dbHelper->query($str_sql, $params2);

} catch (Exception $e) {
    error_log("Error in ver_datos_contactos.php: " . $e->getMessage());
    $contactos = [];
    $documentos = [];
}


?>
<form id="form1" name="form1" method="post" action="">
<p>Secci&oacute;n: <?php echo htmlspecialchars($seccion); ?><br/>C&oacute;digo Barra: <?php echo htmlspecialchars($codigo_barra); ?></p>


<h4> Documentos Cargados</h4>
<br/>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-sm table-striped">
    <thead class="thead-dark">
      <tr>
         <th width="2%"><div align="center"><small>No.</small></div></th>
         <th width="35%"><div align="center"><small>Documento</small></div></th>
         <th width="20%"><div align="center"><small>Fecha Cargo</small></div></th>
         <th width="17%"><div align="center"><small>Usuario Cargo</small></div></th>
         <th width="9%"><div align="center"><small>Valor Declarado</small></div></th>
         <th width="17%"><div align="center"><small>Contenido</small></div></th>
      </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   foreach ($documentos as $registro) {
	 $contador++;

	echo '<tr>';
    echo '<td><div align="center"><small>'.$contador.'</small></div></td>   ';
    echo '<td><div align="center"><small><a href="'. htmlspecialchars(mb_convert_encoding($registro['url'] ?? '',"UTF-8")).'" target="_blank">Ver documento</a></small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['fecha_cargo'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['usuario_cargo'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['valor_declarado'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars($registro['contenido'] ?? '').'</small></div></td>   ';
	echo '</tr>';
  }
  ?>
    </tbody>
</table>
<br />
<br />
<h4>Contactos</h4>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-sm table-striped">
    <thead class="thead-dark">
  <tr>
     <th width="1%"><div align="center"><small>No.</small></div></th>
     <th width="9%"><div align="center"><small>Nombre</small></div></th>
     <th width="9%"><div align="center"><small>Apellido</small></div></th>
     <th width="17%"><div align="center"><small>Dirección</small></div></th>
     <th width="13%"><div align="center"><small>Télefono</small></div></th>
    <th width="12%"><div align="center"><small>Celular</small></div></th>
    <th width="17%"><div align="center"><small>Email</small></div></th>
    <th width="22%"><div align="center"><small>Email2</small></div></th>
    </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   foreach ($contactos as $registro) {
	 $contador++;

	echo '<tr>';
    echo '<td><div align="center"><small>'.$contador.'</div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['nombre'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['apellido'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['direccion'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['telefono'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['celular'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['email'] ?? '',"UTF-8")).'</small></div></td>   ';
    echo '<td><div align="center"><small>'. htmlspecialchars(mb_convert_encoding($registro['email2'] ?? '',"UTF-8")).'</small></div></td>   ';
	echo '</tr>';
  }
  ?>
    </tbody>
</table>
</form>
