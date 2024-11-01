<?php
defined('ABSPATH') || exit;
function ter_kal_felder_options_erstellen() {
/* Termin Felder:
feld_art: Art des Formularfeldes
feld_zeigen: Feld ist in den Formularen sichtbar oder nicht (um nicht zu löschen, wenn Daten evtl noch gebraucht werden)
*/
$option = array(
    array(
        'feld_name' => 'backgroundColor',
        'feld_art' => 'color',
    ),
    array(
        'feld_name' => 'Beschreibung',
        'feld_art' => 'textarea',
    ),
    array(
        'feld_name' => 'end',
        'feld_art' => 'datetime',
    ),
    array(
        'feld_name' => 'icon',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'id',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'kategorie',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'kategorie_id',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'notiz_journal',
        'feld_art' => 'textarea',
    ),
    array(
        'feld_name' => 'Notizen',
        'feld_art' => 'textarea',
    ),
    array(
        'feld_name' => 'Ort',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'start',
        'feld_art' => 'datetime',
    ),
    array(
        'feld_name' => 'Teilnehmer',
        'feld_art' => 'text',
    ),
    array(
        'feld_name' => 'textColor',
        'feld_art' => 'color',
    ),
    array(
        'feld_name' => 'title',
        'feld_art' => 'text',
    ),
        array(
        'feld_name' => 'freq',
        'feld_art' => 'text',
    ),
        array(
        'feld_name' => 'byweekday',
        'feld_art' => 'text',
    ),
);
update_option('ter_kal_termin_basisfelder', $option);

$ter_kal_termin_zusatzfelder_option = get_option('ter_kal_termin_zusatzfelder');
if (empty($ter_kal_termin_zusatzfelder_option)) {
    $ter_kal_termin_zusatzfelder = [
        [
            'feld_key' => 'list_image',
            'feld_name' => 'list_image',
            'feld_art' => 'text',
            'feld_zeigen' => '1',
        ],
        [
            'feld_key' => 'zusatz_url',
            'feld_name' => 'zusatz_url',
            'feld_art' => 'url',
            'feld_zeigen' => '1',
        ],
    ];
    update_option('ter_kal_termin_zusatzfelder', $ter_kal_termin_zusatzfelder);
}

global $wpdb;
$table_name = $wpdb->prefix . 'ter_kal_termin_felder';
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") && $wpdb->get_var("SELECT COUNT(*) FROM $table_name")) {
    $db_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    if (!empty($db_data)) {
    //  'feld_basis' => $item['feld_basis'] entfernt
        $options = array_filter(array_map(function($item) {
            return [
                'feld_key' => $item['feld_key'],
                'feld_name' => $item['feld_name'],
                'feld_art' => $item['feld_art'],
                'feld_zeigen' => $item['feld_zeigen']
            ];
        }, $db_data), function($item) {
            return $item['feld_basis'] != 1;
        });
        foreach ($options as $option) {
                $ter_kal_termin_zusatzfelder_option[$option['feld_key']] = $option;
        }
        update_option('ter_kal_termin_zusatzfelder', $ter_kal_termin_zusatzfelder_option);
    }
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Cleanup duplicate keys or names
$option = get_option('ter_kal_termin_zusatzfelder');
if (is_array($option)) {
    $unique_keys = [];
    $unique_names = [];
    foreach ($option as $key => $value) {
        if (isset($value['feld_key']) && isset($value['feld_name'])) {
            if (in_array($value['feld_key'], $unique_keys) || in_array($value['feld_name'], $unique_names)) {
                unset($option[$key]);
            } else {
                $unique_keys[] = $value['feld_key'];
                $unique_names[] = $value['feld_name'];
            }
        }
    }
    update_option('ter_kal_termin_zusatzfelder', $option);
}
};
