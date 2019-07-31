<?php

define('GC_PATH',plugin_dir_path(__FILE__));
include_once(GC_PATH . "includes/functions.php");

$registros;
global $registros;
?>

<script>
    function showHideFamiliaIndividual(){
        if(document.getElementById('es-familia-si').checked){
            document.getElementById('form_familia').style.display='block';
            document.getElementById('form_nombre').style.display='none';
            document.getElementById('form_apellidos').style.display='none';
            document.getElementById('familia').setAttribute("required", "");
            document.getElementById('nombre').removeAttribute("required");
            document.getElementById('apellidos').removeAttribute("required");
        }else if (document.getElementById('es-familia-no').checked){
            document.getElementById('form_familia').style.display='none';
            document.getElementById('form_nombre').style.display='block';
            document.getElementById('form_apellidos').style.display='block';
            document.getElementById('familia').removeAttribute("required");
            document.getElementById('nombre').setAttribute("required", "");
            document.getElementById('apellidos').setAttribute("required", "");
        }
    }
</script>

<!-- ESTE ARCHIVO DEBE DE ESTAR DENTRO DE LA CARPETA DEL TEMA ACTIVO PARA SU LECTURA POR EL PLUGIN -->
<h4>Para confirmar tu asistencia a nuestra boda, primero indica si tu invitación va dirigida a una familia o es individual. Después introduce tu(s) nombre(s) y apellidos o la familia a la cual se le hizo llegar la invitación.</h4>
<form action="<?php get_the_permalink(); ?>" method="post">

    <!-- CAMPO ES FAMILIA -->
    <div class="form-group">
        <label for="es-familia">¿A quién va dirigida tu invitación?</label>
        <br>
        <input type="radio" name="form-es-familia" id="es-familia-si" value="si" onclick="showHideFamiliaIndividual();"> Es para familia <br>
        <input type="radio" name="form-es-familia" id="es-familia-no" value="no" onclick="showHideFamiliaIndividual();"> Es individual <br>
    </div>

    <!-- CAMPO NOMBRE -->
    <div class="form-group" id="form_nombre" style="display:none"> 
        <label for="nombre">Nombre(s):</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-user fa-fw"></i> </span>
            </div>
            <input type="text" name="form-id-nombre" id="nombre" class="form-control" placeholder="Nombre(s):">
        </div>
    </div>

    <!-- CAMPO APELLIDOS -->
    <div class="form-group" id="form_apellidos" style="display:none">
        <label for="apellidos">Apellidos:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-user fa-fw"></i> </span>
            </div>
            <input type="text" name="form-id-apellidos" id="apellidos" class="form-control" placeholder="Apellidos:">
        </div>
    </div>

    <!-- CAMPO FAMILIA -->
    <div class="form-group" id="form_familia" style="display:none">
        <label for="familia">Familia:</label> 
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-users fa-fw"></i> </span>
            </div>
            <input type="text" name="form-id-familia" id="familia" class="form-control" placeholder="Familia:">
        </div> 
    </div>

    <div class="container">
        <input type="submit" name="send-id" value="Enviar" class="px-5">
        <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
    </div>
</form>