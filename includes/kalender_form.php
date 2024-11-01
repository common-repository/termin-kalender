<?php

defined( 'WPINC' ) || die;
function ter_kal_terminkalender_formular(  $row, $terminkategorie  ) {
    // Termin NEU
    $row_id = '';
    $row_title = esc_html__( 'Schedule', 'termin-kalender' ) . ': ';
    $row_textcolor = '';
    $row_backgroundcolor = '';
    $row_icon = '';
    $row_start = '';
    $row_end = '';
    $row_freq = '';
    $row_byweekday = '';
    $row_startUTC = '';
    $row_endUTC = '';
    $kategorie_id = '1';
    $kategorie_selected = esc_html__( 'Please choose', 'termin-kalender' );
    $row_ort = '';
    $google_maps_url = '';
    $row_teilnehmer = '';
    $teilnehmer_array = '';
    $row_notizen = '';
    $row_beschreibung = esc_html__( 'Description', 'termin-kalender' ) . ':';
    $notiz_journal = wp_get_current_user()->user_login . ': ' . current_time( 'mysql' );
    if ( isset( $row->id ) ) {
        $row_id = $row->id;
        $row_title = $row->title;
        $kategorie_id = $row->kategorie_id;
        $kategorie_selected = $row->kategorie;
        $row_textcolor = $row->textColor;
        $row_backgroundcolor = $row->backgroundColor;
        $row_icon = $row->icon;
        $row_start = $row->start;
        $row_end = $row->end;
        $row_start = substr( $row_start, 0, 17 );
        $row_start .= '00';
        $row_end = substr( $row_end, 0, 17 );
        $row_end .= '00';
        $row_startUTC = $row->startUTC;
        $row_endUTC = $row->endUTC;
        $row_freq = $row->freq;
        $row_byweekday = $row->byweekday;
        $row_ort = $row->Ort;
        $encoded_location = urlencode( $row_ort );
        $google_maps_url = "https://www.google.com/maps/search/?api=1&query={$encoded_location}";
        $row_notizen = $row->Notizen;
        $row_beschreibung = $row->Beschreibung;
        $row_teilnehmer = $row->Teilnehmer;
        $teilnehmer_array = ter_kal_event_teilnehmerliste( $row );
        $notiz_journal = wp_get_current_user()->user_login . ': ' . current_time( 'mysql' ) . ' | ' . $row->notiz_journal;
    }
    ?>
	<span id="ter_kal_icon" style="color:<?php 
    echo $row_textcolor;
    ?>; background:<?php 
    echo $row_backgroundcolor;
    ?>; display: none;" class="kalender_dashicons dashicons dashicons-<?php 
    echo $row_icon;
    ?>"></span>
	<form id="terminkalenderform" name="terminkalenderform" action="event_speichern" method="post" >
		<input id="id" name="id" type="hidden" value="<?php 
    echo esc_attr( $row_id );
    ?>">
		<input id="startUTC" type="hidden"  name="startUTC"  value="<?php 
    echo esc_attr( $row_startUTC );
    ?>">
		<input id="endUTC" type="hidden"  name="endUTC"   value="<?php 
    echo esc_attr( $row_endUTC );
    ?>">
		<!-- Kalender Standardfelder -->
		<div id="title_formgroup" class="form-group">
		    <label for="title" class="col-form-label" style="display: inline-block;"><?php 
    esc_html_e( 'Title', 'termin-kalender' );
    ?>:</label>
			<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
			<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'The titel of your calendar entry. You will see this, with the choosen category color, in all the calendar views ', 'termin-kalender' );
    ?></div>
			<input type="text" class="form-control bg-white"  id="title" name="title" value="<?php 
    echo esc_attr( $row_title );
    ?>" maxlength="100">
		</div>
		<div class="row">
			<div class="col-4">
				<label for="start"   class="col-form-label" ><?php 
    esc_html_e( 'Date FROM', 'termin-kalender' );
    ?>:</label>
				<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>    			<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'Start Date and Time for the calendar entry.', 'termin-kalender' );
    ?></div>
				<input type="datetime-local"  class="form-control bg-white" id="start" name="start" value="<?php 
    echo esc_attr( $row_start );
    ?>" step="60" >
				<div id="start_alert" class="alert alert-primary"  style="display: none">Please check start and end dates</div>
			</div>
			<div class="col-4">
				<label for="end" class="col-form-label"><?php 
    esc_html_e( 'Date TO', 'termin-kalender' );
    ?>:</label>
				<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>			    <div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'End Date and Time of your calendar entry. Single day events will be shown from choosen Start to End time. Multi-day entries will be shown as All Day entries. Repeating entries will be shown between Start and End Date for as many occurences as possible and at the choosen start and end time.', 'termin-kalender' );
    ?></div>
				<input type="datetime-local" class="form-control bg-white" id="end" name="end" value="<?php 
    echo esc_attr( $row_end );
    ?>" step="60">
				<div id="end_alert" class="alert alert-primary" style="display: none">Please check start and end dates</div>
			</div>
