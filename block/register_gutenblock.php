<?php
defined('WPINC') || die;
// Register Custom Gutenberg Block

function ter_kal_register_gutenberg_block() {
    // Block Frontend Script
    //skript registrieren um variablen von php an js zu übergeben
    wp_register_script(
        'ter_kal-gutenberg-block-script',
        TER_KAL_PLUGIN_URL . 'js/block.js',
        array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'jquery' ),
        TER_KAL_TK_VERSION ,
        true
    );
    // variablen zur weitergabe vorbereiten
    // register - VARIABLEN  dann localize und zuletzt enqueue das empfängerskript
    $my_array = array( 'TER_KAL_PLUGIN_URL' => TER_KAL_PLUGIN_URL );   // init
    $my_array['TER_KAL_ADMIN_AJAX_URL'] = TER_KAL_ADMIN_AJAX_URL ;           // add
    // hinzufügen mit komma oder später: $my_array['TER_KAL_PLUGIN_DIR'] = 'TER_KAL_PLUGIN_DIR';
    // in js abrugfen js_wp_php_vars.TER_KAL_PLUGIN_URL
    wp_localize_script( 'ter_kal-gutenberg-block-script', 'js_wp_php_vars', $my_array );
    wp_enqueue_script('ter_kal-gutenberg-block-script' );



    // Register Block Type
    register_block_type( 'termin-kalender/shortcode-block', array(
        'editor_script' => 'ter_kal-gutenberg-block-script',
        'render_callback' => 'ter_kal_shortcode_block_render',
    ) );
  // return;
}
add_action( 'init', 'ter_kal_register_gutenberg_block' );


// Block Frontend Markup
function ter_kal_shortcode_block_render( $attributes ) {
    return '<div class="ter_kal-gutenberg-block-script">[my-termin-kalender]</div>';
}