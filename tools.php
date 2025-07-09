<?php
// Database configuration - move to environment variables in production
$host = $_ENV['DB_HOST'] ?? "192.168.0.2";
$database = $_ENV['DB_NAME'] ?? "db_trans";
$db_usuario = $_ENV['DB_USER'] ?? "web";
$db_contrasena = $_ENV['DB_PASSWORD'] ?? "!nf0rm4t!k";
$db_port = $_ENV['DB_PORT'] ?? 1433;

// PDO connection for SQL Server
function getPDOConnection() {
    global $host, $database, $db_usuario, $db_contrasena, $db_port;
    try {
        $dsn = "sqlsrv:Server=$host,$db_port;Database=$database";
        error_log("getPDOConnection: Attempting connection with DSN: $dsn, User: $db_usuario");
        $pdo = new PDO($dsn, $db_usuario, $db_contrasena, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        error_log("getPDOConnection: Connection successful");
        return $pdo;
    } catch (PDOException $e) {
        error_log("getPDOConnection: Database connection failed - " . $e->getMessage());
        error_log("getPDOConnection: DSN was: $dsn");
        error_log("getPDOConnection: User was: $db_usuario");
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Legacy compatibility - deprecated, use getPDOConnection() instead
$dsn = "mysql:dbname=dbMavsa;host=192.168.0.2;";
$database_prueba = "db_trans";
 
 $fac_nombre_cliente ="";
 $fac_direccion = "" ;
 $fac_telefono = "" ;
 $fac_nit = "" ;
 
 
 // Meses del a�o
 $meses[1] = "Enero";
 $meses[2] = "Febrero";
 $meses[3] = "Marzo";	
 $meses[4] = "Abril";
 $meses[5] = "Mayo";
 $meses[6] = "Junio";
 $meses[7] = "Julio";
 $meses[8] = "Agosto";
 $meses[9] = "Septiembre";
 $meses[10] = "Octubre";
 $meses[11] = "Noviembre";
 $meses[12] = "Diciembre";

 //Estado civil
 
 $est_civil[1] = "Soltero(a)";
 $est_civil[2] = "Casado(a)";
 $est_civil[3] = "Unido(a)";	
 $est_civil[4] = "Viudo(a)";
 global $importe_parcial;

 // Conversion de numeros a letras
function unidad($numuero){
switch ($numuero)
{
case 9:
{
$numu = "NUEVE";
break;
}
case 8:
{
$numu = "OCHO";
break;
}
case 7:
{
$numu = "SIETE";
break;
}
case 6:
{
$numu = "SEIS";
break;
}
case 5:
{
$numu = "CINCO";
break;
}
case 4:
{
$numu = "CUATRO";
break;
}
case 3:
{
$numu = "TRES";
break;
}
case 2:
{
$numu = "DOS";
break;
}
case 1:
{
$numu = "UN";
break;
}
case 0:
{
$numu = "";
break;
}
}
return $numu;
}

function decena($numdero){

if ($numdero >= 90 && $numdero <= 99)
{
$numd = "NOVENTA ";
if ($numdero > 90)
$numd = $numd."Y ".(unidad($numdero - 90));
}
else if ($numdero >= 80 && $numdero <= 89)
{
$numd = "OCHENTA ";
if ($numdero > 80)
$numd = $numd."Y ".(unidad($numdero - 80));
}
else if ($numdero >= 70 && $numdero <= 79)
{
$numd = "SETENTA ";
if ($numdero > 70)
$numd = $numd."Y ".(unidad($numdero - 70));
}
else if ($numdero >= 60 && $numdero <= 69)
{
$numd = "SESENTA ";
if ($numdero > 60)
$numd = $numd."Y ".(unidad($numdero - 60));
}
else if ($numdero >= 50 && $numdero <= 59)
{
$numd = "CINCUENTA ";
if ($numdero > 50)
$numd = $numd."Y ".(unidad($numdero - 50));
}
else if ($numdero >= 40 && $numdero <= 49)
{
$numd = "CUARENTA ";
if ($numdero > 40)
$numd = $numd."Y ".(unidad($numdero - 40));
}
else if ($numdero >= 30 && $numdero <= 39)
{
$numd = "TREINTA ";
if ($numdero > 30)
$numd = $numd."Y ".(unidad($numdero - 30));
}
else if ($numdero >= 20 && $numdero <= 29)
{
if ($numdero == 20)
$numd = "VEINTE ";
else
$numd = "VEINTI".(unidad($numdero - 20));
}
else if ($numdero >= 10 && $numdero <= 19)
{
	switch ($numdero)
	{
	 case 10:
	 {
	  $numd = "DIEZ ";
	  break;
	 }
	 case 11:
	{
	 $numd = "ONCE ";
	 break;
	}
	case 12:
	{
	 $numd = "DOCE ";
	 break;
	}
	case 13:
	{
	 $numd = "TRECE ";
	 break;
	}
	case 14:
	{
	 $numd = "CATORCE ";
	 break;
	}
	case 15:
	{
	 $numd = "QUINCE ";
	 break;
	}
	case 16:
	{
	 $numd = "DIECISEIS ";
	 break;
	}
	case 17:
	{
	 $numd = "DIECISIETE ";
	 break;
	}
	case 18:
	{
	 $numd = "DIECIOCHO ";
	 break;
	}
	case 19:
	{
	 $numd = "DIECINUEVE ";
	 break;
	}
  } 
}
else
$numd = unidad($numdero);
return $numd;
}

function centena($numc){
if ($numc >= 100)
{
if ($numc >= 900 && $numc <= 999)
{
$numce = "NOVECIENTOS ";
if ($numc > 900)
$numce = $numce.(decena($numc - 900));
}
else if ($numc >= 800 && $numc <= 899)
{
$numce = "OCHOCIENTOS ";
if ($numc > 800)
$numce = $numce.(decena($numc - 800));
}
else if ($numc >= 700 && $numc <= 799)
{
$numce = "SETECIENTOS ";
if ($numc > 700)
$numce = $numce.(decena($numc - 700));
}
else if ($numc >= 600 && $numc <= 699)
{
$numce = "SEISCIENTOS ";
if ($numc > 600)
$numce = $numce.(decena($numc - 600));
}
else if ($numc >= 500 && $numc <= 599)
{
$numce = "QUINIENTOS ";
if ($numc > 500)
$numce = $numce.(decena($numc - 500));
}
else if ($numc >= 400 && $numc <= 499)
{
$numce = "CUATROCIENTOS ";
if ($numc > 400)
$numce = $numce.(decena($numc - 400));
}
else if ($numc >= 300 && $numc <= 399)
{
$numce = "TRESCIENTOS ";
if ($numc > 300)
$numce = $numce.(decena($numc - 300));
}
else if ($numc >= 200 && $numc <= 299)
{
$numce = "DOSCIENTOS ";
if ($numc > 200)
$numce = $numce.(decena($numc - 200));
}
else if ($numc >= 100 && $numc <= 199)
{
if ($numc == 100)
$numce = "CIEN ";
else
$numce = "CIENTO ".(decena($numc - 100));
}
}
else
$numce = decena($numc);

return $numce;
}

function miles($nummero){
if ($nummero >= 1000 && $nummero < 2000){
$numm = "MIL ".(centena($nummero%1000));
}
if ($nummero >= 2000 && $nummero <10000){
$numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
}
if ($nummero < 1000)
$numm = centena($nummero);

return $numm;
}

function decmiles($numdmero){
if ($numdmero == 10000)
$numde = "DIEZ MIL";
if ($numdmero > 10000 && $numdmero <20000){
$numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));
}
if ($numdmero >= 20000 && $numdmero <100000){
$numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));
}
if ($numdmero < 10000)
$numde = miles($numdmero);

