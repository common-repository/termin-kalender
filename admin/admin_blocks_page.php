<?php
// Exit if accessed directly.
defined('WPINC') || die;
function ter_kal_terminkalender_settings() {
?>
	<h1><?php esc_html_e('Gutenberg Blocks', 'termin-kalender'); ?><span class="label label-free" >Free</span><span class="label label-pro" >PRO</span></h1>
    <hr>
	<?php ter_kal_banner(); ?>
	<h2><?php esc_html_e('List of available frontend features through Gutenberg blocks', 'termin-kalender') ?></h2>
    <span name="ter_kal_admin_help" class="ter_kal_admin_title"><?php esc_html_e('Choose this Gutenberg Blocks in the frontend Gutenberg editor when you edit a page.', 'termin-kalender');?></span>
    <div style="display: none;"><br>
        <?php esc_html_e('Use the Gutenberg Editor built in Wordpress to use this blocks', 'termin-kalender') ?>
        <br><br>
        <b><?php esc_html_e('For DIVI and other Editor user', 'termin-kalender') ?>:</b><br> <?php esc_html_e('Use a text block and the shortcode my-termin-kalender in square brackets.', 'termin-kalender') ?><br>
        <?php esc_html_e('In the vast majority of use cases, the standard fields are sufficient.', 'termin-kalender') ?><br>
        <br>
        <b><?php esc_html_e('For Elementor user', 'termin-kalender') ?>:</b>
        <?php esc_html_e('Use the Elementor Shortcode Widget with the Shortcode my-termin-kalender in square brackets.', 'termin-kalender') ?>
        <br>
    </div>
    <br>
<hr>
<div class="card">
<h3 style="display: flex; align-items: center;">
	<?php esc_html_e( 'my easy Termin-Kalender CALENDAR Block' , 'termin-kalender' ); ?>
	<span class="label label-free" >Free</span>
</h3>
<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('The main block for the calendar', 'termin-kalender');?></span>
<div style="display: none;" class="" ><br>
	<?php esc_html_e('Your calendar for the monthly overview. All data from the other blocks will be added to or taken from this Calendar. Click on the first page of this menu to find the link to create a calendar. And forward to the calendar.', 'termin-kalender');?>
</div>
</div>

<div class="card">
<h3 style="display: flex; align-items: center;">
	<?php esc_html_e( 'Simple To-Do Tasklist' , 'termin-kalender' ); ?>
	<span class="label label-free" >Free</span>
</h3>
<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('To-Do list: Free GutenBerg Block', 'termin-kalender');?></span>
<div style="display: none;" class="" ><br>
	<?php esc_html_e('Just a simple To-Do list for your tasks and notes in the Backend. But also available with this free GutenBerg Block', 'termin-kalender');?>
</div>
</div>

<div class="card">
<h3 style="display: flex; align-items: center;">
	<?php esc_html_e( 'Simple Appointment List' , 'termin-kalender' ); ?>
	<span class="label label-free" >Free</span>
</h3>
<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('Appointment list: Free GutenBerg Block', 'termin-kalender');?></span>
<div style="display: none;" class="" ><br>
	<?php esc_html_e('A simple and free Gutenberg Block to show a appointments list. For more configuration options use the PRO Event List', 'termin-kalender');?>
</div>
</div>

<div class="card">
<h3 style="display: flex; align-items: center;">
	<?php esc_html_e( 'Reservation Form Block' , 'termin-kalender' ); ?>
	<span class="label label-beta" >Beta</span><span class="label label-pro" >PRO</span>
</h3>
<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('Adding the Reservation Form Block', 'termin-kalender');?></span>
<div style="display: none;" class="" ><br>
	<?php esc_html_e('..lets you pick a category for reservation requests. This category will then be used to label all requests on the calendar.', 'termin-kalender');?>
</div>
</div>
<div class="card">
<h3 style="display: flex; align-items: center;">
  <?php esc_html_e( 'Event List Block' , 'termin-kalender' ); ?>
  <span class="label label-beta" >Beta</span><span class="label label-pro" >PRO</span>
</h3>
<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('To display a list of events with pictures:', 'termin-kalender');?></span>
<div style="display: none;" class="" ><br>
	<?php esc_html_e('Add the "Termin-Kalender Event List" block (Gutenberg) and choose the category you like to show.', 'termin-kalender');?>
</div>
</div>
<?php
// return;
} // end terminkalender settings