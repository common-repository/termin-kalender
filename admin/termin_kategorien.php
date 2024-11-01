<?php
    // Exit if accessed directly.
    defined('WPINC') || die;

function ter_kal_termin_kategorien_page() {

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ter_kal_nonce']) && wp_verify_nonce($_POST['ter_kal_nonce'], 'ter_kal_nonce')) {
		if (isset($_POST['old_id'])) {  // delete kategorie set content to new id
		 $old_id = absint(isset($_POST['old_id']) ? $_POST['old_id'] : ''); // zu löschende kategorie ID
		 $new_id = absint(isset($_POST['new_id']) ? $_POST['new_id'] : '1'); // ausgewählte kategorie id zum ersetzen der gelöschten
		 echo $old_id.' mit '.  $new_id.' ersetzt';
		 global $wpdb;
	     	$wpdb->query('UPDATE '.TER_KAL_TERMIN_DB.' SET kategorie_id = '.$new_id.' WHERE kategorie_id = '.$old_id );
$options = get_option('ter_kal_kategorien');
foreach ($options as $key => $value) {
    if (isset($value['kategorie_id']) && $value['kategorie_id'] == $old_id) {
        unset($options[$key]);
        break; // Assuming kategorie_id is unique, we can break out of the loop
    }
}
update_option('ter_kal_kategorien', $options);
	   } elseif (isset($_POST['ter_kal_kategorien'])) {  // update kategorien
		    $kategorien = $_POST['ter_kal_kategorien'];
		    update_option('ter_kal_kategorien', $kategorien);
		}
	} // end POST

	$dashicons = get_option('ter_kal_dashicon_list');
   	$kategorien = get_option('ter_kal_kategorien');
	$benutzerrechte = ter_kal_benutzerrechte_pruefen();
	// Extract all kategorie_id from $kategorien
	$kategorie_ids = array_column($kategorien, 'kategorie_id');
	// Find the lowest free number within kategorie_id
	$lowest_free_kategorie_id = 1;
	while (in_array($lowest_free_kategorie_id, $kategorie_ids)) {
	    $lowest_free_kategorie_id++;
	}


