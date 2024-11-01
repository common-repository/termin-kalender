<?php

// Exit if accessed directly.
defined( 'WPINC' ) || die;
require_once TER_KAL_PLUGIN_DIR . 'admin/terminkalender_page.php';
require_once TER_KAL_PLUGIN_DIR . 'admin/termin_kategorien.php';
//	require_once TER_KAL_PLUGIN_DIR . 'admin/admin_help_website.php';
require_once TER_KAL_PLUGIN_DIR . 'admin/admin_blocks_page.php';
function ter_kal_add_admin_menu() {
    add_menu_page(
        esc_html__( 'easy Termin-Kalender', 'termin-kalender' ),
        esc_html__( 'easy Termin-Kalender', 'termin-kalender' ),
        'edit_posts',
        'ter_kal_terminkalender',
        'ter_kal_terminkalender_page',
        'dashicons-calendar',
        4
    );
    add_submenu_page(
        'ter_kal_terminkalender',
        esc_html__( 'Calendar Categories', 'termin-kalender' ),
        esc_html__( 'Calendar Categories', 'termin-kalender' ),
        'edit_posts',
        'terminkalender_termin_kategorien',
        'ter_kal_termin_kategorien_page'
    );
    add_submenu_page(
        'ter_kal_terminkalender',
        esc_html__( 'Gutenberg Blocks', 'termin-kalender' ),
        esc_html__( 'Gutenberg Blocks', 'termin-kalender' ),
        'edit_posts',
        'terminkalender_gutenberg',
        'ter_kal_terminkalender_settings'
    );
    add_submenu_page(
        'ter_kal_terminkalender',
        esc_html__( 'To-Do List', 'termin-kalender' ),
        esc_html__( 'To-Do List', 'termin-kalender' ),
        'edit_posts',
        'todo-list',
        'todo_list_admin_page'
    );
    //	add_submenu_page('ter_kal_terminkalender', esc_html__('Help', 'termin-kalender'), esc_html__('Help', 'termin-kalender'), 'edit_posts', 'terminkalender_help_website', 'terminkalender_help_website_page');
    //  return;
}

add_action( 'admin_menu', 'ter_kal_add_admin_menu' );