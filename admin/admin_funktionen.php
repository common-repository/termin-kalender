<?php
defined('WPINC') || die;

	function ter_kal_list_pages_with_shortcode() {
		$shortcode = 'my-termin-kalender';
		$pages     = get_posts([
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);
		$shortcode_found = 0 ;
		if ($pages) {
			echo '<h2 class="admin_blue">';
			esc_html_e('Here is your calendar:', 'termin-kalender');
			echo '</h2><b>';
			esc_html_e("Simply click the button here to get started with your Termin-Kalender. Open the calendar page using this link. Click on an empty slot on the calendar and fill out the details of your first appointment in the form that appears. You can also use the button below to generate sample calendar entries to help you visualize your first bookings.", 'termin-kalender');
			echo '</b><ul>';
				foreach ($pages as $page) {
					if (has_shortcode($page->post_content, $shortcode)) {
						$shortcode_found = 1 ;
						printf('<li><a href="%s" class="button button-primary tk-bigger-button">%s</a></li><br>', esc_attr(get_permalink($page->ID)), esc_html(get_the_title($page->ID)));
					}
				}
			echo '</ul>';
		}
		if ($shortcode_found == 0) {
			// no calendar page found
			esc_html_e('No page with the calendar found.', 'termin-kalender');
			?><br><?php
			esc_html_e('Click here to automatically create a page with the calendar:', 'termin-kalender');
            ?> <br>
			<button type="button" id="kalender_seite_erstellen_button" name="kalender_seite_erstellen_button"  class="button"><?php esc_html_e('Create calendar page', 'termin-kalender');?></button>
    		<?php
		}
     //   return;
	}
    add_action('wp_ajax_ter_kal_list_pages_with_shortcode', 'ter_kal_list_pages_with_shortcode');

function ter_kal_banner() {
	// banner für admin und andere bereiche anzeigen
		echo '<div class="card" ><img src="' . esc_html(TER_KAL_PLUGIN_URL) . 'assets/banner-772x250.png" width="464" height="150" class="terminkalender_banner" alt="Termin-Kalender"></div>';
    //    return;
}


function ter_kal_termin_ajax_feld_neu() {
	if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce') ) {
    	global $wpdb;
		$feld_name = isset($_POST['feld_name']) ? $_POST['feld_name'] : '';
		$feld_name = sanitize_text_field($feld_name);
           $text_area = $_POST['feld_art']; //true - false
           if ($text_area == 'textarea'){
             $feldgroesse = '1024';
           } else {
             $feldgroesse = '128';
           };
           $feld_key   = sanitize_key($feld_name );
           $wpdb->query($wpdb->prepare(
                  "ALTER TABLE ".TER_KAL_TERMIN_DB."
                  ADD $feld_key varchar(%d)",
                  $feldgroesse
               )
 			);
		$options = get_option('ter_kal_termin_zusatzfelder', []);
		$duplicate = array_filter($options, fn($option) => $option['feld_key'] === $feld_key || $option['feld_name'] === $feld_name);
		if (!$duplicate) {
		    $options[] = [
		        'feld_key' => $feld_key,
		        'feld_name' => $feld_name,
		        'feld_art' => $text_area,
		        'feld_zeigen' => '1',
		    ];
		    update_option('ter_kal_termin_zusatzfelder', $options);
		}
		$_POST = array(); // post array löschen um erneutes senden zu verhindern
		return;  // important to stop execution of further code
}};
add_action('wp_ajax_ter_kal_termin_ajax_feld_neu', 'ter_kal_termin_ajax_feld_neu');



	// feld_im_ter_kal_loeschen
	function ter_kal_feld_aus_db_loeschen() {
		if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ter_kal_nonce') ) {
			if (isset($_POST['field'])) {
				$field = $_POST['field'];
				global $wpdb;
				$wpdb->query("ALTER TABLE " . TER_KAL_TERMIN_DB . " DROP COLUMN " . $field);
				// Remove entry from ..._termin_felder option where feld_key matches $field
				$option = get_option('ter_kal_termin_zusatzfelder');
				foreach ($option as $key => $value) {
				    if ($value['feld_key'] === $field) {
				        unset($option[$key]);
				        break; // Exit the loop once the item is found and removed
				    }
				}
				$option = array_values($option); // Reindex array
				update_option('ter_kal_termin_zusatzfelder', $option);
				if ($wpdb->last_error) {
					wp_send_json(['message' => esc_html('Fehler beim löschen von: ') . $wpdb->last_error]);
				} else {
					wp_send_json(['message' => esc_html('Feld  gelöscht: ') . $field]);
				}
			} else {
			}
		}
		return;  // important to stop execution of further code
	};
	add_action('wp_ajax_ter_kal_feld_aus_db_loeschen', 'ter_kal_feld_aus_db_loeschen');