return $numde;
}

function cienmiles($numcmero){
if ($numcmero == 100000)
$num_letracm = "CIEN MIL";
if ($numcmero >= 100000 && $numcmero <1000000){
$num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));
}
if ($numcmero < 100000)
$num_letracm = decmiles($numcmero);
return $num_letracm;
}

function millon($nummiero){
if ($nummiero >= 1000000 && $nummiero <2000000){
$num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
}
if ($nummiero >= 2000000 && $nummiero <10000000){
$num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
}
if ($nummiero < 1000000)
$num_letramm = cienmiles($nummiero);

return $num_letramm;
}

function decmillon($numerodm){
if ($numerodm == 10000000)
$num_letradmm = "DIEZ MILLONES";
if ($numerodm > 10000000 && $numerodm <20000000){
$num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));
}
if ($numerodm >= 20000000 && $numerodm <100000000){
$num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));
}
if ($numerodm < 10000000)
$num_letradmm = millon($numerodm);

return $num_letradmm;
}

function cienmillon($numcmeros){
if ($numcmeros == 100000000)
$num_letracms = "CIEN MILLONES";
if ($numcmeros >= 100000000 && $numcmeros <1000000000){
$num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
}
if ($numcmeros < 100000000)
$num_letracms = decmillon($numcmeros);
return $num_letracms;
}

function milmillon($nummierod){
if ($nummierod >= 1000000000 && $nummierod <2000000000){
$num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
}
if ($nummierod >= 2000000000 && $nummierod <10000000000){
$num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
}
if ($nummierod < 1000000000)
$num_letrammd = cienmillon($nummierod);

return $num_letrammd;
}


