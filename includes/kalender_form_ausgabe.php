<?php

defined( 'WPINC' ) || die;
function ter_kal_terminkalender_formular_ausgabe(  $row, $terminkategorie  ) {
    $row_ort = $row->Ort;
    $encoded_location = urlencode( $row_ort );
    $google_maps_url = "https://www.google.com/maps/search/?api=1&query={$encoded_location}";
    $row_notizen = $row->Notizen;
    $teilnehmer_array = ter_kal_event_teilnehmerliste( $row );
    ?>
<input id="id" name="id" type="hidden" value="<?php 
    echo esc_attr( $row->id );
    ?>">
<span class="tk_modal_zeit_title">  formattedDate  </span>
<hr><?php 
    echo wp_kses_post( nl2br( $row->Beschreibung ) );
    ?>
<div class="row">
    <div class="col">
        <hr>
        <?php 
    if ( !empty( trim( $row_ort ) ) ) {
        ?>
    	<div id="ter_kal_google_ort" class="tk_form_text">
    		<a href="<?php 
        echo esc_url( $google_maps_url );
        ?>" target="_blank">
    			<?php 
        echo esc_html( $row_ort );
        ?>
    		</a>
    	</div>
        <hr>
        <?php 
    }
    ?>
        <?php 
    if ( !empty( $teilnehmer_array ) ) {
        ?>
            <?php 
        echo $teilnehmer_array;
        ?>
            <hr>
        <?php 
    }
    ?>
    </div>
<?php 
    if ( !empty( $row_notizen ) ) {
        ?>
    <div class="col ter_kal_notes" style="margin-right: 1em;">
       <?php 
        echo wp_kses_post( nl2br( $row_notizen ) );
        ?>
    </div>
<?php 
    }
    ?>
</div>
        <?php 
    // end is__premium_only
}