?>
	<h1>
		<?php esc_html_e('Color-Coded Control: Calendar Areas', 'termin-kalender');?><span class="label label-free" >Free</span>
	</h1>
    <div class="wrap">

	<h3>
		<?php esc_html_e('Create Custom Categories for Your Calendar', 'termin-kalender');?>
	</h3>
    <?php ter_kal_banner();?>
	<br>
		<span name="ter_kal_admin_help" class="ter_kal_admin_title" ><?php esc_html_e('Calendar Areas: Organize your schedule with custom categories.', 'termin-kalender');?></span>
	<div style="display: none;" class="" ><br>
		<?php esc_html_e('Create sections for courses, rooms, employees, rental cars, events, meetings or anything you need. Customize each with colors and icons for easy identification.', 'termin-kalender');?>
	</div>
	<br>
        <form id="kat_update" method="post" action="">
        <?php wp_nonce_field('ter_kal_nonce', 'ter_kal_nonce');?>
		<input type="hidden" name="lowest_free_kategorie_id" id="lowest_free_kategorie_id" value="<?php echo $lowest_free_kategorie_id ?>" >
            <table class="form-table widefat striped">
                <thead>
                    <tr>
                        <th style="text-align: center;">Kategorie</th>
                        <th style="text-align: center;">Background Color</th>
                        <th style="text-align: center;">Text Color</th>
						<th></th>
						<th id="icon_info_scrolldown" class="ter_kal_admin_title" >Icon</th>
                        <th></th>
						<th></th>
                    </tr>
                </thead>
                <tbody id="kategorien-rows">
                    <?php if (!empty($kategorien)) : ?>
                        <?php foreach ($kategorien as $index => $kategorie) : ?>
                            <tr id="row<?php echo esc_attr($kategorie['kategorie_id']); ?>" >
                                <td style="background-color: <?php echo esc_attr($kategorie['backgroundColor']); ?>; color: <?php echo esc_attr($kategorie['textColor']); ?>; ">
                                    <input type="text" name="ter_kal_kategorien[<?php echo $index; ?>][kategorie]" value="<?php echo esc_attr($kategorie['kategorie']); ?>" style="font-weight: bold;" />
                                </td>
                                <td style="text-align: center;"><input type="color" name="ter_kal_kategorien[<?php echo $index; ?>][backgroundColor]" value="<?php echo esc_attr($kategorie['backgroundColor']); ?>" /></td>
                                <td style="text-align: center;"><input type="color" name="ter_kal_kategorien[<?php echo $index; ?>][textColor]" value="<?php echo esc_attr($kategorie['textColor']); ?>" /></td>
								<td style="display: flex; justify-content: center;"><span name="show_selected_icon" class="tk_kalender_icons dashicons  dashicons-<?php echo esc_attr($kategorie['icon']); ?>" style="background-color: <?php echo esc_attr($kategorie['backgroundColor']); ?>; color: <?php echo esc_attr($kategorie['textColor']); ?>;" ></span></td>
								<td >
                                <select name="icon_selector" >
                                  <?php foreach ($dashicons as $dashicon) { ?>
                    				<option name="ter_kal_kategorien[<?php echo $index; ?>][icon]"  id="ter_kal_kategorien[<?php echo $index; ?>][icon]" value="<?php echo esc_attr($dashicon); ?>" <?php echo ($dashicon === $kategorie['icon']) ? 'selected="selected"' : ''; ?>>
									 <span class="dashicons dashicons-admin-site"></span>
									<?php echo esc_html($dashicon); ?>
                    				</option>
                                <?php } ?>
								</select>
								<input type="hidden" name="ter_kal_kategorien[<?php echo $index; ?>][icon]" value="<?php echo esc_attr($kategorie['icon']); ?>" />
		 						</td>
                                <td><?php submit_button(); ?>
								<input type="hidden" name="ter_kal_kategorien[<?php echo $index; ?>][kategorie_id]" value="<?php echo esc_attr($kategorie['kategorie_id']); ?>" readonly /></td>
								<td>
									<?php if ($benutzerrechte == 'loeschen') { ?>
									<button type="button" class="delete-row button-secondary" data-kategorie_id="<?php echo esc_attr($kategorie['kategorie_id']); ?>" data-kategorie="<?php echo esc_attr($kategorie['kategorie']); ?>">Delete Category</button>
									<?php } ?>
								</td>
							</tr>
	                        <?php endforeach; ?>
	                    <?php endif; ?>
	                </tbody>
	            </table><br>
	            <button type="button" class="button-secondary" id="add-row">Add new Category</button>
	    </form>
	</div>

<?php
 if ($benutzerrechte == 'loeschen') {
?>
<div id="ter_kal_delete_category" class="card" style="display: none;">
	<form id="kat_delete" method="post" action="">
		<?php wp_nonce_field('ter_kal_nonce', 'ter_kal_nonce');?>
		<h2 class="title">
		<?php echo esc_html_e('DELETE category', 'termin-kalender'); ?> : <b id="old_kategorie_titel">Kategorie</b>
		</h2>
		<?php echo esc_html_e('Heads Up! Events Still Scheduled: There might still be events assigned to this category. To delete, please choose a new category for those events first.', 'termin-kalender'); ?>
		<br> <br>
		<input type="hidden" id="old_id" name="old_id" value="0">
		<?php echo esc_html_e('Move the events to category', 'termin-kalender'); ?>&nbsp;
           <?php
		   $firstRow = true;
		   foreach ($kategorien as $row) {
		   	 if ($firstRow) {?>
				<input type="hidden" id="new_id" name="new_id" value="<?php echo esc_attr($row['kategorie_id']); ?>">
				<select name="change_kategorie" id="change_kategorie">
	       			<option  value="<?php echo esc_attr($row['kategorie_id']); ?>" selected="selected">
	       			<?php echo esc_attr($row['kategorie']); ?></option>
		       		<?php $firstRow = false;
				} else { ?>
					<option  value="<?php echo esc_attr($row['kategorie_id']); ?>"><?php echo esc_attr($row['kategorie']); ?></option>
				<?php } } ?>
           		</select>

		<?php echo esc_html_e('and then delete category', 'termin-kalender'); ?>:<br><br>
		<button type="submit" id="ter_kal_change_delete_kategorie" name="ter_kal_change_delete_kategorie" data-tr_to_remove="<?php echo esc_attr($row['kategorie_id']); ?>" class="button-primary t_k_primary-red tk-bigger-button"><b id="old_kategorie">Kategorie</b></button>
		<br><br> <hr>
		<button  type="reset" id="cancel_delete" name="cancel_delete" class=""><?php echo esc_html_e('Cancel', 'termin-kalender'); ?></button>
	</form>
</div>
<?php } ?>
	<br id="icon_scrollpoint">
	<span name="ter_kal_admin_help"  class="ter_kal_admin_title" ><?php esc_html_e('Icons preview', 'termin-kalender');?></span>
	<div style="display: none;" class="dashicon-container" >
	    <?php foreach ($dashicons as $dashicon) { ?>
	        <div class="dashicon-item">
	            <span class="dashicons dashicons-<?php echo esc_attr($dashicon); ?>"></span>
				<br>
				<?php echo esc_html($dashicon); ?>
	        </div>
	    <?php } ?>
	</div>
	<hr>