<script type="text/javascript">
jQuery(function ($) {

	$('#end').change(function () {
		$startDate = $('#start').val();
		$endDate = $('#end').val();
		if ($endDate <= $startDate) {
			//alert( ter_kal_modal_vars.end_after_start );
			$('#end_alert').html(ter_kal_modal_vars.end_after_start);
			$('#end_alert').show(700).delay(3000).fadeOut();
			//	$('#end').val($startDate);
			var endDate = new Date($('#start').val());
			endDate.setHours(endDate.getHours() + 10);
			var formattedEndDate = endDate.toISOString().slice(0, 19).replace('T', ' ');
			$('#end').val(formattedEndDate);
            //$('#endUTC').val(formattedEndDate);
		}
	});

	$('#start').change(function () {
		$startDate = $('#start').val();
		$endDate = $('#end').val();
		if ($endDate <= $startDate) {
			//alert( ter_kal_modal_vars.start_before_end );
			$('#start_alert').html(ter_kal_modal_vars.start_before_end);
			$('#start_alert').show(700).delay(3000).fadeOut();
			//	$('#end').val($startDate);
			var endDate = new Date($('#start').val());
			endDate.setHours(endDate.getHours() + 10);
			var formattedEndDate = endDate.toISOString().slice(0, 19).replace('T', ' ');
            $('#end').val(formattedEndDate);
		}
	});

    $('#rrule_select').change(function () {
        var freq_val = $("#rrule_select").find(":selected").val();
        if (freq_val == 'WEEKLY') {
            $('#day_check').show(1000);
        } else {
           $('#day_check').hide(1000);
        }
	});

    $('#bold-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<b>' + selectedText + '</b>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#italic-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<i>' + selectedText + '</i>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#hr-button').click(function() {
        var textarea = $('#Beschreibung');
        textarea.val(textarea.val() + '<hr>');
    });

    $('#underline-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<u>' + selectedText + '</u>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#strikethrough-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<del>' + selectedText + '</del>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#heading-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<h4>' + selectedText + '</h4>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#list-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var lines = selectedText.split('\n');
        var newText = '<ul>';
        for (var i = 0; i < lines.length; i++) {
            newText += '<li>' + lines[i] + '</li>';
        }
        newText += '</ul>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#quote-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<blockquote>' + selectedText + '</blockquote>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#code-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var newText = '<code>' + selectedText + '</code>';
        textarea.val(textarea.val().replace(selectedText, newText));
    });

    $('#link-button').click(function() {
        var textarea = $('#Beschreibung');
        var selectedText = textarea.val().substring(textarea.prop('selectionStart'), textarea.prop('selectionEnd'));
        var url = prompt('Enter the URL:', 'https://');
        if (url) {
            var newText = '<a href="' + url + '" target="_blank">' + selectedText + '</a>';
            textarea.val(textarea.val().replace(selectedText, newText));
        }
    });

    $('#image-button').click(function() {
        var textarea = $('#Beschreibung');
        var imageUrl = prompt('Enter the image URL:', 'https://');
        if (imageUrl) {
            var newText = '<img src="' + imageUrl + '">';
            textarea.val(textarea.val() + newText);
        }
    });

});
</script>
			<?php 
    ?>
				<div class="col-4 infotext">
					<div class="form-group"><br><br>
						<?php 
    esc_html_e( 'Recurring events? Get PRO', 'termin-kalender' );
    ?>
					</div>
				</div>
				<?php 
    ?>
		</div>
		<div class="row">
			<div class="col-6">
				<div class="form-group">
				<label for="kategorie_id" class="col-form-label"><?php 
    esc_html_e( 'Category', 'termin-kalender' );
    ?>:</label>
				<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
				<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'Choose the category. Your calendar entry will be shown in this color, for an easy overview in the monthly view. A category can be a Teammember, a Car to rent or a room occupancy and many more.', 'termin-kalender' );
    ?></div>
 				<select class="form-select bg-white" id="kategorie_id" name="kategorie_id"  data-kategorie="<?php 
    echo esc_html( $kategorie_selected );
    ?>">
                	<option name="kategorie_id" value="<?php 
    echo esc_attr( $kategorie_id );
    ?> " selected><?php 
    echo esc_html( $kategorie_selected );
    ?> </option>
					<?php 
    foreach ( $terminkategorie as $kategorie ) {
        ?>
						<option name="kategorie_id" value="<?php 
        echo $kategorie['kategorie_id'];
        ?>">
							<?php 
        echo $kategorie['kategorie'];
        ?>
						</option>
					<?php 
    }
    ?>
                </select>
				</div>
			</div>
			<div class="col-6">
				<div class="form-group">
					<label for="Ort" class="col-form-label"><?php 
    esc_html_e( 'Location', 'termin-kalender' );
    ?>: </label>
					<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
					<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'Is there a location for your event, meeting or appointment? Give any address that can be located in Google Maps and a Google-Maps link to this place will be created for you.', 'termin-kalender' );
    ?></div>
					<input type="text" class="form-control bg-white" id="Ort" name="Ort" value="<?php 
    echo esc_html( $row_ort );
    ?>" maxlength="100">
					<div id="ter_kal_google_ort" class="tk_form_text">
						<a href="<?php 
    echo esc_url( $google_maps_url );
    ?>" target="_blank">
							<?php 
    echo esc_html( $row_ort );
    ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<div class="form-group">
					<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
					<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'PRO: Choose attendees for this entry. Attendee must have a User account on your Wordpress Site, any sort of user account will be shown, also Guest( Not only users you added in admin area to be able to edit calendar entries). Once you choosen all the attendees you can send a invitation for this event to their registered email' );
    ?></div>
					<input type="text" class="form-control bg-white" id="Teilnehmer" name="Teilnehmer" value="<?php 
    echo esc_html( $row_teilnehmer );
    ?>" maxlength="100" style="display: none;" readonly>
				</div>
				<?php 
    ?> <br><br>
					<div class="col-4 infotext">
						<?php 
    esc_html_e( 'Want to invite by Email? Get PRO', 'termin-kalender' );
    ?> </div>
					<?php 
    ?>
			</div>
			<div class="col-6">
				<div class="form-group">
					<label for="Notizen" class="col-form-label"><?php 
    esc_html_e( 'Notes', 'termin-kalender' );
    ?>:</label>
					<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
					<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'Any important notes or something to remember for this event?', 'termin-kalender' );
    ?></div>
					<textarea class="form-control ter_kal_notes" id="Notizen" name="Notizen" rows="3" maxlength="1024"><?php 
    echo esc_textarea( $row_notizen );
    ?></textarea>
				</div>
			</div>
		</div>
		<div id="beschreibung_container" class="form-group tk_form_text">
			<label for="Beschreibung" class="col-form-label"><?php 
    esc_html_e( 'Description', 'termin-kalender' );
    ?>:</label>
			<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
			<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'This is the main description for this entry.', 'termin-kalender' );
    ?><br>
                                                          <?php 
    esc_html_e( 'If you are familar with Basic HTML, you can use the buttons below. To draw text bold, italic, underline or strikethrough. Add a line or draw a part of text as Quote or formatted Code. Add a list or even include links and link images to be displayed. Remember, the PRO Version of the calendar allows to also upload pictures and is easyer to use', 'termin-kalender' );
    ?>


            </div>
			<textarea class="form-control"  id="Beschreibung" name="Beschreibung" maxlength="1024"><?php 
    echo wp_kses_post( $row_beschreibung );
    ?></textarea>
            <div class="formatting-buttons">
                <button type="button" class="btn btn-outline-primary btn-sm" id="heading-button">Title</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="bold-button">Bold</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="italic-button">Italic</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-outline-info btn-sm" id="underline-button">Underline</button>
                <button type="button" class="btn btn-outline-info btn-sm" id="strikethrough-button">Strike</button>
                <button type="button" class="btn btn-outline-success btn-sm" id="hr-button">Line Divider</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-outline-primary btn-sm" id="quote-button">Quote</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="code-button">Code</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-outline-warning btn-sm" id="list-button">List</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-outline-dark btn-sm" id="link-button">Link</button>
                <button type="button" class="btn btn-outline-dark btn-sm" id="image-button">Image</button>
            </div>
		</div>
		<span name="ter_kal_form_help" class="ter_kal_form_help dashicons dashicons-editor-help" ></span>
		<div style="display: none;" class="infotext" ><?php 
    esc_html_e( 'PRO: Need a field like phone number? Create them in the admin area. You can extend your form with either a one line or a multiline field for long text. Use them carefully, mostly you can add all you need in the description field.', 'termin-kalender' );
    ?></div>
        <?php 
    // end is__premium_only
    ?>
		<input id="notiz_journal" name="notiz_journal" type="hidden" value="<?php 
    echo esc_textarea( $notiz_journal );
    ?>">
	</form>
	<div class="infotext">
		<?php 
    echo esc_textarea( $notiz_journal );
    ?>
	</div>
	<?php 
    // return;
}
