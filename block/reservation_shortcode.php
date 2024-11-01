<?php
defined('WPINC') || die;

// API call for user email and category
add_action('rest_api_init', function () {
    register_rest_route('my-termin-reservation/v1', '/email/', array(
        'methods' => 'GET',
        'callback' => 'tk_reservation_get_user_email',
        'permission_callback' => '__return_true',
    ));
    // Adding callback for category
    register_rest_route('my-termin-reservation/v1', '/category/', array(
        'methods' => 'GET',
        'callback' => 'tk_reservation_get_category',
        'permission_callback' => '__return_true',
    ));
});

function tk_reservation_get_user_email() {
    $user_email = wp_get_current_user()->user_email;
    if(empty($user_email)) {
        // Use admin email if no user email found
        $user_email = get_option('admin_email');
    }
    return new WP_REST_Response(array('email' => $user_email), 200);
}

// Function for category list
function tk_reservation_get_category() {
	$kategorien = get_option('ter_kal_kategorien');
	$filtered_kategorien = array();
	foreach ($kategorien as $kategorie) {
	    // Extract only 'kategorie_id' and 'kategorie' from each category
	    $filtered_kategorien[] = array(
	        'kategorie_id' => $kategorie['kategorie_id'],
	        'kategorie' => $kategorie['kategorie']
	    );
	}
	$kategorien = $filtered_kategorien;
    return new WP_REST_Response($kategorien, 200);
}



function ter_kal_reserv_request_shortcode($atts) {

    ob_start(); // Start output buffering
	//
	$attributes = shortcode_atts(array(
	    'category' => '', // Default category is an empty string
	    'email' => '', // Default email is an empty string
	), $atts);

	// Access the category and email
	$event_list_category = $attributes['category'];
	$reservation_email = $attributes['email'];

	$kategorien = get_option('ter_kal_kategorien');
	$event_list_category = (int) $event_list_category; // Ensure it's an integer

	$row = array_filter($kategorien, fn($k) => $k['kategorie_id'] == $event_list_category);
	$row = $row ? (object) array_shift($row) : null;

	$backgroundColor = $row->backgroundColor ?? '';
	$textColor = $row->textColor ?? '';
	$kategorie = $row->kategorie ?? '';
	$icon = $row->icon ?? '';
    ?>

<div class="container t_k_reservation-container ter_kal_font" style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $textColor; ?>;">
  <div class="row">
    <div class="col">
    	<h3 class="mb-3 tk_kategorie-title ter_kal_font" style="color: <?php echo $textColor; ?>;">
			<span class="tk_list_titel_dashicons dashicons dashicons-<?php echo $icon ?>"></span>&nbsp;&nbsp;<?php echo $kategorie ?>
		</h3>
    </div>
  </div>
	<form method="post" id="reservation-form" >
		<?php wp_nonce_field( 'submit_reservation_nonce_action', 'submit_reservation_nonce_field' ); ?>
        <div  class="regenschirm"><input type="text" id="regenschirm" name="regenschirm" value=""></div>
        <input type="hidden" id="action_id" name="action" value="dont_submit_reservation">
		<div class="mb-3 d-flex">
			<div class="me-3 flex-fill">
				<label for="name" class="form-label"><?php esc_html_e('Name', 'termin-kalender'); ?> :</label>
				<input type="text" id="name" name="name" required class="form-control bg-white">
			</div>
			<div class="flex-fill">
				<label for="email" class="form-label"><?php esc_html_e('Email', 'termin-kalender'); ?> :</label>
				<input type="email" id="email" name="email" required class="form-control bg-white">
			</div>
		</div>
		<div class="mb-3">
			<label for="beschreibung" class="form-label"><?php esc_html_e('Your Request Message', 'termin-kalender'); ?> :</label>
			<textarea id="beschreibung" name="beschreibung" maxlength="1024" class="form-control bg-white"></textarea>
		</div>
		<hr>

		<div class="mb-3 d-flex align-items-center">
			<label for="start" class="form-label me-2"><?php esc_html_e('When would you like to reservate, from', 'termin-kalender'); ?> :</label>
			<input type="datetime-local" id="start" name="start" required class="form-control me-2 bg-white datepicker" style="width: auto;">
			<label for="end" class="form-label me-2">to:</label>
			<input type="datetime-local" id="end" name="end" class="form-control bg-white datepicker" style="width: auto;">
		</div>

        <input type="hidden" name="kategorie" value="<?php echo $kategorie ?>">
        <input type="hidden" name="backgroundColor" value="<?php echo $backgroundColor ?>">
        <input type="hidden" name="textColor" value="<?php echo $textColor ?>">
		<input type="hidden" name="kategorie_id" value="<?php echo $event_list_category ?>">
		<input type="hidden" name="reservation_email" value="<?php echo $reservation_email ?>">
		<input type="hidden" name="title" value="Reservation Request!">
		<input type="hidden" name="notizen" value="Reservation Request!">
		<input type="hidden" name="notiz_journal" value="Reservation request">
		<button type="submit" class="btn btn-primary"><?php esc_html_e('Submit', 'termin-kalender'); ?></button>
	</form>
	 <div class="ter_kal_info ter_kal_font"><?php ter_kal_lizenz_info();?></div>
</div>
<script>
jQuery(document).ready(function($) {
    let mouseMoves = 0;
    let keyPresses = 0;
    // Track mouse movement
    $(document).on('mousemove', function() {
        mouseMoves++;
    });
    // Track key presses
    $(document).on('keypress', function() {
        keyPresses++;
    });

    $('#name').focus(function() {
        setTimeout(function() {
            $('#action_id').val('tk_submit_reservation');
        }, 8888);
    });

    $('#reservation-form').submit(function(e) {
    // On form submit, check for human-like interaction
        if (mouseMoves < 10 || keyPresses < 5) {
            e.preventDefault(); // Prevent submission if behavior is suspicious
            alert('Bot behavior detected!');
            return false;
        }
        // Check if all fields are filled
        var name = $('#name').val().trim();
        var email = $('#email').val().trim();
		$startDate = $('#start').val();
		$endDate = $('#end').val();
		if ($endDate <= $startDate) {
			//	$('#end').val($startDate);
			var endDate = new Date($('#start').val());
			endDate.setHours(endDate.getHours() + 10);
			var formattedEndDate = endDate.toISOString().slice(0, 19).replace('T', ' ');
			$('#end').val(formattedEndDate);
		}
        var start = $('#start').val().trim();
        var end = $('#end').val().trim();
        var beschreibung = $('#beschreibung').val().trim();
        $startDate = $('#start').val();
        $action_id = $('#action_id').val();
		e.preventDefault(); // Prevent form submission
        if (!name || !email || !start || !end || !beschreibung ) {
            alert('Please fill all the fields correctly. Make sure dates are chosen and email is valid.');
            return false;
        }

        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: formData + '&action=' + $action_id ,
            success: function(response) {
                $('.t_k_reservation-container').html(response.data);
            },
            error: function() {
                alert('There was an error processing your reservation request. Only Termin-Kalender PRO users are allowed to send email.');
            }
        });

    });
});

