<div id="general-setting" class="nav-content active">
    <table class="form-table rpesp-table-buttons" >
        <tr>
            <th>
		<?php echo esc_html__('Enabled delivery date?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_delivery_date_1" class="input-radio-button" type="radio" name="enable_delivery_date" value="1" <?php echo ($this->getSetting("enable_delivery_date") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_delivery_date_1">
                        <span class="label-button-text"><?php echo esc_html__('Enable',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_delivery_date_0" class="input-radio-button" type="radio" name="enable_delivery_date" value="0" <?php echo ($this->getSetting("enable_delivery_date") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_delivery_date_0">
                        <span class="label-button-text"><?php echo esc_html__('Disable',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
	<tr>
            <th>
		<?php echo esc_html__('Enable carrier delivery message on product page?',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Enable this option if you want to show the date when the product is usually shipped to carrier",self::$textdomain) ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_carrier_1" class="input-radio-button enablecarrier" type="radio" name="enable_carrier" value="1" <?php echo ($this->getSetting("enable_carrier") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_carrier_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_carrier_0" class="input-radio-button enablecarrier" type="radio" name="enable_carrier" value="0" <?php echo ($this->getSetting("enable_carrier") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_carrier_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Enabled for all products?',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("This setting enable delivery time for all products. You can enable this option if all your products have same delivery time.",self::$textdomain) ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_for_1" class="input-radio-button enableforall" type="radio" name="enable_for" value="1" <?php echo ($this->getSetting("enable_for") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_for_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_for_0" class="input-radio-button enableforall" type="radio" name="enable_for" value="0" <?php echo ($this->getSetting("enable_for") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_for_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        
        <tr >
            <th><?php echo esc_html__('Delivery time',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="estimate_time" class="small-input" value="<?php echo $this->getSetting("estimate_time") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
        <tr class="esttimeforcarrier" style="display: <?php echo ($this->getSetting("enable_carrier")==1) ? "table-row" : "none"; ?>;">
            <th><?php echo esc_html__('Delivery time to carrier',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="carrier_estimate_time" class="small-input" value="<?php echo $this->getSetting("carrier_estimate_time") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
	
	<tr class="esttimeforall" style="display: <?php echo ($this->getSetting("enable_for")==1) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Hide product level setting for delivery time?',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Select 'yes' only if all your product have same delivery time and you don't want show product level settings",self::$textdomain) ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="hide_product_setting_1" class="input-radio-button" type="radio" name="hide_product_setting" value="1" <?php echo ($this->getSetting("hide_product_setting") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="hide_product_setting_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="hide_product_setting_0" class="input-radio-button" type="radio" name="hide_product_setting" value="0" <?php echo ($this->getSetting("hide_product_setting") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="hide_product_setting_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        
        <tr>
            <th>
		<?php echo esc_html__('Hide if product is out of stock?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="hide_out_of_stock_1" class="input-radio-button hideoutofstock" type="radio" name="hide_out_of_stock" value="1" <?php echo ($this->getSetting("hide_out_of_stock") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="hide_out_of_stock_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="hide_out_of_stock_0" class="input-radio-button hideoutofstock" type="radio" name="hide_out_of_stock" value="0" <?php echo ($this->getSetting("hide_out_of_stock") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="hide_out_of_stock_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr  class="esttimeforoutof " style="display: <?php echo ($this->getSetting("hide_out_of_stock")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Delivery time for out of stock product',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Leave blank if you want same delivery time for instock/outofstock products",self::$textdomain) ?></span>
            </th>
            <td>
                <input type="text"  name="estimate_time_outofstock" class="small-input" value="<?php echo $this->getSetting("estimate_time_outofstock") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
        <tr  class="esttimeforoutof esttimeforcarrier" style="display: <?php echo ($this->getSetting("hide_out_of_stock") || !$this->getSetting("enable_carrier")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Delivery time to carrier for out of stock product',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Leave blank if you want same delivery time to carrier for instock/outofstock products",self::$textdomain) ?></span>
            </th>
            <td>
                <input type="text"  name="carrier_estimate_time_outofstock" class="small-input" value="<?php echo $this->getSetting("carrier_estimate_time_outofstock") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Hide if product is backorder?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="hide_backorder_1" class="input-radio-button hide_backorder" type="radio" name="hide_backorder" value="1" <?php echo ($this->getSetting("hide_backorder") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="hide_backorder_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="hide_backorder_0" class="input-radio-button hide_backorder" type="radio" name="hide_backorder" value="0" <?php echo ($this->getSetting("hide_backorder") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="hide_backorder_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr  class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Delivery time for backorder product',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Leave blank if you want same delivery time for instock/outofstock/backorder products",self::$textdomain) ?></span>
            </th>
            <td>
                <input type="text"  name="estimate_time_backorder" class="small-input" value="<?php echo $this->getSetting("estimate_time_backorder") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
        <tr  class="esttimeforbackorder esttimeforcarrier" style="display: <?php echo ($this->getSetting("hide_backorder") || !$this->getSetting("enable_carrier")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Delivery time to carrier for backorder product',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Leave blank if you want same delivery time to carrier for instock/outofstock/backorder products",self::$textdomain) ?></span>
            </th>
            <td>
                <input type="text"  name="carrier_estimate_time_backorder" class="small-input" value="<?php echo $this->getSetting("carrier_estimate_time_backorder") ?>"/><?php echo esc_html__(' in day',self::$textdomain) ?>
            </td>
        </tr>
        <tr class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;" >
            <th>
		<?php echo esc_html__('Display on backorder products only?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="backorder_only_1" class="input-radio-button" type="radio" name="backorder_only" value="1" <?php echo ($this->getSetting("backorder_only") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="backorder_only_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="backorder_only_0" class="input-radio-button" type="radio" name="backorder_only" value="0" <?php echo ($this->getSetting("backorder_only") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="backorder_only_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Load delivery date using ajax on product page?',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("Select Yes only if you are using any cache plugin.",self::$textdomain) ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="load_using_ajax_1" class="input-radio-button" type="radio" name="load_using_ajax" value="1" <?php echo ($this->getSetting("load_using_ajax") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="load_using_ajax_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="load_using_ajax_0" class="input-radio-button" type="radio" name="load_using_ajax" value="0" <?php echo ($this->getSetting("load_using_ajax") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="load_using_ajax_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Combine delivery date for order?',self::$textdomain) ?>
                <span class="rpesp-description"><?php echo esc_html__("It will remove delivery date for each product from cart,checkout and order page and display single delivery date for order as per your settings.",self::$textdomain) ?></span>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="combine_date_1" class="input-radio-button combine_date" type="radio" name="combine_date" value="1" <?php echo ($this->getSetting("combine_date") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="combine_date_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="combine_date_0" class="input-radio-button combine_date" type="radio" name="combine_date" value="0" <?php echo ($this->getSetting("combine_date") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="combine_date_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Enable for REST API?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="restapi_1" class="input-radio-button restapi" type="radio" name="restapi" value="1" <?php echo ($this->getSetting("restapi") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="restapi_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="restapi_0" class="input-radio-button combine_date" type="radio" name="restapi" value="0" <?php echo ($this->getSetting("restapi") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="restapi_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
         <?php do_action("rpesp_general_tab_additional_fields",$this->rpesp_settings); ?>
        
        <tr class="last-row">
            <td>&nbsp;</td>
            <td>
                <input type="submit" class="button button-primary" name="btn_submit" value="<?php echo esc_html__("Save Settings",self::$textdomain) ?>" />
            </td>
        </tr>
    </table>
</div>

