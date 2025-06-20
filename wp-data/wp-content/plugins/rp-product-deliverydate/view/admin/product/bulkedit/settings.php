<br><h4><?php echo __(self::$plugin_title,self::$textdomain) ?></h4>
<div class="inline-edit-group product-delivery-field">
    <label class="product-delivery-field-time">
        <span><?php echo esc_html__('Delivery time: ',self::$textdomain); ?></span>
        <input type="text" value="" name="est_delivery_time"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
    </label>
    <?php if($this->getSetting("enable_carrier")): ?>
        <label class="product-delivery-field-time">
    	<span><?php echo esc_html__('Delivery time to carrier: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="carrier_est_delivery_time"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
        </label>
    <?php endif; ?>
    <label class="product-delivery-field-text">
        <span><?php echo esc_html__('Delivery date text: ',self::$textdomain); ?></span>
        <input type="text" value="" name="est_delivery_text"><br>
    </label>
    <label class="product-delivery-field-text">
        <span><?php echo esc_html__('Alternative delivery date text: ',self::$textdomain); ?></span>
        <input type="text" value="" name="alt_est_delivery_text"><br>
    </label>
    
    <?php if(!$this->getSetting("hide_backorder")): ?>
        <label class="product-delivery-field-time">
    	<span><?php echo esc_html__('Delivery time for backorder: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_time_backorder"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
        </label>
	<?php if($this->getSetting("enable_carrier")): ?>
	    <label class="product-delivery-field-time">
		<span><?php echo esc_html__('Delivery time to carrier for backorder: ',self::$textdomain); ?></span>
		<input type="text" value="" name="carrier_est_delivery_time_backorder"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
	    </label>
	<?php endif; ?>
        <label class="product-delivery-field-text">
    	<span><?php echo esc_html__('Delivery text for backorder: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_text_backorder"><br>
        </label>
        <label class="product-delivery-field-text">
    	<span><?php echo esc_html__('Alternative delivery text for backorder: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="alt_est_delivery_text_backorder"><br>
        </label>
    <?php endif; ?>
    
    <?php if(!$this->getSetting("hide_out_of_stock")): ?>
        <label class="product-delivery-field-time">
    	<span><?php echo esc_html__('Delivery time for out of stock: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_time_outofstock"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
        </label>
	<?php if($this->getSetting("enable_carrier")): ?>
	    <label class="product-delivery-field-time">
		<span><?php echo esc_html__('Delivery time to carrier for out of stock: ',self::$textdomain); ?></span>
		<input type="text" value="" name="carrier_est_delivery_time_outofstock"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
	    </label>
	<?php endif; ?>
        <label class="product-delivery-field-text">
    	<span><?php echo esc_html__('Delivery text for out of stock: ',self::$textdomain); ?></span>
    	<input type="text" value="" name="est_delivery_text_outofstock"><br>
        </label>
    <?php endif; ?>
    
    <label class="product-delivery-field-text">
        <span><?php echo esc_html__('Delivery text on order page: ',self::$textdomain); ?></span>
        <input type="text" value="" name="est_order_delivery_text"><br>
    </label>
</div>
