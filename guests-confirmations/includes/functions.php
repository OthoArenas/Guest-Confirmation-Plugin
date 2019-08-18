<?php

define('GC_PATH',plugin_dir_path(__FILE__));

session_start();

$tabla_invitados = $wpdb->prefix . 'invitados';

function guest_register_form() {
    global $wpdb; // Este objeto global permite acceder a la base de datos de WP
    // Si viene del formulario  graba en la base de datos
    // Cuidado con el último igual de la condición del if que es doble
    if ($_POST['send']
        && current_user_can('administrator')
        && $_POST['form-nombre'] != ''
        && $_POST['form-apellidos'] != ''
        && $_POST['form-pases'] != ''
        && $_POST['form-mesa'] != ''
    ) {
        $tabla_invitados = $wpdb->prefix . 'invitados'; 
        $nombre = sanitize_text_field($_POST['form-nombre']);
        $apellidos = sanitize_text_field($_POST['form-apellidos']);
        $es_familia = $_POST['form-es-familia'];
        $familia = sanitize_text_field($_POST['form-familia']);
        $correo = is_email($_POST['form-correo']);
        $pases = (int)$_POST['form-pases'];
        $mesa = (int)$_POST['form-mesa'];
        $asistencia = $_POST['form-asistencia'];
        $pases_confirmados = (int)$_POST['form-pases_confirmados'];
        $created_at = getSQLDate();
        $pase_adicional = $_POST['form-pase-adicional'];
        $pases_adicionales = (int)$_POST['form-pases-adicionales'];
        $wpdb->insert(
            $tabla_invitados,
            array(
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'es_familia' => $es_familia,
                'familia' => $familia,
                'correo' => $correo,
                'pases' => $pases,
                'mesa' => $mesa,
                'asistencia' => $asistencia,
                'pases_confirmados' => $pases_confirmados,
                'created_at' => $created_at,
                'pase_adicional' => $pase_adicional,
                'pases_adicionales' => $pases_adicionales
            )
        );
        echo "<p class='exito'><b>Los datos del invitado han sido registrados.</b><p>";
    }
    elseif ($_POST['send']&& !current_user_can('administrator')) {
        echo "<p class='falla'><b>Los datos del invitado no se han registrado. No cuentas con los permisos necesarios.</b><p>";
    }
    include(GC_PATH . 'includes/register-form.php');
    /* ob_start();
    get_template_part('register-form');
    return ob_get_clean();   */ 
} 
add_shortcode( 'guest-register-form', 'guest_register_form' );

/* Función para obtener el ID del invitado de acuerdo a la información enviada. */
function guest_id_form() {
    global $wpdb; // Este objeto global permite acceder a la base de datos de WP
    // Si viene del formulario  graba en la base de datos
    // Cuidado con el último igual de la condición del if que es doble
    
    if($_POST['send-id']){
        if (($_POST['form-id-nombre'] != '' && $_POST['form-id-apellidos'] != '') 
            || $_POST['form-id-familia'] != ''){
            $tabla_invitados = $wpdb->prefix . 'invitados'; 
            $nombre = sanitize_text_field($_POST['form-id-nombre']);
            $apellidos = sanitize_text_field($_POST['form-id-apellidos']);
            $familia = sanitize_text_field($_POST['form-id-familia']);
            
            $registros = getRegistros($tabla_invitados,$nombre,$apellidos,$familia);
           
            if($registros){

                if($_POST['form-id-familia'] != ''){

                    confirmar_asistencia();
                    
                    ob_start();
                    get_template_part('confirm-assistance-form');
                    return ob_get_clean(); 

                }else{

                    confirmar_asistencia_singular();
                    
                    ob_start();
                    get_template_part('confirm-assistance-form-singular');
                    return ob_get_clean(); 

                }

            } elseif($_POST['form-id-familia'] != ''){
                echo "<p class='falla'><b>No se ha encontrado la familia \"".$_POST['form-id-familia']."\". Favor de intentar con el nombre completo del invitado o en su defecto con el nombre de familia tal cual aparece en su invitación. ¡Gracias!</b><p>";
            }else{
                echo "<p class='falla'><b>No se ha encontrado al invitado \"".$_POST['form-id-nombre']." ".$_POST['form-id-apellidos']."\". Favor de intentar con el nombre completo del invitado o en su defecto con el nombre de familia tal cual aparece en su invitación. ¡Gracias!</b><p>";
            }
        }
    }

    ob_start();
    get_template_part('guest-id-form');
    return ob_get_clean();    
} 
add_shortcode( 'guest-id-form', 'guest_id_form' );

