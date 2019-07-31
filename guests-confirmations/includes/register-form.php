<script>
    function showHideFormFamilia(){
        if(document.getElementById('es-familia-si').checked){
            document.getElementById('form_familia').style.display='block';
            document.getElementById('familia').setAttribute("required", "");
        }else if (document.getElementById('es-familia-no').checked){
            document.getElementById('form_familia').style.display='none';
            document.getElementById('familia').removeAttribute("required");
        }
    }
    function showHidePasesAdicionales(){
        if(document.getElementById('pase-adicional-si').checked){
            document.getElementById('pases_adicionales').style.display='block';
            document.getElementById('pases-adicionales').setAttribute("required", "");
        }else if (document.getElementById('pase-adicional-no').checked){
            document.getElementById('pases_adicionales').style.display='none';
            document.getElementById('pases-adicionales').removeAttribute("required");
        }
    }
</script>


<!-- ESTE ARCHIVO DEBE DE ESTAR DENTRO DE LA CARPETA DEL TEMA ACTIVO PARA SU LECTURA POR EL PLUGIN -->
<h4>Ingresa los datos del invitado:</h4>
<form action="<?php get_the_permalink(); ?>" method="post">

    <!-- CAMPO NOMBRE -->
    <div class="form-group"> 
        <label for="nombre">Nombre(s):</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-user fa-fw"></i> </span>
            </div>
            <input type="text" name="form-nombre" id="nombre" class="form-control" placeholder="Nombre(s):" required>
        </div>
    </div>

    <!-- CAMPO APELLIDOS -->
    <div class="form-group">
        <label for="apellidos">Apellidos:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-user fa-fw"></i> </span>
            </div>
            <input type="text" name="form-apellidos" id="apellidos" class="form-control" placeholder="Apellidos:" required>
        </div>
    </div>

    <!-- CAMPO FAMILIA -->
    <div class="form-group">
        <label for="es-familia">¿Es familia?</label>
        <br>
        <input type="radio" name="form-es-familia" id="es-familia-si" value="si" onclick="showHideFormFamilia();"> Si<br>
        <input type="radio" name="form-es-familia" id="es-familia-no" value="no" onclick="showHideFormFamilia();"> No<br>
    </div>

    <!-- CAMPO FAMILIA -->
    <div class="form-group" id="form_familia" style="display:none">
        <label for="familia">Familia:</label> 
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-users fa-fw"></i> </span>
            </div>
            <input type="text" name="form-familia" id="familia" class="form-control" placeholder="Familia:">
        </div>
    </div>

    <!-- CAMPO EMAIL -->
    <div class="form-group">
        <label for="correo">Email:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-envelope fa-fw"></i></span>
            </div>
            <input type="email" name="form-correo" id="correo" class="form-control" placeholder="Email:">
        </div>
    </div>

    <!-- CAMPO PASES -->
    <div class="form-group">
        <label for="pases">Número de pases asignados a la familia:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="text" name="form-pases" id="pases" placeholder="Número de pases:" class="form-control" required>
        </div>
    </div>

    <!-- CAMPO MESA -->
    <div class="form-group"> 
        <label for="mesa">Mesa asignada:</label> 
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-birthday-cake fa-fw"></i></span>
            </div>
            <input type="text" name="form-mesa" id="mesa" class="form-control" placeholder="Número de mesa:" required>
        </div>
    </div>

    <!-- CAMPO PASE ADICIONAL-->
    <div class="form-group">
        <label for="pase-adicional">¿Se le asigna pase adicional?</label>
        <br>
        <input type="radio" name="form-pase-adicional" id="pase-adicional-si" value="si" onclick="showHidePasesAdicionales();"> Si<br>
        <input type="radio" name="form-pase-adicional" id="pase-adicional-no" value="no" checked onclick="showHidePasesAdicionales();"> No<br>
    </div>

    <!-- CAMPO PASES ADICIONALES-->
    <div class="form-group" id="pases_adicionales" style="display:none">
        <label for="pases-adicionales">Número de pases adicionales:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="text" name="form-pases-adicionales" id="pases-adicionales" placeholder="Número de pases adicionales:" class="form-control">
        </div>
    </div>

    <!-- CAMPO ASISTENCIA -->
    <div class="form-group">
        <label for="asistencia">¿Confirma asistencia?</label>
        <br>
        <input type="radio" name="form-asistencia" value="si"> Si<br>
        <input type="radio" name="form-asistencia" value="no"> No<br>
    </div>

    <!-- CAMPO PASES CONFIRMADOS -->
    <div class="form-group">
        <label for="pases_confirmados">Número de pases que confirma asistencia:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-ticket fa-fw"></i></span>
            </div>
            <input type="text" name="form-pases_confirmados" id="pases_confirmados" placeholder="Número de pases que confirma asistencia:" class="form-control">
        </div>
    </div>

    <div class="container">
        <input type="submit" name="send" value="Enviar" class="px-5">
        <input type="button" value="Ir a Inicio" class="px-5" onclick = "location='<?php echo getBaseUrl();?>'">
    </div>
</form>