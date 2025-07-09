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
    
    if (empty($codigo_barra)) {
        throw new Exception("Missing codigo_barra parameter");
    }

    $str_sql = "SET DATEFORMAT DMY; ";
    $str_sql .= "SELECT a.*  ";
    $str_sql .= "FROM VW_PAQUETERIA_MIAMI_ACCIONES a WITH(NOLOCK) ";
    $str_sql .= "WHERE a.codigo_barra= ? ";
    $str_sql .= "ORDER BY a.fecha_accion DESC ";
    
    $params = [$codigo_barra];
    $acciones = $dbHelper->query($str_sql, $params);

} catch (Exception $e) {
    error_log("Error in ver_acciones.php: " . $e->getMessage());
    $acciones = [];
    $seccion = htmlspecialchars($_REQUEST['seccion'] ?? '');
    $codigo_barra = htmlspecialchars($_REQUEST['codigo_barra'] ?? '');
}


?>
<form id="form1" name="form1" method="post" action="">
<p>Secci&oacute;n: <?php echo htmlspecialchars($seccion); ?><br/>C&oacute;digo Barra: <?php echo htmlspecialchars($codigo_barra); ?></p>


<h4> ACCIONES REALIZADAS</h4>
<br/>
<div class="table-responsive">
<table class="table table-sm table-striped table-hover">
    <thead class="table-dark">
      <tr>
         <th class="text-center" style="width: 5%;">No.</th>
         <th class="text-center" style="width: 40%;">Acción</th>
         <th class="text-center" style="width: 25%;">Usuario Acción</th>
         <th class="text-center" style="width: 30%;">Fecha Acción</th>
      </tr>
    </thead>
    <tbody>
  <?php
   $contador = 0;
   foreach ($acciones as $registro) {
	 $contador++;

	echo '<tr>';
    echo '<td class="text-center"><small>'.$contador.'</small></td>';
    echo '<td class="text-start"><small>'. htmlspecialchars(mb_convert_encoding($registro['accion'] ?? '',"UTF-8")).'</small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars(mb_convert_encoding($registro['usuario_grabo'] ?? '',"UTF-8")).'</small></td>';
    echo '<td class="text-center"><small>'. htmlspecialchars(mb_convert_encoding($registro['fecha_accion'] ?? '',"UTF-8")).'</small></td>';
	echo '</tr>';
  }
  ?>
    </tbody>
</table>
</div>
</form>
