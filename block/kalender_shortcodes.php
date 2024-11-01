<?php
defined('WPINC') || die;
// add FullCalendar to a page or post
function ter_kal_kalender_shortcode() {
//return 'my-termin-kalender';  // return;
};
add_shortcode('my-termin-kalender', 'ter_kal_kalender_shortcode');
// Hook into template_include to override the template
add_filter('template_include', 'kalender_template_override', 99);
function kalender_template_override($template) {
    global $post;
    if (is_object($post) && has_shortcode($post->post_content, 'my-termin-kalender')) {

        $plugin_template = TER_KAL_PLUGIN_DIR . 'templates/kalender-template.php';

        if (file_exists($plugin_template)) {

            return $plugin_template;
        }
    }
    return $template; // MUST Return the standard template for other pages
}
