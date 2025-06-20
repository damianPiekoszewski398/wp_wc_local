<div id="display-setting" class="nav-content">
    <table class="form-table rpesp-table-buttons" >
        <tr>
            <th>
		<?php echo esc_html__('Display on product archive/shop pages?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="display_on_product_archive_1" class="input-radio-button display_on_product_archive" type="radio" name="display_on_product_archive" value="1" <?php echo ($this->getSetting("display_on_product_archive") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="display_on_product_archive_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="display_on_product_archive_0" class="input-radio-button display_on_product_archive" type="radio" name="display_on_product_archive" value="0" <?php echo ($this->getSetting("display_on_product_archive") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="display_on_product_archive_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr class="text_position_product_archive" style="display: <?php echo ($this->getSetting("display_on_product_archive")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Delivery text position on product archive page',self::$textdomain) ?>
            </th>
            <td>
                <select name="text_pos_archive">
                    <option value="woocommerce_before_shop_loop_item_title" <?php echo ($this->getSetting("text_pos_archive") == 'woocommerce_before_shop_loop_item_title') ? "selected=selected" : ""; ?>><?php echo esc_html__('Before product image',self::$textdomain) ?></option>
                    <option value="woocommerce_shop_loop_item_title" <?php echo ($this->getSetting("text_pos_archive") == 'woocommerce_shop_loop_item_title') ? "selected=selected" : ""; ?>><?php echo esc_html__('Before product title',self::$textdomain) ?></option>
                    <option value="woocommerce_after_shop_loop_item_title" <?php echo ($this->getSetting("text_pos_archive") == 'woocommerce_after_shop_loop_item_title') ? "selected=selected" : ""; ?>><?php echo esc_html__('After product title',self::$textdomain) ?></option>
                    <option value="woocommerce_after_shop_loop_item" <?php echo ($this->getSetting("text_pos_archive") == 'woocommerce_after_shop_loop_item') ? "selected=selected" : ""; ?>><?php echo esc_html__('Before add to cart',self::$textdomain) ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Display on product page?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="display_on_product_1" class="input-radio-button display_on_product" type="radio" name="display_on_product" value="1" <?php echo ($this->getSetting("display_on_product") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="display_on_product_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="display_on_product_0" class="input-radio-button display_on_product" type="radio" name="display_on_product" value="0" <?php echo ($this->getSetting("display_on_product") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="display_on_product_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr class="text_position_product" style="display: <?php echo ($this->getSetting("display_on_product")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Delivery text position on product page',self::$textdomain) ?>
            </th>
            <td>
                <select name="text_pos">
                    <option value="0" <?php echo ($this->getSetting("text_pos") == 0) ? "selected=selected" : ""; ?>><?php echo esc_html__('Below  Title',self::$textdomain) ?></option>
                    <option value="1" <?php echo ($this->getSetting("text_pos") == 1) ? "selected=selected" : ""; ?>><?php echo esc_html__('After Description',self::$textdomain) ?></option>
                    <option value="2" <?php echo ($this->getSetting("text_pos") == 2) ? "selected=selected" : ""; ?>><?php echo esc_html__('After Price',self::$textdomain) ?></option>
                    <option value="3" <?php echo ($this->getSetting("text_pos") == 3) ? "selected=selected" : ""; ?>><?php echo esc_html__('After Add To Cart',self::$textdomain) ?></option>
                    <option value="4" <?php echo ($this->getSetting("text_pos") == 4) ? "selected=selected" : ""; ?>><?php echo esc_html__('Before Add To Cart',self::$textdomain) ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Display on cart and checkout page?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_on_cart_1" class="input-radio-button" type="radio" name="enable_on_cart" value="1" <?php echo ($this->getSetting("enable_on_cart") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_on_cart_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_on_cart_0" class="input-radio-button" type="radio" name="enable_on_cart" value="0" <?php echo ($this->getSetting("enable_on_cart") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_on_cart_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Display on order page?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_on_orderpage_1" class="input-radio-button" type="radio" name="enable_on_orderpage" value="1" <?php echo ($this->getSetting("enable_on_orderpage") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_on_orderpage_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_on_orderpage_0" class="input-radio-button" type="radio" name="enable_on_orderpage" value="0" <?php echo ($this->getSetting("enable_on_orderpage") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_on_orderpage_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Display in order email?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="enable_on_orderemail_1" class="input-radio-button" type="radio" name="enable_on_orderemail" value="1" <?php echo ($this->getSetting("enable_on_orderemail") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="enable_on_orderemail_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="enable_on_orderemail_0" class="input-radio-button" type="radio" name="enable_on_orderemail" value="0" <?php echo ($this->getSetting("enable_on_orderemail") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="enable_on_orderemail_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine delivery message position on cart page?',self::$textdomain) ?>
            </th>
            <td>
		<select name="cart_position">
		    <option value="woocommerce_before_cart_table" <?php echo ($this->getSetting("cart_position") == "woocommerce_before_cart_table") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Cart Table',self::$textdomain) ?></option>
		    <option value="woocommerce_cart_coupon" <?php echo ($this->getSetting("cart_position") == "woocommerce_cart_coupon") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Cart Coupon',self::$textdomain) ?></option>
		    <option value="woocommerce_after_cart_contents" <?php echo ($this->getSetting("cart_position") == "woocommerce_after_cart_contents") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Cart Content',self::$textdomain) ?></option>
		    <option value="woocommerce_after_cart_table" <?php echo ($this->getSetting("cart_position") == "woocommerce_after_cart_table") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Cart Table',self::$textdomain) ?></option>
		    <option value="woocommerce_cart_collaterals" <?php echo ($this->getSetting("cart_position") == "woocommerce_cart_collaterals") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Cart Collaterals',self::$textdomain) ?></option>
		    <option value="woocommerce_before_cart_totals" <?php echo ($this->getSetting("cart_position") == "woocommerce_before_cart_totals") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Cart Total',self::$textdomain) ?></option>
		</select>
                
            </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine delivery message position on checkout page?',self::$textdomain) ?>
            </th>
            <td>
		<select name="checkout_position">
		    <option value="woocommerce_before_checkout_form" <?php echo ($this->getSetting("checkout_position") == "woocommerce_before_checkout_form") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Checkout Form',self::$textdomain) ?></option>
		    <option value="woocommerce_checkout_before_customer_details" <?php echo ($this->getSetting("checkout_position") == "woocommerce_checkout_before_customer_details") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Customer Detail',self::$textdomain) ?></option>
		    <option value="woocommerce_before_checkout_billing_form" <?php echo ($this->getSetting("checkout_position") == "woocommerce_before_checkout_billing_form") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Billing Detail',self::$textdomain) ?></option>
		    <option value="woocommerce_after_checkout_billing_form" <?php echo ($this->getSetting("checkout_position") == "woocommerce_after_checkout_billing_form") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Billing Detail',self::$textdomain) ?></option>
		    <option value="woocommerce_before_checkout_shipping_form" <?php echo ($this->getSetting("checkout_position") == "woocommerce_before_checkout_shipping_form") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Shipping Detail',self::$textdomain) ?></option>
		    <option value="woocommerce_before_order_notes" <?php echo ($this->getSetting("checkout_position") == "woocommerce_before_order_notes") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Order Notes',self::$textdomain) ?></option>
		    <option value="woocommerce_after_order_notes" <?php echo ($this->getSetting("checkout_position") == "woocommerce_after_order_notes") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Order Notes',self::$textdomain) ?></option>
		    <option value="woocommerce_checkout_before_order_review" <?php echo ($this->getSetting("checkout_position") == "woocommerce_checkout_before_order_review") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Order Review',self::$textdomain) ?></option>
		</select>
	  </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine delivery message position on customer order detail page?',self::$textdomain) ?>
            </th>
            <td>
		<select name="orderpage_position">
		    <option value="woocommerce_order_details_before_order_table" <?php echo ($this->getSetting("orderpage_position") == "woocommerce_order_details_before_order_table") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Order Detail Table',self::$textdomain) ?></option>
		    <option value="woocommerce_order_details_after_order_table" <?php echo ($this->getSetting("orderpage_position") == "woocommerce_order_details_after_order_table") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Order Detail Table',self::$textdomain) ?></option>
		    <option value="woocommerce_after_order_details" <?php echo ($this->getSetting("orderpage_position") == "woocommerce_after_order_details") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Order Detail',self::$textdomain) ?></option>
		    <option value="woocommerce_order_details_after_customer_details" <?php echo ($this->getSetting("orderpage_position") == "woocommerce_order_details_after_customer_details") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Customer Detail',self::$textdomain) ?></option>
		</select>
	  </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine delivery message position on email?',self::$textdomain) ?>
            </th>
            <td>
		<select name="email_position">
		    <option value="0" <?php echo ($this->getSetting("email_position") == "0") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Order Detail',self::$textdomain) ?></option>
		    <option value="1" <?php echo ($this->getSetting("email_position") == "1") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Order Detail',self::$textdomain) ?></option>
		    <option value="2" <?php echo ($this->getSetting("email_position") == "2") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Order Meta',self::$textdomain) ?></option>
		    <option value="3" <?php echo ($this->getSetting("email_position") == "3") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Before Customer Detail',self::$textdomain) ?></option>
		    <option value="4" <?php echo ($this->getSetting("email_position") == "4") ? "selected=selected" : ""; ?> ><?php echo esc_html__('After Customer Detail  ',self::$textdomain) ?></option>
		</select>
	  </td>
        </tr>
        <?php do_action("rpesp_display_tab_additional_fields",$this->rpesp_settings); ?>
        <tr class="last-row">
            <td>&nbsp;</td>
            <td>
                <input type="submit" class="button button-primary" name="btn_submit" value="<?php echo esc_html__("Save Settings",self::$textdomain) ?>" />
            </td>
        </tr>
    </table>
</div>

