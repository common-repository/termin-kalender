<?php
// Exit if accessed directly.
defined('WPINC') || die;
function ter_kal_admin_info_pro() {
?>
<h2 class="title admin_red"><?php esc_html_e('Termin-Kalender PRO Version - Upgrade Your Calendar!', 'termin-kalender');?></h2>

<?php esc_html_e('Get advanced features and unlock the full potential of your calendar.', 'termin-kalender');?>
<br><hr><b>
 <?php esc_html_e("Here's what you get with the PRO version:", 'termin-kalender');?>
</b><br><hr><b>
 <?php esc_html_e('Form Fields:', 'termin-kalender');?>
</b>
 <?php esc_html_e('Capture additional details in your calendar entries. Add fields for phone numbers, addresses, mileage, or anything specific to your needs.', 'termin-kalender');?>
<hr><b>
 <?php esc_html_e('Task repetition:', 'termin-kalender');?>
</b>
 <?php esc_html_e('Weekly, Monthly, Yearly recurring events.', 'termin-kalender');?>
<hr><b>
 <?php esc_html_e('Schedule participants and send invites.', 'termin-kalender');?>
 <hr>
 <?php esc_html_e('Calendar Users', 'termin-kalender');?>:
</b>
 <?php esc_html_e('Manage user access. Assign edit or edit & delete permissions to specific users registered in your WordPress.', 'termin-kalender');?>
<hr><b>
 <?php esc_html_e('Backup', 'termin-kalender');?>
</b>
 <?php esc_html_e('Protect your schedule. Download backups to your computer for safekeeping. Restore or transfer your calendar data to another WordPress site with ease.', 'termin-kalender');?>
<hr><b>
<?php esc_html_e('Request form Gutenberg block:', 'termin-kalender');?>
</b>
 <?php esc_html_e('Capture visitor input for appointments, bookings, etc. Lets you pick a category for reservation requests.', 'termin-kalender');?>
<hr><b>
	<?php esc_html_e('List Gutenberg block:', 'termin-kalender');?>
</b>
 <?php esc_html_e('Display specific categories on your site. To display a list of events with pictures.', 'termin-kalender');?>
<hr><b>
 <?php esc_html_e('Support Development:', 'termin-kalender');?>
</b>
 <?php esc_html_e("Your purchase helps us keep improving Termin-Kalender! Share your ideas and let us know what new features you'd like to see.", 'termin-kalender');?>
<hr>
<h1>
	<a class="button" href="<?php echo esc_html(TER_KAL_UPGRADE_URL); ?>">
    <?php esc_html_e('Upgrade today', 'termin-kalender');?></a>
</h1>
<b>
	<?php esc_html_e(' and take your calendar organization to the next level!', 'termin-kalender');?>
</b>
<?php
// return;
} // end ter_kal_admin_info_pro
