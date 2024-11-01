<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function ter_kal_free_uninstall_check() {
    $free_version_file = 'termin-kalender/termin-kalender.php';
    $pro_version_file = 'termin-kalender-pro/termin-kalender.php';
    global $wp_filesystem;
    require_once ABSPATH . 'wp-admin/includes/file.php';
    // This is the FREE Version
    if ( file_exists( WP_PLUGIN_DIR . '/' . $pro_version_file ) && is_plugin_active( $free_version_file ) ) {
        deactivate_plugins( $free_version_file );
        add_action( 'admin_notices', 'ter_kal_pro_version__notice' );
        wp_redirect( admin_url( 'plugins.php' ) );
        exit;
    }
}

// end ter_kal_free_uninstall_check
add_action( 'admin_init', 'ter_kal_free_uninstall_check' );
function ter_kal_pro_version__notice() {
    ?><div class="notice notice-warning is-dismissible">
        <p><?php 
    _e( 'The PRO version of Termin-Kalender has been activated or installed. PLEASE UNINSTALL FREE.', 'text-domain' );
    ?></p>
    </div><?php 
}

/*
function ter_kal_free_uninstall_check() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
	    WP_Filesystem();
	}
	$plugin_main_path = WP_PLUGIN_DIR;
	$free_version_base = 'termin-kalender/termin-kalender.php';
    $pro_version_base = 'termin-kalender-pro/termin-kalender.php';
	$pro_version_test_base = 'termin-kalender0.99.24/termin-kalender.php'; // ANPASSEN für TEST    /
	$free_version_file = $plugin_main_path.'/'.$free_version_base;
    $pro_version_file = $plugin_main_path.'/'.$pro_version_base;
	$pro_version_test = $plugin_main_path.'/'.$pro_version_test_base; // ANPASSEN für TEST
	$infotext = '';
	$pro_installed = 'no';
	$test_installed = 'no';
	// Check if the files exist
	if (file_exists($free_version_file)) {
		$free_installed = 'yes';
	} else {
		$free_installed = 'no';
	}
	if (file_exists($pro_version_file)) {
		$pro_installed = 'yes';
	}
	if (file_exists($pro_version_test)) {
		$pro_installed = 'yes';
		$test_installed = 'yes';
}
	if ( $free_installed == 'yes' &&  $pro_installed == 'yes'  ) {
		if (!is_plugin_active($free_version_base)) {
            deactivate_plugins( $free_version_file );
            add_action( 'admin_notices', 'ter_kal_pro_version__notice' );
            wp_redirect( admin_url( 'plugins.php' ) );
            exit;
		};
		deactivate_plugins( $free_version_base );
		wp_delete_file($free_version_file);
		$free_version_folder = $plugin_main_path . '/' . dirname($free_version_base);
		// Check if the folder exists.
		if ($wp_filesystem->is_dir($free_version_folder)) {
		    // Delete the folder recursively.
		    $wp_filesystem->delete($free_version_folder, true);
		}
		if ($test_installed == 'yes') {
		    //if (!is_plugin_active($pro_version_test_base)) {
		        activate_plugins($pro_version_test_base);
		    //}
		} else {
		    //if (!is_plugin_active($pro_version_base)) {
		        activate_plugins($pro_version_base);
		    //}
		}

	}
}
function ter_kal_pro_version__notice() {
    ?><div class="notice notice-warning is-dismissible">
        <p><?php
    _e( 'The PRO version of Termin-Kalender has been installed. PLEASE ACTIVATE PRO.', 'termin-kalender' );
    ?></p>
    </div><?php
}


include_once ABSPATH . 'wp-admin/includes/plugin.php';
function ter_kal_free_uninstall_check() {
    $free_version_file = 'termin-kalender/termin-kalender.php';
    $pro_version_file = 'termin-kalender-pro/termin-kalender.php';
    global $wp_filesystem;
    require_once ABSPATH . 'wp-admin/includes/file.php';
    // This is the FREE Version
    if ( file_exists( WP_PLUGIN_DIR . '/' . $pro_version_file ) && is_plugin_active( $free_version_file ) ) {
        deactivate_plugins( $free_version_file );
        add_action( 'admin_notices', 'ter_kal_pro_version__notice' );
        wp_redirect( admin_url( 'plugins.php' ) );
        exit;
    }
}
// end ter_kal_free_uninstall_check
add_action( 'admin_init', 'ter_kal_free_uninstall_check' );*/