function NumerosALetras($numero){
$numf = milmillon($numero);
return $numf;
} 

    
 //
 // Clase de manejo de conexiones y consultas a tablas
 //
 class conexion
 {
  var $conexion_db;
  var $rs_consulta;
  var $anfitrion;
  var $catalogo_db;
  var $accesos;
  var $fecha_cierre;
  var $nombre_usuario;
  var $etiqueta_periodo;
  var $id_periodo;  
  var $inicio_periodo;
  var $fin_periodo;
  var $ingeniero;

  // Constructor de la clase
  
  function conexion($anfitrion,$usuario_db,$contrasena_db,$catalogo_db)
  {
   $this->conectar_db($anfitrion,$usuario_db,$contrasena_db,$catalogo_db);
  }
  
  // Función que abre la conexión a la base de datos SQL Server
  function conectar_db($anfitrion,$usuario_db,$contrasena_db,$catalogo_db)
  {
   try {
       $dsn = "sqlsrv:Server=$anfitrion,1433;Database=$catalogo_db";
       $this->conexion_db = new PDO($dsn, $usuario_db, $contrasena_db, [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false
       ]);
   } catch (PDOException $e) {
       error_log("Database connection failed: " . $e->getMessage());
       throw new Exception("Database connection failed");
   }
  }

  // Ejecutar una consulta 
  function ejecutar_consulta($str_consulta)
  {
   try {
       $this->rs_consulta = $this->conexion_db->query($str_consulta);
       return $this->rs_consulta;
   } catch (PDOException $e) {
       error_log("Query failed: " . $e->getMessage());
       throw new Exception("Query execution failed");
   }
  }
  
  // ejecutar una consulta que no tiene resultado alguno de registros
  function ejecutar_instruccion($str_consulta)
  {
   try {
       $this->conexion_db->exec($str_consulta);
       $this->conexion_db = null; // Close connection
   } catch (PDOException $e) {
       error_log("Instruction execution failed: " . $e->getMessage());
       throw new Exception("Instruction execution failed");
   }
  }
  
  function ejecutar_instruccion_noclose($str_consulta)
  {
   try {
       $this->conexion_db->exec($str_consulta);
   } catch (PDOException $e) {
       error_log("Instruction execution failed: " . $e->getMessage());
       throw new Exception("Instruction execution failed");
   }
  }

  // Obtener una fila de la consulta
  function obtener_registro()
  {
   if ($this->rs_consulta) {
       return $this->rs_consulta->fetch(PDO::FETCH_ASSOC);
   }
   return false;
  }
  
  // Función que devuelve el número de registros de una consulta
  function num_registros()
  {
   if ($this->rs_consulta) {
       return $this->rs_consulta->rowCount();
   }
   return 0;
  }


  //  Función que cierra la conexión a la base de datos
  function cerrar_conexion()
  {
   $this->rs_consulta = null;
   $this->conexion_db = null;
  }
  
    // Funci�n para verificar la existencia de un usuario

  function validar_opcion($usuario_login,$id_opcion)
  {
   $return[] = "";
   $str_sql  = "";
   $str_sql .= "SELECT a.* ";
   $str_sql .= "FROM acceso_usuario a ";
   $str_sql .= "WHERE a.id_usuario='$usuario_login' AND a.id_opcion='$id_opcion' ";
   
   $this->ejecutar_consulta($str_sql);
   
   $registro = $this->obtener_registro() ;
   $this->nombre_usuario = $registro['nombre_vendedor'];
   
   $no_registros = $this->num_registros();
   
   $return[0] = $no_registros;
   $return[1]= ($return[0] > 0 ?$registro['agregar']:0);
   $return[2]= ($return[0] > 0 ?$registro['modificar']:0);
   $return[3]= ($return[0] > 0 ?$registro['eliminar']:0);
   $return[4]= ($return[0] > 0 ?$registro['ejecutar']:0);			
   $return[5]= ($return[0] > 0 ?$registro['imprimir']:0);			   
   
   $this->cerrar_conexion();
   return $return;
  }
  function validar_usuario($usuario_login,$contrasena_login)
  {

   $str_sql = "";
   $str_sql .= "SELECT a.* ";
   $str_sql .= "FROM vendedor a ";
   $str_sql .= "WHERE a.id_vendedor='" . $usuario_login . "' AND ";
   $str_sql .= "      a.contrasena = '" . $contrasena_login."' ";

   $this->ejecutar_consulta($str_sql);

   $no_registros = $this->num_registros();
   $registro = $this->obtener_registro();
   $this->nombre_usuario = $registro['nombre_vendedor'];
   
   $this->cerrar_conexion();
   
   return $no_registros;
  }
  function inicio()
  {
   // PDO doesn't support seeking to beginning like mysql_data_seek
   // Would need to re-execute the query to reset cursor
   // For now, this method will be a no-op
   // In modern applications, consider re-executing the query instead
  }
  function combobox($key,$display,$label,$name,$onchange,$opcion_inicio,$mensaje_inicio,$valor_inicio,$value_selected,$estilo ='inputbox',$disabled='')
  {
    // $key = Campo de la tabla el cual sera devuelto al seleccionar esa opci�n
	// $display = Campo que se despliega en el combo box
	// $label = Campo etiqueta del combo box
	// $name = Nombre del combo box
	// $onchange = Evento que se generara en caso de un "ONCHANGE"
	// $opcion_inicio = Variable (1-0) que lleva 1 en caso de mensaje de inicio en el combobox ("seleccione una opcion")
	// $mensaje_inicio = Mensaje de la opci�n de inicio
	// $valor_inicio = valor que tendra la opcion de inicio
	// $value_selected = valor de la opci�n que aparecera como seleccionada en el combobox
   $utilidades = new utilidades();
   $this->inicio();
   echo '<select name="'.$name.'" class="'.$estilo.'" onchange="'.$onchange.'" '.$disabled.'>';
   if ( $opcion_inicio )
    echo '<option value="'.$valor_inicio.'" '.$utilidades->iif($valor_inicio==$value_selected,'selected','').' >'.$mensaje_inicio.'</option>';
   while ( $registro = $this->obtener_registro() )
   {
   echo '<option value="'.$registro[$key].'" '.$utilidades->iif($registro[$key]==$value_selected,'selected','').' id="'.$registro[$label].'">'.$registro[$display].'</option>';
   }	
        
   echo '</select>';
   $this->inicio();
  }
  
 }
 
  //
  // Clase de utilidades varias
  //
  class utilidades
  {
  
  
  
  function toolbar($botones)
  {
   $url = "";
   $tam_tabla = ( count($botones) * 2 )  * 45 ;
   echo "<table width='".$tam_tabla."' border='0' cellspacing='0' cellpadding='0'>";
   echo "<tr valign='middle'>";
   for ( $contador = 0; $contador < count($botones) ; $contador++ )
   {
    $url = ( $botones[$contador][4] == 1 ? $botones[$contador][3] : 'javascript:alert(\'No tiene acceso a esta opci�n!!!\');' );
    echo'<td width="45" align="center"><a target="'.$botones[$contador][5].'" href="'.$url.'" onMouseOver="window.status=\''.$botones[$contador][2].'\';return(true);"><img alt="'.$botones[$contador][2].'" src="imagenes/'.$botones[$contador][0].'" border="0"></a></td>';
    echo '<td width="45" align="left"><a target="'.$botones[$contador][5].'" href="'.$url.'" onMouseOver="window.status=\''.$botones[$contador][2].'\';return(true);">'.$botones[$contador][1].'</a></td>';
   }
  echo '</tr>';
  echo '</table>';
   
  }  
  function combobox_numerico($inicio,$fin,$nombre_combo,$onchange,$value_selected,$class,$inc_dec,$tam,$opcion_inicio,$mensaje_inicio,$valor_inicio)
  {
   // $inicio: Valor n�merico de inicio
   // $fin: Valor n�merico del fin
   // $nombre_combo: nombre del combo
   // $onchange: secuencia script que se ejecutara al momento de cambiar el item del combobox
   // $value_selected: valor seleccionado
   // $class: Clase CSS del combobox
   // $inc_dec: 1: Incremente 0: Decremento
   // $tam: Tama�o del combo
   // $opcion_inicio: Indica si el combobox tiene una opci�n de inicio
   // $mensaje_inicio: Mensaje de inicio del combobox
   // $valor_mensaje_inicio

   echo '<select name="'.$nombre_combo.'" class="'.$class.'" onchange="'.$onchange.'" style="width:'.$tam.';">';
   if ( $opcion_inicio )
    echo '<option value="'.$this->ceros($valor_inicio,2).'" '.($valor_inicio==$value_selected?'selected':'').' label="'.$this->ceros($valor_inicio,2).'">'.$mensaje_inicio.'</option>';      
   if  ( $inc_dec )
   {
    for ( $a=$inicio; $a <= $fin ;$a++)
    {
     echo '<option value="'.$this->ceros($a,2).'" '.($a==$value_selected?'selected':'').' label="'.$this->ceros($a,2).'">'.$this->ceros($a,2).'</option>';   
    }
   }
   else
   {
    for ( $a=$inicio; $a >= $fin ;$a--)
    {
     echo '<option value="'.$this->ceros($a,2).'" '.($a==$value_selected?'selected':'').' label="'.$this->ceros($a,2).'">'.$this->ceros($a,2).'</option>';   
    }
   }
   echo '</select>';
  }

  function combobox($items,$nombre_combo,$onchange,$value_selected)
  {
   //$items matriz
   // 1: Campo llave
   // 2: Display
   // 3: Label
   // $nombre_combo: nombre del combo
   // $onchange: secuencia script que se ejecutara al momento de cambiar el item del combobox
   // $value_selected: valor seleccionado

   echo '<select name="'.$nombre_combo.'" class="inputbox" onchange="'.$onchange.'">';
   for ( $a=0; $a < sizeof($items);$a++)
   {
    echo '<option value="'.$items[$a][0].'" '.$this->iif($items[$a][0]==$value_selected,'selected','').' label="'.$items[$a][2].'">'.$items[$a][1].'</option>';   
   }
   echo '</select>';
  }
  



 function pie($tam_x,$tam_y,$matriz,$color_fondo,$titulo)
 {
  $total_distribucion = 0 ;
  $tam_text_max = 0 ;
  $tam_val_max = 0 ;
  // Obtener total de la distribucion
  for ( $a = 0 ; $a < sizeof($matriz) ; $a++ )
  {
   $total_distribucion+=$matriz[$a][2];
   if ( $tam_text_max < strlen($matriz[$a][1]) )
     $tam_text_max = strlen($matriz[$a][1]);
   if ( $tam_val_max < strlen(number_format($matriz[$a][2],2,'.',',')) )
     $tam_val_max = strlen(number_format($matriz[$a][2],2,'.',','));
   $matriz_colores[$a][1] = rand(0,255);
   $matriz_colores[$a][2] = rand(0,255);
   $matriz_colores[$a][3] = rand(0,255);   
  }
  
  for ( $a = 0 ; $a < sizeof($matriz) ; $a++ )
  {
   $arreglo_porcentajes[$a][1] = round(( $matriz[$a][2] * 100 ) / $total_distribucion ,2); // Obtiene el porcentaje sobre la distribuci�n
   $arreglo_porcentajes[$a][2] =  ( 360 * ( $arreglo_porcentajes[$a][1] / 100 ) ); // Obtiene la cantidad de angulos
  }
  // Creare el area de la imagen
  $image = imagecreate($tam_x, $tam_y);

  // Fondo de la imagen

  sscanf($color_fondo, "%2x%2x%2x", $red, $green, $blue);

  $bg = imagecolorallocate($image, $red, $green, $blue);
  
  $color = imagecolorallocate($image, 0, 0, 0);
  imagettftext($image, 20, 0, 10, 20, $color, "fonts/arial.ttf",$titulo);


  // Dibujar el Pie
  $angulo_inicio = 1;
  $angulo_final = 0;
  for ( $a = 0 ; $a < sizeof($matriz) ; $a++ )
  {
   $col_ellipse = imagecolorallocate($image,$matriz_colores[$a][1],$matriz_colores[$a][2],$matriz_colores[$a][3]);  
   $angulo_inicio =  $angulo_final  ;
   $angulo_final = $angulo_inicio + $arreglo_porcentajes[$a][2];
   imagefilledarc($image, 200, ($tam_y / 2)+35, 300, 300,$angulo_inicio,$angulo_final,$col_ellipse,IMG_ARC_PIE);
  }
  // Dibujar las series
  $inicio_x = 405 ;
  $inicio_y = 75 ;  
  $alto = 10 ;
  $ancho = 10;
  $salto = 20 ;
  $inicio_x_rec = $inicio_x - 5 ;
  $inicio_y_rec = $inicio_y - 10 - $alto  ; 
  $tam_text = 7 ;

  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 10, $inicio_y - $alto + 4, $color, "fonts/arial.ttf","Serie");
  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 15+( $tam_text * $tam_text_max),$inicio_y - $alto + 4, $color, "fonts/arial.ttf","Valor");
  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 15+( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max) + 1, $inicio_y - $alto + 4, $color, "fonts/arial.ttf","%");   
  
  for ( $a = 0 ; $a < sizeof($matriz) ; $a++ )
  {
   $col_cuadro = imagecolorallocate($image,$matriz_colores[$a][1],$matriz_colores[$a][2],$matriz_colores[$a][3]);  
   $bg = imagecolorallocate($image, $matriz_colores[$a][1],$matriz_colores[$a][2],$matriz_colores[$a][3]);
   imagefilledrectangle($image,$inicio_x,$inicio_y,$inicio_x+$ancho,$inicio_y+$alto,$bg);
   $color = imagecolorallocate($image, 0, 0, 0);
   imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 10, $inicio_y+$alto, $color, "fonts/arial.ttf",$matriz[$a][1]);
   imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 15+( $tam_text * $tam_text_max), $inicio_y+$alto, $color, "fonts/arial.ttf",number_format($matriz[$a][2],2,'.',','));
   imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 15+( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max) + 1, $inicio_y+$alto, $color, "fonts/arial.ttf",number_format($arreglo_porcentajes[$a][1],2,'.',','));   

   $inicio_y += $salto ;
  }  
  $fin_x_rec = $inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max)+( $tam_text * $tam_val_max)+40 ;
  $fin_y_rec = $inicio_y + 5 ; 

  imagerectangle ( $image, $inicio_x_rec, $inicio_y_rec, $fin_x_rec, $fin_y_rec , 1)  ;

  imageline ( $image, $inicio_x_rec, $inicio_y_rec + $alto + 6 ,$fin_x_rec, $inicio_y_rec + $alto + 6, 1)  ;
  
  imageline ( $image, $inicio_x + $ancho + 6, $inicio_y_rec  ,$inicio_x + $ancho + 6, $fin_y_rec, 1)  ;  

  imageline ( $image,  $inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max), $inicio_y_rec  ,$inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max), $fin_y_rec, 1)  ;  

  imageline ( $image,  $inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max), $inicio_y_rec  ,$inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max), $fin_y_rec, 1)  ;    

  imagerectangle ( $image, $inicio_x_rec, $fin_y_rec+5, $fin_x_rec, $fin_y_rec+25 , 1)  ;  

  imageline ( $image,  $inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max), $fin_y_rec+5  ,$inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max), $fin_y_rec+25, 1)  ;  

  imageline ( $image,  $inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max), $fin_y_rec+5  ,$inicio_x + 5 + $ancho +  ( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max), $fin_y_rec+25, 1)  ;    


  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 10, $fin_y_rec+28-$tam_text , $color, "fonts/arial.ttf","T O T A L E S");
  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 15+( $tam_text * $tam_text_max),$fin_y_rec+28-$tam_text , $color, "fonts/arial.ttf",number_format($total_distribucion,2,'.',','));
  imagettftext($image, $tam_text, 0, $inicio_x + $ancho + 10+( $tam_text * $tam_text_max) + ( $tam_text * $tam_val_max) + 1, $fin_y_rec+28-$tam_text , $color, "fonts/arial.ttf","100.00");   

 
  header("Content-type: image/png");
  imagepng($image);
}
   function ceros($valor,$no_ceros)
   {
     $tam = strlen($valor);
	 $max = $no_ceros - $tam;
     $str_ceros = "";
	 for ($i= 1; $i<= $max ; $i++ )
	  $str_ceros = $str_ceros . "0" ;
	 $str_ceros  =  $str_ceros . $valor;
	 return $str_ceros;
   }
   function zpic($valor,$tam)
   {
    $tam_ceros = $tam - strlen($valor) ;
	$resultado = "";
	for ($a  = 1 ; $a <= $tam_ceros ; $a++)
	 $resultado = $resultado . "0";
	$resultado = $resultado . $valor;
	return $resultado; 
   }
    function iif($cond, $if_true, $if_false){
    	if ($cond) return $if_true;
    	else return $if_false;
    }

   function calendario($fecha_parametro,$url)
   // 
   // Despliega el calendario 
   //
   {
    $meses[1] = "ENERO";
    $meses[2] = "FEBRERO";
    $meses[3] = "MARZO";	
    $meses[4] = "ABRIL";
    $meses[5] = "MAYO";
    $meses[6] = "JUNIO";
    $meses[7] = "JULIO";
    $meses[8] = "AGOSTO";
    $meses[9] = "SEPTIEMBRE";
    $meses[10] = "OCTUBRE";
    $meses[11] = "NOVIEMBRE";
    $meses[12] = "DICIEMBRE";
    for ( $contador = 1 ; $contador <= 6 ; $contador++)
    {
     $calendario[$contador][1]="";
     $calendario[$contador][2]="";
     $calendario[$contador][3]="";
     $calendario[$contador][4]="";
     $calendario[$contador][5]="";         
     $calendario[$contador][6]="";
     $calendario[$contador][7]="";      
    }
    $calendario_parametro = getdate(strtotime($fecha_parametro));
    $calendario_inicio = getdate(date(mktime(0,0,0,$calendario_parametro['mon'],1,$calendario_parametro['year'])));
    if ($calendario_parametro['mon'] == 12)
    {
     $siguiente_mes  = 1;
     $siguiente_anio = $calendario_parametro['year'] + 1 ;
     }
    else
    {
     $siguiente_anio = $calendario_parametro['year'];
     $siguiente_mes = $calendario_parametro['mon'] + 1;
    }
	if ($calendario_parametro['mon'] == 01)
    {
     $anterior_mes  = 12;
     $anterior_anio = $calendario_parametro['year'] - 1 ;
     }
    else
    {
     $anterior_anio = $calendario_parametro['year'];
     $anterior_mes = $calendario_parametro['mon'] - 1;
    }

    $calendario_final = getdate(date(mktime(0,0,0,$siguiente_mes,0,$siguiente_anio)));
    $calendario_fila = 1 ;
    $calendario_columna = $calendario_inicio['wday'] + 1;
    $calendario_total = $calendario_final['mday'] - $calendario_inicio['mday'] + 1;
    for ( $i = 1 ; $i <= $calendario_total; $i++ )
    {
     $calendario[$calendario_fila][$calendario_columna] = $i;
     if ($calendario_columna == 7)
     {
       $calendario_fila = $calendario_fila +1;
       $calendario_columna = 1;
     }
     else
       $calendario_columna = $calendario_columna + 1 ;
    }
	$url_siguiente = $url ."?fecha=".$siguiente_anio."-".$this->zpic($siguiente_mes,2)."-01";
	$url_anterior = $url ."?fecha=".$anterior_anio."-".$this->zpic($anterior_mes,2)."-01";
	$url_actual = $url . "?fecha=".date ("Y-m-d"); 
    echo "<table width=\"75%\"  border=\"0\" cellpadding=\"2\" cellspacing=\"2\" class=\"adminlist\">";
    echo "<tr>";
    echo "<td colspan=\"7\">";
    echo "<table width=\"100%\"  border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	echo "<tr>";
	echo "<th width=\"25%\" align=\"center\">";
	echo "<a href=\"".$url_anterior ."\" onmouseover=\"window.status='Mes Anterior';return(true);\" ><<</a>";
    echo "</th>";
    echo "<th width=\"50%\ align=\"center\">";
	echo "<div align=\"center\"><span class=\"Estilo1\">" . $meses[$calendario_parametro['mon']] . " - " .  $calendario_parametro['year']. "</span></div>";
    echo "</th>";
	echo "<th width=\"25%\" align=\"center\">";
	echo "<a href=\"".$url_siguiente."\" onmouseover=\"window.status='Siguiente Mes';return(true);\" >>></a>";
    echo "</th>";
    echo "</tr>";
	echo "</table>";
	echo "</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td width=\"13%\" ><div align=\"center\">Dom.</div></td>";
    echo "<td width=\"15%\" ><div align=\"center\">Lunes</div></td>";
    echo "<td width=\"17%\" ><div align=\"center\">Martes</div></td>";
    echo "<td width=\"14%\" ><div align=\"center\">Mier.</div></td>";
    echo "<td width=\"17%\" ><div align=\"center\">Jueves </div></td>";
    echo "<td width=\"12%\" ><div align=\"center\">Vier.</div></td>";
    echo "<td width=\"12%\" ><div align=\"center\">S&aacute;b.</div></td>";
    echo "</tr>";
    for ( $semana = 1 ; $semana <= 6 ; $semana++ )
    {
     echo "<tr>";
     for ( $dia = 1 ; $dia <= 7;  $dia++ )
     {
      $url_cambio = $url . "?fecha=".$calendario_parametro['year']."-".$this->zpic($calendario_parametro['mon'],2)."-". $this->zpic($calendario[$semana][$dia],2) 	;
      $mouse_over = $calendario_parametro['year']."-".$this->zpic($calendario_parametro['mon'],2)."-". $this->zpic($calendario[$semana][$dia],2) 	;	  
   	  if  ( $calendario[$semana][$dia] == $calendario_parametro['mday'])
	  {
        echo "<td align=\"center\">";
        echo "";
		if ( $calendario[$semana][$dia] != "" )
   	     echo $calendario[$semana][$dia];
		else 
		 echo "&nbsp;"; 
    	echo "";
   	  }  
      else
	  {
        echo "<td align=\"center\" >";		
        echo "<a href=\"".$url_cambio."\" onmouseover=\"window.status='".$mouse_over."';return(true);\">";
		if ( $calendario[$semana][$dia] != "" )		
   	     echo $calendario[$semana][$dia];
		else 
		 echo "&nbsp;"; 
    	echo "</a>";
	  }	
 	  echo "</td>";
     }
     echo "</tr>";
    }
    echo  "<tr class=\"encabezado_3\">";
    echo  "<td colspan=\"7\"  align=\"left\">";
	echo  "<a href=\"".$url_actual."\" onmouseover=\"window.status='".date ("Y-m-d")."';return(true);\"><img src=\"imagenes/calendario.gif\" alt=\"Reestablecer fecha actual\" width=\"23\" height=\"23\" border=\"0\"></a>";
	echo "</td>";
    echo  "</tr>";
    echo "</table>";
   }

  
   // Convierte del formato de horas a entero 
   function horas_entero($horas,$minutos)
   {
    $parte_entera = $horas ;
	$parte_decimal = ( $minutos * 100 ) / 60 ;
	
	return $parte_entera .".". $parte_decimal ;
   } 
  } 

