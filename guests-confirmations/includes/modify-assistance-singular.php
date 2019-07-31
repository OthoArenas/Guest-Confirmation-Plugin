<?php

define('GC_PATH',plugin_dir_path(__FILE__));
include_once(GC_PATH . "includes/functions.php");

session_start();

global $tabla_invitados;

$registros = $_SESSION['invitados'];
$num_pases = 1;
$sqldate = getSQLDate();
$asistencia = $registros[0][asistencia];
$nombre = $registros[0][nombre];
$apellidos = $registros[0][apellidos];
$es_familia = $registros[0][es_familia];
$familia = $registros[0][familia];
$pase_adicional = $registros[0][pase_adicional];
$pases_adicionales = $registros[0][pases_adicionales];

$registros = getRegistros($tabla_invitados, $nombre, $apellidos, $familia);

$_SESSION['invitados'] = $registros;

$fecha = getFecha($sqldate);

?>

<script>
    function showHidePasesAdicionalesForm(){
        if(document.getElementById('form-asistencia-si').checked){
            document.getElementById('pase_adicional_form').style.display='block';
        }else if (document.getElementById('form-asistencia-no').checked){
            document.getElementById('pase_adicional_form').style.display='none';
        }
    }

</script>

<?php if($asistencia != 'si') : ?>

<?php if ($es_familia == 'si') : ?>

<p class='info'><b>¡Bienvenid@ <?php echo $registros[0][nombre];?>! Favor de indicar tu asistencia. ¡Gracias!</b><p>
<p><b><small style="text-transform:uppercase">NOTA: Estás contemplado en una invitación familiar, de preferencia, se deberá confirmar la asistencia de todos sus integrantes seleccionando en el formulario anterior la opción "Es familia".</small></b></p>
<p><b><small>Por favor, ten en cosideración que al enviar como respuesta un "No" tu lugar será asignado a otra persona.</small></b></p>

<form action="" method="post">
    <input type="button" value="Regresar" class="px-5" onclick = "location='<?php echo getBaseUrl()."confirmacion-de-asistencia";?>'">
</form>
<br>

<?php elseif($pase_adicional=='si' && $pases_adicionales==1 ) : ?>

<p class='info'><b>¡Bienvenid@ <?php echo $registros[0][nombre];?>! Cuentas con un pase adicional. Has escogido hacer una modificación a tu confirmación. Favor de indicar su asistencia así como el de tu pase adicional ¡Gracias!</b><p>
<p><b><small>Por favor, ten en cosideración que al enviar como respuesta un "No" tus lugares serán asignados a otras personas.</small></b></p>

<?php elseif($pase_adicional=='si' && $pases_adicionales>1 ) : ?>

<p class='info'><b>¡Bienvenid@ <?php echo $registros[0][nombre];?>! Cuentas con <?php echo $pases_adicionales?> pases adicionales. Favor de indicar su asistencia así como el de tus pases adicionales ¡Gracias!</b><p>
<p><b><small>Por favor, ten en cosideración que al enviar como respuesta un "No" tus lugares serán asignados a otras personas.</small></b></p>

<?php else : ?>

<p class='info'><b>¡Bienvenid@ <?php echo $registros[0][nombre];?>!. Has escogido hacer una modificación a tu confirmación. Favor de indicar tu asistencia. ¡Gracias!</b><p>
<p><b><small>Por favor, ten en cosideración que al enviar como respuesta un "No" tu lugar será asignado a otra persona.</small></b></p>

<?php endif; ?>
 
<!-- ESTE ARCHIVO DEBE DE ESTAR DENTRO DE LA CARPETA DEL TEMA ACTIVO PARA SU LECTURA POR EL PLUGIN -->
<form action="<?php echo getBaseUrl()."confirmacion-de-asistencia-singular"; ?>" method="post">

    <!-- CAMPO ASISTENCIA -->
    <div class="form-group">
        <label for="asistencia">¿Confirma asistencia?</label>
        <br>
        <input type="radio" name="form-singular-assistance" id="form-asistencia-si" value="si" onclick="showHidePasesAdicionalesForm();"> Si<br>
        <input type="radio" name="form-singular-assistance" id="form-asistencia-no" value="no" onclick="showHidePasesAdicionalesForm();"> No<br> 
    </div>

    <?php if($pase_adicional == 'si' && $pases_adicionales==1) : ?>

    <!-- CAMPO PASE ADICIONAL-->
    <div class="form-group" id="pase_adicional_form" style="display:none">
        <label for="pase-adicional">Cuentas con pase adicional. ¿Confirmas también su asistencia?</label>
        <br>
        <input type="radio" id="form-si-singular" name="form-pase-adicional-singular" value="si"> Si<br>
        <input type="radio" id="form-no-singular" name="form-pase-adicional-singular" value="no"> No<br>
    </div>

    <?php elseif($pase_adicional == 'si' && $pases_adicionales>1) : ?>

    <!-- CAMPO PASES ADICIONALES-->
    <div class="form-group" id="pase_adicional_form" style="display:none"> 
        <label for="pase-adicional">Cuentas con <?php echo $pases_adicionales?> pases adicionales. Indica cuántos confirman asistencia</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="number" name="form-pases-adicionales-singular" id="pases_adicionales_confirmados" placeholder="Número de pases adicionales que confirman asistencia:" min="0" max="<?php echo $pases_adicionales;?>" value="<?php echo $pases_adicionales;?>" class="form-control">
        </div>
    </div>  

    <?php endif; ?>

    <!-- CAMPO PASES CONFIRMADOS -->
    <div class="form-group" id="confirm-passes-singular" style="display:none"> 
        <label for="pases_confirmados">Número de pases que confirma asistencia:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="number" name="form-singular-pases_confirmados" id="pases_confirmados" placeholder="Número de pases que confirma asistencia:" min="1" max="<?php echo $num_pases;?>" value="<?php echo $num_pases;?>" class="form-control">
        </div>
    </div>  

    <!-- CAMPO EMAIL -->
    <div class="form-group" id="confirm-email"> 
        <label for="confirm_email">Ingresa un email al cual se enviará la confirmación:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-at fa-fw"></i></span>
            </div>
            <input type="email" name="confirm_singular_email" id="confirm_email" placeholder="Ingresa tu email:" class="form-control" required>
        </div>
    </div>  

    <!-- CAMPO MENSAJE -->
    <div class="form-group">
        <label for="form-message">Envíanos un mensaje:</label>
        <textarea name="form-message" id="form-message" placeholder="Escribe un mensaje:" class="form-control"> </textarea>
    </div> 

    <div class="container">
        <input type="submit" name="send-singular-assistance" value="Enviar" class="px-5">
        <input type="button" value="Regresar" class="px-5" onclick = "location='<?php echo getBaseUrl()."confirmacion-de-asistencia";?>'">
    </div>
</form>
 
<?php elseif ($asistencia == 'si' || $asistencia == 'no') : ?>

<p class='exito'><b>¡Bienvenid@ <?php echo $registros[0][nombre] ?>. Ya has confirmado asistencia de <?php echo $registros[0][pases]?> pases el día <?php echo $fecha[0];?> a las <?php echo $fecha[1];?> hrs., y asignando el correo: <?php echo $registros[0][correo];?>.</b></p>

<form action="" method="post">
    <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
    <input type="button" value="Modificar" class="px-5" onclick = "location='<?php echo getBaseUrl()."modificar-confirmacion-singular";?>'">
</form>

<?php endif ; ?>
