<?php
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>
 <div class="wrap">
 <h1><?php _e( 'Confirmación de Invitados', 'confirmacion de invitados' ) ?></h1>
 <h3>Bienvenido a la configuración de Confirmación de Invitados.</h3>
 <h4>Aquí se mostrará la tabla de invitados registrados mediante el formulario del administrador.</h4>
 </div>
<?php
 ?>