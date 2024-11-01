function ter_kal_initializeFullCalendar() {
//document.addEventListener('DOMContentLoaded', function() {
jQuery(function($) {
// js  für ter_kal_global_nonce  und weitere php var laden:
  var isUserLoggedIn = ter_kal_kalender_vars.is_user_logged_in;
if (isUserLoggedIn) {
   var editable_true_false = true ;
} else {
   var editable_true_false = false ;
}
  var ter_kal_lang =   ter_kal_kalender_vars.ter_kal_lang;
  var formattedDate = 'date empty'; // init formattedDate for mousenter AND MODAL
  //----------------------------------------------------------------
            var calendarEl = document.getElementById('termin-kalender-calendar');
            var calendar = new FullCalendar.Calendar(calendarEl,
              {
			  height: 'auto', /* height auto und full browser content auch auto */
              locale: ter_kal_lang ,
              headerToolbar: {
                left: 'prev,next,today',
                center: 'title',
                right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
              themeSystem: 'bootstrap5',
                // ohne Zeitzonenangabe:  to: 'bis', fehler im titel  UTC = unverändertes Eingabedatum ohne Zeitzonen-Veränderung
               timeZone: 'UTC',
              initialView:  'dayGridMonth' ,
              events: '' ,
                /**
		         events: {
		         googleCalendarId: '7ec2a83b2df12d5e67d29dbda320fccbaa75c0d9d3369a4281d2f7487ece87e3@group.calendar.google.com',
		         //  className: 'gcal-event' // an option!
		         }
		         */
              stickyHeaderDates: true,
              eventDisplay: 'block',
              firstDay: 1,
              editable: editable_true_false ,
			  displayEventTime: false ,
              //eventDurationEditable: true,
              selectable: false,
              buttonIcons: true, // show the prev/next text
              weekNumbers: true,
              // eventBorderColor: '#DBFDFF', nicht möglich wegen effekt auf listendarstellung
              navLinks: true // can click day/week names to navigate views
                //dayMaxEvents: true // allow "more" link when too many events
                //--------------------------------------------------------------------------
              }
            ); // ende fullcalendar
            //--------------------------------------------------------------------------
            $('#kalender_modal').on('hidden.bs.modal', function ()
              {
                //modal geschlossen  daten neu laden  reload bereits im hide, schneller
              });

            $('#kalender_modal').on('show.bs.modal', function () {
                // Code to execute when the modal is about to be shown
            }); //end  modal show

			// reload page and load updated data
            $('#kalender_modal').on('hide.bs.modal', function (event) {
                // Code to execute when the modal is about to be hidden  alert('Modal is about to be hidden');
				window.location.reload()
                // TODOS: reset view 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            });  //end  modal hide

            //--------------------------------------------------------------------------

            //event erstellen Modal öffnen und klick-Datum eintragen
            calendar.on('dateClick', function(info)
              {
			    // Check if the user is logged in
				var tk_benutzerrechte = $('#tk_benutzerrechte').val();
				if ($.inArray(tk_benutzerrechte.toLowerCase(), ['bearbeiten', 'loeschen']) == -1) {
				    //alert('You must be logged in to add dates.');
				    return;
				}
              	$('#termin_speichern').show();
				$('#termin_bearbeiten').hide();
				$('#termin_aktualisieren').hide();
				$('#termin_loeschen').hide();
				$('#ter_kal_btn-group').hide();
				$('#select_user').hide();
				$("#send-invitation").html(ter_kal_kalender_vars.save_before);
				$('[name="ter_kal_form_help"]').show();
				$('label[for="select_user"]').hide();
                info.dayEl.style.backgroundColor = '#F0F5FF';
                $("#kalender_modal .modal-title").html('<span style="font-size: 2em; vertical-align:-20%; " class="dashicons dashicons-plus"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '+ ter_kal_kalender_vars.neuer_termin );
                info.date.setHours(info.date.getHours() + 8);
                var termin_start = info.date.toISOString().slice(0, -8);
                info.date.setHours(info.date.getHours() + 8);
                var termin_end = info.date.toISOString().slice(0, -8);
                $('#start').val(termin_start);
                $('#end').val(termin_end);
                $('#kalender_modal').modal({backdrop: true});
                $('#kalender_modal').modal('show');
              }
            );

	      // EVENT IM MODAL ANZEIGEN
	      calendar.on('eventClick', function(info)
              {
                $('#termin_speichern').hide();
				$('#termin_bearbeiten').show();
				$('#termin_aktualisieren').hide();
		  //$('#termin_duplizieren').hide();
		        //erst die daten ausgeben, welche mit dem event bekannt sind
                $("#kalender_modal .modal-header").css("background", info.event.backgroundColor);
                $("#kalender_modal .modal-title").css("color", info.event.textColor);
				$("#kalender_modal .modal-title").html('<span id="ter_kal_icon" style="font-size: 2em; vertical-align:-20%; " class="dashicons dashicons-' + info.event.extendedProps.icon + '"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:0.7em; opacity:0.6;"> ' + info.event.extendedProps.kategorie + ' </span> &nbsp;&nbsp;' + info.event.title);
				$('#kalender_modal .modal-title').prepend($('#ter_kal_icon'));
			    var nonce = $('#ter_kal_nonce_id').val();
		    $.ajax({
			        url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
			        method: 'POST',
			        data: {
			            'action': 'ter_kal_kalender_termin_zeigen_form',
			            'nonce': nonce,
                        'tk_form' : 'ausgabe',
			            'id':  info.event.id
			        },
			        success: function(data) {
			        //alert(data);
                    //d ata = data.replace(/0+$/, ''); // Remove trailing zeros TODOS: Prüfen woher die Null kommt return; irgendwo ??
                     $("#kalender_modal .modal-body").html(data);
						$("#id").val(info.event.id);  // update id

                        ter_kal_get_formated_date(info);
                        $(".tk_modal_zeit_title").html(formattedDate);
					    $('#kalender_modal').modal({backdrop: true});
		                $('#kalender_modal').modal('show');
					} // end function data
			    });
              });


            //eventChange datum geändert
            calendar.on('eventDrop', function(info)
              {
			    // Check if the user is logged in
				var tk_benutzerrechte = $('#tk_benutzerrechte').val();
				if ($.inArray(tk_benutzerrechte.toLowerCase(), ['bearbeiten', 'loeschen']) == -1) {
				    return;
				}
                var nonce = $('#ter_kal_nonce_id').val();
                $.ajax({
                    url: js_wp_php_vars.TER_KAL_ADMIN_AJAX_URL,
                    method: 'POST',
                    data: {
                        'action': 'ter_kal_kalender_datum_aendern',
                        'start':  info.event.start.toISOString() ,
                        'end':    info.event.end.toISOString() ,
                        'nonce': nonce,
                        'id':  info.event.id
                    },
                    success: function(data) {
                      //  window.location.reload();
                    }
                });

              }
            );

            /** termin verschieben element markieren: */
            calendar.on('eventDragStart', function(info)
              {
			    // Check if the user is logged in
				var tk_benutzerrechte = $('#tk_benutzerrechte').val();
				if ($.inArray(tk_benutzerrechte.toLowerCase(), ['bearbeiten', 'loeschen']) == -1) {
				    return;
				}
			//  alert(JSON.stringify(info, null, 2)); // This will show the content of info in a formatted string
				if (info.event._def.recurringDef) {
					window.location.reload();
					alert('Recurring events can not be moved');
                }
              });


			/* termin mouseover:  */
			calendar.on('eventMouseEnter', function(info) {
		    	var currentView = calendar.view.type;
		    	if (currentView === 'dayGridMonth') {
					// info.el element, info.event eventdaten
					info.el.style.border = "1px solid white";
					info.el.style.boxShadow = "0 0px 14px #000";
          			info.el.style.fontWeight = "bold";

					ter_kal_get_formated_date(info);

					var beschreibung = info.event.extendedProps.Beschreibung;
					var beschreibungDivs = [
					  document.getElementById('ter_kal__mouseover_info'),
					  document.getElementById('ter_kal__mouseover_info2'),
					  document.getElementById('ter_kal__mouseover_info3'),
					  document.getElementById('ter_kal__mouseover_info4')
					];
					var text = [
					  `<b>${info.event.title}:</b> ${formattedDate} `,
					  `${beschreibung}...<sup>${ter_kal_kalender_vars.click_more}</sup>`
					];
					beschreibungDivs.forEach((div, index) => {
					  div.innerHTML = text[index % 2];
					});
				}
			});

function ter_kal_get_formated_date(info)
{
	var start = info.event.start;
  var start_std = String(start.getUTCHours()).padStart(2, '0');
  var start_min = String(start.getUTCMinutes()).padStart(2, '0');
	var end = info.event.end;
	if (end) { //  kein recurring event. fullcalendar entfernt end wenn recurring
        		var formattedDateRange = FullCalendar.formatRange(start, end, {
        		  month: 'long',
        		  year: 'numeric',
        		  day: 'numeric',
                  timeZone: 'UTC', // wichtige zeitzonen korrektur um zeitzonen neutral zu bleiben
        		  separator: ' - ',
        		  locale: ter_kal_lang
        		});
        var end_std = String(end.getUTCHours()).padStart(2, '0');
        var end_min = String(end.getUTCMinutes()).padStart(2, '0');
	   // formattedDate inited before
	    formattedDate = formattedDateRange + ',<b> ' + start_std + ':' + start_min + ' - ' + end_std + ':' + end_min + '</b> ' ;
       var emailDate = formattedDateRange + ', ' + start_std + ':' + start_min + ' - ' + end_std + ':' + end_min  ;
	} else {
        var rrule_freq = info.event.extendedProps.freq
	    if (rrule_freq === 'WEEKLY') {
	        rrule_freq = ter_kal_kalender_vars.WEEKLY;
	    } else if (rrule_freq === 'MONTHLY') {
	        rrule_freq = ter_kal_kalender_vars.MONTHLY;
	    } else if (rrule_freq === 'YEARLY') {
	        rrule_freq = ter_kal_kalender_vars.YEARLY;
	    }
        var rrule_dtstart = info.event.extendedProps.startUTC;
		var rrule_until = info.event.extendedProps.endUTC;
        total_time_start =  start_std + ':' + start_min  ; // start zeit ist nur bei nicht wiederhgol events gegeben in end  total_time_until = '' ; //   end_std + ':' + end_min
        let [hoursUntil, minutesUntil] = rrule_until.split('T')[1].match(/.{1,2}/g).map(Number);
        let total_time_until = `${String(hoursUntil).padStart(2, '0')}:${String(minutesUntil).padStart(2, '0')}`;
		var rrule_bis = FullCalendar.formatDate(rrule_until, {
		  month: 'long',
		  year: 'numeric',
		  day: 'numeric',
		  timeZone: 'UTC',
		  locale: ter_kal_lang
		})
		var rrule_von = FullCalendar.formatDate(rrule_dtstart, {
		  month: 'long',
		  year: 'numeric',
		  day: 'numeric',
		  timeZone: 'UTC',
		  locale: ter_kal_lang
		})
		var selected_day_start = FullCalendar.formatDate(start, {
		  month: 'long',
		  year: 'numeric',
		  day: 'numeric',
		  timeZone: 'UTC',
		  locale: ter_kal_lang
		})

	  // formattedDate inited before
	    formattedDate =   '<mark >' + selected_day_start + '</mark><b> ' + total_time_start + ' - ' + total_time_until   + ' </b> <mark >' + rrule_freq + '</mark> <span class="tk_modal_zeit_recur">' + rrule_von + ' - ' + rrule_bis + '</span>' ;
       var emailDate =   selected_day_start + ', ' + total_time_start + ' - ' + total_time_until   + '. \n' + rrule_freq + ': ' + rrule_von + ' - ' + rrule_bis ;
    }

   $("#formattedDate").html(emailDate);
   return formattedDate;
} // end get  formattedDate


			calendar.on('eventMouseLeave', function(info) {
				var beschreibungDivs = document.getElementsByClassName('ter_kal__mouseover_info_div');
				for (var i = 0; i < beschreibungDivs.length; i++) {
				    beschreibungDivs[i].innerHTML = '&nbsp;';
				    beschreibungDivs[i].style.backgroundColor = '#fff';
				}
					var formattedStart =  '';
					var formattedEnd =  '';
					var beschreibung = '';
                var currentView = calendar.view.type;
		    	if (currentView === 'dayGridMonth') {
    				info.el.style.border = "1px solid #ddd";
    				info.el.style.boxShadow = "none";
                  	info.el.style.fontWeight = "normal";
    				info.el.style.color = info.event.textColor;
    				info.el.style.background = info.event.backgroundColor;
                }
			});

            // ende kalenderanzeige------------------------------------------------
            //-----------------------------------------------
			// Make the FullCalendar object global
			window.calendar = calendar;
			//alert ( ter_kal_event_daten ) ;
}); // end jquery --------------------------------------------------
//}); // end document listener
}