function getRegistros($tabla_invitados,$nombre,$apellidos,$familia){

    global $wpdb;
            
    $sql = 'SELECT * FROM '.$tabla_invitados.' WHERE nombre LIKE "%'.$nombre.'%" AND apellidos LIKE "'.$apellidos.'" OR familia LIKE "'.$familia.'" AND es_familia LIKE "si"';
    global $registros;
    $registros = $wpdb->get_results($sql,ARRAY_A);
    $_SESSION['invitados'] = $registros;

    return $registros;
}

/* Función para modificar la tabla de invitados de acuerdo a la respuesta de confirmación */
function confirmar_asistencia(){
    
    if($_POST['send-assistance']){
        
        $invitados = $_SESSION['invitados'];

        $modified_at = getSQLDate();

        $asistencia = $_POST['form-assistance-asistencia'];
        $correo = $_POST['confirm_email'];
        $pases_confirmados = (int)$_POST['form-assistance-pases_confirmados'];
        $pases_adicionales = (int)$_POST['form-pases-adicionales'];
        $pase_adicional = $_POST['form-pase-adicional'];
        if($pase_adicional=='si'){
            $pases_confirmados++;
        }
        $pases_confirmados += $pases_adicionales;

        for ($i=0; $i < count($invitados) ; $i++) { 
            $ids[] = (int)$invitados[$i][id];
        }

        $where = ' WHERE ';

        for ($i=0; $i < count($ids)-1; $i++) { 
            $where .= 'id = '.$ids[$i].' OR ';
        }

        $where .= 'id = '.$ids[(count($ids)-1)].' ';
        
        global $wpdb;
        $tabla_invitados = $wpdb->prefix . 'invitados'; 

        if($asistencia == 'si'){
            for ($i=0; $i < count($ids); $i++) { 
                
                $wpdb->update(
                    $tabla_invitados,
                    array(
                        'correo' => $correo,
                        'asistencia' => $asistencia,
                        'pases_confirmados' => $pases_confirmados,
                        'modified_at' => $modified_at
                    ), 
                    array(
                        'id' => $ids[$i]
                    )
                );
            }
        }else{
            for ($i=0; $i < count($ids); $i++) { 
                
                $wpdb->update(
                    $tabla_invitados,
                    array(
                        'correo' => $correo,
                        'asistencia' => $asistencia,
                        'pases_confirmados' => 0,
                        'modified_at' => $modified_at
                    ), 
                    array(
                        'id' => $ids[$i]
                    )
                );
            }
        }

        mostrar_confirmacion();
        enviar_correo();

        return ; 
    }

    ob_start();
    get_template_part('confirm-assistance-form');
    return ob_get_clean(); 
    
}
add_shortcode( 'guest-confirmation-form', 'confirmar_asistencia' ); 

