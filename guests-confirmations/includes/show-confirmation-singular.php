<?php

define('GC_PATH',plugin_dir_path(__FILE__));
include_once(GC_PATH . "includes/functions.php");

session_start();

$invitados = $_SESSION['invitados'];
$asistencia = $invitados[0][asistencia];
$sqldate = $invitados[0][modified_at];
$pase_adicional = $invitados[0][pase_adicional];

$fecha = getFecha($sqldate);

?>

<?php if ($asistencia == 'si') : ?>
<!-- ESTE ARCHIVO DEBE DE ESTAR DENTRO DE LA CARPETA DEL TEMA ACTIVO PARA SU LECTURA POR EL PLUGIN -->
<p><b>
¡Gracias por tu confirmación <?php echo $invitados[0][nombre];?>!. 
Los datos que se han registrado con fecha <?php echo getFecha($invitados[0][modified_at])[0];?> y horario de <?php echo getFecha($invitados[0][modified_at])[1];?> son los siguientes:
</b></p>
<p><b>
Asistencia: <?php echo $invitados[0][asistencia];?> </br>
Pases confirmados: <?php echo $invitados[0][pases_confirmados];?> </br>
Se enviará un correo de confirmación al correo proporcionado: <?php echo $invitados[0][correo];?> </br>  
</b></p>
<p><b>
¡Te esperamos!
</b></p>

<form action="" method="post">
    <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
    <input type="button" value="Modificar" class="px-5" onclick = "location='<?php echo getBaseUrl()."modificar-confirmacion-singular";?>'">
</form>

<?php else : ?>
<p class='exito'><b>Gracias por tu respuesta <?php echo $invitados[0][nombre] ?>. Has indicado que no asistirás al evento. Por cuestiones de actualización de espacios e invitados no se puede modificar esta respuesta. Si la confirmación ha sido errónea, por favor diríjete a la sección de contaco y comunícate con los novios para que se haga la modificación. Gracias. </b></p>
<p><b>Fecha de registro: <?php echo $fecha[0];?> a las <?php echo $fecha[1];?> hrs. </b></p>

<input type="button" value="Ir a inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'" >

<?php endif; ?>