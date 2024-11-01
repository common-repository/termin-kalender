<?php

/*
Plugin Name: Termin-Kalender
Plugin URI: https://termin-kalender.pro
Description: Termin-Kalender is your easy monthly planner. This calendar app provides a clear, monthly overview to keep you organized.
Version: 1.00.04
Author: Beat Kueffer
License: GPL2+
Text Domain:       termin-kalender
Domain Path:       /languages */
defined( 'WPINC' ) || die;
//------------------------------------------------
//try to solve problem with free and pro version activated
//------------------------------------------------
$free_version_file = 'termin-kalender/termin-kalender.php';
$pro_version_file = 'termin-kalender-pro/termin-kalender.php';
$dev_version_file = 'termin-kalender1.00.04/termin-kalender.php';
//  add Version every update
$pro_installed = file_exists( WP_PLUGIN_DIR . '/' . $pro_version_file ) || file_exists( WP_PLUGIN_DIR . '/' . $dev_version_file );
if ( file_exists( WP_PLUGIN_DIR . '/' . $free_version_file ) && $pro_installed ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    deactivate_plugins( $free_version_file );
}
//------------------------------------------------
if ( function_exists( 'ter_kal_fs' ) ) {
    ter_kal_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    //beginn Freemius konfiguration--------------------------
    // Override the Freemius-related function
    if ( !function_exists( 'ter_kal_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ter_kal_fs() {
            global $ter_kal_fs;
            if ( !isset( $ter_kal_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ter_kal_fs = fs_dynamic_init( array(
                    'id'             => '13582',
                    'slug'           => 'termin-kalender',
                    'premium_slug'   => 'termin-kalender-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_09e08cdfe1622dc7ef147ce403bab',
                    'is_premium'     => false,
                    'premium_suffix' => 'PRO',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                        'slug'       => 'ter_kal_terminkalender',
                        'first-path' => 'admin.php?page=ter_kal_terminkalender',
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $ter_kal_fs;
        }

        // Init Freemius.
        ter_kal_fs();
        // Signal that SDK was initiated.
        do_action( 'ter_kal_fs_loaded' );
    }
    //ende Freemius konfiguration--------------------------
    // ... Your plugin's main file logic ...
}
// end freemius
//------------------------------------------------
global $wpdb;
//define( 'TK_SLUG', 'termin-kalender');  //define( 'MY_HOME', esc_url( home_url() ).'/' );  //define( 'MY_SITEURL', esc_url( site_url() ).'/' );
/* tabellen und feld (COLUMN) namen können nicht mit %... "prepared" werden. table names are not user-supplied and do not require sanitization. */
define( 'TER_KAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TER_KAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TER_KAL_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'TER_KAL_TERMIN_DB', $wpdb->prefix . 'ter_kal_termin_kalender' );
define( 'TER_KAL_ADMIN_AJAX_URL', admin_url( 'admin-ajax.php' ) );
define( 'TER_KAL_BASIS_URL', 'http://termin-kalender.pro' );
define( 'TER_KAL_TK_HEADLINE', 'my easy Termin-Kalender calendar' );
define( 'TER_KAL_BACKUP_PFAD', WP_CONTENT_DIR . '/uploads/_tk_/' );
define( 'TER_KAL_BACKUP_URL', content_url( '/uploads/_tk_/' ) );
$plugin_data = get_plugin_data( __FILE__ );
define( 'TER_KAL_TK_VERSION', $plugin_data['Version'] );
$upgrade_url = ter_kal_fs()->get_upgrade_url();
define( 'TER_KAL_UPGRADE_URL', $upgrade_url );
//interne (admin) fremium kaufen url
// ajaxurl for javascript is defined by wordpress
add_action( 'init', function () {
    $locale = determine_locale();
    // Bestimmt die aktuelle Locale basierend auf WordPress-Einstellungen oder Benutzerprofil
    $locale_prefix = substr( $locale, 0, 3 );
    //define('TER_KAL_LOCALE', $locale_prefix );
    // Setze die Plugin-Sprache auf de_DE oder fr_FR, wenn eine Variante erkannt wird
    if ( 'de_' === $locale_prefix ) {
        load_textdomain( 'termin-kalender', TER_KAL_PLUGIN_DIR . '/languages/termin-kalender-de_DE.mo' );
    } elseif ( 'fr_' === $locale_prefix ) {
        load_textdomain( 'termin-kalender', TER_KAL_PLUGIN_DIR . '/languages/termin-kalender-fr_FR.mo' );
    } else {
        load_plugin_textdomain( 'termin-kalender', false, dirname( TER_KAL_PLUGIN_BASE ) . '/languages' );
        // ebenfalls TER_KAL_PLUGIN_DIR ??
    }
} );
//------------------------------------------------
// Define activation and deactivation functions
function ter_kal_activate() {
    // Code to execute on plugin activation
    if ( file_exists( WP_PLUGIN_DIR . '/termin-kalender-pro/termin-kalender.php' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins( 'termin-kalender/termin-kalender.php' );
    }
    delete_transient( 'ter_kal_plugin_ran' );
    // activation check must run again
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    // von freemius (auskommentiert) ?
    require_once TER_KAL_PLUGIN_DIR . 'db/kategorie_options.php';
    require_once TER_KAL_PLUGIN_DIR . 'db/termin_db.php';
    require_once TER_KAL_PLUGIN_DIR . 'db/felder_options.php';
    ter_kal_kategorie_options_erstellen();
    ter_kal_termin_db_erstellen();
    ter_kal_felder_options_erstellen();
}

// end plugin activation
//------------------------------------------------
function ter_kal_deactivate() {
    //delete hiding dashboard installation info for all users   delete_metadata('user', 0, 'ter_kal_hide_dash_info', '', true);
    delete_transient( 'ter_kal_plugin_ran' );
    delete_transient( 'ter_kal_kalender_data' );
    delete_transient( 'ter_kal_user_selected' );
    delete_transient( 'ter_kal_lang' );
    $options = wp_load_alloptions();
    $transient_prefix = 'notification_sent_';
    // ? ter_kal_
    foreach ( $options as $option_name => $option_value ) {
        // Check if the option name starts with the specified prefix
        if ( strpos( $option_name, $transient_prefix ) === 0 ) {
            delete_transient( substr( $option_name, strlen( $transient_prefix ) ) );
        }
    }
}

function ter_kal_fs_uninstall_cleanup() {
    // Code to execute on plugin deactivation
    // Auto Daten-Backup - Cleanup - DB löschen?  IF PRO: ter_kal_backup_selected_tables()
    // ask if db entries should be deleted
    // remove options: ter_kal_benutzer
}

// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'ter_kal_activate' );
register_deactivation_hook( __FILE__, 'ter_kal_deactivate' );
ter_kal_fs()->add_action( 'after_uninstall', 'ter_kal_fs_uninstall_cleanup' );
//----------------------------------------------------------------------------------------
// Define plugin actions functions
require_once TER_KAL_PLUGIN_DIR . 'init/register_enqueue.php';
// Define plugin actions, scripts and styles
add_action( 'wp_enqueue_scripts', 'ter_kal_enqueue_scripts' );
/* Admin scripts and style*/
add_action( 'admin_enqueue_scripts', 'ter_kal_admin_enqueue_scripts' );
//plugin aktivieren funktionen laden admin und allgemein
require_once TER_KAL_PLUGIN_DIR . 'db/activate.php';
require_once TER_KAL_PLUGIN_DIR . 'db/load_transients.php';
require_once TER_KAL_PLUGIN_DIR . 'admin/benutzer.php';
require_once TER_KAL_PLUGIN_DIR . 'includes/kalender_functions.php';
require_once TER_KAL_PLUGIN_DIR . 'admin/admin_funktionen.php';
// oben: funktionen zuerst laden   // frontend code:
require_once TER_KAL_PLUGIN_DIR . 'includes/kalender_form.php';
require_once TER_KAL_PLUGIN_DIR . 'includes/kalender_form_ausgabe.php';
// shortcodes
require_once TER_KAL_PLUGIN_DIR . 'block/kalender_shortcodes.php';
require_once TER_KAL_PLUGIN_DIR . 'block/reservation_shortcode.php';
require_once TER_KAL_PLUGIN_DIR . 'block/event_list_shortcode.php';
//
require_once TER_KAL_PLUGIN_DIR . 'includes/kalender_help_page.php';
// admin code
require_once TER_KAL_PLUGIN_DIR . 'admin/admin.php';
require_once TER_KAL_PLUGIN_DIR . 'block/register_gutenblock.php';
// extensions
require_once TER_KAL_PLUGIN_DIR . 'extensions/todo_list.php';
require_once TER_KAL_PLUGIN_DIR . 'extensions/todo_list_gutenberg.php';
require_once TER_KAL_PLUGIN_DIR . 'extensions/event_list_gutenberg_free.php';