// Clase que despliega el grid en pantalla  
 class grid
 {
   var $conexion;
   var $data;
   var $matriz;
   var $tam_grid;
   var $no_pagina;
   var $no_registros;
   var $componente;
   var $total_registros;
   var $paginamiento;
   var $pie;
   var $muestra_forma;
   
   // Constructor de la clase Grid
  function grid($parametrosGrid,$strSql,$strSql_,$tamGrid, $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db,$no_pagina,$no_registros,$componente,$paginacion=1,$pie=1,$forma=1)
  {
   $this->conexion =  new conexion( $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db); // Obtiene los datos de la p�gina seleccinados
   $this->data  =  new conexion( $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db); // Obtiene la cantidad total de registros de la tabla

   $this->conexion->ejecutar_consulta($strSql);
   $this->data->ejecutar_consulta($strSql_);   

   $registro = $this->data->obtener_registro();
   
   $this->total_registros = $registro['total_registros'];
   $this->paginacion = $paginacion;
   $this->pie = $pie;
   
   $this->matriz = $parametrosGrid;
   $this->tam_grid = $tamGrid;
   $this->no_pagina = $no_pagina;
   $this->no_registros = $no_registros;
   $this->componente = $componente;   
   $this->muestra_forma = $forma;
   $this->desplegarGrid();   
  } 
  
  // Despliega el grid en pantalla
  function desplegarGrid()
  {
   $utilidades = new utilidades();
   $registro_final = $utilidades->iif($this->no_pagina * $this->no_registros>$this->total_registros,$this->total_registros,$this->no_pagina * $this->no_registros);
   $registro_inicio = $registro_final - $this->conexion->num_registros()+1;
   $num_celdas = count($this->matriz); 
   if ( $this->muestra_forma == 1 )
   {
    echo "<form name='frm_data' method='post' action=''>";
  	}
   echo "<input name='h_id_1' type='hidden' value=''>";
   echo "<input name='h_id_2' type='hidden' value=''>";
   echo "<input name='h_id_3' type='hidden' value=''>";
   echo "<input name='h_id_4' type='hidden' value=''>";
   echo "<input name='h_id_5' type='hidden' value=''>";            
   echo "<table width='".$this->tam_grid."' class='adminlist'>\n";
   echo "<tr >\n";
   for ( $contador = 0 ; $contador < $num_celdas ; $contador++)
   {
    if ( $contador == 0 )
	{
 	 echo  "<th class='title'  align='left' width='2%'>";
 	 echo "#";
	 echo "</th>\n";
     echo  "<th class='title'  align='center' width='3%'>";
//	 echo "<INPUT onclick='checkAll(".$this->no_registros.");' type='checkbox' value=''  name='toggle'>\n";
 	 echo "</th>\n";
	}
    echo  "<th class='title'  align='".$this->matriz[$contador][2]."' width='".$this->matriz[$contador][1]."'>";
	echo $this->matriz[$contador][0];
	echo "</th>\n";
   }
   echo "</tr>\n";   
   $no_registros= $registro_inicio - 1  ;
   $contador_checkbox = 0;
   $tipo_columna = 0;
   while ( $registro = $this->conexion->obtener_registro())
   {
    $no_registros++;
	$contador_checkbox++;
    echo "<tr class='row".$tipo_columna."'>\n";      
    for ( $contador = 0 ; $contador < $num_celdas ; $contador++)
    {
	 $url = $this->matriz[$contador][4];
	 $where_delete = "";
	 $primera_vez = 1 ;
	 	 
     for ( $cnt = 0 ; $cnt < $num_celdas ; $cnt++)
	   if ($this->matriz[$cnt][5])
	   {
 	    $url = $url . '&' . $this->matriz[$cnt][3] .'='.$registro[$this->matriz[$cnt][3]];	   
		if ($primera_vez)
		{
		 $primera_vez = 0;
		 $where_delete .= $this->matriz[$cnt][3] ."='".$registro[$this->matriz[$cnt][3]]."'" ;	    
		}
		else
		{
		 $where_delete .= ' and ' . $this->matriz[$cnt][3] ."='".$registro[$this->matriz[$cnt][3]]."'" ;	    
		}
	   }
	 if ( $contador == 0 )
	 {
     echo  "<td  align='left'>";
 	 echo $no_registros;
 	 echo "</td>\n";
     echo  "<td class='title'  align='center' width='3%'>";
	 echo "<input name='cid[]' id='cb".$contador_checkbox."' type='checkbox' value=\"".$where_delete."\" onclick=isChecked(this);>";
//	 echo "<input name='cid[]' id='cb".$contador_checkbox."' type='checkbox' value='".$this->matriz[0][6]."' onclick=isChecked(this);>";
	 echo "</td>\n";
	 }
     echo  "<td  align='".$this->matriz[$contador][2]."' width='".$this->matriz[$contador][1]."'>";

	 
	 
	 if (strlen($this->matriz[$contador][4]))
	  echo "<a href='".$url."'>\n";

 	 echo $utilidades->iif(strlen($registro[$this->matriz[$contador][3]])==0,'&nbsp;',$registro[$this->matriz[$contador][3]]);

	 if (strlen($this->matriz[$contador][4]))
	  echo "</a>\n";
	 
 	 echo "</td>\n";
    }
    echo "</tr>\n";      	
	if ( !$tipo_columna )
	 $tipo_columna = 1 ;
	else
	 $tipo_columna = 0;
   }
   $num_celdas+=2;
   
   $cinco='';
   $diez='';
   $quince='';
   $veinte='';
   $veinticinco= '';
   $treinta= '';   
   $cincuenta='';
   
   switch($this->no_registros)
   {
    case 5:
		   {
		    $cinco='selected';
		    break;
		   }
	case 10:
		   {
		    $diez='selected';		   
		    break;
		   }
	case 15:
		   {
		    $quince='selected';		   		   
		    break;
		   }
	case 20:
		   {
		    $veinte='selected';		   		   
		    break;
		   }
	case 25:
		   {
   		    $veinticinco='selected';		   
		    break;
		   }
	case 30:
		   {
   		    $treinta='selected';		   
		    break;
		   }
	case 50:
		   {
		    $cincuenta='selected';		   		   
		    break;
		   }
   }
   $total_paginas = ceil ($this->total_registros / $this->no_registros );
   echo "<tr>\n";
   echo "<th colspan='". $num_celdas ."' align='right'>\n";
   if ($this->paginacion)
   {
    echo "Ir a p&aacute;gina:";
    echo "<select name='pagina' class='inputbox' onchange='javascript:cambiar_pagina()'>";   
    for($a=1;$a<= $total_paginas;$a++) 
    {
     if ( $a == $this->no_pagina )
       echo "<option value='".$a."' selected>".$a."</option>";
     else
       echo "<option value='".$a."' >".$a."</option>";		  
    }
    echo "</select>";   
  }	
   echo "</th>\n";
  	 echo "</tr>\n";
   echo "</table>\n";
   echo "<table width='".$this->tam_grid."'>\n";
   echo "<tr>\n";
   echo "<td align='center'>\n";
   echo "<input type='hidden' name='componente' value='".$this->componente."'>";
   echo "<INPUT type='hidden' value='0' name='boxchecked'>";
   echo "<INPUT type='hidden' value='' name='valuechecked'>";   
   if ( $this->pie ) 
   {
    echo " &nbsp;&nbsp;Resultado ";
    echo $registro_inicio;
    echo " - ";
    echo $registro_final;
    echo " de ";
    echo $this->total_registros;
   }	
   echo "</td>\n";  
   echo "</tr>\n";
   echo "</table>\n";
   if ( $this->muestra_forma == 1 )
   {
    echo "</form>\n";
	}
   $this->conexion->cerrar_conexion();
   $this->data->cerrar_conexion();
  }
 }
