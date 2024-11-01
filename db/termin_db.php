<?php
defined('ABSPATH') || exit;
function ter_kal_termin_db_erstellen() {
// Datenbank f�r Kalender erstellen   Update 0.98 Datenbank erstellen mit CREATE TABLE IF NOT EXIST
    global $wpdb;
    $sql = "CREATE TABLE IF NOT EXISTS " . TER_KAL_TERMIN_DB . " (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(128) NOT NULL,
        start datetime NOT NULL,
        end datetime,
        startUTC varchar(32),
        endUTC varchar(32),
        freq varchar(32),
        byweekday varchar(32),
        kategorie_id mediumint(9),
        Beschreibung varchar(1024),
        Ort varchar(128),
        Teilnehmer varchar(128),
        Notizen varchar(1024),
        notiz_journal varchar(1024),
		list_image varchar(128),
        zusatz_url varchar(128), 
        PRIMARY KEY  (id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
};
