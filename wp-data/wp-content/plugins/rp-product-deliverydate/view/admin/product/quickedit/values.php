<div class="hidden" id="rpwoo_product_delivery_inline_<?php echo stripslashes(esc_html__($productId)); ?>">
    <div class="_delivery_esttime"><?php echo isset($estMeta['esttime']) ? stripslashes(esc_html__($estMeta['esttime'])) : ""; ?></div>
    <div class="_delivery_carrier_esttime"><?php echo isset($estMeta['carrier_esttime']) ? stripslashes(esc_html__($estMeta['carrier_esttime'])) : ""; ?></div>
    <div class="_delivery_esttime_outofstock"><?php echo isset($estMeta['esttime_outofstock']) ? stripslashes(esc_html__($estMeta['esttime_outofstock'])) : ""; ?></div>
    <div class="_delivery_carrier_esttime_outofstock"><?php echo isset($estMeta['carrier_esttime_outofstock']) ? stripslashes(esc_html__($estMeta['carrier_esttime_outofstock'])) : ""; ?></div>
    <div class="_delivery_esttime_backorder"><?php echo isset($estMeta['esttime_backorder']) ? stripslashes(esc_html__($estMeta['esttime_backorder'])) : ""; ?></div>
    <div class="_delivery_carrier_esttime_backorder"><?php echo isset($estMeta['carrier_esttime_backorder']) ? stripslashes(esc_html__($estMeta['carrier_esttime_backorder'])) : ""; ?></div>
    <?php if($this->getSetting("show_text_product")): ?>
        <div class="_delivery_esttext"><?php echo isset($estMeta['esttext']) ? stripslashes(esc_html__($estMeta['esttext'])) : ""; ?></div>
        <div class="_alt_delivery_esttext"><?php echo isset($estMeta['alt_esttext']) ? stripslashes(esc_html__($estMeta['alt_esttext'])) : ""; ?></div>
        <div class="_delivery_esttext_outofstock"><?php echo isset($estMeta['esttext_outofstock']) ? stripslashes(esc_html__($estMeta['esttext_outofstock'])) : ""; ?></div>
        <div class="_delivery_esttext_backorder"><?php echo isset($estMeta['esttext_backorder']) ? stripslashes(esc_html__($estMeta['esttext_backorder'])) : ""; ?></div>
        <div class="_alt_delivery_esttext_backorder"><?php echo isset($estMeta['alt_esttext_backorder']) ? stripslashes(esc_html__($estMeta['alt_esttext_backorder'])) : ""; ?></div>
        <div class="_delivery_esttext_order"><?php echo isset($estMeta['esttext_orderpage']) ? stripslashes(esc_html__($estMeta['esttext_orderpage'])) : ""; ?></div>
    <?php endif; ?>
</div>
