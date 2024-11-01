    /* Admin JS
    */
jQuery(function($) {
// Admin Seite
    $('#beispieltermine_button').on('click', function() {
     //  ter_kal_beispieltermine_eintragen
        var tk_nonce = $(this).data('tk_nonce'); // Get the nonce from the button's data attribute
        $.ajax({
            url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
            method: 'POST',
            data: {
                'action': 'ter_kal_beispieltermine_eintragen',
                'tk_nonce': tk_nonce // Include the nonce in the request data
            },
            success: function(data) {
                $('#responseMessage').html('<b class=admin_green>'+ ter_kal_admin_vars.sample_data_loded +'</b>');
                      //  alert(JSON.stringify(termin_data));
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#responseMessage').html('<b class=admin_red>Error: ' + textStatus + '</b>');
            }
        });
    });

   $('#kalender_seite_erstellen_button').on('click', function() {
        //  Beispielseite generieren
        var tk_nonce = $(this).data('tk_nonce');
        $.ajax({
            url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
            method: 'POST',
            data: {
                'action': 'ter_kal_create_calendar_page',
                'tk_nonce': tk_nonce
            },
            success: function(data) {
                     window.location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#responseMessage').html('<b class=admin_red>Error: ' + textStatus + '</b>');
            }
        });
    });

// Backup  PREMIUM
    $('[name="backup_loeschen"]').on('click', function() {
        $(this).hide(700)
    });

   $('[name="backup_herunterladen"]').on('click', function() {
        $(this).hide(700)
    });

// Kalender Benutzer PRO
    $('#checkbox_roles').on('change', function() {
      $('#ter_kal_check_userrole').show(500);
    });
    $('#ter_kal_check_userrole').on('click', function() {
      $('#ter_kal_check_userrole').hide(500);
    });
    $('#rechte_eintragen').on('click', function() {
      $('#benutzer_wahl').show(500);
    });
    $('#edit-user-button').on('click', function() {
      $('#edit-user-button').hide(500);
    });
// Termin Bereiche
// Termin Felder
    //  IM FORMULAR NICHT ANZEIGEN
    $('[name="ter_kal_remove_field"]').on('click', function() {
        var nonce = $('#ter_kal_nonce_neu').val();
        var data = {
        'action': 'ter_kal_termin_ajax_show_field',
        'nonce': nonce,
        'field': $(this).data("feld")
        };
        $.post( ajaxurl, data, function(response) {
        location.reload();
        //alert(response.message);
        });
    });
    //remove_field_from_im_ter_kal_nicht_anzeigen: IM FORMULAR ZEIGEN
    $('[name="ter_kal_add_field"]').on('click', function() {
        var nonce = $('#ter_kal_nonce_neu').val();
        var data = {
        'action': 'ter_kal_termin_ajax_hide_field',
        'nonce': nonce,
        'field': $(this).data("feld")
        };
        $.post( ajaxurl, data, function(response) {
        location.reload();
        //alert(response.message);
        });
    });
    // feld_im_ter_kal_loeschen
    $('[name="feld_im_ter_kal_loeschen"]').on('click', function() {
        var feld = $(this).val();
        var nonce = $('#ter_kal_nonce_neu').val();
        var data = {
        'action': 'ter_kal_feld_aus_db_loeschen',
        'nonce': nonce,
        'field': feld
        };
        $.post( ajaxurl, data, function(response) {
        location.reload();
        //alert(response.message);
        });
    });
    // ter_kal_termin_ajax_feld_umbenennen
    $('#umbenennen').on('click', function() {
        var alter_feldname = document.getElementById('alter_feldname').value;
        var neuer_feldname = document.getElementById('neuer_feldname').value;
        var nonce = $('#ter_kal_nonce_id').val();
        var data = {
        'action': 'ter_kal_termin_ajax_feld_umbenennen',
        'nonce': nonce,
        'alter_feldname': alter_feldname,
        'neuer_feldname': neuer_feldname
        };
        $.post( ajaxurl, data, function(response) {
        location.reload();
        //alert(response.message);
        });
    });

        // Handle button click to hide the widget
    $('#ter_kal_close_dashboard_widget').on('click', function() {
        // Make an AJAX request to update the user's option
        $.ajax({
            url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
            method: 'POST',
            data: {
                'action': 'ter_kal_hide_dashboard_widget'
            },
            success: function(response) {
                // Hide the widget on success
                $('#ter_kal_widget').hide(3000);
            }
        });
    });


    $('#toggle-trigger').click(function() {
        var toggleDiv = $('#toggle-div');
        var iconSpan = $('#toggle-icon');
        toggleDiv.slideToggle(function() {
            // Toggle dashicon arrow based on the toggle state
            iconSpan.toggleClass('dashicons-arrow-down-alt2', toggleDiv.is(':hidden'));
            iconSpan.toggleClass('dashicons-arrow-up-alt2', !toggleDiv.is(':hidden'));
        });
    });

	$('[name="ter_kal_admin_help"]').on('click', function(event) {
	   $(this).next().toggle(500);
	});

    $('input[name="ter_kal_submit_kategorien"]').click(function() {
		var button_id = $(this).attr('itemprop');
		$("#submit"+button_id).fadeOut(200);
		$("#bereich_notice"+button_id).fadeIn(200);
    });


}); //----------------------------------------