<?php
defined('WPINC') || die;
// Exit if accessed directly.
// API call for event list
add_action('init', function() {
    register_block_type('termin-kalender/my-termin-list', [
        'render_callback' => 'ter_kal_termin_list_free',
        'attributes' => [
            'tasks' => [
                'type' => 'array',
                'default' => [],
            ],
        ],
    ]);
});

function ter_kal_termin_list_free($attributes) {
    ob_start(); // Start output buffering
    global $wpdb;

    $rows = $wpdb->get_results(
        "SELECT id, title, beschreibung, start, end, zusatz_url, kategorie_id, Ort, list_image
        FROM " . TER_KAL_TERMIN_DB . "
        WHERE start >= NOW()
        ORDER BY start ASC
        LIMIT 20;"
    );

    if (empty($rows)) {
        echo '<hr><h2>No Data found</h2><hr>';
        return ob_get_clean();
    }

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

        $page = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            's' => 'my-termin-kalender'
        ]);

        if ($page) {
            $shortcodepage = sprintf('<a href="%s">open the calendar</a>', esc_attr(get_permalink($page[0]->ID)));
        } else {
            $shortcodepage = "(no calendar found)";
        }
        $kategorien = get_option('ter_kal_kategorien');
?>

<div class="accordion" id="eventlist">
<?php foreach ($rows as $row):
        $dtStart = new DateTime($row->start);
        $row->startdate = $dtStart->format("D. M. d, Y");
        $row->start = $dtStart->format("$date_format, $time_format");
        $dayName = $dtStart->format('l'); // Extract the day name

        $dtEnd = new DateTime($row->end);
        $row->end = $dtEnd->format("$date_format, $time_format");

if ($kategorien && isset($row->kategorie_id)) {
    foreach ($kategorien as $k) {
        if ($k['kategorie_id'] === $row->kategorie_id) {
            $kategorie = $k['kategorie'];
            $backgroundColor = $k['backgroundColor'];
            $textColor = $k['textColor'];
            break;
        }
    }
}

 ?>
  <div class="accordion-item">
<div class="accordion-header" id="heading<?php echo esc_html($row->id); ?>" >
      <button class="accordion-button collapsed"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo esc_html($row->id); ?>" aria-expanded="false" aria-controls="collapse<?php echo esc_html($row->id); ?>">
<span class="dashicons dashicons-calendar-alt" style="color: <?php echo $textColor ?>; background-color: <?php echo $backgroundColor ?>; font-size: 1.8em; height: 1.4em; width: 1.4em; margin-left: -0.6em; border: 2px solid <?php echo $textColor ?>; border-radius: 8px; padding: 4px;""></span><b>&nbsp;<?php echo esc_html($row->startdate); ?></b>
        &nbsp;&nbsp;<?php echo esc_html($row->title); ?>
      </button>
    </div>
    <div id="collapse<?php echo esc_html($row->id); ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo esc_html($row->id); ?>" data-bs-parent="#eventlist">
      <div class="accordion-body">
            <?php echo $kategorie ?>:&nbsp;<b><?php echo $dayName ?></b> <?php echo esc_html($row->start); ?>&nbsp;-&nbsp;<?php echo esc_html($row->end); ?>
            <h4><?php echo esc_html($row->title); ?></h4>
            <?php echo !empty($row->beschreibung) ? wp_kses_post($row->beschreibung) : ''; ?>
            <?php if (!empty($row->zusatz_url)): ?>
                <a href="<?php echo esc_url($row->zusatz_url); ?>" target="_blank"><?php echo esc_html($row->zusatz_url); ?></a><br>
            <?php endif; ?><br>
            <i><?php echo esc_html($row->Ort); ?></i>&nbsp;&nbsp;&nbsp; <?php echo $shortcodepage ?>
      </div>

  </div>
  </div>
<?php endforeach; ?>
<hr>

</div>
    <?php

    return ob_get_clean(); // Return the buffered output
}