function confirmar_asistencia_singular(){
    
    if($_POST['send-singular-assistance']){
        
        $invitados = $_SESSION['invitados'];

        $modified_at = getSQLDate();

        $asistencia = $_POST['form-singular-assistance'];
        $correo = $_POST['confirm_singular_email'];
        $pases_confirmados = (int)$_POST['form-singular-pases_confirmados'];
        $pases_adicionales = (int)$_POST['form-pases-adicionales-singular'];
        $pase_adicional = $_POST['form-pase-adicional-singular'];
        $mensaje = $_POST['form-message'];

        if($pase_adicional=='si'){
            $pases_confirmados++;
        }
        $pases_confirmados += $pases_adicionales;

        for ($i=0; $i < count($invitados) ; $i++) { 
            $ids[] = (int)$invitados[$i][id];
        }

        $where = ' WHERE ';

        $where .= 'id = '.$ids[(count($ids)-1)].' ';
        
        global $wpdb;
        $tabla_invitados = $wpdb->prefix . 'invitados'; 

        if($asistencia == 'si'){
            for ($i=0; $i < count($ids); $i++) { 
                
                $wpdb->update(
                    $tabla_invitados,
                    array(
                        'correo' => $correo,
                        'asistencia' => $asistencia,
                        'pases_confirmados' => $pases_confirmados,
                        'modified_at' => $modified_at,
                        'mensaje' => $mensaje
                    ), 
                    array(
                        'id' => $ids[$i]
                    )
                );
            }
        }else{
            for ($i=0; $i < count($ids); $i++) { 
                
                $wpdb->update(
                    $tabla_invitados,
                    array(
                        'correo' => $correo,
                        'asistencia' => $asistencia,
                        'pases_confirmados' => 0,
                        'modified_at' => $modified_at
                    ), 
                    array(
                        'id' => $ids[$i]
                    )
                );
            }
        }

        mostrar_confirmacion_singular();
        enviar_correo();

        return ; 
    }

    ob_start();
    get_template_part('confirm-assistance-form-singular');
    return ob_get_clean(); 
    
}
add_shortcode( 'singular-confirmation-form', 'confirmar_asistencia_singular' ); 

function mostrar_confirmacion(){
    global $wpdb;
    $invitados = $_SESSION['invitados'];
    $nombre = $invitados[0][nombre];
    $apellidos = $invitados[0][apellidos];
    $familia = $invitados[0][familia];
    $tabla_invitados = $wpdb->prefix . 'invitados'; 

    getRegistros($tabla_invitados,$nombre,$apellidos,$familia);

    include(GC_PATH . 'includes/show-confirmation.php');

}

function mostrar_confirmacion_singular(){
    global $wpdb;
    $invitados = $_SESSION['invitados'];
    $nombre = $invitados[0][nombre];
    $apellidos = $invitados[0][apellidos];
    $familia = $invitados[0][familia];
    $tabla_invitados = $wpdb->prefix . 'invitados'; 

    getRegistros($tabla_invitados,$nombre,$apellidos,$familia);

    include(GC_PATH . 'includes/show-confirmation-singular.php');

}

function modificar_confirmacion(){
    
    global $wpdb;
    $tabla_invitados = $wpdb->prefix . 'invitados'; 
    $invitados = $_SESSION['invitados'];
    $modified_at = getSQLDate();

    for ($i=0; $i < count($invitados) ; $i++) { 
        $ids[] = (int)$invitados[$i][id];
    }

    $where = ' WHERE ';

    for ($i=0; $i < count($ids)-1; $i++) { 
        $where .= 'id = '.$ids[$i].' OR ';
    }

    $where .= 'id = '.$ids[(count($ids)-1)].' ';

    for ($i=0; $i < count($ids); $i++) { 
            
        $wpdb->update(
            $tabla_invitados,
            array(
                'asistencia' => '',
                'pases_confirmados' => 0,
                'created_at' => $modified_at
            ), 
            array(
                'id' => $ids[$i]
            )
        );
    } 

    global $tabla_invitados;
    $nombre = $invitados[0][nombre];
    $apellidos = $invitados[0][apellidos];
    $familia = $invitados[0][familia];
    $_SESSION['invitados'] = getRegistros($tabla_invitados,$nombre,$apellidos,$familia);

    ob_start();
    get_template_part('modify-assistance-form');
    return ob_get_clean(); 

}
add_shortcode( 'modify-confirmation-form', 'modificar_confirmacion' ); 

