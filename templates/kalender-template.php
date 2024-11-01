<?php

/*
Template Name: Termin-Kalender Full Page
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
 <html>
 <head>
 	<title>Termin-Kalender Full Page</title>
<?php 
wp_head();
$tk_benutzerrechte = ter_kal_benutzerrechte_pruefen();
//bearbeiten  loeschen  (zeigen oder LEER)
?>
</head>
<body>
<div class="tk-full-browser-content">
<!-- Bereiche -->
<div class="kategoriezeile ter_kal_font">
    <?php 
$terminkategorie = get_option( 'ter_kal_kategorien' );
?><a href="<?php 
echo esc_url( home_url() );
?>"><button type="button" class="btn btn-primary kat_btn"><span class="fc-icon fc-icon-chevron-left"  ></span> HOME </button></a> &nbsp;&nbsp;
	<?php 
foreach ( $terminkategorie as $kategorie ) {
    ?>
		<div name="bereich_button" class="kat_btn" style="background-color:<?php 
    echo esc_attr( $kategorie['backgroundColor'] );
    ?>; color:<?php 
    echo esc_attr( $kategorie['textColor'] );
    ?>; ">
		    <span class="tk_kalender_button_icons dashicons dashicons-<?php 
    echo esc_attr( $kategorie['icon'] );
    ?>" style="background-color:<?php 
    echo esc_attr( $kategorie['backgroundColor'] );
    ?>; color:<?php 
    echo esc_attr( $kategorie['textColor'] );
    ?>;"></span>
		     <?php 
    if ( is_user_logged_in() ) {
        ?><a href="<?php 
        echo esc_url( admin_url( 'admin.php?page=terminkalender_termin_kategorien' ) );
        ?>" style="text-decoration: none; color: inherit;"><?php 
    }
    ?>
		     <b class="tk_kalender_button_text"><?php 
    echo esc_html( $kategorie['kategorie'] );
    ?></b>
		    <?php 
    if ( is_user_logged_in() ) {
        ?></a><?php 
    }
    ?>
		</div>
		<?php 
}
if ( $tk_benutzerrechte == 'bearbeiten' || $tk_benutzerrechte == 'loeschen' ) {
    ?>
		&nbsp;<a href="#" name="show_ter_kal_help" id="show_ter_kal_help"><button type="button" class="btn btn-primary kat_btn"><span class="dashicons dashicons-admin-generic" style="font-size: 1.5em;" ></span>&nbsp;&nbsp;</button></a>
		<?php 
} else {
    ?>
            &nbsp;<a  href="<?php 
    echo wp_login_url();
    ?>"><button type="button" class="btn btn-outline-primary kat_btn btn-sm"><?php 
    echo esc_html__( 'Log in to edit tasks', 'termin-kalender' );
    ?></button></a>
        <?php 
}
?>
</div>
<div id="formattedDate" type="hidden" name="formattedDate" style="display: none;">Formatiertes Datum</div>
<div name="ter_kal__mouseover_info" id="ter_kal__mouseover_info" class="ter_kal__mouseover_info_div">&nbsp;</div>
<div name="ter_kal__mouseover_info2" id="ter_kal__mouseover_info2" class="ter_kal__mouseover_info_div">&nbsp;</div>
<!-- Kalender -->
<div id="termin-kalender-calendar" name="termin-kalender" class="ter_kal_kalender"></div><!-- end kalender -->
<!-- mouse over info -->
<div name="ter_kal__mouseover_info" id="ter_kal__mouseover_info3" class="ter_kal__mouseover_info_div">&nbsp;</div>
<div name="ter_kal__mouseover_info2" id="ter_kal__mouseover_info4" class="ter_kal__mouseover_info_div">&nbsp;</div>
<!-- Modal -->
<div class="modal fade" id="kalender_modal" tabindex="-1" role="dialog" aria-labelledby="kalender_modalLabel" aria-hidden="true">
    <div id="termin-kalender-modal" class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-md-down" role="document">
        <div class="modal-content">
            <div class="modal-header">
               <?php 
wp_nonce_field( 'ter_kal_nonce', 'ter_kal_nonce_id' );
?>
                <h5 class="modal-title" id="kalender_modalLabel">Termin-Kalender</h5>
				<button type="button" class="btn-close ter_kal_submit_reload" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
				<div class="container-fluid">
                <?php 
//	if (isset($row->id)) {
// im js ..._kalender_termin_zeigen_form(); //  TODOS:  ANZEIGEN oder AENDERN
// ter_kal_terminkalender_formular_ausgabe($row, $terminkategorie);
//	} else {
$row = '';
ter_kal_terminkalender_formular( $row, $terminkategorie );
// TODOS: NEUEINTRAG
//	}
?>
				</div>
            </div>
            <div class="modal-footer">
                <?php 
if ( $tk_benutzerrechte == 'bearbeiten' || $tk_benutzerrechte == 'loeschen' ) {
    ?>
                        <button type="button" name="termin_bearbeiten" id="termin_bearbeiten" class="btn btn-primary" style="display: none;"><span class="dashicons  dashicons-lock"></span><?php 
    esc_html_e( 'Edit', 'termin-kalender' );
    ?></button>
                        <button type="button" name="termin_aktualisieren" id="termin_aktualisieren" class="btn btn-primary ter_kal_submit_update" style="display: none;"><span class="dashicons  dashicons-unlock"></span><?php 
    esc_html_e( 'Update', 'termin-kalender' );
    ?></button>
                        <button type="button" name="submit" id="termin_speichern" class="btn btn-primary ter_kal_submit_update"><?php 
    esc_html_e( 'Save', 'termin-kalender' );
    ?></button>
						<button type="button" name="termin_duplizieren" id="termin_duplizieren" class="btn btn-light ter_kal_submit_update" style="display: none;" ><span class="dashicons dashicons-admin-page"></span><?php 
    esc_html_e( 'Duplicate', 'termin-kalender' );
    ?></button>
					<?php 
} else {
    ?>
						<p class="infotext" > <?php 
    esc_html__( 'No user rights to save or edit appointments', 'termin-kalender' );
    ?> </p><?php 
}
if ( $tk_benutzerrechte == 'loeschen' ) {
    ?>
                        <button type="button" name="termin_loeschen" id="termin_loeschen" class="btn btn-danger ter_kal_submit_update" style="display: none;"><?php 
    esc_html_e( 'Delete', 'termin-kalender' );
    ?></button>
                    <?php 
} else {
    ?>
						<p class="infotext" > <?php 
    esc_html__( 'Not sufficient user rights to delete appointments', 'termin-kalender' );
    ?></p><?php 
}
?>
                        <button type="button" class="btn btn-secondary ter_kal_submit_reload" id="schliessen" data-bs-dismiss="modal"><?php 
esc_html_e( 'Close', 'termin-kalender' );
?></button>
						<div class="benutzer_rechte"><a href="https://termin-kalender.pro" target="_blank" style="text-decoration: none; color: inherit;"><?php 
ter_kal_lizenz_info();
?></a>  <?php 
// echo  ter_kal_aktueller_benutzer();
?></div>
			</div>
        </div>
    </div>
</div> <!-- end modal -->
</div> <!-- end full browser -->
<input id="tk_benutzerrechte" type="hidden" name="tk_benutzerrechte" value="<?php 
echo esc_attr( $tk_benutzerrechte );
?>">
</body>
<footer>
<script>
// jQuery(document).ready(function($) {  funktioniert hier nicht wegen fullcalendar
document.addEventListener('DOMContentLoaded', function() {
	ter_kal_initializeFullCalendar();
<?php 
?>
jQuery(function($) {
		// Remove standard WordPress and theme template elements
    	$('#masthead, #wpadminbar, .site-header, .entry-title, .page-title, #colophon, .site-footer, #secondary, .widget-area, .sidebar, .post-meta, .nav-links, .comments-area, .entry-footer').remove();
	     // jQuery code to remove all stylesheets containing 'wp-content/themes/' in their path
	   $('link[rel="stylesheet"]').each(function() {
	    var href = $(this).attr('href');
	    if(href && href.includes('wp-content/themes/')) { $(this).remove(); }
	   });
		$('script').filter((_, el) => el.src.includes('wp-content/themes/')).remove();
			window.calendar.addEventSource( <?php 
ter_kal_event_daten_return();
?> );
		<?php 
if ( wp_is_mobile() ) {
    // Code to execute if the user is on a mobile device
    ?>
			 window.calendar.changeView('listWeek'); // small
			 window.calendar.setOption('headerToolbar', { left: 'prev,next,today', center: 'title', right: 'dayGridMonth,listWeek' });
			  $('#termin-kalender-modal').attr('class', 'modal-dialog modal-dialog-scrollable modal-fullscreen');
		    var elements = document.querySelectorAll("#ter_kal__mouseover_info, #ter_kal__mouseover_info2, #ter_kal__mouseover_info3, #ter_kal__mouseover_info4");
		    elements.forEach(function(element) {
		    element.remove()
			});
		<?php 
} else {
    // Code to execute if the user is not on a mobile device
    ?>
			//window.calendar.changeView('dayGridMonth'); // big  Standardeinstellung nicht nötig zu wechseln
		<?php 
}
?>
			window.calendar.render();
}); // end jquery
}); // end event listener
</script>
<?php 
wp_footer();
$must_resize = ter_kal_check_template_must_resize_list();
if ( wp_is_mobile() && $must_resize ) {
    echo '<style type="text/css">';
    require_once TER_KAL_PLUGIN_DIR . 'templates/theme_resize.css';
    echo '</style>';
}
?>
</footer>
</html>