<script type="text/javascript">
// document.ready scheint nur in separatem script tag zu funktionieren ??
jQuery(document).ready(function($){
	$('#add-row').on('click', function() {
            var table = $('#kategorien-rows');
			$('#add-row').hide(500);
			var lowest_free_kategorie_id = $('#lowest_free_kategorie_id').val();
		    var nextRowId = lowest_free_kategorie_id +'00';
  	    	function getRandomColor() {  return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'); }
		    var randomBackgroundColor = getRandomColor();
			var dashicons = <?php echo json_encode($dashicons); ?>;
    		var randomIcon = dashicons[Math.floor(Math.random() * dashicons.length)];
		    var newRow = `
				<tr id="row${nextRowId}">
		            <td style="background-color: ${randomBackgroundColor}; color: #000000;">
		                <input type="text" name="ter_kal_kategorien[${nextRowId}][kategorie]" value="Kategorie ${lowest_free_kategorie_id}" >
		            </td>
		            <td style="text-align: center;">
		                <input type="color" name="ter_kal_kategorien[${nextRowId}][backgroundColor]" value="${randomBackgroundColor}" >
		            </td>
		            <td style="text-align: center;">
		                <input type="color" name="ter_kal_kategorien[${nextRowId}][textColor]" value="#ffffff" >
		            </td>
		            <td style="display: flex; justify-content: center;">
		                <span name="show_selected_icon" class="tk_kalender_icons dashicons dashicons-${randomIcon}" style="background-color: ${randomBackgroundColor}; color: #ffffff;"></span>
		            </td>
		            <td>
						${randomIcon}
						<input type="hidden" name="ter_kal_kategorien[${nextRowId}][icon]" value="${randomIcon}" >
		            </td>
		            <td>
						<?php submit_button(); ?>
		            </td>
					<td>
						<input type="hidden" name="ter_kal_kategorien[${nextRowId}][kategorie_id]" value="${lowest_free_kategorie_id}" readonly >
		            </td>
		        </tr>
		    	`;
            table.append(newRow);
    });

	$('[name="icon_selector"]').change(function() {
	    var selectedValue = $(this).val();
		//var offset = $(this).offset();
		var index = $(this).closest('tr').find('input[type="hidden"][name^="ter_kal_kategorien["]').attr('name').match(/\[(\d+)\]/)[1];
		$(this).closest('tr').find('input[type="hidden"][name="ter_kal_kategorien[' + index + '][icon]"]').val(selectedValue);
        $(this).closest('tr').find('[name="show_selected_icon"]').attr('class', 'tk_kalender_icons dashicons dashicons-' + selectedValue);
	});


	$('#change_kategorie').change(function() {
	    var selectedValue = $(this).val();
		$('#new_id').val(selectedValue);
	});


  	$('#kategorien-rows').on('click', '.delete-row', function() {
		var kategorie = $(this).data('kategorie');
		var kategorie_id = $(this).data('kategorie_id');
		$('#old_kategorie').html( kategorie );
		$('#old_kategorie_titel').html(kategorie);
		$('#old_id').val( kategorie_id );
	  	$('#ter_kal_delete_category').show(500);
		$('#kat_update').hide(500);
	});


  	$('#ter_kal_change_delete_kategorie').on('click', function() {
		var kategorie_id = $(this).data('tr_to_remove');
	 });


	$('#cancel_delete').on('click', function() {
	   $('#ter_kal_delete_category').hide(500);
	   $('#kat_update').show(500);
	});

$('#icon_info_scrolldown').on('click', function() {
	//alert('hit');
   $('html, body').animate({
       scrollTop: $('#icon_scrollpoint').offset().top
   }, 1000);
});

/**/
});
</script>

<?php
// return;
  } //end ter_kal_termin_kategorien_page