function modificar_confirmacion_singular(){
    
    global $wpdb;
    $tabla_invitados = $wpdb->prefix . 'invitados'; 
    $invitados = $_SESSION['invitados'];
    $modified_at = getSQLDate();

    for ($i=0; $i < count($invitados) ; $i++) { 
        $ids[] = (int)$invitados[$i][id];
    }

    $where = ' WHERE ';

    $where .= 'id = '.$ids[(count($ids)-1)].' ';

    for ($i=0; $i < count($ids); $i++) { 
            
        $wpdb->update(
            $tabla_invitados,
            array(
                'asistencia' => '',
                'pases_confirmados' => 0,
                'created_at' => $modified_at
            ), 
            array(
                'id' => $ids[$i]
            )
        );
    } 

    global $tabla_invitados;
    $nombre = $invitados[0][nombre];
    $apellidos = $invitados[0][apellidos];
    $familia = $invitados[0][familia];
    $_SESSION['invitados'] = getRegistros($tabla_invitados,$nombre,$apellidos,$familia);

    ob_start();
    get_template_part('modify-assistance-singular');
    return ob_get_clean(); 

}
add_shortcode( 'modify-confirmation-singular-form', 'modificar_confirmacion_singular' ); 

function enviar_correo(){
    $invitados = $_SESSION['invitados'];
    $nombre = $invitados[0][nombre];
    $apellidos = $invitados[0][apellidos];
    $es_familia = $invitados[0][es_familia];
    $correo = $invitados[0][correo];
    $familia = $invitados[0][familia];
    $asistencia = ucwords($invitados[0][asistencia]);
    $pases_confirmados = $invitados[0][pases_confirmados];
    $sqldate = getSQLDate($invitados[0][modified_at]);
    $fecha = getFecha($sqldate);
    $dia = $fecha[0];
    $hora = $fecha[1];   
    $mensaje = $invitados[0][mensaje];
    $subject = 'Confirmacion de asistencia';
    $headers = "From: Wedding M&O <othoniel.salazar@weddingmyo.com>" . "\r\n";
    $headers .= "Cc: othoniel.salazar@dsignstudio.com.mx, marividalloyda@icloud.com" . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $logo = 'https://weddingmyo.com/wp-content/plugins/guests-confirmations/includes/images/Wedding-Logo.png?_t=1564541353';
    $asanoha = 'https://weddingmyo.com/wp-content/plugins/guests-confirmations/includes/images/asanoha-400px.png?_t=1564541345';
    $facebook = 'https://weddingmyo.com/wp-content/plugins/guests-confirmations/includes/images/facebook.png?_t=1564541345';
    $linkedin = 'https://weddingmyo.com/wp-content/plugins/guests-confirmations/includes/images/linkedin%402x.png?_t=1564541353';
    $twitter = 'https://weddingmyo.com/wp-content/plugins/guests-confirmations/includes/images/twitter.png?_t=1564541353';

    if($es_familia == 'si' && $asistencia == 'Si'){
        $message = "";
        $dir = GC_PATH.'includes/';
        if(file_exists($dir.'Acepta_familia.html')){
            $message = file_get_contents($dir.'Acepta_familia.html');
            $parts_to_mod = array("NOMBREFAMILIA", "ASISTENCIA","PASESCONFIRMADOS","FECHADECONFIRMACION","HORADECONFIRMACION","LOGOIMG","ASANOHAIMG","FACEBOOKIMG","LINKEDINIMG","TWITTERIMG");
            $replace_with = array($familia, $asistencia,$pases_confirmados,$dia,$hora,$logo,$asanoha,$facebook,$linkedin,$twitter);
            for($i=0; $i<count($parts_to_mod); $i++){
                $message = str_replace($parts_to_mod[$i], $replace_with[$i], $message);
            }
            
        }
        if(mail($correo,$subject,$message,$headers)){
            echo '<br>Se ha enviado el correo exitosamente';
        }else{
            echo '<br>No se ha enviado el correo';
        }
    }
    elseif($es_familia == 'si' && $asistencia == 'No'){
        $message = "";
        $dir = GC_PATH.'includes/';
        if(file_exists($dir.'Rechaza_familia.html')){
            $message = file_get_contents($dir.'Rechaza_familia.html');
            $parts_to_mod = array("NOMBREFAMILIA", "ASISTENCIA","PASESCONFIRMADOS","FECHADECONFIRMACION","HORADECONFIRMACION","LOGOIMG","ASANOHAIMG","FACEBOOKIMG","LINKEDINIMG","TWITTERIMG");
            $replace_with = array($familia, $asistencia,$pases_confirmados,$dia,$hora,$logo,$asanoha,$facebook,$linkedin,$twitter);
            for($i=0; $i<count($parts_to_mod); $i++){
                $message = str_replace($parts_to_mod[$i], $replace_with[$i], $message);
            }
            
        }
        if(mail($correo,$subject,$message,$headers)){
            echo '<br>Se ha enviado el correo exitosamente';
        }else{
            echo '<br>No se ha enviado el correo';
        }
    }
    elseif($es_familia == 'no' && $asistencia == 'Si'){
        $message = "";
        $dir = GC_PATH.'includes/';
        if(file_exists($dir.'Acepta_individual.html')){
            $message = file_get_contents($dir.'Acepta_individual.html');
            $parts_to_mod = array("NOMBRESINGULAR", "ASISTENCIA","PASESCONFIRMADOS","FECHADECONFIRMACION","HORADECONFIRMACION","LOGOIMG","ASANOHAIMG","FACEBOOKIMG","LINKEDINIMG","TWITTERIMG","MENSAJESINGULAR");
            $replace_with = array($nombre, $asistencia,$pases_confirmados,$dia,$hora,$logo,$asanoha,$facebook,$linkedin,$twitter,$mensaje);
            for($i=0; $i<count($parts_to_mod); $i++){
                $message = str_replace($parts_to_mod[$i], $replace_with[$i], $message);
            }
            
        }
        if(mail($correo,$subject,$message,$headers)){
            echo '<br>Se ha enviado el correo exitosamente';
        }else{
            echo '<br>No se ha enviado el correo';
        }
    }
    elseif($es_familia == 'no' && $asistencia == 'No'){
        $message = "";
        $dir = GC_PATH.'includes/';
        if(file_exists($dir.'Rechaza_individual.html')){
            $message = file_get_contents($dir.'Rechaza_individual.html');
            $parts_to_mod = array("NOMBRESINGULAR", "ASISTENCIA","PASESCONFIRMADOS","FECHADECONFIRMACION","HORADECONFIRMACION","LOGOIMG","ASANOHAIMG","FACEBOOKIMG","LINKEDINIMG","TWITTERIMG","MENSAJESINGULAR");
            $replace_with = array($nombre, $asistencia,$pases_confirmados,$dia,$hora,$logo,$asanoha,$facebook,$linkedin,$twitter,$mensaje);
            for($i=0; $i<count($parts_to_mod); $i++){
                $message = str_replace($parts_to_mod[$i], $replace_with[$i], $message);
            }
            
        }
        if(mail($correo,$subject,$message,$headers)){
            echo '<br>Se ha enviado el correo exitosamente';
        }else{
            echo '<br>No se ha enviado el correo';
        }
    }
}

