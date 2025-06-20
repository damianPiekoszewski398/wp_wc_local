<?php
$estMeta = get_post_meta($product_id,self::$meta_key,true);
?>

<div id="rpesp_product_data" class="panel woocommerce_options_panel">
    

    <div class="dokan-other-options dokan-edit-row dokan-clearfix ">
        <div class="dokan-section-heading" data-togglehandler="dokan_other_options">
            <h2><i class="fas fa-cog" aria-hidden="true"></i>
                <?php echo esc_html__('Delivery Time', self::$textdomain); ?>
            </h2>
            <p>
                <?php echo esc_html__('Manage your product delivery time', self::$textdomain); ?>
            </p>

            <div class="dokan-clearfix"></div>
        </div>

        <div class="dokan-section-content">
            <div class="dokan-form-group content-half-part">
                <label for="post_status" class="form-label">
                    <?php echo esc_html__('Delivery time :', self::$textdomain); ?>
                </label>
                <input type="text"
                    value="<?php echo isset($estMeta['esttime']) ? stripslashes(esc_html__($estMeta['esttime'])) : ""; ?>"
                    name="esttime" class="dokan-form-control">
            </div>
            <?php if (!$this->getSetting("hide_backorder")): ?>
                <div class="dokan-form-group content-half-part">
                    <label for="post_status" class="form-label">
                        <?php echo esc_html__('Delivery time for backorder:', self::$textdomain); ?>
                    </label>
                    <input type="text"
                        value="<?php echo isset($estMeta['esttime_backorder']) ? stripslashes(esc_html__($estMeta['esttime_backorder'])) : ""; ?>"
                        name="esttime_backorder" class="dokan-form-control">
                </div>
                <div class="dokan-clearfix"></div>
            <?php endif; ?>
            <?php if (!$this->getSetting("hide_out_of_stock")): ?>
                <div class="dokan-form-group content-half-part">
                    <label for="post_status" class="form-label">
                        <?php echo esc_html__('Delivery time for out of stock:', self::$textdomain); ?>
                    </label>
                    <input type="text"
                        value="<?php echo isset($estMeta['esttime_outofstock']) ? stripslashes(esc_html__($estMeta['esttime_outofstock'])) : ""; ?>"
                        name="esttime_outofstock" class="dokan-form-control">
                </div>


            <?php endif; ?>
        </div>
    </div>
</div>