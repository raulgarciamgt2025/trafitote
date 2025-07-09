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
    $seccion = SecurityManager::sanitizeInput($_REQUEST['seccion'] ?? '', 'alphanumeric');
    
    // Execute stored procedure
    // Sanitize inputs
    $seccion = SecurityManager::sanitizeInput($_REQUEST['seccion'] ?? '');
    $codigo_barra = SecurityManager::sanitizeInput($_REQUEST['codigo_barra'] ?? '');
    
    if (empty($codigo_barra)) {
        throw new Exception("Missing codigo_barra parameter");
    }

    // Get documents outside of time
    $str_sql = "SET DATEFORMAT DMY; ";
    $str_sql .= "SELECT a.* ";
    $str_sql .= "FROM PAQUETE_MIAMI_FUERA_HORA a WITH(NOLOCK) ";
    $str_sql .= "WHERE a.codigo_barra= ? ";
    
    $params = [$codigo_barra];
    $documentos = $dbHelper->query($str_sql, $params);

} catch (Exception $e) {
    error_log("Error in ver_fuera_de_tiempo.php: " . $e->getMessage());
    $documentos = [];
    $seccion = htmlspecialchars($_REQUEST['seccion'] ?? '');
    $codigo_barra = htmlspecialchars($_REQUEST['codigo_barra'] ?? '');
}


?>
<form id="form1" name="form1" method="post" action="">
<p>Secci&oacute;n: <?php echo htmlspecialchars($seccion); ?><br/>C&oacute;digo Barra: <?php echo htmlspecialchars($codigo_barra); ?></p>


<h4> Documentos Fuera de Tiempo</h4>
<br/>
<table class="table table-sm table-striped table-hover">
    <thead class="table-dark">
      <tr>
         <th width="2%" class="text-center"><small>No.</small></th>
         <th width="35%" class="text-center"><small>Documento</small></th>
         <th width="20%" class="text-center"><small>Fecha Cargo</small></th>
         <th width="17%" class="text-center"><small>Usuario Cargo</small></th>
         <th width="9%" class="text-center"><small>Valor Declarado</small></th>
         <th width="17%" class="text-center"><small>Contenido</small></th>
      </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   foreach ($documentos as $registro) {
	 $contador++;

	echo '<tr>';
    echo '<td class="text-center"><small>'.$contador.'</small></td>';
    echo '<td class="text-center"><small><a href="'. htmlspecialchars(mb_convert_encoding($registro['url'] ?? '',"UTF-8")).'" target="_blank" class="btn btn-sm btn-outline-primary">Ver documento</a></small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars(mb_convert_encoding($registro['fecha_cargo'] ?? '',"UTF-8")).'</small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars(mb_convert_encoding($registro['usuario_cargo'] ?? '',"UTF-8")).'</small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars(mb_convert_encoding($registro['valor_declarado'] ?? '',"UTF-8")).'</small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars($registro['contenido'] ?? '').'</small></td>';
	echo '</tr>';
  }
  ?>
    </tbody>
</table>
</form>
