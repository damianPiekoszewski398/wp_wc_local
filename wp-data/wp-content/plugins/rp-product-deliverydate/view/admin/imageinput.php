<div class="image_input" id="<?php echo $id; ?>">
    <div class="image">
	<?php
	if($value != "" && is_numeric($value)){
	    $ImageData = wp_get_attachment_image_src($value,'thumbnail',true);
	    if($ImageData && count($ImageData) > 0){
		echo '<img src="' . $ImageData[0] . '" width="60px" height="60px" />';
	    }
	}
	?>
    </div>
    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value ?>" class="hidden_imginput" />
    <input type="button" style="display:<?php echo ($value != "" && is_numeric($value))?"none":"block"; ?>" class="button-primary rpesp-upload-button" onclick="objRpesp.openUploader('<?php echo $id; ?>');" value="<?php echo $buttonText; ?>" />
    <span class="rccb_remove_button">
	<?php if($value != "" && is_numeric($value)){ ?>
    	<input type="button" class="button-primary" onclick="objRpesp.removeImage('<?php echo $id; ?>');" value="<?php echo esc_html__('Remove','rrccb') ?>" />
	<?php } ?>
    </span>
</div>
