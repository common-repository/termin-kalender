<?php
	defined('WPINC') || die;
	function ter_kal_kalender_help_page() {
		$tk_benutzerrechte = ter_kal_benutzerrechte_pruefen();
		?>
        <b>
        <?php echo ter_kal_lizenz_info(); ?>
        </b>
		<?php if ($tk_benutzerrechte == 'bearbeiten' || $tk_benutzerrechte == 'loeschen') {  ?>
        <p>
        <?php _e('Click a calendar entry to open and see details (and edit, delete, or duplicate it).
        Click on a free space on the date you prefer and add your calendar entry (Appointment, Event, Schedule...).', 'termin-kalender');
        ?>
        </p>
		<br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=terminkalender_termin_kategorien')); ?>" >
            <button class="btn btn-primary justify-content-md-end">
            <?php esc_html_e('Edit the main categories, icon, title and colors', 'termin-kalender');?>
            </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-free" >Free</span>
		<br>
		<br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=terminkalender_custom_fields')); ?>" >
            <button class="btn btn-primary justify-content-md-end">
            <?php esc_html_e('Calendar form settings, add & edit fields', 'termin-kalender');?>
            </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-pro" >PRO</span>
		<br>
		<br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=terminkalender_kalender_benutzer')); ?>" >
            <button class="btn btn-primary justify-content-md-end">
            <?php esc_html_e('User rights', 'termin-kalender');?>
            </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-pro" >PRO</span>
		<br>
		<br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=terminkalender_backup')); ?>" >
            <button class="btn btn-primary justify-content-md-end">
            <?php esc_html_e('Backup & Restore', 'termin-kalender');?>
            </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-pro" >PRO</span>
		<br>
		<br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=terminkalender_gutenberg')); ?>" >
            <button class="btn btn-primary justify-content-md-end">
            <?php esc_html_e('Gutenberg Blocks for the Termin-Kalender', 'termin-kalender');?>
            </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-free" >Free</span><span class="label label-pro" >PRO</span><span class="label label-beta" >Beta</span>
		<br>
        <hr>
    <?php
    $template_info =  'System check '.TER_KAL_TK_VERSION.': '. ter_kal_get_active_template();
     $must_resize = ter_kal_check_template_must_resize_list();
    if ($must_resize) {
        // Code to execute if must_resize is true
    	$template_info .=   ' must resize. ';
    } else {
        // Code to execute if must_resize is false
    	$template_info .=   ', no resize needed. ';
     //$templates = get_installed_and_active_templates(); print_r($templates);
    }
    if ( wp_is_mobile() ) {
        // Code to execute if the user is on a mobile device
        $template_info .= 'This is a mobile device.';
    } else {
        // Code to execute if the user is not on a mobile device
        $template_info .= 'This is not a mobile device.';
    }
    ?>
    <div name="ter_kal__mouseover_info" id="ter_kal__mouseover_info" class="ter_kal__mouseover_info_div"><?php echo $template_info;?></div>
        <hr>
		<br>
                <a href="<?php echo esc_url(admin_url('admin.php?page=ter_kal_terminkalender')); ?>" >
                <button class="btn btn-primary justify-content-md-end">
                <?php esc_html_e('To the calendar main page', 'termin-kalender');?>
                </button></a>&nbsp;&nbsp;&nbsp;<span class="label label-free" >Free</span>&nbsp;&nbsp;&nbsp;
		<?php }
            	echo '<a href="' . esc_url('https://termin-kalender.pro') . '" target="_blank"><button type="button" class="btn btn-primary">'. esc_html__('Termin-Kalender Website', 'termin-kalender') . '</button></a>';
    	wp_die(); // important to stop execution of further code
    }

   add_action('wp_ajax_ter_kal_kalender_help_page', 'ter_kal_kalender_help_page'); // for logged in users
   add_action('wp_ajax_nopriv_ter_kal_kalender_help_page', 'ter_kal_kalender_help_page');



