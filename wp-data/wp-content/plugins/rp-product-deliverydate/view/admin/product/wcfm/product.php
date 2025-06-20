<?php
$estMeta = get_post_meta($product_id, self::$meta_key, true);
?>
<!-- collapsible  - Delivery Date -->
<div class="page_collapsible products_manage_delivery_date simple variable " id="wcfm_products_manage_form_delivery_date_head"><label
        class="wcfmfa fa-link"></label>
    <?php _e('Delivery time settings', self::$textdomain); ?><span></span>
</div>
<div class="wcfm-container simple variable">
    <div id="wcfm_products_manage_form_delivery_date_expander" class="wcfm-content">
        <p class="wcfm_title"><strong>
                <?php echo esc_html__('Delivery Time', self::$textdomain); ?>
            </strong></p>
        <label class="screen-reader-text">
            <?php echo esc_html__('Delivery Time', self::$textdomain); ?>
        </label>
        <input type="number"
            value="<?php echo isset($estMeta['esttime']) ? stripslashes(esc_html__($estMeta['esttime'])) : ""; ?>"
            name="esttime" class="wcfm-text">
        <?php if (!$this->getSetting("hide_backorder")): ?>
            <p class="wcfm_title"><strong>
                    <?php echo esc_html__('Delivery time for backorder:', self::$textdomain); ?>
                </strong></p>
            <label class="screen-reader-text">
                <?php echo esc_html__('Delivery time for backorder:', self::$textdomain); ?>
            </label>
            <input type="number"
                value="<?php echo isset($estMeta['esttime_backorder']) ? stripslashes(esc_html__($estMeta['esttime_backorder'])) : ""; ?>"
                name="esttime_backorder" class="wcfm-text">
        <?php endif; ?>
        <?php if (!$this->getSetting("hide_out_of_stock")): ?>
            <p class="wcfm_title"><strong>
                    <?php echo esc_html__('Delivery time for out of stock:', self::$textdomain); ?>
                </strong></p>
            <label class="screen-reader-text">
                <?php echo esc_html__('Delivery time for out of stock:', self::$textdomain); ?>
            </label>
            <input type="number"
                value="<?php echo isset($estMeta['esttime_outofstock']) ? stripslashes(esc_html__($estMeta['esttime_outofstock'])) : ""; ?>"
                name="esttime_outofstock" class="wcfm-text">
        <?php endif; ?>
    </div>
</div>
<!-- end collapsible -->
<div class="wcfm_clearfix"></div>
