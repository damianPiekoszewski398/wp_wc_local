<?php
$estMeta = get_post_meta($variation->ID, self::$meta_key, true);
$class = "form-row-first";
?>
<div>
    <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
        <label><?php echo esc_html__('Delivery time :', self::$textdomain); ?></label>
        <input type="text" value="<?php echo isset($estMeta['esttime']) ? stripslashes(esc_html__($estMeta['esttime'])) : ""; ?>" name="esttime_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
    </p>
    <?php
    if ($this->getSetting("enable_carrier")):
        $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
        ?>


        <div>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label><?php echo esc_html__('Delivery time to carrier :', self::$textdomain); ?></label>
                <input type="text" value="<?php echo isset($estMeta['carrier_esttime']) ? stripslashes(esc_html__($estMeta['carrier_esttime'])) : ""; ?>" name="carrier_esttime_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
            </p>
        </div>
    <?php endif; ?>
    <?php
    if (!$this->getSetting("hide_backorder")):
        $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
        ?>
        <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
            <label><?php echo esc_html__('Delivery time for backorder:', self::$textdomain); ?></label>
            <input  type="text" value="<?php echo isset($estMeta['esttime_backorder']) ? stripslashes(esc_html__($estMeta['esttime_backorder'])) : ""; ?>" name="esttime_backorder_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
        </p>
        <?php
        if ($this->getSetting("enable_carrier")):
            $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
            ?>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label><?php echo esc_html__('Delivery time to carrier for backorder:', self::$textdomain); ?></label>
                <input  type="text" value="<?php echo isset($estMeta['carrier_esttime_backorder']) ? stripslashes(esc_html__($estMeta['carrier_esttime_backorder'])) : ""; ?>" name="carrier_esttime_backorder_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
            </p>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    if (!$this->getSetting("hide_out_of_stock")):
        $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
        ?>

        <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
            <label><?php echo esc_html__('Delivery time for out of stock:', self::$textdomain); ?></label>
            <input  type="text" value="<?php echo isset($estMeta['esttime_outofstock']) ? stripslashes(esc_html__($estMeta['esttime_outofstock'])) : ""; ?>" name="esttime_outofstock_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
        </p>
        <?php
        if ($this->getSetting("enable_carrier")):
            $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
            ?>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label><?php echo esc_html__('Delivery time to carrier for out of stock:', self::$textdomain); ?></label>
                <input  type="text" value="<?php echo isset($estMeta['carrier_esttime_outofstock']) ? stripslashes(esc_html__($estMeta['carrier_esttime_outofstock'])) : ""; ?>" name="carrier_esttime_outofstock_shipping[<?php echo esc_attr($variation->ID) ?>]"><small><i>&nbsp;<?php echo esc_html__('In Day') ?></i></small>
            </p>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    if ($this->getSetting("show_text_product")):
        $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first";
        ?>
        <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
            <label><?php echo esc_html__('Delivery date text:', self::$textdomain); ?></label>
            <?php
            echo wc_help_tip("<br>{d}:" . esc_html__(' For display number of remaining days', self::$textdomain) . "
        <br>{d+x}:" . esc_html__(' x is number of additional day Example:{d+1},{d+2}..', self::$textdomain) . "
        <br>{d-x}:" . esc_html__(' x is number of additional day Example:{d-1},{d-2}..', self::$textdomain) . "
        <br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
        <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
        <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain) . "
	<br>{time_limit}: " . esc_html__('Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days..', self::$textdomain), true);
            ?>
            <input type="text" value="<?php echo isset($estMeta['esttext']) ? stripslashes(esc_html__($estMeta['esttext'])) : ""; ?>" name="esttext_shipping[<?php echo esc_attr($variation->ID) ?>]">

        </p>
        <?php $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first"; ?>
        <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
            <label><?php echo esc_html__('Alternative delivery text:', self::$textdomain); ?></label>
            <?php
            echo wc_help_tip("<br>{d}:" . esc_html__(' For display number of remaining days', self::$textdomain) . "
        <br>{d+x}:" . esc_html__(' x is number of additional day Example:{d+1},{d+2}..', self::$textdomain) . "
        <br>{d-x}:" . esc_html__(' x is number of additional day Example:{d-1},{d-2}..', self::$textdomain) . "
        <br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
        <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
        <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain), true);
            ?>
            <input type="text" value="<?php echo isset($estMeta['alt_esttext']) ? stripslashes(esc_html__($estMeta['alt_esttext'])) : ""; ?>" name="alt_esttext_shipping[<?php echo esc_attr($variation->ID) ?>]">

        </p>
        <?php if (!$this->getSetting("hide_backorder")): ?>
            <?php $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first"; ?>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label ><?php echo esc_html__('Delivery text for backorder:', self::$textdomain); ?></label>
                <?php
                echo wc_help_tip("<br>{d}:" . esc_html__(' For display number of remaining days', self::$textdomain) . "
            <br>{d+x}:" . esc_html__(' x is number of additional day Example:{d+1},{d+2}..', self::$textdomain) . "
            <br>{d-x}:" . esc_html__(' x is number of additional day Example:{d-1},{d-2}..', self::$textdomain) . "
            <br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
            <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
            <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain) . "
	    <br>{time_limit}: " . esc_html__('Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days..', self::$textdomain), true);
                ?>
                <input  type="text" value="<?php echo isset($estMeta['esttext_backorder']) ? stripslashes(esc_html__($estMeta['esttext_backorder'])) : ""; ?>" name="esttext_backorder_shipping[<?php echo esc_attr($variation->ID) ?>]">

            </p>
            <?php $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first"; ?>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label ><?php echo esc_html__('Alternative delivery text for backorder product:', self::$textdomain); ?></label>
                <?php
                echo wc_help_tip("<br>{d}:" . esc_html__(' For display number of remaining days', self::$textdomain) . "
            <br>{d+x}:" . esc_html__(' x is number of additional day Example:{d+1},{d+2}..', self::$textdomain) . "
            <br>{d-x}:" . esc_html__(' x is number of additional day Example:{d-1},{d-2}..', self::$textdomain) . "
            <br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
            <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
            <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain), true);
                ?>
                <input  type="text" value="<?php echo isset($estMeta['alt_esttext_backorder']) ? stripslashes(esc_html__($estMeta['alt_esttext_backorder'])) : ""; ?>" name="alt_esttext_backorder_shipping[<?php echo esc_attr($variation->ID) ?>]">

            </p>
        <?php endif; ?>
        <?php if (!$this->getSetting("hide_out_of_stock")): ?>
            <?php $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first"; ?>
            <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
                <label ><?php echo esc_html__('Delivery text for out of stock:', self::$textdomain); ?></label>
                <?php
                echo wc_help_tip("<br>{d}:" . esc_html__(' For display number of remaining days', self::$textdomain) . "
            <br>{d+x}:" . esc_html__(' x is number of additional day Example:{d+1},{d+2}..', self::$textdomain) . "
            <br>{d-x}:" . esc_html__(' x is number of additional day Example:{d-1},{d-2}..', self::$textdomain) . "
            <br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
            <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
            <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain), true);
                ?>
                <input  type="text" value="<?php echo isset($estMeta['esttext_outofstock']) ? stripslashes(esc_html__($estMeta['esttext_outofstock'])) : ""; ?>" name="esttext_outofstock_shipping[<?php echo esc_attr($variation->ID) ?>]">

            </p>
        <?php endif; ?>
        <?php $class = ($class == "form-row-first") ? "form-row-last" : "form-row-first"; ?>
        <p class="form-row hide_if_variation_virtual <?php echo $class; ?>">
            <label ><?php echo esc_html__('Delivery text on order page :', self::$textdomain); ?></label>
            <?php
            echo wc_help_tip("<br>{date}:" . esc_html__(' For display delivery date', self::$textdomain) . "
        <br>{date+x}:" . esc_html__(' x is number of additional day Example:{date+1},{date+2}..', self::$textdomain) . "
        <br>{date-x}:" . esc_html__(' x is number of additional day Example:{date-1},{date-2}..', self::$textdomain), true);
            ?>
            <input type="text" value="<?php echo isset($estMeta['esttext_orderpage']) ? stripslashes(esc_html__($estMeta['esttext_orderpage'])) : ""; ?>" name="esttext_orderpage_shipping[<?php echo esc_attr($variation->ID) ?>]">

        </p>
    <?php endif; ?>
</div>
