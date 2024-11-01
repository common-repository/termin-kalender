<?php
// Exit if accessed directly.
defined('WPINC') || die;

// API call for event list
add_action('rest_api_init', function () {
    register_rest_route('my-termin-eventlist/v1', '/categories/', array(
        'methods' => 'GET',
        'callback' => 'ter_kal_get_event_list_categories',
        'permission_callback' => '__return_true',
    ));
});

function ter_kal_get_event_list_categories() {
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



function ter_kal_termin_eventlist_shortcode($atts) {
    
    ob_start(); // Start output buffering
	global $wpdb;
	$event_list_category = shortcode_atts(['category' => ''], $atts)['category'];
	$event_list_category = (int) $event_list_category; // Ensure it's an integer
	$rows = $wpdb->get_results(
	"SELECT title, beschreibung, start, end, startUTC, endUTC, freq, byweekday, ort, list_image, zusatz_url, kategorie_id
       FROM  " . TER_KAL_TERMIN_DB . "
	   WHERE kategorie_id = ". $event_list_category ."
       ORDER BY start ASC;"
	);
    // statt rrule :  startUTC, endUTC, freq, byweekday,
	if (empty($rows)) {
	    // Handle the case where no rows are found
	    echo '<hr><h2>No Data found for this category</h2> Admin info: You might like to change the appointment category for this list.<hr>';
		return;
	} else {
		$date_format = get_option('date_format');   // $dates[] = date_i18n(get_option('date_format');
        $time_format = get_option('time_format');
        $time_format = preg_replace('/(:\s*s)/', '', $time_format); // sekunden vom zeitformat aus optionen entfdernen  //$time_format = 'H:i'; // 24-hour format without seconds  // $time_format = 'g:i a'; // 12-hour format without seconds
        $now = new DateTimeImmutable('today');      // $current->getTimestamp());
        $yesterday = (new DateTimeImmutable())->modify('-1 day'); // aktuelle daten zeigen, alles nach gestern
        $tomorrow = (new DateTimeImmutable())->modify('+1 day');
        $weekdayMap = [
            'MO' => 'Monday',
            'TU' => 'Tuesday',
            'WE' => 'Wednesday',
            'TH' => 'Thursday',
            'FR' => 'Friday',
            'SA' => 'Saturday',
            'SU' => 'Sunday'
        ];
	foreach ($rows as $key => $row){  //--------------------------------------------------------------
        $start = $row->start;
        $end = $row->end;
        $dtStart = new DateTime($start);
        $dtEnd = new DateTime($end);
        $next_time_start = $dtStart->format($time_format);
        $next_time_end = $dtEnd->format($time_format);
        //$startUTC = $row->startUTC;  // evtl nicht benötigt hier
        //$endUTC = $row->endUTC;      // evtl nicht benötigt hier
        //echo 'start: '.$start.', end: '.$end.'<br>startUTC: '.$startUTC.', endUTC: '.$endUTC.'<br>';
        $freq = $row->freq;
        $byweekday = $row->byweekday;
        // Initialize the new variables
        $all_dates = [];
        $passed_dates = [];
        $next_date_found = null;
        $following_dates = [];
        $dates = [];
        $current = clone $dtStart;
        $row->row_to_remove = '';
        $row->obj_to_hide = '';
        $row->old_dates = 0;
        // freq ----------------------------------------------------------------
		if ($freq) {
		    switch ($freq) {
                case 'WEEKLY':
                    $frequency = __('Weekly, at ', 'termin-kalender');
                                $interval = new DateInterval("P1W");
                    break;
                case 'MONTHLY':
                    $frequency = __('Monthly, at ', 'termin-kalender');
                                $interval = new DateInterval("P1M");
                    break;
                case 'YEARLY':
                    $frequency = __('Yearly, at ', 'termin-kalender');
                                $interval = new DateInterval("P1Y");
                    break;
                default:
                    $frequency = __('Unknown ', 'termin-kalender');
                                $interval = null;
                    break;
		    };
            //--------------------------------------------------------
            if ($byweekday) {
                $weekdays = explode(',', $byweekday);
                while ($current <= $dtEnd) {

                    foreach ($weekdays as $weekday) {
                        $dayName = $weekdayMap[$weekday];
                        $day = clone $current;
                        $day->modify("next $dayName");
/*                        if ($day <= $dtEnd) {
                            $formatted_date = $day->format("$date_format") ;
                            $all_dates[] = ['date' => $day, 'formatted' => $formatted_date.'.all-W'];
                        }*/

                        if ($day < $tomorrow) {
                            // If the date is in the past, add it to $passed_dates
                            $passed_dates[] = ['date' => $day, 'formatted' => $day->format("l $date_format")];
                        } else if ($day > $now && $next_date_found === null) {
                           $next_date_found = 1;
                            $formatted_date = $day->format("l $date_format"). ', ' . $next_time_start . ' - ' . $next_time_end;
                            //$formatted_time = $current->format("$time_format");

                            //$formatted_date_time = $formatted_date . ', ' . $next_time_start . ' - ' . $next_time_end;
                           //$formatted_date = $day->format("l $date_format $time_format").' - '.$next_time_end ;
                           //$row->start_date = $row->start;
                           $row->start_date = $day->format("$date_format");
                            $row->start = $formatted_date; // test    // das 'aktuelle' (nächste) datum setzen bei recurring dates


                        } else if ($day > $yesterday) {
                            // All other future dates are following dates
                            $following_dates[] = ['date' => $day, 'formatted' => $day->format("l $date_format")];
                        }
                    }
                    $current->add($interval);
                }
            } else {    //---------------------------------------------------------

                // listen der recurring daten erstellen
                while ($current <= $dtEnd) {
                    //$formatted_date = $current->format("$date_format $time_format").' - '.$next_time_end . ".all ";
                    //$all_dates[] = ['date' => $current, 'formatted' => $formatted_date];
                    if ($current < $tomorrow) {
                        // If the date is in the past, add it to $passed_dates
                        $passed_dates[] = ['date' => $current, 'formatted' => $current->format("$date_format")];
                    } else if ($current > $now && $next_date_found === null) {
                       $next_date_found = 1;
                       $formatted_date = $current->format("$date_format $time_format").' - '.$next_time_end . ". ";
                       $row->start_date = $row->start;
                        $row->start = $formatted_date; // test    // das 'aktuelle' (nächste) datum setzen bei recurring dates

                    } else if ($current > $yesterday) {
                        // All other future dates are following dates
                        $following_dates[] = ['date' => $current, 'formatted' => $current->format("$date_format")];
                    }

                    //$dates[] = clone $current;
                    $current->add($interval);
                }
            }   // end weekly ---------------------------------------------------------
// Sortiert passed und next dates für wiederhol-events
            usort($passed_dates, function($a, $b) {
                return $a['date'] <=> $b['date'];
            });
            $passed_dates = array_slice($passed_dates, -6); // Keep only the most recent 3 dates
            $passed_dates = array_column($passed_dates, 'formatted'); // Extract the formatted dates
    		$row->passed_dates = $passed_dates;        //array
            $passed_dates = [];
            usort($following_dates, function($a, $b) {
                return $a['date'] <=> $b['date'];
            });
            $following_dates = array_slice($following_dates, 0, 15); // Keep only next 10 dates
            $following_dates = array_column($following_dates, 'formatted'); // Extract the formatted dates
            $row->following_dates = $following_dates;  //array
            $following_dates = [];
    		$row->next_time_end = $next_time_end;

    		if (empty($next_date_found)) { // recurring event hat keine folgedaten
        	    $row->obj_to_hide = 'display:none;';
                $row->row_to_remove = 'opacity:0.5; font-size:0.6em;';
                $row->start_date = $row->start;
                $row->start = 'all dates in the past';
    		}  // end

/*            // Output the chosen days array
            foreach ($all_dates as $chosenDay) {
                echo $chosenDay . PHP_EOL;
            }*/


         // end freq ----------------------------------------------------------------
		} else { // kein recurring event

    if ($dtStart < $yesterday) {
            $row->row_to_remove = 'opacity:0.5; font-size:0.6em;';
            $row->obj_to_hide = 'display:none;';
            $row->old_dates = 1 ;
    }
    $row->start_date = $row->start;
    $row->start = $dtStart->format("$date_format,  $time_format").' - '.$next_time_end;
};

	}; //----------------------------------------------------------------------------
	unset($row); // Break the reference with the last element
    //-------------------------------------------------------------------------------
    usort($rows, function($a, $b) {
        return strtotime($a->start_date) - strtotime($b->start_date);
    });

    // Filter out rows with old_dates = 1
    $filtered_rows = array_filter($rows, function($row) {
        return isset($row->old_dates) && $row->old_dates == 1;
    });

    // Get the 5 most recent dates from the past
    $recent_dates = array_slice($filtered_rows, -3);

    // Keep rows where old_dates == 0
    $non_old_dates = array_filter($rows, function($row) {
        return !isset($row->old_dates) || $row->old_dates == 0;
    });

    // Merge the non-old dates with the recent old dates
    $rows = array_merge($non_old_dates, $recent_dates);
    usort($rows, function($a, $b) {
        return strtotime($a->start_date) - strtotime($b->start_date);
    });
	//--------
	} //  check again for empty  after unset
if (empty($rows)) {
    echo '<hr><h2>No future Data found for this category</h2> Admin info: All data is in the past.<hr>';
} else {
$kategorien = get_option('ter_kal_kategorien');
$kategorie_content = array_values(array_filter($kategorien, fn($k) => $k['kategorie_id'] == $event_list_category))[0] ?? null;
$kategorie = $kategorie_content['kategorie'] ?? null;
$backgroundColor = $kategorie_content['backgroundColor'] ?? null;
$textColor = $kategorie_content['textColor'];
$icon = $kategorie_content['icon'] ?? null;
$style = 'background-color:'. $backgroundColor .'; color:'. $textColor .';' ;
//echo $event_list_category.' '.$kategorie.' - option kat_id: '.$kategorie_content['kategorie_id'];
?>
<br>
<div class="responsive-table-display ter_kal_font">
    <h2 class="tk_kategorie_border tk_kategorie-title " style="<?php echo $style ?>" >
      <span class="tk_list_titel_dashicons dashicons dashicons-<?php echo $icon ?>"></span>
      <?php echo $kategorie ?><br>
    </h2>
    <div class="responsive-row tk_zeit_block_info" ><?php ter_kal_lizenz_info();?></div>
    <?php // <button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse" data-target=".tk_past" aria-expanded="false" aria-controls="tk_past">Show past dates</button> ?>
    <?php
    $row_count = 0;
	foreach ($rows as $row):
    ?>
        <div class="responsive-row tk_responsive-row" style="<?php echo $row->row_to_remove ?> background-color: <?php echo $row_count % 2 == 0 ? 'transparent' : '#F0F0F0'; ?>;">
            <div class="tk_zeit_block_title"><?php echo !empty($row->title) ? htmlspecialchars($row->title) : '' ?></div>
		<?php
    if ($row->freq) {
        // PAST // echo implode(', ', $row->all_dates); // show all dates
        ?>
        <div id class="tk_zeit_block_next tk_zeit_block_veryold">past:<i>
        <?php
        foreach ($row->passed_dates as $passed_date) {
                echo "&nbsp;&nbsp;" . $passed_date . ", ";
        }
        ?>
        </i></div>
         <?php
        // AKTUELL
        echo '<div class="tk_zeit_block">'.$row->freq.' : '. $row->start .'</div>';
        // FOLGENDE
		?>
        <div class="tk_zeit_block_next">next:
	    <?php
        $counter = 0;
	    foreach ($row->following_dates as $following_date) {
	          if ($counter < 4) {
                  echo "<i class='tk_zeit_block_old'>&nbsp&nbsp;" . $following_date . ", </i>";
	          } else {
	              echo "<i class='tk_zeit_block_veryold'>&nbsp&nbsp;" . $following_date . ", </i>";
	          }
	          $counter++;
	    }
 	  	?>
        </div>
        <?php
	} else {    // end if $row->freq
			echo '<div class="tk_zeit_block"><b>'. $row->start .'</b></div>';
	}           // end else $row->freq
            if (!empty($row->zusatz_url)): ?>
            <a href="<?php echo esc_url($row->zusatz_url); ?>" id="urllink" target="_blank"><?php echo esc_url($row->zusatz_url); ?></a><br>
            <?php endif; ?>
	    <div id="img_div" style="<?php echo $row->obj_to_hide ?>" ><?php if (!empty($row->list_image)): ?><img src="<?php echo htmlspecialchars($row->list_image) ?>" alt="" style="max-width: 100%; height: auto;"><?php endif; ?></div>
	          <div style="<?php echo $row->obj_to_hide ?>"><?php echo !empty($row->beschreibung) ? wp_kses($row->beschreibung, wp_kses_allowed_html('post')) : ''; ?></div>
	            <?php if (!empty($row->ort)): ?>
	                <?php $encoded_location = urlencode($row->ort); ?>
	                <?php $google_maps_url = "https://www.google.com/maps/search/?api=1&query={$encoded_location}"; ?>
	                <div id="btn_div"  style="<?php echo $row->obj_to_hide ?>"><button type="button" class="btn btn btn-outline-primary"  onclick="window.open('<?php echo esc_url($google_maps_url); ?>')"><?php echo htmlspecialchars($row->ort); ?></button></div>
	            <?php endif; ?>
	    </div>
	        <?php $row_count++; ?>
	    <?php endforeach; ?>
</div>
	<?php
	} // end second if no data
    return ob_get_clean(); // Return the buffered output
} // end my_termin_eventlist_shortcode

add_shortcode('my-termin-eventlist', 'ter_kal_termin_eventlist_shortcode');