// Clase que despliega el grid en pantalla  
 class grid2
 {
   var $conexion;
   var $data;
   var $matriz;
   var $tam_grid;
   var $no_pagina;
   var $no_registros;
   var $componente;
   var $total_registros;
   var $paginacion;
   var $pie;
   
   
   // Constructor de la clase Grid
  function grid2($parametrosGrid,$strSql,$strSql_,$tamGrid, $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db,$no_pagina,$no_registros,$componente,$paginacion=1,$pie=1)
  {
   $this->conexion =  new conexion( $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db); // Obtiene los datos de la p�gina seleccinados
   $this->data  =  new conexion( $anfitrion_db,$usuario_db,$contrasena_db,$catalogo_db); // Obtiene la cantidad total de registros de la tabla

   $this->conexion->ejecutar_consulta($strSql);
   $this->data->ejecutar_consulta($strSql_);   

   $registro = $this->data->obtener_registro();
   
   $this->total_registros = $registro['total_registros'];
   $this->paginacion = $paginacion;
   $this->pie = $pie;
   
   $this->matriz = $parametrosGrid;
   $this->tam_grid = $tamGrid;
   $this->no_pagina = $no_pagina;
   $this->no_registros = $no_registros;
   $this->componente = $componente;   
   $this->desplegarGrid();   
  } 
  
  // Despliega el grid en pantalla
  function desplegarGrid()
  {
   $utilidades = new utilidades();
   $registro_final = $utilidades->iif($this->no_pagina * $this->no_registros>$this->total_registros,$this->total_registros,$this->no_pagina * $this->no_registros);
   $registro_inicio = $registro_final - $this->conexion->num_registros()+1;
   $num_celdas = count($this->matriz); 
   echo "<form name='frm_data' method='post' action=''>";
   echo "<input name='h_id_1' type='hidden' value=''>";
   echo "<input name='h_id_2' type='hidden' value=''>";
   echo "<input name='h_id_3' type='hidden' value=''>";
   echo "<input name='h_id_4' type='hidden' value=''>";
   echo "<input name='h_id_5' type='hidden' value=''>";            
   echo "<table width='".$this->tam_grid."' class='adminlist'>\n";
   echo "<tr >\n";
   for ( $contador = 0 ; $contador < $num_celdas ; $contador++)
   {
    if ( $contador == 0 )
	{
 	 echo  "<th class='title'  align='left' width='2%'>";
 	 echo "#";
	 echo "</th>\n";
     echo  "<th class='title'  align='center' width='3%'>";
//	 echo "<INPUT onclick='checkAll(".$this->no_registros.");' type='checkbox' value=''  name='toggle'>\n";
 	 echo "</th>\n";
	}
    echo  "<th class='title'  align='".$this->matriz[$contador][2]."' width='".$this->matriz[$contador][1]."'>";
	echo $this->matriz[$contador][0];
	echo "</th>\n";
   }
   echo "</tr>\n";   
   $no_registros= $registro_inicio - 1  ;
   $contador_checkbox = 0;
   $tipo_columna = 0;
   while ( $registro = $this->conexion->obtener_registro())
   {
    $no_registros++;
	$contador_checkbox++;
    echo "<tr class='row".$tipo_columna."'>\n";      
    for ( $contador = 0 ; $contador < $num_celdas ; $contador++)
    {
	 $url = $this->matriz[$contador][4];
	 $where_delete = "";
	 $primera_vez = 1 ;
	 	 
     for ( $cnt = 0 ; $cnt < $num_celdas ; $cnt++)
	   if ($this->matriz[$cnt][5])
	   {
 	    $url = $url . '&' . $this->matriz[$cnt][3] .'='.$registro[$this->matriz[$cnt][3]];	   
		if ($primera_vez)
		{
		 $primera_vez = 0;
		 $where_delete .= $this->matriz[$cnt][3] ."='".$registro[$this->matriz[$cnt][3]]."'" ;	    
		}
		else
		{
		 $where_delete .= ' and ' . $this->matriz[$cnt][3] ."='".$registro[$this->matriz[$cnt][3]]."'" ;	    
		}
	   }
	 if ( $contador == 0 )
	 {
     echo  "<td  align='left'>";
 	 echo $no_registros;
 	 echo "</td>\n";
     echo  "<td class='title'  align='center' width='3%'>";
	 echo "<input name='cid[]' id='cb".$contador_checkbox."' type='checkbox' value=\"".$where_delete."\" onclick=isCheckedMultiple(this);>";
//	 echo "<input name='cid[]' id='cb".$contador_checkbox."' type='checkbox' value='".$this->matriz[0][6]."' onclick=isChecked(this);>";
	 echo "</td>\n";
	 }
     echo  "<td  align='".$this->matriz[$contador][2]."' width='".$this->matriz[$contador][1]."'>";

	 
	 
	 if (strlen($this->matriz[$contador][4]))
	  echo "<a href='".$url."'>\n";

 	 echo $utilidades->iif(strlen($registro[$this->matriz[$contador][3]])==0,'&nbsp;',$registro[$this->matriz[$contador][3]]);

	 if (strlen($this->matriz[$contador][4]))
	  echo "</a>\n";
	 
 	 echo "</td>\n";
    }
    echo "</tr>\n";      	
	if ( !$tipo_columna )
	 $tipo_columna = 1 ;
	else
	 $tipo_columna = 0;
   }
   $num_celdas+=2;
   
   $cinco='';
   $diez='';
   $quince='';
   $veinte='';
   $veinticinco= '';
   $treinta= '';   
   $cincuenta='';
   
   switch($this->no_registros)
   {
    case 5:
		   {
		    $cinco='selected';
		    break;
		   }
	case 10:
		   {
		    $diez='selected';		   
		    break;
		   }
	case 15:
		   {
		    $quince='selected';		   		   
		    break;
		   }
	case 20:
		   {
		    $veinte='selected';		   		   
		    break;
		   }
	case 25:
		   {
   		    $veinticinco='selected';		   
		    break;
		   }
	case 30:
		   {
   		    $treinta='selected';		   
		    break;
		   }
	case 50:
		   {
		    $cincuenta='selected';		   		   
		    break;
		   }
   }
   $total_paginas = ceil ($this->total_registros / $this->no_registros );
   echo "<tr>\n";
   echo "<th colspan='". $num_celdas ."' align='right'>\n";
   if ($this->paginacion)
   {
    echo "Ir a p&aacute;gina:";
    echo "<select name='pagina' class='inputbox' onchange='javascript:cambiar_pagina()'>";   
    for($a=1;$a<= $total_paginas;$a++) 
    {
     if ( $a == $this->no_pagina )
       echo "<option value='".$a."' selected>".$a."</option>";
     else
       echo "<option value='".$a."' >".$a."</option>";		  
    }
    echo "</select>";   
  }	
   echo "</th>\n";
  	 echo "</tr>\n";
   echo "</table>\n";
   echo "<table width='".$this->tam_grid."'>\n";
   echo "<tr>\n";
   echo "<td align='center'>\n";
   echo "<input type='hidden' name='componente' value='".$this->componente."'>";
   echo "<INPUT type='hidden' value='0' name='boxchecked'>";
   echo "<INPUT type='hidden' value='' name='valuechecked'>";   
   if ( $this->pie ) 
   {
    echo " &nbsp;&nbsp;Resultado ";
    echo $registro_inicio;
    echo " - ";
    echo $registro_final;
    echo " de ";
    echo $this->total_registros;
   }	
   echo "</td>\n";  
   echo "</tr>\n";
   echo "</table>\n";
   echo "</form>\n";
   $this->conexion->cerrar_conexion();
   $this->data->cerrar_conexion();
  }
 }


