<div id="design-settings" class="nav-content">
    <table class="form-table rpesp-table-buttons" >
	<tr>
            <th><?php echo esc_html__('Delivery text icon',self::$textdomain) ?></th>
            <td>
                <?php $this->getImageInput('delivery_text_icon', esc_html__('Upload Icon', self::$textdomain), 'delivery_text_icon', $this->getSetting("delivery_text_icon")); ?>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Hide icon on shop page?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="hide_icon_1" class="input-radio-button" type="radio" name="hide_icon" value="1" <?php echo ($this->getSetting("hide_icon") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="hide_icon_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="hide_icon_0" class="input-radio-button" type="radio" name="hide_icon" value="0" <?php echo ($this->getSetting("hide_icon") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="hide_icon_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Hide icon on cart/checkout page?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="hide_icon_cart_1" class="input-radio-button" type="radio" name="hide_icon_cart" value="1" <?php echo ($this->getSetting("hide_icon_cart") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="hide_icon_cart_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="hide_icon_cart_0" class="input-radio-button" type="radio" name="hide_icon_cart" value="0" <?php echo ($this->getSetting("hide_icon_cart") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="hide_icon_cart_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Delivery text backgorund color',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="bg_text_color" class="txtcolor" value="<?php echo $this->getSetting("bg_text_color") ?>"/>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Delivery text color',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_color" class="txtcolor" value="<?php echo $this->getSetting("text_color") ?>"/>
            </td>
        </tr>
        <tr  class="esttimeforcarrier" style="display: <?php echo $this->getSetting("enable_carrier") ? "table-row":"none"; ?>;">
            <th><?php echo esc_html__('Carrier delivery text icon',self::$textdomain) ?></th>
            <td>
                <?php $this->getImageInput('carrier_delivery_text_icon', esc_html__('Upload Icon', self::$textdomain), 'carrier_delivery_text_icon', $this->getSetting("carrier_delivery_text_icon")); ?>
            </td>
        </tr>
        <tr  class="esttimeforcarrier" style="display: <?php echo $this->getSetting("enable_carrier") ? "table-row":"none"; ?>;">
            <th><?php echo esc_html__('Carrier delivery text backgorund color',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="carrier_bg_text_color" class="txtcolor" value="<?php echo $this->getSetting("carrier_bg_text_color") ?>"/>
            </td>
        </tr>
        <tr  class="esttimeforcarrier" style="display: <?php echo $this->getSetting("enable_carrier") ? "table-row":"none"; ?>;">
            <th><?php echo esc_html__('Carrier delivery text color',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="carrier_text_color" class="txtcolor" value="<?php echo $this->getSetting("carrier_text_color") ?>"/>
            </td>
        </tr>
        <tr  class="esttimeforoutof " style="display: <?php echo ($this->getSetting("hide_out_of_stock")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text backgorund color for out of stock product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="bg_text_color_outofstock" class="txtcolor" value="<?php echo $this->getSetting("bg_text_color_outofstock") ?>"/>
            </td>
        </tr>
        <tr  class="esttimeforoutof " style="display: <?php echo ($this->getSetting("hide_out_of_stock")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text color for out of stock product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_color_outofstock" class="txtcolor" value="<?php echo $this->getSetting("text_color_outofstock") ?>"/>
            </td>
        </tr>
        <tr class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text backgorund color for backorder product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="bg_text_color_backorder" class="txtcolor" value="<?php echo $this->getSetting("bg_text_color_backorder") ?>"/>
            </td>
        </tr>
        <tr class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text color for backorder product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_color_backorder" class="txtcolor" value="<?php echo $this->getSetting("text_color_backorder") ?>"/>
            </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th><?php echo esc_html__('Delivery text icon for cart and checkout for combine delivery date',self::$textdomain) ?></th>
            <td>
                <?php $this->getImageInput('delivery_text_icon_combine', esc_html__('Upload Icon', self::$textdomain), 'delivery_text_icon_combine', $this->getSetting("delivery_text_icon_combine")); ?>
            </td>
        </tr>
        <tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th><?php echo esc_html__('Delivery text backgorund color for cart and checkout for combine delivery date',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="bg_text_color_combine_date" class="txtcolor" value="<?php echo $this->getSetting("bg_text_color_combine_date") ?>"/>
            </td>
        </tr>
        <tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th><?php echo esc_html__('Delivery text color for cart and checkout for combine delivery date',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_color_combine_date" class="txtcolor" value="<?php echo $this->getSetting("text_color_combine_date") ?>"/>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Delivery text size',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_size" class="small-input" value="<?php echo $this->getSetting("text_size") ?>"/><?php echo esc_html__(' px',self::$textdomain) ?>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Custom Css',self::$textdomain) ?></th>
            <td>
                <textarea name="custom_css" ><?php echo $this->getSetting("custom_css") ?></textarea>
            </td>
        </tr>
        <?php do_action("rpesp_design_tab_additional_fields",$this->rpesp_settings); ?>
        <tr class="last-row">
            <td>&nbsp;</td>
            <td>
                <input type="submit" class="button button-primary" name="btn_submit" value="<?php echo esc_html__("Save Settings",self::$textdomain) ?>" />
            </td>
        </tr>
    </table>
</div>