/* Esta función es llamada desde guest-confirmations.php */
function GR_database_init() 
{
    global $wpdb; // Este objeto global permite acceder a la base de datos de WP
    // Crea la tabla sólo si no existe
    // Utiliza el mismo prefijo del resto de tablas
    $tabla_invitados = $wpdb->prefix . 'invitados';
    // Utiliza el mismo tipo de orden de la base de datos
    $charset_collate = $wpdb->get_charset_collate();
    // Prepara la consulta
    $query = "CREATE TABLE IF NOT EXISTS $tabla_invitados (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        apellidos varchar(40) NOT NULL,
        es_familia varchar(2),
        familia varchar(40) NOT NULL,
        correo varchar(100),
        pases smallint(4) NOT NULL,
        mesa smallint(4) NOT NULL,
        asistencia varchar(2),
        pases_confirmados smallint(4),
        created_at datetime NOT NULL,
        modified_at datetime NOT NULL,
        pase_adicional varchar(2),
        pases_adicionales smallint(4) NOT NULL,
        mensaje varchar(500),
        UNIQUE (id)
        ) $charset_collate;";
    // La función dbDelta permite crear tablas de manera segura se
    // define en el archivo upgrade.php que se incluye a continuación
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query); // Lanza la consulta para crear la tabla de manera segura
}

