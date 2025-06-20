<div style="display: none;">
    <table id="rpesp_tblinitrow">
        <tr class="rpesp_initrow">
            <td>
                <select name="specific_day[day][]">
                    <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
                    <?php
                    for ($day = 1; $day <= 31; $day++):
                        echo '<option value="' . $day . '">' . $day . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_day[month][]">
                    <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
                    <?php
                    foreach (self::$month as $key => $value):
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    endforeach;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_day[year][]">
                    <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
                    <option value="every"><?php echo esc_html__('Every year', self::$textdomain) ?></option>
                    <?php
                    for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                        echo '<option value="' . $year . '">' . $year . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td><a href="javascript:void(0);" class="rpesp_removedayrow  remove-icon"><?php echo esc_html__("Remove", self::$textdomain); ?></a></td>
        </tr>
    </table>
    <table id="rpesp_tblinitperiodrow">
        <tr class="rpesp_thinitperiodrow">
            <td>
                <select name="specific_period[fday][]">
                    <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
                    <?php
                    for ($day = 1; $day <= 31; $day++):
                        echo '<option value="' . $day . '">' . $day . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_period[fmonth][]">
                    <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
                    <?php
                    foreach (self::$month as $key => $value):
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    endforeach;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_period[fyear][]">
                    <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
                    <option value="every"><?php echo esc_html__('Every year', self::$textdomain) ?></option>
                    <?php
                    for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                        echo '<option value="' . $year . '">' . $year . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_period[tday][]">
                    <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
                    <?php
                    for ($day = 1; $day <= 31; $day++):
                        echo '<option value="' . $day . '">' . $day . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_period[tmonth][]">
                    <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
                    <?php
                    foreach (self::$month as $key => $value):
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    endforeach;
                    ?>
                </select>
            </td>
            <td>
                <select name="specific_period[tyear][]">
                    <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
                    <option value="every"><?php echo esc_html__('Every year', self::$textdomain) ?></option>
                    <?php
                    for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                        echo '<option value="' . $year . '">' . $year . '</option>';
                    endfor;
                    ?>
                </select>
            </td>
            <td><a href="javascript:void(0);" class="rpesp_removeperiodrow remove-icon"><?php echo esc_html__("Remove", self::$textdomain) ?></a></td>
        </tr>
    </table>
</div>
