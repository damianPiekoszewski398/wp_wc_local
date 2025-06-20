<div id="delivery-text-setting" class="nav-content">
    <table class="form-table rpesp-table-buttons" >
	<tr>
            <th>
		<?php echo esc_html__('Enable text settings at product level?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="show_text_product_1" class="input-radio-button" type="radio" name="show_text_product" value="1" <?php echo ($this->getSetting("show_text_product") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="show_text_product_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="show_text_product_0" class="input-radio-button" type="radio" name="show_text_product" value="0" <?php echo ($this->getSetting("show_text_product") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="show_text_product_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
	<tr  class="item_order_text" style="display: <?php echo ($this->getSetting("combine_date")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Use alternative delivery text for cart and checkout?',self::$textdomain) ?>
            </th>
            <td>
                <div class="radio-buttons-wrapper">
                    <input id="alt_text_cart_checkout_1" class="input-radio-button" type="radio" name="alt_text_cart_checkout" value="1" <?php echo ($this->getSetting("alt_text_cart_checkout") == 1) ? "checked=checked" : ""; ?> >
                    <label class="input-label-button label-button-left" for="alt_text_cart_checkout_1">
                        <span class="label-button-text"><?php echo esc_html__('Yes',self::$textdomain) ?></span>
                    </label>

                    <input id="alt_text_cart_checkout_0" class="input-radio-button" type="radio" name="alt_text_cart_checkout" value="0" <?php echo ($this->getSetting("alt_text_cart_checkout") == 0) ? "checked=checked" : ""; ?>>
                    <label class="input-label-button label-button-right" for="alt_text_cart_checkout_0">
                        <span class="label-button-text"><?php echo esc_html__('No',self::$textdomain) ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Delivery text ',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="estimate_text"  value="<?php echo stripslashes(esc_html__($this->getSetting("estimate_text"))); ?>"/><br>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span>
		    <span><code>{time_limit}</code>: <?php echo esc_html__('Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
        <tr>
            <th>
		<?php echo esc_html__('Alternative delivery text ',self::$textdomain) ?>
		<span class="rpesp-description"><?php echo esc_html__("This text will be use if you use time limit in delivery text and it is not possible to calculate the time limit",self::$textdomain) ?></span>
	    </th>
            <td>
                <input type="text"  name="alt_estimate_text"  value="<?php echo stripslashes(esc_html__($this->getSetting("alt_estimate_text"))); ?>"/><br>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
	<tr class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text for backorder product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="estimate_text_backorder"  value="<?php echo stripslashes(esc_html__($this->getSetting("estimate_text_backorder"))) ?>"/>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span> 
		    <span><code>{time_limit}</code>: Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days</span> 
                </div>
            </td>
        </tr>
	<tr class="esttimeforbackorder" style="display: <?php echo ($this->getSetting("hide_backorder")) ? "none" : "table-row"; ?>;">
            <th>
		<?php echo esc_html__('Alternative delivery text for backorder product ',self::$textdomain) ?>
		<span class="rpesp-description"><?php echo esc_html__("This text will be use if you use time limit in delivery text and it is not possible to calculate the time limit",self::$textdomain) ?></span>
	    </th>
            <td>
                <input type="text"  name="alt_estimate_text_backorder"  value="<?php echo stripslashes(esc_html__($this->getSetting("alt_estimate_text_backorder"))); ?>"/><br>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>

        <tr class="esttimeforoutof" style="display: <?php echo ($this->getSetting("hide_out_of_stock")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text for out of stock product',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="estimate_text_outofstock"  value="<?php echo stripslashes(esc_html__($this->getSetting("estimate_text_outofstock"))) ?>"/>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span> 
                </div>
            </td>
        </tr>
        <tr class="item_order_text" style="display: <?php echo ($this->getSetting("combine_date")) ? "none" : "table-row"; ?>;">
            <th><?php echo esc_html__('Delivery text on order page',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="text_order"  value="<?php echo stripslashes(esc_html__($this->getSetting("text_order"))) ?>"/>
                <div class="input-tips">
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine date text for cart and checkout page?',self::$textdomain) ?>
            </th>
            <td>
		<input type="text"  name="text_cart_checkout_combine_date"  value="<?php echo stripslashes(esc_html__($this->getSetting("text_cart_checkout_combine_date"))) ?>"/>
                <div class="input-tips">
                    <span><code>{product_with_max_d}</code>: <?php echo esc_html__('For display number of remaining days of product with maximum delivery day',self::$textdomain) ?></span>
                    <span><code>{product_with_max_d + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_d+1},{product_with_max_d+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_max_d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_d-1},{product_with_max_d-2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_max_date}</code>: <?php echo esc_html__('For display delivery date of product with maximum delivery date',self::$textdomain) ?></span>
                    <span><code>{product_with_max_date + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_date+1},{product_with_max_date+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_max_date - x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_date-1},{product_with_max_date-2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_d}</code>: <?php echo esc_html__('For display number of remaining days of product with minimum delivery day',self::$textdomain) ?></span>
                    <span><code>{product_with_min_d + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_d+1},{product_with_min_d+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_d-1},{product_with_min_d-2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date}</code>: <?php echo esc_html__('For display delivery date of product with minimum delivery date',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_date+1},{product_with_min_date+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date - x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_date-1},{product_with_min_date-2}..',self::$textdomain) ?></span>
                </div>
	    </td>
        </tr>
	<tr class="combine_date_option" style="display: <?php echo ($this->getSetting("combine_date")) ? "table-row" : "none"; ?>;">
            <th>
		<?php echo esc_html__('Combine date text for order page?',self::$textdomain) ?>
            </th>
            <td>
		<input type="text"  name="text_order_combine_date"  value="<?php echo stripslashes(esc_html__($this->getSetting("text_order_combine_date"))) ?>"/>
                <div class="input-tips">
                    <span><code>{product_with_max_date}</code>: <?php echo esc_html__('For display delivery date of product with maximum delivery date',self::$textdomain) ?></span>
                    <span><code>{product_with_max_date + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_date+1},{product_with_max_date+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_max_date - x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_max_date-1},{product_with_max_date-2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date}</code>: <?php echo esc_html__('For display delivery date of product with minimum delivery date',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date + x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_date+1},{product_with_min_date+2}..',self::$textdomain) ?></span>
                    <span><code>{product_with_min_date - x}</code>: <?php echo esc_html__('x is number of additional day Example:{product_with_min_date-1},{product_with_min_date-2}..',self::$textdomain) ?></span>
                </div>
	    </td>
        </tr>
	<tr class="esttimeforcarrier" style="display: <?php echo $this->getSetting("enable_carrier") ? "table-row" : "none"; ?>;">
            <th><?php echo esc_html__('Delivery text for carrier',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="delivery_text_carier"  value="<?php echo stripslashes(esc_html__($this->getSetting("delivery_text_carier"))); ?>"/><br>
                <div class="input-tips">
                    <span><code>{d}</code>: <?php echo esc_html__('For display number of remaining days',self::$textdomain) ?></span>
                    <span><code>{d+x}</code>: <?php echo esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) ?></span>
                    <span><code>{d-x}</code>: <?php echo esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) ?></span>
                    <span><code>{date}</code>: <?php echo esc_html__('For display delivery date',self::$textdomain) ?></span>
                    <span><code>{date+x}</code>: <?php echo esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) ?></span>
                    <span><code>{date-x}</code>: <?php echo esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Text when no variation selected ',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="delivery_text_no_vairation"  value="<?php echo stripslashes(esc_html__($this->getSetting("delivery_text_no_vairation"))); ?>"/><br>
                <div class="input-tips">
                    <span><code>{variation_min_d}</code>: <?php echo esc_html__('For display minimum delivery days for product variations',self::$textdomain) ?></span>
                    <span><code>{variation_max_d}</code>: <?php echo esc_html__('For display maximum delivery days for product variations',self::$textdomain) ?></span>
                    <span><code>{variation_min_date}</code>: <?php echo esc_html__('For display minimum delivery date for product variations',self::$textdomain) ?></span>
                    <span><code>{variation_max_date}</code>: <?php echo esc_html__('For display maximum delivery date for product variations',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Date Format',self::$textdomain) ?></th>
            <td>
                <input type="text"  name="date_format" class="small-input"  value="<?php echo stripslashes(esc_html__($this->getSetting("date_format"))) ?>"/>
                <div class="input-tips">
                    <span><code>d</code>: <?php echo esc_html__('Day of month (2 digits with leading zeros)',self::$textdomain) ?></span>
                    <span><code>D</code>: <?php echo esc_html__('A textual representation of a day, three letters',self::$textdomain) ?></span>
                    <span><code>j</code>: <?php echo esc_html__('Day of the month without leading zeros',self::$textdomain) ?></span>
                    <span><code>l</code>:<?php echo esc_html__('A full textual representation of the day of the week',self::$textdomain) ?></span>
                    <span><code>m</code>: <?php echo esc_html__('Numeric representation of a month, with leading zeros',self::$textdomain) ?></span>
                    <span><code>F</code>: <?php echo esc_html__('A full textual representation of a month, with leading zeros',self::$textdomain) ?></span>
                    <span><code>M</code>: <?php echo esc_html__('A short textual representation of a month, three letters',self::$textdomain) ?></span>
                    <span><code>n</code>: <?php echo esc_html__('Numeric representation of a month, without leading zeros',self::$textdomain) ?></span>
                    <span> <code>Y</code>: <?php echo esc_html__('A full numeric representation of a year, 4 digits',self::$textdomain) ?></span>
                    <span><code>y</code>: <?php echo esc_html__('A two digit representation of a year',self::$textdomain) ?></span>
                </div>
            </td>
        </tr>
        <?php do_action("rpesp_deliverytext_tab_additional_fields",$this->rpesp_settings); ?>
        <tr class="last-row">
            <td>&nbsp;</td>
            <td>
                <input type="submit" class="button button-primary" name="btn_submit" value="<?php echo esc_html__("Save Settings",self::$textdomain) ?>" />
            </td>
        </tr>
    </table>
</div>
