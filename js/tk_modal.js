jQuery(function ($) {

	//show_ter_kal_help
	$('#show_ter_kal_help').on('click', function (event) {
		$('#kalender_modal .modal-title').html(
			'<span style="font-size: 2em; vertical-align:-20%; " class="dashicons dashicons-editor-help"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
				ter_kal_modal_vars.help_title
		);
		$('#termin_speichern').css('display', 'none');
		$('#kalender_modal').modal({ backdrop: true });
		$('#kalender_modal .modal-body').html('Help..loading');
		$('#kalender_modal').modal('show');
		var nonce = $('#ter_kal_nonce_id').val();
		$.ajax({
			url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
			method: 'POST',
			data: {
				'action': 'ter_kal_kalender_help_page',
				'nonce': nonce,
			},
			success: function (data) {
				$('#kalender_modal .modal-body ').html(data);
			},
            error: function(error) {
          		console.error("Fehler beim Senden des Ajax-Requests:", error);
            }
		});
	});

	$('#termin_speichern').on('click', function(event) {
			var startDate = $('#start').val();
			var endDate = $('#end').val();
			if (endDate <= startDate) {
			$('#end').val(startDate);
			}
	        var termin_data = {};
	        var nonce = $('#ter_kal_nonce_id').val();
	        $('#terminkalenderform input, textarea, select').each(function() {  var name = $(this).attr('name');  var value = $(this).val();
	            if (name !== undefined && name !== '' && value !== '') { termin_data[name] = value;  }  });   // Check if the name attribute or the value is not empty
	        $.ajax({
	        url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
	        method: 'POST',  data: {
	            'action': 'ter_kal_kalender_termin_speichern',
	            'nonce': nonce,
	            'insert_update': 'insert',
	            'termin_data':  termin_data   },
	        success: function(data) { $('#kalender_modal').modal('hide'); // alert(JSON.stringify(termin_data)); //window.location.reload();
			},
            error: function(error) {
          		console.error("Fehler beim Senden des Ajax-Requests:", error);
            }
              });  });

	$('#termin_aktualisieren').on('click', function(event) {
			var startDate = $('#start').val();
			var endDate = $('#end').val();
			if (endDate <= startDate) {
				$('#end').val(startDate);
			}
	        var termin_data = {};
	        var nonce = $('#ter_kal_nonce_id').val();
	        $('#terminkalenderform input, textarea, select').each(function() {
	            var name = $(this).attr('name');
	            var value = $(this).val();
                // removed && value !== to keep emptied values
	            if (name !== undefined && name !== '') {
	                termin_data[name] = value;
	            }
	        });
	        var id = $('#terminkalenderform #id').val() ;   // unterschied zu neu hier mit ID
	        $.ajax({
	        url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
	        method: 'POST', data: {
	            'action': 'ter_kal_kalender_termin_speichern',
	            'nonce': nonce,
	            'insert_update': 'update',
	            'termin_data':  termin_data ,
	             'id' : id
	        },
	        success: function(data) {
	                    $('#kalender_modal').modal('hide');
						//window.location.reload();  alert(JSON.stringify(termin_data));
	                },
    		error: function(error) {
          		console.error("Fehler beim Senden des Ajax-Requests:", error);
            }
	        });
	});


	// termin_duplizieren   TODOS: aktuell bearbeitete speichern bevor duplizieren
	$('#termin_duplizieren').on('click', function (event) {
		var termin_data = {};
		var nonce = $('#ter_kal_nonce_id').val();
		$('#terminkalenderform input, textarea, select').each(function () {
			var name = $(this).attr('name');
			var value = $(this).val();
			if (name !== undefined && name !== '' && value !== '') {
				termin_data[name] = value;
			}
		}); // Check if the name attribute or the value is not empty
		//beschreibung_container
		//termin_data['Beschreibung'] = $('#beschreibung_container').html();
		delete termin_data['id'];
		termin_data['notiz_journal'] = '';
		//alert( JSON.stringify(termin_data) );
		$.ajax({
			url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
			method: 'POST',
			data: {
				'action': 'ter_kal_kalender_termin_speichern',
				'nonce': nonce,
				'insert_update': 'duplicate',
				'termin_data': termin_data
			},
			success: function (data) {
				$('#kalender_modal').modal('hide'); // alert(JSON.stringify(termin_data)); //window.location.reload();
			},
    		error: function(error) {
          		console.error("Fehler beim duplizieren:", error);
            }
		}); // end ajax
	}); // end termin duplizieren

	$('#termin_bearbeiten').on('click', function (event) {
		$('#termin_bearbeiten').hide(1000);
		//var einzel_id = $('#einzel_id').html();
		var id = $('#id').val();
		var nonce = $('#ter_kal_nonce_id').val();
		$('#termin_duplizieren').show();

		$.ajax({
			url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
			method: 'POST',
			data: {
				'action': 'ter_kal_kalender_termin_zeigen_form',
				'nonce': nonce,
				'id': id,
			},
			success: function (data) {
				$('#kalender_modal .modal-body ').html(data);
				$('#ter_kal_google_ort').hide();
				$('#termin_aktualisieren').show(1000);
				$('#termin_loeschen').show(1000);
                if ($('#freq').length && $.trim($('#freq').val()) !== '') { // if freq exists and is not empty
					var freq_val = $('#freq').val();
				    $("#rrule_select").val(freq_val);
                    if (freq_val == 'WEEKLY') {
                        $('#day_check').show(1000);
                    } else {
                        $('#day_check').hide(1000);
                    }
				}
                // Set checkboxes based on hidden field value
                var byweekday = $('#byweekday').val();
                if (byweekday) {
                    var selectedDays = byweekday.split(',');
                    selectedDays.forEach(function(day) {
                        $('#day_check input[type="checkbox"][value="' + day + '"]').prop('checked', true);
                    });
                }
			},
            error: function(error) {
          		console.error("Fehler beim Senden des Ajax-Requests:", error);
            }
		});
	});

	$('#termin_loeschen').on('click', function (event) {
		let confirmAction = confirm(ter_kal_modal_vars.sure_delete);
		if (confirmAction) {
			var id = $('#terminkalenderform #id').val();
			var nonce = $('#ter_kal_nonce_id').val();
			$.ajax({
				url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
				method: 'POST',
				data: {
					'action': 'ter_kal_kalender_termin_loeschen',
					'nonce': nonce,
					'id': id,
				},
				success: function (data) {
				    //alert(data.message);
                    $('.modal-body').fadeOut(700, function() {
                        $(this).html('<h4>'+data.message+'</h4>').fadeIn(500).delay(1500).fadeOut(function() {
                            $('#kalender_modal').modal('hide');
                        });
                    });
				},
            error: function(error) {
          		console.error("Fehler beim Senden des Ajax-Requests:", error);
            }
			});
		}
	});

	$('[name="ter_kal_form_help"]').on('click', function (event) {
		$(this).next().toggle(500);
	});

	//----------------
}); //jquery end

