<?php
defined('ABSPATH') || exit;
function ter_kal_kategorie_options_erstellen() {
$data = [
	        [
	            'kategorie_id' => '1',
	            'kategorie' => esc_html__('Regular dates', 'termin-kalender'),
	            'backgroundColor' => '#0407A4',
	            'textColor' => '#FFFFFF',
	            'icon' => 'groups'
	        ],
	        [
	            'kategorie_id' => '2',
	            'kategorie' => esc_html__('Tasks', 'termin-kalender'),
	            'backgroundColor' => '#2051CC',
	            'textColor' => '#FFFFFF',
	            'icon' => 'yes'
	        ],
	        [
	            'kategorie_id' => '3',
	            'kategorie' => esc_html__('Events', 'termin-kalender'),
	            'backgroundColor' => '#3DA3DE',
	            'textColor' => '#FFFFFF',
	            'icon' => 'tickets'
	        ],
	        [
	            'kategorie_id' => '4',
	            'kategorie' => esc_html__('Private dates', 'termin-kalender'),
	            'backgroundColor' => '#0E9AA4',
	            'textColor' => '#FFFFFF',
	            'icon' => 'universal-access'
	        ],
	        [
	            'kategorie_id' => '5',
	            'kategorie' => esc_html__('Meeting', 'termin-kalender'),
	            'backgroundColor' => '#5B9C35',
	            'textColor' => '#FFFFFF',
	            'icon' => 'clock'
	        ],
	        [
	            'kategorie_id' => '6',
	            'kategorie' => esc_html__('Holidays and absences', 'termin-kalender'),
	            'backgroundColor' => '#8A9E05',
	            'textColor' => '#FFFFFF',
	            'icon' => 'palmtree'
	        ],
	        [
	            'kategorie_id' => '7',
	            'kategorie' => esc_html__('Important Reminder', 'termin-kalender'),
	            'backgroundColor' => '#B42808',
	            'textColor' => '#FFFFFF',
	            'icon' => 'flag'
	        ],
	        [
	            'kategorie_id' => '8',
	            'kategorie' => esc_html__('Reservation', 'termin-kalender'),
	            'backgroundColor' => '#ffff00',
	            'textColor' => '#000000',
	            'icon' => 'smiley'
	        ]
		];
global $wpdb;
	// Check if the table with the former constant name TER_KAL_KAT_DB exists
	$table_name = $wpdb->prefix . 'ter_kal_termin_kategorien';
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") && $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name")) {
	    $db_data = $wpdb->get_results("SELECT kategorie_id, kategorie, backgroundColor, textColor, icon FROM $table_name", ARRAY_A);
	    if (!empty($db_data)) {
	        $options = array_map(function($item) {
	            return [
	                'kategorie_id' => $item['kategorie_id'],
	                'kategorie' => $item['kategorie'],
	                'backgroundColor' => $item['backgroundColor'],
	                'textColor' => $item['textColor'],
	                'icon' => $item['icon']
	            ];
	        }, $db_data);
	        update_option('ter_kal_kategorien', $options);
	    } else {
			if (empty(get_option('ter_kal_kategorien'))) {
				update_option('ter_kal_kategorien', $data);
			}
	    }
		 $wpdb->query("DROP TABLE IF EXISTS $table_name");
	} else {
	        // No entries in TER_KAL_KAT_DB found, create the option if empty
			if (empty(get_option('ter_kal_kategorien'))) {
				update_option('ter_kal_kategorien', $data);
			}
	}
   // return;
 }


