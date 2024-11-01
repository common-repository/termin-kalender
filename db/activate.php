<?php
defined('ABSPATH') || exit;

function ter_kal_create_calendar_page() {
	// block nicht verwenden, weil sonst page builder evtl ausgeschlossen werden?:
	// $kal_seite_content = '<!-- wp:block {"ref": "termin-kalender/shortcode-block"} /-->';
	// 'post_content' => '<header></header><div class="page-content">' . $kal_seite_content . '</div>',
    if (!isset($_POST['tk_nonce']) || !wp_verify_nonce($_POST['tk_nonce'], 'tk_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        wp_die(); // Exit the script
    }    
    $page_id = wp_insert_post([
        'post_title' => TER_KAL_TK_HEADLINE,
        'post_content' => '<div class="page-content"><!-- wp:shortcode -->[my-termin-kalender]<!-- /wp:shortcode --></div>',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'page'
    ]);
    return;
}
add_action('wp_ajax_ter_kal_create_calendar_page', 'ter_kal_create_calendar_page');

function ter_kal_beispieltermine_eintragen() {
/*    if (!isset($_POST['tk_nonce']) || !wp_verify_nonce($_POST['tk_nonce'], 'tk_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        wp_die(); // Exit the script
    }*/
	global $wpdb;
	$cities        = ['350 Fifth Avenue, New York', '221B Baker Street, London', '31 Rue Cambon, Paris', 'Dogenzaka, Shibuya-ku, Tokyo 150-0043, Japan', 'Oxford Street, Sydney', 'Apgujeong-dong, Seoul', 'Unter den Linden, Berlin', 'Marktgasse, Bern', 'Freie Strasse, Basel', 'Chapel Bridge, Luzern'];
	$quotes        = ['The best way to predict the future is to invent it. - Alan Kay', 'Life is really simple, but we insist on making it complicated. - Confucius', 'Love all, trust a few, do wrong to none. - William Shakespeare', 'In three words I can sum up everything Ive learned about life: it goes on. - Robert Frost'];
	$sprichwoerter = ['Zeit heilt alle Wunden. - Geoffrey Chaucer', 'Die Zeit heilt nichts, sie verändert nur. - Friedrich Hebbel ', 'Zeit haben ist besser als Zeit brauchen. - Ernst Ferstl ', 'Die Zeit vergeht, ob du etwas tust oder nicht. - Sam Levenson ', 'Wer Zeit hat, zu klagen, hat auch Zeit, etwas zu tun. - Marie von Ebner-Eschenbach ', 'Nicht die Zeit vergeht, wir vergehen. - Ingeborg Bachmann ', 'Zeit ist ein Geschenk, das wir uns selbst geben. - Peter Bregman ', 'Die Zeit ist ein Mass für die Veränderung. - Aristoteles ', 'Zeit ist das, was wir am meisten verschwenden, weil wir glauben, wir hätten genug davon. - Leo Buscaglia ', 'Die Zeit ist ein guter Arzt, aber ein schlechter Kosmetiker. - Lucille S. Harper ', 'Zeit ist das, was man an der Uhr abliest. - Albert Einstein ', 'Zeit ist das, was uns fehlt, um das zu tun, was wir eigentlich tun möchten. - Paulo Coelho ', 'Man kann die Zeit nicht festhalten, aber man kann sie nutzen. - Tim Ferriss ', 'Zeit ist das, was uns bleibt, wenn alles andere vorbei ist. - Ursula K. Le Guin ', 'Zeit ist eine Illusion. Das Leben ist eine Realität. - Eckhart Tolle ', 'Die Zeit flieht uns wie der Schatten. - Horaz'];
	$weisheiten    = ['Das Leben ist eine Reise, kein Ziel. - Ralph Waldo Emerson ', 'Lebe das Leben, das du liebst. Liebe das Leben, das du lebst. - Bob Marley ', 'Das Leben besteht nicht aus dem, was passiert, sondern aus dem, was du daraus machst. - Hans Christian Andersen ', 'Das Leben ist zu kurz, um auf jemand anderen zu warten, um dich glücklich zu machen. - Steve Jobs ', 'Geniesse das Leben, es ist von begrenzter Dauer. - Unknown ', 'Lebe jeden Tag, als wäre es dein letzter, denn eines Tages wird es das sein. - Steve Jobs ', 'Das Leben ist wie ein Fahrrad. Um das Gleichgewicht zu halten, musst du in Bewegung bleiben. - Albert Einstein ', 'Du bist der Meister deines Schicksals. Du bist der Kapitän deiner Seele. - William Ernest Henley ', 'Das Leben ist zu kurz, um sich über Dinge aufzuregen, die du nicht ändern kannst. - Unknown ', 'Das Glück deines Lebens hängt von der Beschaffenheit deiner Gedanken ab. - Marcus Aurelius ', 'Das Leben ist zu kostbar, um Zeit zu verschwenden. - Unknown ', 'Lebe heute, vergiss gestern und fürchte nicht morgen. - Unknown ', 'Glücklich zu sein bedeutet, das Leben so zu leben, wie du es leben möchtest. - Unknown ', 'Das Leben ist eine Chance, nutze sie. Das Leben ist Schönheit, bewundere sie. Das Leben ist ein Traum, mache ihn wahr. - Mother Teresa ', 'Nimm das Leben nicht zu ernst, sonst verpasst du den Spass;. - Unknown ', 'Das Leben ist ein Abenteuer, wage es. - Mother Teresa ', 'Lebe im Moment, denn das ist alles, was du hast. - Unknown ', 'Das Leben ist wie ein Buch. Manchmal muss man die Seite umblättern und weiterlesen. - Unknown'];
	$terminkategorie = get_option('ter_kal_kategorien');
	$i = 0;
    foreach ($terminkategorie as $row) {
    	$kategorie_id = $row['kategorie_id'];
 		$i++ ;
		// Store the category as a string variable  $titel     = $titles[$i-1] . ' - DEMO Data' ;
		$titel     = 'DEMO Data' ;
		$beschreibung = $quotes[array_rand($quotes)];
		$beschreibung .= '  -  ';
		$beschreibung .= $weisheiten[array_rand($weisheiten)];
		$beschreibung = wp_kses_post($beschreibung);
		$notizen      = $sprichwoerter[array_rand($sprichwoerter)];
		$notizen = wp_kses_post($notizen);
		$stadt   = $cities[array_rand($cities)];

		$data = [
			'title' => $titel,
			'start' => date('Y-m-d H:i', strtotime('+' . $i . ' day')),
			'end' => date('Y-m-d H:i', strtotime('+' . $i . ' day +2 hour')),
			'kategorie_id' => $kategorie_id,
			'Beschreibung' => $beschreibung,
			'Ort' => $stadt,
			'Notizen' => $notizen
		];

		$wpdb->insert(TER_KAL_TERMIN_DB, $data);
	}
    //return;
    wp_send_json_success(['message' => 'Data inserted successfully']);
}
add_action('wp_ajax_ter_kal_beispieltermine_eintragen', 'ter_kal_beispieltermine_eintragen');

function ter_kal_activate_update() {
// ausgelöst von load_transient.php ter_kal_plugin_check_transient ter_kal_plugin_ran
//----------------------------------------------------------------------------------------------

	global $wpdb;
	$table_felder = $wpdb->prefix . 'ter_kal_termin_felder';
	$table_kategorien = $wpdb->prefix . 'ter_kal_termin_kategorien';
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_felder'") || $wpdb->get_var("SHOW TABLES LIKE '$table_kategorien'")) {
	    ter_kal_activate();  // activate ausführen wenn felder oder kategorien tabelle noch existiert
	}
	// alte einträge löschen
    delete_option('ter_kal_checkbox_values');
    delete_option('ter_kal_timed_event_duration');
	unregister_setting('ter_kal_options_group', 'ter_kal_benutzer');
	unregister_setting('ter_kal_kategorien_group', 'ter_kal_kategorien');
	remove_filter('allowed_options', 'ter_kal_allowed_options');
	remove_filter('whitelist_options', 'ter_kal_allowed_options');
	// beispieltermine eintragen wenn kalender noch leer
	if ($wpdb->get_var("SHOW TABLES LIKE '" . TER_KAL_TERMIN_DB . "'") == TER_KAL_TERMIN_DB) {
    	$count = $wpdb->get_var("SELECT COUNT(*) FROM " . TER_KAL_TERMIN_DB);
	    if (!$count > 0) {  //ersteintrag nach aktivierung
		 	$data = [
				'title' =>  __('Termin-Kalender activated', 'termin-kalender'),
				'start' => date('Y-m-d H:i', strtotime('today +8 hour')),
				'end' => date('Y-m-d H:i', strtotime('today +30 hour')),
				'kategorie_id' => 7 ,
				'Beschreibung' => __('You can now enter your schedules', 'termin-kalender')
			];
			$wpdb->insert(TER_KAL_TERMIN_DB, $data);
		}
		if (!$count > 3) {
			// termin db is empty
			ter_kal_beispieltermine_eintragen();
		}
	}
	//update feld_art to 'text' where it was set to 128 from version 0.98.3 ----
	$option = get_option('ter_kal_termin_zusatzfelder');
	if (is_array($option)) {
	    foreach ($option as &$entry) {
	        if (isset($entry['feld_art']) && $entry['feld_art'] == 128) {
	            $entry['feld_art'] = 'text';
	        }
	    }
	    update_option('ter_kal_termin_zusatzfelder', $option);
	}
    // zeige bilder bei update auf zeigen einstellen
    $ter_kal_termin_zusatzfelder_option = get_option('ter_kal_termin_zusatzfelder');
    $found = false;
    foreach ($ter_kal_termin_zusatzfelder_option as &$feld) {
        if ($feld['feld_key'] === 'list_image') {
            $feld['feld_zeigen'] = '1';
            $found = true;
            break;
        }
    }
    if (!$found) {
        $ter_kal_termin_zusatzfelder_option[] = [
            'feld_key' => 'list_image',
            'feld_name' => 'list_image',
            'feld_art' => 'text',
            'feld_zeigen' => '1',
        ];
    }
    
    if (!array_search('zusatz_url', array_column($ter_kal_termin_zusatzfelder_option, 'feld_key'))) {
        $ter_kal_termin_zusatzfelder_option[] = [
            'feld_key' => 'zusatz_url',
            'feld_name' => 'zusatz_url',
            'feld_art' => 'url',
            'feld_zeigen' => '1',
        ];
    }

    update_option('ter_kal_termin_zusatzfelder', $ter_kal_termin_zusatzfelder_option);


    $ter_kal_termin_basisfelder = get_option('ter_kal_termin_basisfelder');
    // Define the new fields to be added
    $new_fields = [
        ['feld_name' => 'startUTC', 'feld_art' => 'text'],
        ['feld_name' => 'endUTC', 'feld_art' => 'text'],
        ['feld_name' => 'freq', 'feld_art' => 'text'],
        ['feld_name' => 'byweekday', 'feld_art' => 'text']
    ];
    // Iterate over the new fields
    foreach ($new_fields as $new_field) {
        $exists = false;
        // Check if the field already exists
        foreach ($ter_kal_termin_basisfelder as &$existing_field) {
            if ($existing_field['feld_name'] === $new_field['feld_name']) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $ter_kal_termin_basisfelder[] = $new_field;
        }
    }
    update_option('ter_kal_termin_basisfelder', $ter_kal_termin_basisfelder);

    // Get all existing column names from the table
    $existing_columns = $wpdb->get_col("SHOW COLUMNS FROM " . TER_KAL_TERMIN_DB);
    // Define the columns to be added if they don't exist        //'interval' => 'VARCHAR(128)',
    $columns_to_add = [
        'startUTC' => 'VARCHAR(32)',
        'endUTC' => 'VARCHAR(32)',
        'freq' => 'VARCHAR(32)',
        'byweekday' => 'VARCHAR(32)'
    ];
    // Add missing columns
    foreach ($columns_to_add as $column => $type) {
        if (!in_array($column, $existing_columns)) {
            $wpdb->query("ALTER TABLE " . TER_KAL_TERMIN_DB . " ADD COLUMN $column $type");
        }
    }

// Check if the column 'rrule' exists in the table
$column_exists = $wpdb->get_results("SHOW COLUMNS FROM ". TER_KAL_TERMIN_DB ." LIKE 'rrule'");
if (!empty($column_exists)) {
    $rrule_events = $wpdb->get_results( "SELECT id, start, end, rrule, freq, byweekday, startUTC, endUTC FROM ". TER_KAL_TERMIN_DB . " WHERE rrule IS NOT NULL AND rrule != ''" );
    foreach ($rrule_events as $event) {
        $event->startUTC = str_replace(['-', ':', ' '], ['', '', 'T'], $event->start) . 'Z';
        $event->endUTC   = str_replace(['-', ':', ' '], ['', '', 'T'], $event->end) . 'Z';
    preg_match('/FREQ=(WEEKLY|MONTHLY|YEARLY)/', $event->rrule, $matches) && $event->freq = $matches[1];
        $wpdb->update(
            TER_KAL_TERMIN_DB,
            array(
                'startUTC' => $event->startUTC,
                'endUTC' => $event->endUTC,
                'freq' => $event->freq
            ),
            array('id' => $event->id),
            array(
                '%s', // startUTC
                '%s', // endUTC
                '%s'  // freq
            ),
            array('%d') // id
        );
    }
    // Remove rrule and rruleUTC fields from the database
    $wpdb->query("ALTER TABLE ". TER_KAL_TERMIN_DB ." DROP COLUMN rrule");
    $wpdb->query("ALTER TABLE ". TER_KAL_TERMIN_DB ." DROP COLUMN rruleUTC");
}

} // end ter_kal_activate_update
