<?php
$dokansettings=isset($settings['allow_dokan_vendor'])?$settings['allow_dokan_vendor']:"";
?>
<tr>
    <th>
        <?php echo esc_html__('Allow vendor to change delivery time?', self::$textdomain); ?>
    </th>
    <td>
        <div class="radio-buttons-wrapper">
            <input id="allow_dokan_vendor_1" class="input-radio-button" type="radio" name="allow_dokan_vendor" value="1" <?php echo ($dokansettings == 1) ? "checked=checked" : ""; ?>>
            <label class="input-label-button label-button-left" for="allow_dokan_vendor_1">
                <span class="label-button-text">
                    <?php echo esc_html__('Yes', self::$textdomain) ?>
                </span>
            </label>

            <input id="allow_dokan_vendor_0" class="input-radio-button" type="radio" name="allow_dokan_vendor" value="0" <?php echo ($dokansettings == 0) ? "checked=checked" : ""; ?>>
            <label class="input-label-button label-button-right" for="allow_dokan_vendor_0">
                <span class="label-button-text">
                    <?php echo esc_html__('No', self::$textdomain) ?>
                </span>
            </label>
        </div>
    </td>
</tr>