// Security Functions
class SecurityManager {
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Redirect to logout page with security message
     */
    public function redirectToLogout() {
        echo '<script>alert("Su sesión venció"); self.location="logout.php";</script>';
        return; // Allow the script to execute before exit
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input data (static method)
     */
    public static function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $input);
        }
        
        $input = trim($input);
        
        switch ($type) {
            case 'int':
                return filter_var($input, FILTER_VALIDATE_INT) !== false ? (int)$input : 0;
            case 'float':
                return filter_var($input, FILTER_VALIDATE_FLOAT) !== false ? (float)$input : 0.0;
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false ? $input : '';
            case 'url':
                return filter_var($input, FILTER_VALIDATE_URL) !== false ? $input : '';
            case 'alphanumeric':
                return preg_replace('/[^a-zA-Z0-9]/', '', $input);
            case 'username':
                // Allow letters, numbers, dots, underscores, and hyphens for usernames
                return preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $input);
            case 'component':
                // Allow letters, numbers, and underscores for component names
                return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
            case 'codigo_barra':
                return preg_replace('/[^a-zA-Z0-9\-_]/', '', $input);
            case 'string':
            default:
                return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate session and check for session fixation
     */
    public static function validateSession() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: logout.php");
            exit();
        }
        
        // Check for session timeout (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_destroy();
            header("Location: index.php?timeout=1");
            exit();
        }
        
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 300) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
    
    /**
     * Secure file inclusion
     */
    public static function includeComponent($component) {
        // Whitelist of allowed components
        $allowedComponents = [
            'agentes', 'panel_avances', 'paquetes_todos', 'paquetes_todosv2',
            'activar_codigo_barra', 'ver_link_compra', 'ver_link_compra_pre',
            'validar_paquete', 'actualizar_partida', 'upload_file_todos'
        ];
        
        if (!in_array($component, $allowedComponents)) {
            error_log("Attempted to include unauthorized component: " . $component);
            return false;
        }
        
        $file = $component . ".php";
        if (file_exists($file)) {
            // Set tokenid for backward compatibility with older components
            $_REQUEST['tokenid'] = $_REQUEST['token'] ?? '';
            include($file);
            return true;
        }
        
        return false;
    }
}

