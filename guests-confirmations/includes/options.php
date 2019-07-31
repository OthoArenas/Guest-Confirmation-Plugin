<?php


// Top level menu del plugin
function gc_menu_administrator()
{
 add_menu_page("Confirmación de Invitados","Confirmación de Invitados",'manage_options',GC_PATH . '/admin/configuration.php','gc_print_confirmaciones','dashicons-feedback');
}

add_action( 'admin_menu', 'gc_menu_administrator' );

function gc_print_confirmaciones(){
    global $wpdb;
    $tabla_invitados = $wpdb->prefix . 'invitados';
    echo '<div class="wrap"><h1>Lista de invitados</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th width="30%">Nombre</th><th width="30%">Apellidos</th><th width="30%">Familia</th><th width="30%">Correo</th><th width="30%">Pases Asignados</th><th width="30%">Número de Mesa</th><th width="30%">Asistencia</th><th width="30%">Pases confirmados</th><th width="30%">Fecha de creación</th></tr></thead>';
    echo '<tbody id="the-list">';
    $invitados = $wpdb->get_results("SELECT * FROM $tabla_invitados");
    foreach ( $invitados as $invitado ) {
        $nombre = esc_textarea($invitado->nombre);
        $apellidos = esc_textarea($invitado->apellidos);
        $familia = esc_textarea($invitado->familia);
        $correo = esc_textarea($invitado->correo);
        $pases = (int)$invitado->pases;
        $mesa = (int)$invitado->mesa;
        $asistencia = esc_textarea($invitado->asistencia);
        $pases_confirmados = (int)$invitado->pases_confirmados;
        $created_at = date($invitado->created_at);
        echo "<tr><td><a href='#' title='Invitados'>$nombre</a></td><td>$apellidos</td><td>$familia</td>
            <td>$correo</td><td>$pases</td><td>$mesa</td>
            <td>$asistencia</td><td>$pases_confirmados</td><td>$created_at</td></tr>";
    }
    echo '</tbody></table></div>';
}

?>