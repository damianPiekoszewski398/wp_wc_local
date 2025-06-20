<tr>
    <td>
        <select name="specific_period[fday][]">
            <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
            <?php
            for ($day = 1; $day <= 31; $day++):
                $selected = (isset($specific_period) && isset($specific_period["fday"][$sp]) && $specific_period["fday"][$sp] == $day) ? "selected=selected" : "";
                echo '<option value="' . $day . '" ' . $selected . '>' . $day . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_period[fmonth][]">
            <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
            <?php
            foreach (self::$month as $key => $value):
                $selected = (isset($specific_period) && isset($specific_period["fmonth"][$sp]) && $specific_period["fmonth"][$sp] == $key) ? "selected=selected" : "";
                echo '<option value="' . $key . '" ' . $selected . ' >' . $value . '</option>';
            endforeach;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_period[fyear][]">
            <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
            <option value="every" <?php echo (isset($specific_period) && isset($specific_period["fyear"][$sp]) && $specific_period["fyear"][$sp] == "every") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Every year', self::$textdomain) ?></option>
            <?php
            for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                $selected = (isset($specific_period) && isset($specific_period["fyear"][$sp]) && $specific_period["fyear"][$sp] == $year) ? "selected=selected" : "";
                echo '<option value="' . $year . '" ' . $selected . ' >' . $year . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_period[tday][]">
            <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
            <?php
            for ($day = 1; $day <= 31; $day++):
                $selected = (isset($specific_period) && isset($specific_period["tday"][$sp]) && $specific_period["tday"][$sp] == $day) ? "selected=selected" : "";
                echo '<option value="' . $day . '" ' . $selected . '>' . $day . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_period[tmonth][]">
            <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
            <?php
            foreach (self::$month as $key => $value):
                $selected = (isset($specific_period) && isset($specific_period["tmonth"][$sp]) && $specific_period["tmonth"][$sp] == $key) ? "selected=selected" : "";
                echo '<option value="' . $key . '" ' . $selected . ' >' . $value . '</option>';
            endforeach;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_period[tyear][]">
            <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
            <option value="every" <?php echo (isset($specific_period) && isset($specific_period["tyear"][$sp]) && $specific_period["tyear"][$sp] == "every") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Every year', self::$textdomain) ?></option>
            <?php
            for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                $selected = (isset($specific_period) && isset($specific_period["tyear"][$sp]) && $specific_period["tyear"][$sp] == $year) ? "selected=selected" : "";
                echo '<option value="' . $year . '" ' . $selected . ' >' . $year . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td><a href="javascript:void(0);" class="rpesp_removeperiodrow  remove-icon"><?php echo esc_html__("Remove", self::$textdomain) ?></a></td>
</tr>