</script>
	<?php
    return ob_get_clean(); // Return the buffered output
} // end ter_kal_reserv_request_shortcode

add_shortcode('my-termin-reservation', 'ter_kal_reserv_request_shortcode');

// Handle form submission
function ter_kal_reservation_form_submission() {
    // Check for nonce security
    if (!isset($_POST['submit_reservation_nonce_field']) || !wp_verify_nonce($_POST['submit_reservation_nonce_field'], 'submit_reservation_nonce_action')) {
        wp_send_json_error('Security check failed');
    }
	if (isset($_POST['name']) && isset($_POST['email'])) {

    // Check for honeyPot field
    if (!empty($_POST['regenschirm'])) {
        wp_die('Your submission is likely spam.');
    }

        $name =  sanitize_text_field($_POST['name']);
        $email = sanitize_text_field($_POST['email']);
        $start = $_POST['start'];
        $end = $_POST['end'];
        $start_date = substr($start ?? '', 0, 10);
        $start_time = substr($start ?? '', 11);
        $end_date = substr($end ?? '', 0, 10);
        $end_time = substr($end ?? '', 11);
        $reservation_email = sanitize_text_field($_POST['reservation_email'] ?? '');
        $kategorie = sanitize_text_field($_POST['kategorie'] ?? '');
        $title = "New Request for: ".$kategorie ;
        $kategorie_id = sanitize_text_field($_POST['kategorie_id'] ?? '');
        $backgroundColor = sanitize_text_field($_POST['backgroundColor'] ?? '');
        $textColor = sanitize_text_field($_POST['textColor'] ?? '');
	    $notizen = "<b>Reservation Request from <br>" . htmlspecialchars($name) . " - " . htmlspecialchars($email) . " <hr>from ".htmlspecialchars($start_date) . " at ".htmlspecialchars($start_time). " to ".htmlspecialchars($end_date)." at ".htmlspecialchars($end_time)." : </b><hr><br>  ";
	    if (isset($_POST['beschreibung'])) {
	        $beschreibung = $_POST['beschreibung'];
	    } else { // sonst alle anderen Daten anzeigen
	        $beschreibung = $notizen;
	    }
        //--- DB ----------------
        $form_data = array(
            'start' => $start,
            'end' => $end,
            'kategorie_id' => $kategorie_id,
            'Notizen' => $notizen,
            'Beschreibung' => $beschreibung,
            'title' => $title
        );
        global $wpdb;
        $wpdb->insert(TER_KAL_TERMIN_DB, $form_data);
         //--- DB ----------------*/
            // Send confirmation email
            require_once TER_KAL_PLUGIN_DIR . 'includes_pro/teilnehmer_htmlmail__premium_only.php';
             //data for email
    		 $schedule_data = [
				'title' => $title,
				'bg_color' => $backgroundColor,
				'color' => $textColor,
				'blogname' => get_option('blogname'),
                'content' => $beschreibung,
				'start_datetime' => $start_date . ', ' . $start_time,
				'end_datetime' => $end_date . ', ' . $end_time,
				'category' => $kategorie ,
                'guests' => $name.' : '.$email,
                'calendar_link' => get_option('home') . '/my-easy-termin-kalender-calendar',
				'from' => $email ,
                'headline' => __('New request from ', 'termin-kalender') ,
                'formular' => 'reservation' ,
    		 ];
            $to[]       = $name . ' <' . $email . '>';
            $to[]       = get_option('blogname') . ' <' . $reservation_email . '>';
            ter_kal_send_invitation_email($to, $schedule_data);
            // display a message after saving the data
            wp_send_json_success('<h3 class="mb-3">' . esc_html__('Your request has been sent, thank you', 'termin-kalender') . '</h3>');
    } else {  // no name or no email  ?
        wp_send_json_error('<h3 class="mb-3">' . esc_html__('There was an error with your request, please try again', 'termin-kalender') . '</h3>');
    }
   // return;
}
add_action('wp_ajax_tk_submit_reservation', 'ter_kal_reservation_form_submission');
add_action('wp_ajax_nopriv_tk_submit_reservation', 'ter_kal_reservation_form_submission'); // for not logged in users