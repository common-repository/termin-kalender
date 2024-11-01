<?php
    defined('WPINC') || die;
    //  liest die eventdaten aus der db zur anzeige im kalender
function ter_kal_event_daten_return() {
		global $wpdb;
		/* TER_KAL_TERMIN_DB    Constants: no need for wpdb->prepare() since the table names are not user-supplied and do not require sanitization. */
		$events = $wpdb->get_results( "SELECT id, title, Beschreibung, start, end, startUTC, endUTC, freq, byweekday, kategorie_id FROM ". TER_KAL_TERMIN_DB );
		if ($events) {
			// Filter and modify the results to remove generate 'rrule'
    		$events = array_map(function ($result) {
                if (!empty($result->freq) && !empty($result->startUTC) && !empty($result->endUTC)) {
                $result->rrule = 'FREQ='.$result->freq.';DTSTART='.$result->startUTC.';UNTIL='.$result->endUTC ;
                    if (!empty($result->byweekday)) {
                        $weekdays = ';BYDAY='.$result->byweekday ;
                        $result->rrule .=  $weekdays ;
                    } else {
                        // no $weekdays
                    }
                };
    		    if (isset($result->Beschreibung)) {
    		        $result->Beschreibung = substr($result->Beschreibung, 0, 100); // Limit to 100 characters
    		        //$result->Beschreibung = json_encode($result->Beschreibung, JSON_UNESCAPED_UNICODE); // Make JSON compatible and preserve special characters
                    $result->Beschreibung = json_encode(preg_replace('/\s+/', ' ', strip_tags($result->Beschreibung)), JSON_UNESCAPED_UNICODE); // Remove HTML, line breaks, and special characters, then make JSON compatible
    		    }
    		    return $result;
    		}, $events);
		}
		$terminkategorie = get_option('ter_kal_kategorien');
		foreach ($events as &$event) {
		    foreach ($terminkategorie as $kategorie) {
		        if ($kategorie['kategorie_id'] == $event->kategorie_id) {
		        	$event->backgroundColor = $kategorie['backgroundColor'];
		            $event->textColor = $kategorie['textColor'];
		            $event->kategorie = $kategorie['kategorie'];
		            $event->icon = $kategorie['icon'];
		            break;
		        }
		    }
		}
		unset($event); // Break the reference with the last element
		echo wp_json_encode($events);
    //    return;
};  // end ter_kal_event_daten_return


    //termin_bearbeiten formular anzeigen:
    function ter_kal_kalender_termin_zeigen_form() {
           if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce')) {
               return;
           }
           $id = (isset($_POST['id']) ? $_POST['id'] : '');
           $tk_form = (isset($_POST['tk_form']) ? $_POST['tk_form'] : '');
           global $wpdb;
           $row = $wpdb->get_row(
               $wpdb->prepare(
                   "SELECT * FROM " . TER_KAL_TERMIN_DB . " WHERE id = %d",
                   $id
               )
           );
			$terminkategorie = get_option('ter_kal_kategorien');
		    foreach ($terminkategorie as $kategorie) {
		        if ($kategorie['kategorie_id'] == $row->kategorie_id) {
		        	$row->backgroundColor = $kategorie['backgroundColor'];
		            $row->textColor = $kategorie['textColor'];
		            $row->kategorie = $kategorie['kategorie'];
		            $row->icon = $kategorie['icon'];
		            break;
		        }
		    }
            if ($tk_form == 'ausgabe') {
                ter_kal_terminkalender_formular_ausgabe($row, $terminkategorie);
            } else {
                 ter_kal_terminkalender_formular($row, $terminkategorie);
            }
            exit; // TODOS: null ausgabe am ende verhindern ayax rückgabewert?
    };
    add_action('wp_ajax_ter_kal_kalender_termin_zeigen_form', 'ter_kal_kalender_termin_zeigen_form'); // for logged in users
    add_action('wp_ajax_nopriv_ter_kal_kalender_termin_zeigen_form', 'ter_kal_kalender_termin_zeigen_form'); // for guests




    function ter_kal_kalender_termin_speichern() {
        global $wpdb;
        /* Verify the nonce     */
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce')) {
            $id            = (isset($_POST['id']) ? $_POST['id'] : '');
            $termin_data   = (isset($_POST['termin_data']) ? $_POST['termin_data'] : '');
            $insert_update = (isset($_POST['insert_update']) ? $_POST['insert_update'] : '');
            /* anstatt felder blockieren und lauslöschen:
            0.99.38 nur felder welche in db existieren zulassen: $fields = $wpdb->get_col("DESCRIBE " . TER_KAL_TERMIN_DB, 0);
            */
            $data = [];
            $fields = $wpdb->get_col("DESCRIBE " . TER_KAL_TERMIN_DB, 0);
            foreach ($termin_data as $key => $value) {
                if (in_array($key, $fields)) {
                    //$data[$key] = sanitize_text_field(stripslashes($value));
                    //$data[$key] = sanitize_text_field($value);
                    $data[$key] = wp_kses_post($value);
                }
            }
            if ($insert_update == 'update') {
                $wpdb->update(TER_KAL_TERMIN_DB, $data,
                    ['id' => $id]
                );
            } else {
                $wpdb->insert(TER_KAL_TERMIN_DB, $data);
            }
            return;
        } // ende verify nonce
    };
    add_action('wp_ajax_ter_kal_kalender_termin_speichern', 'ter_kal_kalender_termin_speichern'); // for logged in users
    //add_action('wp_ajax_nopriv_ter_kal_kalender_termin_speichern', 'ter_kal_kalender_termin_speichern');
    // for guests



    // ter_kal_kalender_datum_aendern
    function ter_kal_kalender_datum_aendern() {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce')) {
            $data = [];
            if (isset($_POST['start'])) {
                $data['start'] = sanitize_text_field($_POST['start']);
                $data['end']   = sanitize_text_field($_POST['end']);
                $data['start'] = substr($data['start'], 0, 17);
                $data['start'] .= '00';
                $data['end'] = substr($data['end'], 0, 17);
                $data['end'] .= '00';
                global $wpdb;
                $wpdb->update(TER_KAL_TERMIN_DB, $data,
                    ['id' => $_POST['id']]
                );
            }
        }
        return;
    };
    add_action('wp_ajax_ter_kal_kalender_datum_aendern', 'ter_kal_kalender_datum_aendern'); // for logged in users
    add_action('wp_ajax_nopriv_ter_kal_kalender_datum_aendern', 'ter_kal_kalender_datum_aendern');  // for guests