function getBaseUrl() 
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF']; 
    
    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
    $pathInfo = pathinfo($currentPath); 
    
    // output: localhost
    $hostName = $_SERVER['HTTP_HOST']; 
    
    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
    
    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."/";
}

function getFecha($sqldate){
    $fecha = date("d/m/Y H:i", strtotime($sqldate));
    $fecha = explode(" ",$fecha);

    return $fecha;
}

function getSQLDate(){
    $timezone = 'America/Mexico_City';
    $timestamp = time();
    $date = new DateTime("now", new DateTimeZone($timezone)); //first argument "must" be a string
    $date->setTimestamp($timestamp); //adjust the object to correct timestamp
    return $date->format('Y-m-d H:i:s');
}

function add_registration_page() {
    global $wpdb;

    $the_page_title = 'Registro de invitados';
    $the_page_name = 'registro-de-invitados';

    // the menu entry...
    delete_option("pagina_registro_titulo");
    add_option("pagina_registro_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_registro_name");
    add_option("pagina_registro_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_registro_id");
    add_option("pagina_registro_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_name'] = $the_page_name;
        $_p['post_content'] = "[guest-register-form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_registro_id' );
    add_option( 'pagina_registro_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_registration_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_registro_titulo" );
    $the_page_name = get_option( "pagina_registro_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_registro_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_registro_titulo");
    delete_option("pagina_registro_name");
    delete_option("pagina_registro_id");

}

function add_modify_singular_confirmation_page() {
    global $wpdb;

    $the_page_title = 'Modificar confirmación personal';
    $the_page_name = 'modificar-confirmacion-singular';

    // the menu entry...
    delete_option("pagina_modificar_singular_titulo");
    add_option("pagina_modificar_singular_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_modificar_singular_name");
    add_option("pagina_modificar_singular_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_modificar_singular_id");
    add_option("pagina_modificar_singular_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_q = array();
        $_q['post_title'] = $the_page_title;
        $_q['post_name'] = $the_page_name;
        $_q['post_content'] = "[modify-confirmation-singular-form]";
        $_q['post_status'] = 'publish';
        $_q['post_type'] = 'page';
        $_q['comment_status'] = 'closed';
        $_q['ping_status'] = 'closed';
        $_q['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_q );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_modificar_singular_id' );
    add_option( 'pagina_modificar_singular_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_modify_singular_confirmation_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_modificar_singular_titulo" );
    $the_page_name = get_option( "pagina_modificar_singular_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_modificar_singular_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_modificar_singular_titulo");
    delete_option("pagina_modificar_singular_name");
    delete_option("pagina_modificar_singular_id");

}

function add_modify_confirmation_page() {
    global $wpdb;

    $the_page_title = 'Modificar confirmación';
    $the_page_name = 'modificar-confirmacion';

    // the menu entry...
    delete_option("pagina_modificar_titulo");
    add_option("pagina_modificar_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_modificar_name");
    add_option("pagina_modificar_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_modificar_id");
    add_option("pagina_modificar_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_name'] = $the_page_name;
        $_p['post_content'] = "[modify-confirmation-form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_modificar_id' );
    add_option( 'pagina_modificar_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_modify_confirmation_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_modificar_titulo" );
    $the_page_name = get_option( "pagina_modificar_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_modificar_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_modificar_titulo");
    delete_option("pagina_modificar_name");
    delete_option("pagina_modificar_id");

}

function add_singular_confirmation_page() {
    global $wpdb;

    $the_page_title = 'Confirmación de asistencia personal';
    $the_page_name = 'confirmacion-de-asistencia-singular';

    // the menu entry...
    delete_option("pagina_confirmacion_singular_titulo");
    add_option("pagina_confirmacion_singular_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_confirmacion_singular_name");
    add_option("pagina_confirmacion_singular_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_confirmacion_singular_id");
    add_option("pagina_confirmacion_singular_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_name'] = $the_page_name;
        $_p['post_content'] = "[singular-confirmation-form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_confirmacion_singular_id' );
    add_option( 'pagina_confirmacion_singular_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_singular_confirmation_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_confirmacion_singular_titulo" );
    $the_page_name = get_option( "pagina_confirmacion_singular_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_confirmacion_singular_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_confirmacion_singular_titulo");
    delete_option("pagina_confirmacion_singular_name");
    delete_option("pagina_confirmacion_singular_id");

}

function add_confirmation_page() {
    global $wpdb;

    $the_page_title = 'Confirmación de asistencia';
    $the_page_name = 'confirmacion-de-asistencia';

    // the menu entry...
    delete_option("pagina_confirmacion_titulo");
    add_option("pagina_confirmacion_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_confirmacion_name");
    add_option("pagina_confirmacion_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_confirmacion_id");
    add_option("pagina_confirmacion_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_r = array();
        $_r['post_title'] = $the_page_title;
        $_r['post_name'] = $the_page_name;
        $_r['post_content'] = "[guest-id-form]";
        $_r['post_status'] = 'publish';
        $_r['post_type'] = 'page';
        $_r['comment_status'] = 'closed';
        $_r['ping_status'] = 'closed';
        $_r['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_r );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_confirmacion_id' );
    add_option( 'pagina_confirmacion_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_confirmation_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_confirmacion_titulo" );
    $the_page_name = get_option( "pagina_confirmacion_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_confirmacion_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_confirmacion_titulo");
    delete_option("pagina_confirmacion_name");
    delete_option("pagina_confirmacion_id");

}

function add_show_confirmation_page() {
    global $wpdb;

    $the_page_title = 'Confirmación';
    $the_page_name = 'confirmacion';

    // the menu entry...
    delete_option("pagina_mostrar_confirmacion_titulo");
    add_option("pagina_mostrar_confirmacion_titulo", $the_page_title, '', 'yes');
    // the slug...
    delete_option("pagina_mostrar_confirmacion_name");
    add_option("pagina_mostrar_confirmacion_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("pagina_mostrar_confirmacion_id");
    add_option("pagina_mostrar_confirmacion_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_name'] = $the_page_name;
        $_p['post_content'] = "[guest-confirmation-form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'pagina_mostrar_confirmacion_id' );
    add_option( 'pagina_mostrar_confirmacion_id', $the_page_id );

}

/* Runs on plugin deactivation */
function remove_show_confirmation_page() {

    global $wpdb;

    $the_page_title = get_option( "pagina_mostrar_confirmacion_titulo" );
    $the_page_name = get_option( "pagina_mostrar_confirmacion_name" );

    //  the id of our page...
    $the_page_id = get_option( 'pagina_mostrar_confirmacion_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("pagina_mostrar_confirmacion_titulo");
    delete_option("pagina_mostrar_confirmacion_name");
    delete_option("pagina_mostrar_confirmacion_id");

}

?>