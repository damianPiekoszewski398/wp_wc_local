<div id="shipping-delivery-settings" class="nav-content">
    <table class="rp_table shipping-delivery-time">
        <tr>
            <td colspan="2">
                <?php echo esc_html__("This delivery time only use in calculation if user selected shipping method from cart or checkout page.", self::$textdomain) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="tbl_zone_setting">
                    <?php
                    $shippingSetting = $this->getSetting('addition_shipping_time');
                    $zones = \WC_Shipping_Zones::get_zones();
                    if (!empty($zones)):

                        foreach ($zones as $zone_id => $zone_data):

                            $zone = \WC_Shipping_Zones::get_zone($zone_id);
                            $zone_methods = $zone->get_shipping_methods();
                            if (!empty($zone_methods)):
                                ?>
                                <thead>
                                    <tr class="zone_row">
                                        <th colspan="3">
                                            <?php echo $zone->get_zone_name(); ?>
                                            <?php esc_html_e('Methods', self::$textdomain); ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="name" style="padding-left: 2% !important">
                                            <?php esc_html_e('Name', self::$textdomain); ?>
                                        </th>
                                        <th class="type">
                                            <?php esc_html_e('Type', self::$textdomain); ?>
                                        </th>
                                        <th>
                                            <?php esc_html_e('Additional delivery time', self::$textdomain); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($zone->get_shipping_methods() as $instance_id => $method):

                                        $shippingId = $method->id . ":" . $instance_id;
                                        ?>
                                        <tr>
                                            <td style="padding-left: 2%" class="name">
                                                <?php echo esc_html($method->get_title()); ?>
                                            </td>
                                            <td class="type">
                                                <?php echo esc_html($method->get_method_title()); ?>
                                            </td>
                                            <td class="day-from">

                                                <input type="text" name="addition_shipping_time[<?php echo $shippingId ?>]" value='<?php echo isset($shippingSetting[$shippingId]) ? $shippingSetting[$shippingId] : ""; ?>' />

                                            </td>


                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
                    <?php
                    $world_zone = \WC_Shipping_Zones::get_zone(0);
                    $world_zone_methods = $world_zone->get_shipping_methods();
                    if (!empty($world_zone_methods)):
                        ?>
                        <thead>
                            <tr class="zone_row">
                                <th colspan="4">
                                    <?php $zone_name = esc_html__('Rest of the World', self::$textdomain); ?>
                                    <?php echo $zone_name; ?>
                                    <?php esc_html_e('Methods', self::$textdomain); ?>
                                </th>
                            </tr>
                            <tr>
                                <th class="name" style="padding-left: 2% !important">
                                    <?php esc_html_e('Name', self::$textdomain); ?>
                                </th>
                                <th class="type">
                                    <?php esc_html_e('Type', self::$textdomain); ?>
                                </th>
                                <th>
                                    <?php esc_html_e('Additional delivery time', self::$textdomain); ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($world_zone_methods as $instance_id => $method):
                                $shippingId = $method->id . ":" . $instance_id;
                                ?>
                                <tr>
                                    <td style="padding-left: 2%" class="name">
                                        <?php echo esc_html($method->get_title()); ?>
                                    </td>
                                    <td class="type">
                                        <?php echo esc_html($method->get_method_title()); ?>
                                    </td>
                                    <td class="day-from">

                                        <input type="text" name="addition_shipping_time[<?php echo $shippingId ?>]" value='<?php echo isset($shippingSetting[$shippingId]) ? $shippingSetting[$shippingId] : ""; ?>' />

                                    </td>


                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php
                    endif;
                    $methods = WC()->shipping->get_shipping_methods();
                    unset($methods['flat_rate'], $methods['free_shipping'], $methods['local_pickup']);
                    if (!empty($methods)):
                        ?>
                        <thead>
                            <tr class="zone_row">
                                <th colspan="4">
                                    <?php esc_html_e('Other Methods', 'woocommerce-shipping-estimate'); ?>
                                </th>
                            </tr>
                            <tr>
                                <th class="name" style="padding-left: 2% !important">
                                    <?php esc_html_e('Name', self::$textdomain); ?>
                                </th>
                                <th class="type">
                                    <?php esc_html_e('Type', self::$textdomain); ?>
                                </th>
                                <th>
                                    <?php esc_html_e('Additional delivery time', self::$textdomain); ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($methods as $instance_id => $method):
                                $shippingId = $method->id;
                                ?>
                                <tr>
                                    <td style="padding-left: 2%" class="name">
                                        <?php echo esc_html($method->get_title()); ?>
                                    </td>
                                    <td class="type">
                                        <?php echo esc_html($method->get_method_title()); ?>
                                    </td>
                                    <td class="day-from">

                                        <input type="text" name="addition_shipping_time[<?php echo $shippingId ?>]" value='<?php echo isset($shippingSetting[$shippingId]) ? $shippingSetting[$shippingId] : "" ?>' />

                                    </td>


                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php
                    endif;
                    ?>
                </table>
            </td>
        </tr>
        <tr class="last-row">
            <td><input type="submit" class="button button-primary" name="btn_submit" value="<?php echo esc_html__("Save Settings", self::$textdomain) ?>" /></td>
            <td>
                &nbsp;
            </td>
        </tr>
    </table>
</div>