//Termin loeschen nur admin, kein nopriv
function ter_kal_kalender_termin_loeschen() {
    $response = ['success' => false, 'message' => 'Invalid request'];

    if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce')) {
        if (ter_kal_benutzerrechte_pruefen() == 'loeschen') {
            $id = absint(isset($_POST['id']) ? $_POST['id'] : '');
            if ($id) {
                global $wpdb;
                $deleted = $wpdb->delete(TER_KAL_TERMIN_DB, ['id' => $id]);

                if ($deleted) {
                    $response = ['success' => true, 'message' => 'Termin erfolgreich gelöscht'];
                } else {
                    $response['message'] = 'Fehler beim Löschen des Termins';
                }
            } else {
                $response['message'] = 'Ungültige ID';
            }
        } else {
            $response['message'] = 'Unzureichende Berechtigungen';
        }
    } else {
        $response['message'] = 'Ungültige Nonce';
    }

    wp_send_json($response);
}
add_action('wp_ajax_ter_kal_kalender_termin_loeschen', 'ter_kal_kalender_termin_loeschen');



    //Teilnehmerliste ausgeben
    function ter_kal_event_teilnehmerliste($row) {
        $row_teilnehmer = $row->Teilnehmer;
        if (!empty($row_teilnehmer)) {
            $row_teilnehmer_array = array_filter(explode(',', $row_teilnehmer));
            if (empty($row_teilnehmer_array)) {
                return;
            }
        }
        $teilnehmer_array  = [];
        foreach ($row_teilnehmer_array as $user_id) {
            $user = get_userdata(intval($user_id));
            if ($user) {
                $teilnehmer_array[] = [
                    'user_id' => $user->ID,
                    'user_name' => $user->display_name,
                    'user_email' => $user->user_email,
                ];
            }
        }
        if (!empty($teilnehmer_array)){
                $teilnehmerliste = '<ul>';
                foreach ($teilnehmer_array as $user) {
                    $teilnehmerliste .= '<li><div data-user="' . $user['user_id'] . '">' . $user['user_name'] . ' : ' . $user['user_email'] . '</div></li>';
                }
                $teilnehmerliste .= '</ul>';
                return $teilnehmerliste;
           } else {
                $teilnehmerliste = null;
                return $teilnehmerliste;
           }
    };


// TODOS: check ter kal pro

function ter_kal_check_template_must_resize_list() {
    // Get the active theme
    $active_theme = wp_get_theme();

    // List of most commonly used WordPress themes
    $common_themes = array(
        'Astra',
        'BeOnePage',
        'Blocksy',
        'Kadence',
        'Kubio',
        'Neve'
    );

	// Noch nicht OK 'Hello Elementor', Enfold: rechts abgeschnitten  'Hestia': Button zu groos darst?
	// OK: Wordpress Standard Divi GeneratePress OceanWP

    // Check if the active theme is in the list
    if (in_array($active_theme->get('Name'), $common_themes)) {
        return true;
    } else {
        return false;
    }
}


function ter_kal_get_active_template() {
    // Get the active theme
    $active_theme = wp_get_theme();
    return $active_theme;
}

function ter_kal_get_if_template_in_list() {
    // Get the active theme
    $active_theme = wp_get_theme();

    // List of most commonly used WordPress themes
    $common_themes = array(
        'Astra',
        'OceanWP',
        'Twenty Twenty-One',
        'Twenty Twenty',
        'Twenty Nineteen',
        'GeneratePress',
        'Neve',
        'Hello Elementor',
        'Hestia',
        'Sydney',
        'Storefront',
        'Zakra',
        'ColorMag',
        'Shapely',
        'Spacious',
        'Customify',
        'Mesmerize',
        'Vantage',
        'OnePress',
        'Phlox'
    );

    // Check if the active theme is in the list
    if (in_array($active_theme->get('Name'), $common_themes)) {
        return true;
    } else {
        return false;
    }
}
