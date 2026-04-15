<?php
/**
 * Template Name: Simulador de Crisis Full Page (with Header/Footer)
 * Description: Template que incluye el header y footer del sitio, pero permite que el simulador ocupe el ancho completo.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<div id="sdc-full-width-wrapper">
    <?php 
    // Incluimos la vista del simulador
    include plugin_dir_path( __FILE__ ) . 'simulador-view.php'; 
    ?>
</div>

<style>
    /* Estilos para forzar el ancho completo si el tema tiene contenedores restrictivos */
    #sdc-full-width-wrapper {
        position: relative;
        width: 100dvw;
    }
    
    /* Aseguramos que no haya scroll horizontal por el 100vw */
    body {
        overflow-x: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    body::-webkit-scrollbar {
        display: none;
    }

    /* Ocultar posibles paddings del contenedor principal del tema si es posible identificarlo */
    .site-content, .content-area, .entry-content {
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
    }
</style>

<?php
get_footer();
