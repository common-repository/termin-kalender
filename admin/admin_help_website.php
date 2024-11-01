<?php
// Exit if accessed directly.
defined('WPINC') || die;

function terminkalender_help_website_page() {
  ter_kal_show_external_content('https://termin-kalender.pro/help/');
}

// Function to fetch and display external content
//show_external_content('https://termin-kalender.pro/faq-haeufige-fragen/');
function ter_kal_show_external_content($url) {
  $url = esc_url($url);
  $content = wp_remote_get( $url );
  if ( is_wp_error( $content ) ) {
    $error_message = $content->get_error_message();
    echo '<p>Error fetching content: ' . $error_message . '</p>';
    return;
  }
  $body = wp_remote_retrieve_body( $content );
  // Optionally, you can parse the content here to extract specific elements
  echo $body;
  return;
}
