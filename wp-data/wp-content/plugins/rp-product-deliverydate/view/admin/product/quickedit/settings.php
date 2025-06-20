<br class="clear">
<h4><?php echo __(self::$plugin_title,self::$textdomain) ?></h4>
<div class="inline-quick-edit estdelivery-fields" >
    <br class="clear">
    <label class="delivery-field-time">
        <span ><?php echo esc_html__('Delivery time: ',self::$textdomain); ?></span>
        <input type="text" value="" name="est_delivery_time"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
    </label>
    <?php if($this->getSetting("enable_carrier")): ?>
        <br class="clear">
        <label class="delivery-field-time">
    	<span ><?php echo esc_html__('Delivery time to carrier: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="carrier_est_delivery_time"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
        </label>
    <?php endif; ?>
    <?php if($this->getSetting("show_text_product")): ?>
        <br class="clear">
        <label class="product-delivery-field-text">
    	<span ><?php echo esc_html__('Delivery date text: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_text">
        </label>
        <br class="clear">
        <label class="product-delivery-field-text">
    	<span ><?php echo esc_html__('Alternative delivery text: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="alt_est_delivery_text">
        </label>
    <?php endif; ?>

    <?php if(!$this->getSetting("hide_backorder")): ?>
        <br class="clear">
        <label class="delivery-field-time">
    	<span ><?php echo esc_html__('Delivery time for backorder: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_time_backorder"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
        </label>
	<?php if($this->getSetting("enable_carrier")): ?>
	    <br class="clear">
	    <label class="delivery-field-time">
		<span ><?php echo esc_html__('Delivery time to carrier for backorder: ',self::$textdomain); ?></span>
		<input type="text" value="" name="carrier_est_delivery_time_backorder"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
	    </label>
	<?php endif; ?>
	<?php if($this->getSetting("show_text_product")): ?>
	    <br class="clear">
	    <label class="product-delivery-field-text">
		<span ><?php echo esc_html__('Delivery text for backorder: ',self::$textdomain); ?></span>
		<input type="text" value="" name="est_delivery_text_backorder">
	    </label>
	    <br class="clear">
	    <label class="product-delivery-field-text">
		<span ><?php echo esc_html__('Alternative delivery text for backorder product: ',self::$textdomain); ?></span>
		<input type="text" value="" name="alt_est_delivery_text_backorder">
	    </label>

	<?php endif; ?>
    <?php endif; ?>
    <?php if(!$this->getSetting("hide_out_of_stock")): ?>
        <br class="clear">
        <label class="delivery-field-time">
    	<span ><?php echo esc_html__('Delivery time for out of stock: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_time_outofstock"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
        </label>
	<?php if($this->getSetting("enable_carrier")): ?>
	    <br class="clear">
	    <label class="delivery-field-time">
		<span ><?php echo esc_html__('Delivery time to carrier for out of stock: ',self::$textdomain); ?></span>
		<input type="text" value="" name="carrier_est_delivery_time_outofstock"><small><i>&nbsp;<?php echo esc_html__('In Day ',self::$textdomain); ?></i></small><br>
	    </label>
	<?php endif; ?>
	<?php if($this->getSetting("show_text_product")): ?>
	    <br class="clear">
	    <label class="product-delivery-field-text">
		<span ><?php echo esc_html__('Delivery text for out of stock: ',self::$textdomain); ?></span>
		<input type="text" value="" name="est_delivery_text_outofstock">
	    </label>

	<?php endif; ?>
    <?php endif; ?>
    <?php if($this->getSetting("show_text_product")): ?>
        <br class="clear">
        <label class="product-delivery-field-text">
            <span ><?php echo esc_html__('Delivery text on order page: ',self::$textdomain); ?></span>
            <input type="text" value="" name="est_order_delivery_text">
        </label>
        <br class="clear">
    <?php endif; ?>
</div>
