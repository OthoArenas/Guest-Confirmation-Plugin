<?php

/**
 * Plugin Name: Guests Confirmations
 * Plugin URI:  https://dsignstudio.com.mx
 * Description: Implements a form to register guests, and also, another form that allows guests to confirm their    assistance to an event.
 * Version:     1.0.0
 * Author:      Othoniel Eduardo Salazar Arenas
 * Author URI:  https://dsignstudio.com.mx
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: guests-confirmations
 * Domain Path: /languages
 */

defined('ABSPATH') or die("Bye bye");
define('GC_PATH',plugin_dir_path(__FILE__));
include(GC_PATH . 'includes/functions.php');
include(GC_PATH . 'includes/functions.js');
include(GC_PATH . 'includes/options.php');

// Cuando el plugin se active se crea la tabla para recoger los datos si no existe
register_activation_hook(__FILE__, 'GR_database_init');
register_activation_hook(__FILE__, 'add_registration_page');
register_activation_hook(__FILE__, 'add_modify_singular_confirmation_page');
register_activation_hook(__FILE__, 'add_modify_confirmation_page');
register_activation_hook(__FILE__, 'add_singular_confirmation_page');
register_activation_hook(__FILE__, 'add_confirmation_page');
register_activation_hook(__FILE__, 'add_show_confirmation_page');
register_deactivation_hook( __FILE__, 'remove_registration_page');
register_deactivation_hook( __FILE__, 'remove_modify_singular_confirmation_page');
register_deactivation_hook( __FILE__, 'remove_modify_confirmation_page');
register_deactivation_hook( __FILE__, 'remove_singular_confirmation_page');
register_deactivation_hook( __FILE__, 'remove_confirmation_page');
register_deactivation_hook( __FILE__, 'remove_show_confirmation_page');

 
/**
 * Crea la tabla para recoger los datos del formulario
 *
 * @return void
 */

?>