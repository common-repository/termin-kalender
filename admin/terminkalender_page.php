<?php

// Exit if accessed directly.
defined( 'WPINC' ) || die;
function ter_kal_terminkalender_page() {
    ?>
    <h1><?php 
    ter_kal_lizenz_info();
    ?></h1>
    <div class="card-row">
            <div class="card">
                    <h2 class="title"><?php 
    esc_html_e( 'Frontend Schedule and Resource Calendar.', 'termin-kalender' );
    ?></h2>
                    <p><?php 
    ter_kal_banner();
    ?></p>
                    <?php 
    ter_kal_list_pages_with_shortcode();
    ?>
                    <div id="responseMessage" name="responseMessage">
                            <button type="button" id="beispieltermine_button" name="beispieltermine_button" class="button">
                                    <?php 
    esc_html_e( 'Load some example dates in the calendar', 'termin-kalender' );
    ?>
                            </button>
                    </div>
                    <br>
            </div>
            <div class="card">
                    <h2 class="title"><?php 
    esc_html_e( 'Installation & Help', 'termin-kalender' );
    ?></h2>
					<hr>
                    <span name="ter_kal_admin_help" class="ter_kal_admin_title"><?php 
    esc_html_e( 'Infos for Installation', 'termin-kalender' );
    ?></span>
                    <div style="display: none;">
                            <br>
                            <?php 
    esc_html_e( "If you encounter any issues: Review our Help & FAQ on our website. If you can't find a solution, feel free to contact us. We're happy to help!", 'termin-kalender' );
    ?>
                            <br><hr>
                            <?php 
    esc_html_e( 'If changes in the calendar does not show immediately, try to EXCLUDE the calendar page in your caching plugin settings.', 'termin-kalender' );
    require_once TER_KAL_PLUGIN_DIR . 'admin/admin_detect_caching.php';
    ter_kal_detect_caching_plugins_get_instructions( TER_KAL_TK_HEADLINE );
    ?>
                            <hr>
                            <?php 
    esc_html_e( 'Right after installation, the calendar is shown to all visitors of your site. Depending on Template and Plugins you use, think about restricting the access to the page with the calendar to your team or usergroup. For example, go to admin, page, quick edit, use Password or Privat option', 'termin-kalender' );
    ?>
                            <hr>
                            <?php 
    esc_html_e( 'In the free version of the calendar, only YOU (admins) can add, edit and delete calendar entrys. Get the PRO version, if you like to give users the right to manage calendar entries.', 'termin-kalender' );
    ?>
                    </div>
					<hr>
					<a href="https://termin-kalender.pro" class="button" target="_blank"><?php 
    echo esc_html__( 'Help Website', 'termin-kalender' );
    ?></a>
					<hr>
					<?php 
    //if (ter_kal_fs()->is__premium_only()) {
    ?>
					<a href="<?php 
    echo esc_url( admin_url( 'admin.php?page=ter_kal_terminkalender-contact' ) );
    ?>" class="button"><?php 
    echo esc_html__( 'Ask for help', 'termin-kalender' );
    ?></a>
                    <hr>
					<?php 
    //}
    ?>
                    <h2 class="title"><?php 
    ter_kal_lizenz_info();
    ?></h2>
                    <?php 
    ter_kal_aktueller_benutzer();
    require_once TER_KAL_PLUGIN_DIR . 'admin/admin_info_pro.php';
    ter_kal_admin_info_pro();
    ?>
            </div>
    </div>
    <?php 
    // return;
}
