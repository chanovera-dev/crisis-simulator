<?php
/**
 * Plugin Name: Simulador de Crisis
 * Plugin URI: https://thecrisisacademy.com/
 * Description: Plugin especializado para la gestión y simulación de crisis.
 * Version: 1.0.0
 * Author: The Crisis Academy
 * Author URI: https://thecrisisacademy.com/
 * License: GPL2
 * Text Domain: simulador-de-crisis
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Función de activación del plugin.
 * Se encarga de verificar y crear la página inicial del simulador.
 */
function sdc_activate_plugin() {
    global $wpdb;
    $page_title = 'Simulador de Crisis';
    $page_slug  = 'simulador-de-crisis';
    
    // Verificar si la página ya existe por su slug (más fiable que el título)
    $query = new WP_Query( array(
        'post_type'      => 'page',
        'name'           => $page_slug,
        'post_status'    => array( 'publish', 'pending', 'draft', 'private' ),
        'posts_per_page' => 1,
    ) );

    if ( ! $query->have_posts() ) {
        // La página no existe, procedemos a crearla
        $new_page = array(
            'post_type'    => 'page',
            'post_title'   => $page_title,
            'post_content' => '[simulador_de_crisis]',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_name'    => $page_slug
        );
        
        $page_id = wp_insert_post( $new_page );
        update_post_meta( $page_id, '_wp_page_template', 'templates/page-simulator.php' );
    } else {
        $page_id = $query->posts[0]->ID;
        update_post_meta( $page_id, '_wp_page_template', 'templates/page-simulator.php' );
    }

    $charset_collate = $wpdb->get_charset_collate();
    $table_applicants = $wpdb->prefix . 'sdc_applicants';
    $table_simulations = $wpdb->prefix . 'sdc_simulations';

    // Tabla de perfiles únicos (Aplicantes)
    $sql_applicants = "CREATE TABLE $table_applicants (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        company_name varchar(255) NOT NULL,
        user_name varchar(255) NOT NULL,
        user_position varchar(255) DEFAULT '',
        user_email varchar(255) NOT NULL,
        user_phone varchar(50) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        UNIQUE KEY user_email (user_email),
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Tabla de ejecuciones (Simulaciones)
    $sql_simulations = "CREATE TABLE $table_simulations (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        applicant_id mediumint(9) NOT NULL,
        results text DEFAULT '', 
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_applicants );
    dbDelta( $sql_simulations );
}
register_activation_hook( __FILE__, 'sdc_activate_plugin' );

/**
 * Registrar el template en la lista de WordPress.
 */
function sdc_add_page_template( $templates ) {
    $templates['templates/page-simulator.php'] = 'Simulador de Crisis Full Page';
    return $templates;
}
add_filter( 'theme_page_templates', 'sdc_add_page_template' );

/**
 * Cargar el archivo del template desde el plugin.
 */
