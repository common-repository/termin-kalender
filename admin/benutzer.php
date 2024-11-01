<?php

defined( 'WPINC' ) || die;
// benutzerrechte prüfen: zeigen bearbeiten loeschen (oder LEER)
function ter_kal_benutzerrechte_pruefen() {
    // Admin SuperAdmin(Multisite)
    $current_user = wp_get_current_user();
    if ( in_array( 'administrator', $current_user->roles ) || is_super_admin( $current_user->ID ) ) {
        return 'loeschen';
    }
    $benutzer_id = get_current_user_id();
    // $option = get_option('ter_kal_benutzer_' . $benutzer_id);  // ALT
    $option = get_user_meta( $benutzer_id, 'ter_kal_rights', true );
    if ( $option !== false ) {
        return $option;
        // (leer oder) zeigen bearbeiten loeschen
    }
    // Sonst oder nicht eigelogt
    return 'zeigen';
}

// Benutzer-Rechte mit Rolle und Name anzeigen
function ter_kal_aktueller_benutzer() {
    $user = wp_get_current_user();
    $rights = ter_kal_benutzerrechte_pruefen();
    $rights_explained = 'Show the calendar';
    if ( $rights == 'bearbeiten' ) {
        $rights_explained = 'Add and edit calendar entries';
    }
    if ( $rights == 'loeschen' ) {
        $rights_explained = 'Add, edit and delete calendar entries';
    }
    if ( !is_user_logged_in() ) {
        // keep for frontend use
        $login_url = wp_login_url();
        echo ' <sub> <a href=" ' . esc_url( $login_url ) . '"> Login to edit</a></sub>';
    } else {
        echo '<sub>User: ' . $user->display_name . ', roles: ' . implode( ', ', $user->roles ) . ', rights: ' . $rights_explained . '</sub>';
    }
    //   return;
}

// Benutzer Info ausgeben
function ter_kal_lizenz_info() {
    // keine lizenz
    echo 'my easy Termin-Kalender calendar FREE';
    //  return;
}
