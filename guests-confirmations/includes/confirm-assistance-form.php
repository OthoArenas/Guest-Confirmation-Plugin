<?php

define('GC_PATH',plugin_dir_path(__FILE__));
include_once(GC_PATH . "includes/functions.php");

$registros;
global $registros;
$num_pases = intval ($registros[0][pases]);
$sqldate = $registros[0][modified_at];
$pase_adicional = $registros[0][pase_adicional];
$pases_adicionales = (int)$registros[0][pases_adicionales];

$fecha = getFecha($sqldate);

?>

<?php if($registros[0][asistencia] == '') : ?>

<script>
    function showHideFormField(){
        if(document.getElementById('form-si').checked){
            document.getElementById('confirm-passes').style.display='block';
            document.getElementById('pase_adicional_form').style.display='block';
        }else if (document.getElementById('form-no').checked){
            document.getElementById('confirm-passes').style.display='none';
            document.getElementById('pase_adicional_form').style.display='none';
        }
    }
</script>

<?php if($pase_adicional=='si' && $pases_adicionales==1) : ?>

<p class='info'><b>¡Bienvenidos familia <?php echo $registros[0][familia];?>! Cuentan con <?php echo $registros[0][pases];?> pases asignados y un pase adicional. Favor de confirmar asistencia, así como el número de pases que utilizarán ¡Gracias!</b><p>
<p><b><small>Por favor, tengan en cosideración que al enviar como respuesta un "No" sus lugares serán asignados a otras personas.</small></b></p>

<?php elseif($pase_adicional=='si' && $pases_adicionales>1) : ?>

<p class='info'><b>¡Bienvenidos familia <?php echo $registros[0][familia];?>! Cuentan con <?php echo $registros[0][pases];?> pases asignados y <?php echo $pases_adicionales?> pases adicionales. Favor de confirmar asistencia, así como el número de pases que utilizarán ¡Gracias!</b><p>
<p><b><small>Por favor, tengan en cosideración que al enviar como respuesta un "No" sus lugares serán asignados a otras personas.</small></b></p>

<?php else : ?>
<p class='info'><b>¡Bienvenidos familia <?php echo $registros[0][familia];?>! Cuentan con <?php echo $registros[0][pases];?> pases asignados. Favor de confirmar asistencia, así como el número de pases que utilizarán ¡Gracias!</b><p>
<p><b><small>Por favor, tengan en cosideración que al enviar como respuesta un "No" sus lugares serán asignados a otras personas.</small></b></p>

<?php endif; ?>
 
<!-- ESTE ARCHIVO DEBE DE ESTAR DENTRO DE LA CARPETA DEL TEMA ACTIVO PARA SU LECTURA POR EL PLUGIN -->
<form action="<?php echo getBaseUrl()."confirmacion"; ?>" method="post">

    <!-- CAMPO ASISTENCIA -->
    <div class="form-group">
        <label for="asistencia">¿Confirma asistencia?</label>
        <br>
        <input type="radio" id="form-si" name="form-assistance-asistencia" value="si" onclick="showHideFormField();"> Si<br>
        <input type="radio" id="form-no" name="form-assistance-asistencia" value="no" onclick="showHideFormField();"> No<br> 
    </div>

    <!-- CAMPO PASES CONFIRMADOS -->
    <div class="form-group" id="confirm-passes" style="display:none"> 
        <label for="pases_confirmados">Número de pases que confirma asistencia:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="number" name="form-assistance-pases_confirmados" id="pases_confirmados" placeholder="Número de pases que confirma asistencia:" min="1" max="<?php echo $num_pases;?>" value="<?php echo $num_pases;?>" class="form-control">
        </div>
    </div>  

    <?php if($pase_adicional=='si' && $pases_adicionales==1) : ?> 

    <!-- CAMPO PASE ADICIONAL-->
    <div class="form-group" id="pase_adicional_form" style="display:none">
        <label for="form-pase-adicional">Cuentan con pase adicional. ¿Confirmas también su asistencia?</label>
        <br>
        <input type="radio" id="form-si" name="form-pase-adicional" value="si"> Si<br>
        <input type="radio" id="form-no" name="form-pase-adicional" value="no"> No<br>
    </div>

    <?php elseif($pase_adicional=='si' && $pases_adicionales>1) : ?> 

    <!-- CAMPO PASES ADICIONALES-->
    <div class="form-group" id="pase_adicional_form" style="display:none"> 
        <label for="form-pases-adicionales">Cuentan con <?php echo $pases_adicionales?> pases adicionales. Indica cuántos confirman asistencia</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="number" name="form-pases-adicionales" id="pases_adicionales_confirmados" placeholder="Número de pases adicionales que confirman asistencia:" min="0" max="<?php echo $pases_adicionales;?>" value="<?php echo $pases_adicionales;?>" class="form-control">
        </div>
    </div>  

    <?php endif;?>

    <!-- CAMPO EMAIL -->
    <div class="form-group" id="confirm-email"> 
        <label for="confirm_email">Ingresa un email al cual se enviará la confirmación:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-at fa-fw"></i></span>
            </div>
            <input type="email" name="confirm_email" id="confirm_email" placeholder="Ingresa tu email:" class="form-control" required>
        </div>
    </div>  

    <div class="container">
        <input type="submit" name="send-assistance" value="Enviar" class="px-5">
        <input type="button" value="Regresar" class="px-5" onclick = "location='<?php echo getBaseUrl()."confirmacion-de-asistencia";?>'">
    </div>
</form>
 
<?php elseif ($registros[0][asistencia] == 'si') : ?>

<p class='exito'><b>¡Bienvenidos! familia <?php echo $registros[0][familia] ?>. Ya han confirmado asistencia de <?php echo $registros[0][pases_confirmados]?> pases el día <?php echo $fecha[0];?> a las <?php echo $fecha[1];?> hrs., y asignando el correo: <?php echo $registros[0][correo];?>.</b></p>

<form action="" method="post">
    <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
    <input type="button" value="Modificar" class="px-5" onclick = "location='<?php echo getBaseUrl()."modificar-confirmacion";?>'">
</form>

<?php elseif ($registros[0][asistencia] == 'no') : ?>

<p class='exito'><b>¡Bienvenidos familia <?php echo $registros[0][familia] ?>!. Han indicado que no asistirán al evento. Por cuestiones de actualización de espacios e invitados no se puede modificar esta respuesta. Si la confirmación ha sido errónea, por favor diríjase a la sección de contaco y comuníquese con los novios para que se haga la modificación. Gracias. </b></p>
<p><b>Fecha de registro: <?php echo $fecha[0];?> a las <?php echo $fecha[1];?> hrs. </b></p>

<form action="" method="post">
    <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
</form>

<?php endif ; ?>