function sdc_load_page_template( $template ) {
    if ( get_page_template_slug() === 'templates/page-simulator.php' ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/page-simulator.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'sdc_load_page_template' );

/**
 * Cargar estilos del simulador.
 */
function sdc_enqueue_scripts() {
    // Solo cargar si es la página del simulador o usa el template del simulador
    if ( ! is_page( 'simulador-de-crisis' ) && ! is_page_template( 'templates/page-simulator.php' ) ) {
        return;
    }

    wp_enqueue_style( 'sdc-styles', plugin_dir_url( __FILE__ ) . 'assets/css/simulador-styles.css', array(), '1.0.5' );
    wp_enqueue_script( 'sdc-chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.1', true );
    wp_enqueue_script( 'sdc-counter', plugin_dir_url( __FILE__ ) . 'assets/js/counter.js', array(), '1.0.0', true );
    wp_enqueue_script( 'sdc-animate-in', plugin_dir_url( __FILE__ ) . 'assets/js/animate-in.js', array(), '1.0.0', true );
    wp_enqueue_script( 'sdc-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/simulador-scripts.js', array('jquery', 'sdc-chartjs', 'sdc-animate-in'), '1.0.5', true );
    
    // Pasar URL de AJAX al frontend
    wp_localize_script( 'sdc-scripts', 'sdc_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( 'wp_enqueue_scripts', 'sdc_enqueue_scripts' );

/**
 * Shortcode para mostrar el simulador.
 */
function sdc_simulator_shortcode() {
    ob_start();
    include plugin_dir_path( __FILE__ ) . 'templates/simulador-view.php';
    return ob_get_clean();
}
add_shortcode( 'simulador_de_crisis', 'sdc_simulator_shortcode' );

/**
 * Cargar lógica administrativa.
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/admin-pages.php';

/**
 * AJAX Handler para guardar/actualizar los datos del aplicante e iniciar simulación.
 */
function sdc_save_applicant_handler() {
    global $wpdb;
    $table_applicants = $wpdb->prefix . 'sdc_applicants';
    $table_simulations = $wpdb->prefix . 'sdc_simulations';

    $company  = sanitize_text_field( $_POST['company_name'] );
    $name     = sanitize_text_field( $_POST['user_name'] );
    $position = sanitize_text_field( $_POST['user_position'] );
    $email    = sanitize_email( $_POST['user_email'] );
    $phone    = sanitize_text_field( $_POST['user_phone'] );

    // Verificar si el aplicante ya existe
    $applicant_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_applicants WHERE user_email = %s", $email ) );

    if ( $applicant_id ) {
        // Actualizar datos del perfil existente
        $wpdb->update(
            $table_applicants,
            array(
                'company_name'  => $company,
                'user_name'     => $name,
                'user_position' => $position,
                'user_phone'    => $phone,
            ),
            array( 'id' => $applicant_id )
        );
    } else {
        // Crear nuevo perfil de aplicante
        $wpdb->insert(
            $table_applicants,
            array(
                'company_name'  => $company,
                'user_name'     => $name,
                'user_position' => $position,
                'user_email'    => $email,
                'user_phone'    => $phone,
                'created_at'    => current_time( 'mysql' )
            )
        );
        $applicant_id = $wpdb->insert_id;
    }

    // Buscar simulación pendiente (sin resultados) para este aplicante
    $existing_sim = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM $table_simulations WHERE applicant_id = %d AND (results = '' OR results IS NULL) ORDER BY id DESC LIMIT 1",
        $applicant_id
    ) );

    if ( $existing_sim ) {
        // Reutilizar la simulación incompleta existente
        $simulation_id = $existing_sim;
    } else {
        // Crear nuevo registro de simulación
        $wpdb->insert(
            $table_simulations,
            array(
                'applicant_id' => $applicant_id,
                'results'      => '',
                'created_at'   => current_time( 'mysql' )
            )
        );
        $simulation_id = $wpdb->insert_id;
    }

    if ( $simulation_id ) {
        wp_send_json_success( array( 
            'message' => 'Simulación iniciada',
            'simulation_id' => $simulation_id 
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al iniciar la simulación' ) );
    }
}
add_action( 'wp_ajax_sdc_save_applicant', 'sdc_save_applicant_handler' );
add_action( 'wp_ajax_nopriv_sdc_save_applicant', 'sdc_save_applicant_handler' );

/**
 * AJAX Handler para guardar los resultados finales de la simulación.
 */
function sdc_save_results_handler() {
    global $wpdb;
    $table_simulations = $wpdb->prefix . 'sdc_simulations';

    $simulation_id = intval( $_POST['simulation_id'] );
    $results_json  = wp_unslash( $_POST['results_json'] ); // wp_unslash para quitar magic quotes de WP

    $updated = $wpdb->update(
        $table_simulations,
        array( 'results' => $results_json ),
        array( 'id' => $simulation_id )
    );

    if ( $updated !== false ) {
        wp_send_json_success( array( 'message' => 'Resultados guardados' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al guardar resultados' ) );
    }
}
add_action( 'wp_ajax_sdc_save_results', 'sdc_save_results_handler' );
add_action( 'wp_ajax_nopriv_sdc_save_results', 'sdc_save_results_handler' );
