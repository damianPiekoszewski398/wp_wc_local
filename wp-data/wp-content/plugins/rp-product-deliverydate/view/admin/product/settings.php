<?php
$estMeta = get_post_meta($thepostid,self::$meta_key,true);
?>
<style>
    #rpesp_product_data td{padding:10px;}
</style>
<div id="rpesp_product_data" class="panel woocommerce_options_panel">
    <table style="width:90%;">
        <tr valign="top">
            <td>
		<?php echo esc_html__('Delivery time :',self::$textdomain); ?>
            </td>
            <td>
                <input type="text" value="<?php echo isset($estMeta['esttime']) ? stripslashes(esc_html__($estMeta['esttime'])) : ""; ?>" name="esttime"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
                <div class="input-tips">
                    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
                </div>
            </td>
        </tr>
	<?php if($this->getSetting("enable_carrier")): ?>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Delivery time to carrier:',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['carrier_esttime']) ? stripslashes(esc_html__($estMeta['carrier_esttime'])) : ""; ?>" name="carrier_esttime"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
	<?php endif; ?>
	<?php if($this->getSetting("show_text_product")): ?>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Delivery date text:',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['esttext']) ? stripslashes(esc_html__($estMeta['esttext'])) : ""; ?>" name="esttext">
		    <?php
		    echo wc_help_tip("<br>{d}:" . esc_html__('For display number of remaining days',self::$textdomain) . "
                    <br>{d+x}: " . esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) . "
                    <br>{d-x}: " . esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) . "
                    <br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                    <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                    <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) . "
                    <br>{time_limit}: " . esc_html__('Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days..',self::$textdomain),true);
		    ?>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Alternative delivery text:',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['alt_esttext']) ? stripslashes(esc_html__($estMeta['alt_esttext'])) : ""; ?>" name="alt_esttext">
		    <?php
		    echo wc_help_tip("<br>{d}:" . esc_html__('For display number of remaining days',self::$textdomain) . "
                    <br>{d+x}: " . esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) . "
                    <br>{d-x}: " . esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) . "
                    <br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                    <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                    <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain),true);
		    ?>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('This text will be use if you use time limit in delivery text and it is not possible to calculate the time limit,Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
	<?php endif; ?>
	<?php if(!$this->getSetting("hide_backorder")): ?>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Delivery time for backorder:',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['esttime_backorder']) ? stripslashes(esc_html__($estMeta['esttime_backorder'])) : ""; ?>" name="esttime_backorder"><small><i>&nbsp;In Day</i></small>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
	    <?php if($this->getSetting("enable_carrier")): ?>
		<tr valign="top">
		    <td>
			<?php echo esc_html__('Delivery time to carrier for backorder:',self::$textdomain); ?>
		    </td>
		    <td>
			<input type="text" value="<?php echo isset($estMeta['carrier_esttime_backorder']) ? stripslashes(esc_html__($estMeta['carrier_esttime_backorder'])) : ""; ?>" name="carrier_esttime_backorder"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
			<div class="input-tips">
			    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
			</div>
		    </td>
		</tr>
	    <?php endif; ?>
	    <?php if($this->getSetting("show_text_product")): ?>
		<tr valign="top">
		    <td>
			<?php echo esc_html__('Delivery text for backorder:',self::$textdomain); ?>
		    </td>
		    <td>
			<input type="text" value="<?php echo isset($estMeta['esttext_backorder']) ? stripslashes(esc_html__($estMeta['esttext_backorder'])) : ""; ?>" name="esttext_backorder">
			<?php
			echo wc_help_tip("<br>{d}:" . esc_html__('For display number of remaining days',self::$textdomain) . "
                        <br>{d+x}: " . esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) . "
                        <br>{d-x}: " . esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) . "
                        <br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                        <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                        <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain),true);
			?>
			<div class="input-tips">
			    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
			</div>
		    </td>
		</tr>
		<tr valign="top">
		    <td>
			<?php echo esc_html__('Alternative delivery text for backorder product:',self::$textdomain); ?>
		    </td>
		    <td>
			<input type="text" value="<?php echo isset($estMeta['alt_esttext_backorder']) ? stripslashes(esc_html__($estMeta['alt_esttext_backorder'])) : ""; ?>" name="alt_esttext_backorder">
			<?php
			echo wc_help_tip("<br>{d}:" . esc_html__('For display number of remaining days',self::$textdomain) . "
                        <br>{d+x}: " . esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) . "
                        <br>{d-x}: " . esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) . "
                        <br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                        <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                        <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain) . "
			<br>{time_limit}: " . esc_html__('Use {time_limit} to show the time limit. For example: Order in 5 hours and 10 minitues and get delivery in 2 days..',self::$textdomain),true);
			?>
			<div class="input-tips">
			    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
			</div>
		    </td>
		</tr>
	    <?php endif; ?>
	<?php endif; ?>
	<?php if(!$this->getSetting("hide_out_of_stock")): ?>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Delivery time for out of stock:',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['esttime_outofstock']) ? stripslashes(esc_html__($estMeta['esttime_outofstock'])) : ""; ?>" name="esttime_outofstock"><small><i>&nbsp;In Day</i></small>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
	    <?php if($this->getSetting("enable_carrier")): ?>
		<tr valign="top">
		    <td>
			<?php echo esc_html__('Delivery time to carrier for out of stock:',self::$textdomain); ?>
		    </td>
		    <td>
			<input type="text" value="<?php echo isset($estMeta['carrier_esttime_outofstock']) ? stripslashes(esc_html__($estMeta['carrier_esttime_outofstock'])) : ""; ?>" name="carrier_esttime_outofstock"><small><i><?php echo esc_html__('&nbsp;In Day',self::$textdomain); ?></i></small>
			<div class="input-tips">
			    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
			</div>
		    </td>
		</tr>
	    <?php endif; ?>
	    <?php if($this->getSetting("show_text_product")): ?>
		<tr valign="top">
		    <td>
			<?php echo esc_html__('Delivery text for out of stock:',self::$textdomain); ?>
		    </td>
		    <td>
			<input type="text" value="<?php echo isset($estMeta['esttext_outofstock']) ? stripslashes(esc_html__($estMeta['esttext_outofstock'])) : ""; ?>" name="esttext_outofstock">
			<?php
			echo wc_help_tip("<br>{d}:" . esc_html__('For display number of remaining days',self::$textdomain) . "
                        <br>{d+x}: " . esc_html__('x is number of additional day Example:{d+1},{d+2}..',self::$textdomain) . "
                        <br>{d-x}: " . esc_html__('x is number of additional day Example:{d-1},{d-2}..',self::$textdomain) . "
                        <br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                        <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                        <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain),true);
			?>
			<div class="input-tips">
			    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
			</div>
		    </td>
		</tr>
	    <?php endif; ?>
	<?php endif; ?>
	<?php if($this->getSetting("show_text_product")): ?>
    	<tr valign="top">
    	    <td>
		    <?php echo esc_html__('Delivery text on order page :',self::$textdomain); ?>
    	    </td>
    	    <td>
    		<input type="text" value="<?php echo isset($estMeta['esttext_orderpage']) ? stripslashes(esc_html__($estMeta['esttext_orderpage'])) : ""; ?>" name="esttext_orderpage">
		    <?php
		    echo wc_help_tip("<br>{date}: " . esc_html__('For display delivery date',self::$textdomain) . "
                    <br>{date+x}: " . esc_html__('x is number of additional day Example:{date+1},{date+2}..',self::$textdomain) . "
                    <br>{date-x}: " . esc_html__('x is number of additional day Example:{date-1},{date-2}..',self::$textdomain),true);
		    ?>
    		<div class="input-tips">
    		    <br><i><small><?php echo esc_html__('Leave blank if you want to use global settings.',self::$textdomain) ?></small></i>
    		</div>
    	    </td>
    	</tr>
	<?php endif; ?>
    </table>
</div>
