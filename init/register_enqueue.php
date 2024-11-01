<?php

defined( 'ABSPATH' ) || exit;
function ter_kal_enqueue_scripts() {
    // externe scripts registrieren
    wp_register_style(
        'terminkalender_bootstrap-css',
        TER_KAL_PLUGIN_URL . 'dist/bootstrap/dist/css/bootstrap.min.css',
        [],
        '5.3.3',
        'all'
    );
    wp_register_style(
        'terminkalender_bootstrap-icons',
        TER_KAL_PLUGIN_URL . 'dist/bootstrap-icons/font/bootstrap-icons.min.css',
        [],
        '1.11.3',
        'all'
    );
    wp_register_script(
        'terminkalender_bootstrap',
        TER_KAL_PLUGIN_URL . 'dist/bootstrap/dist/js/bootstrap.bundle.min.js',
        ['jquery'],
        '5.3.3',
        true
    );
    wp_register_script(
        'terminkalender_rrule',
        TER_KAL_PLUGIN_URL . 'dist/rrule/dist/es5/rrule.min.js',
        ['jquery'],
        '2.8.1',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar',
        TER_KAL_PLUGIN_URL . 'dist/fullcalendar/index.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar-google-calendar',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/google-calendar/index.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar-icalendar',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/icalendar/index.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    //   WP_Date_Query verwenden
    wp_register_script(
        'terminkalender_fullcalendar-locales',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/core/locales-all.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar-rrule',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/rrule/index.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar-bootstrap5',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/bootstrap5/index.global.min.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_fullcalendar-react',
        TER_KAL_PLUGIN_URL . 'dist/@fullcalendar/react/dist/index.js',
        ['jquery'],
        '6.1.14',
        true
    );
    wp_register_script(
        'terminkalender_script',
        TER_KAL_PLUGIN_URL . 'js/terminkalender.js',
        [
            'jquery',
            'wp-date',
            'terminkalender_rrule',
            'media-views'
        ],
        TER_KAL_TK_VERSION,
        true
    );
    //$event_daten = ter_kal_event_daten_return(); 		'ter_kal_addEventSource' => $event_daten ,
    wp_localize_script( 'terminkalender_script', 'ter_kal_kalender_vars', [
        'is_user_logged_in' => ( is_user_logged_in() ? '1' : '0' ),
        'ter_kal_lang'      => get_transient( 'ter_kal_lang' ),
        'select_user'       => __( 'Participants', 'termin-kalender' ),
        'neuer_termin'      => __( 'New Schedule', 'termin-kalender' ),
        'neuer_termin_am'   => __( 'New schedule at ', 'termin-kalender' ),
        'click_more'        => __( 'click for more', 'termin-kalender' ),
        'WEEKLY'            => __( 'Weekly ', 'termin-kalender' ),
        'MONTHLY'           => __( 'Monthly ', 'termin-kalender' ),
        'YEARLY'            => __( 'Annual ', 'termin-kalender' ),
    ] );
    wp_register_script(
        'terminkalender_modal',
        TER_KAL_PLUGIN_URL . 'js/tk_modal.js',
        ['jquery', 'wp-date', 'terminkalender_rrule'],
        TER_KAL_TK_VERSION,
        true
    );
    wp_localize_script( 'terminkalender_modal', 'ter_kal_modal_vars', [
        'sure_delete'      => __( 'Do you really want to delete this schedule?', 'termin-kalender' ),
        'help_title'       => __( 'HELP', 'termin-kalender' ),
        'end_after_start'  => __( 'End date after start date please', 'termin-kalender' ),
        'start_before_end' => __( 'Please set the start date before end date', 'termin-kalender' ),
    ] );
    wp_register_style(
        'terminkalender_style',
        TER_KAL_PLUGIN_URL . 'css/terminkalender.css',
        [],
        TER_KAL_TK_VERSION,
        'all'
    );
    if ( has_shortcode( get_post()->post_content, 'my-termin-kalender' ) || has_shortcode( get_post()->post_content, 'my-termin-reservation' ) || has_shortcode( get_post()->post_content, 'my-termin-eventlist' ) || has_block( 'termin-kalender/todo-list', get_post() ) || has_block( 'termin-kalender/my-termin-list', get_post() ) ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_media();
        // später um bilder einfügen zu können
        // wp_enqueue_editor();  removed tinymce editor frontend issues
        wp_enqueue_style( 'dashicons' );
        // important to show dashicons in frontend
        //jquery ddashicon underscore  wp-util --if (wp_util.currentUserHasCapability('edit_posts')-- wp-api-fetch sind bereits core registriert, direkt enqueuen
        wp_enqueue_style( 'terminkalender_bootstrap-css' );
        wp_enqueue_style( 'terminkalender_bootstrap-icons' );
        wp_enqueue_script( 'terminkalender_bootstrap' );
        wp_enqueue_style( 'terminkalender_style' );
    }
    if ( has_shortcode( get_post()->post_content, 'my-termin-kalender' ) ) {
        wp_enqueue_script( 'terminkalender_rrule' );
        wp_enqueue_script( 'terminkalender_fullcalendar' );
        //wp_enqueue_script('terminkalender_fullcalendar-google-calendar');
        //wp_enqueue_script('terminkalender_fullcalendar-icalendar');
        wp_enqueue_script( 'terminkalender_fullcalendar-locales' );
        wp_enqueue_script( 'terminkalender_fullcalendar-rrule' );
        wp_enqueue_script( 'terminkalender_fullcalendar-bootstrap5' );
        //wp_enqueue_script('terminkalender_fullcalendar-react');
        //internal
        wp_enqueue_script( 'terminkalender_script' );
        wp_enqueue_script( 'terminkalender_modal' );
    }
    return;
}

//end front
/* Admin scripts and style*/
function ter_kal_admin_enqueue_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    // Enqueue jQuery UI Sortable
    //wp_enqueue_style('dashicons');
    wp_enqueue_style(
        'ter_kal_AdminCSS',
        TER_KAL_PLUGIN_URL . 'css/admin.css',
        [],
        TER_KAL_TK_VERSION,
        'all'
    );
    wp_enqueue_script(
        'ter_kal_AdminJS',
        TER_KAL_PLUGIN_URL . 'js/admin.js',
        ['jquery'],
        TER_KAL_TK_VERSION,
        true
    );
    wp_localize_script( 'ter_kal_AdminJS', 'ter_kal_admin_vars', [
        'sample_data_loded' => __( 'Example dates where loaded into the calendar', 'termin-kalender' ),
    ] );
    //return;
}

//end admin