// Database helper class for secure queries
class DatabaseHelper {
    private $pdo;
    
    public function __construct() {
        try {
            error_log("DatabaseHelper: Attempting to create PDO connection");
            $this->pdo = getPDOConnection();
            error_log("DatabaseHelper: PDO connection created successfully");
        } catch (Exception $e) {
            error_log("DatabaseHelper: Failed to create connection - " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute a prepared statement with parameters
     */
    public function executeQuery($sql, $params = []) {
        try {
            error_log("DatabaseHelper: Preparing query: " . $sql);
            error_log("DatabaseHelper: Parameters: " . json_encode($params));
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            error_log("DatabaseHelper: Query executed successfully");
            return $stmt;
        } catch (PDOException $e) {
            error_log("DatabaseHelper: PDO query failed - " . $e->getMessage());
            error_log("DatabaseHelper: SQL was: " . $sql);
            error_log("DatabaseHelper: Parameters were: " . json_encode($params));
            throw new Exception("Database operation failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get single record
     */
    public function getRecord($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Get multiple records
     */
    public function getRecords($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute stored procedure
     */
    public function executeStoredProcedure($procedureName, $params = []) {
        if (empty($params)) {
            $sql = "EXEC $procedureName";
        } else {
            $placeholders = str_repeat('?,', count($params) - 1) . '?';
            $sql = "EXEC $procedureName $placeholders";
        }
        return $this->executeQuery($sql, $params);
    }
    
    /**
     * Alias for getRecords - for backward compatibility
     */
    public function query($sql, $params = []) {
        return $this->getRecords($sql, $params);
    }
}
