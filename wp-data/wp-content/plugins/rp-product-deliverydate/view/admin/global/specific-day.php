<tr>
    <td>
        <select name="specific_day[day][]">
            <option value="0"><?php echo esc_html__('Select Day', self::$textdomain) ?></option>
            <?php
            for ($day = 1; $day <= 31; $day++):
                $selected = (isset($specific_day) && isset($specific_day["day"][$spe]) && $specific_day["day"][$spe] == $day) ? "selected=selected" : "";
                echo '<option value="' . $day . '" ' . $selected . ' >' . $day . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_day[month][]">
            <option value="0"><?php echo esc_html__('Select Month', self::$textdomain) ?></option>
            <?php
            foreach (self::$month as $key => $value):
                $selected = (isset($specific_day) && isset($specific_day["month"][$spe]) && $specific_day["month"][$spe] == $key) ? "selected=selected" : "";
                echo '<option value="' . $key . '" ' . $selected . ' >' . $value . '</option>';
            endforeach;
            ?>
        </select>
    </td>
    <td>
        <select name="specific_day[year][]">
            <option value="0"><?php echo esc_html__('Select Year', self::$textdomain) ?></option>
            <option value="every" <?php echo (isset($specific_day) && isset($specific_day["year"][$spe]) && $specific_day["year"][$spe] == "every") ? "selected=selected" : ""; ?> ><?php echo esc_html__('Every year', self::$textdomain) ?></option>
            <?php
            for ($year = date("Y"); $year <= date("Y") + 50; $year++):
                $selected = (isset($specific_day) && isset($specific_day["year"][$spe]) && $specific_day["year"][$spe] == $year) ? "selected=selected" : "";
                echo '<option value="' . $year . '" ' . $selected . ' >' . $year . '</option>';
            endfor;
            ?>
        </select>
    </td>
    <td><a href="javascript:void(0);" class="rpesp_removedayrow  remove-icon"><?php echo esc_html__("Remove", self::$textdomain) ?></a></td>
</tr>
