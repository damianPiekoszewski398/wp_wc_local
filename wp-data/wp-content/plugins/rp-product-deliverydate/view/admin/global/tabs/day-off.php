<div id="dayoff-settings" class="nav-content">
	<table class="form-table rpesp-table-buttons">
		<tr>
			<th>
				<?php echo esc_html__('Week Day Off', self::$textdomain) ?>
			</th>
			<td>
				<ul class="checkbox">
					<?php
					$weekday = $this->getSetting("weekdayoff");
					foreach (self::$day as $key => $value):
						$checked = (!empty($weekday) && is_array($weekday) && in_array($key, $weekday)) ? "checked=checked" : "";
						echo '<li><input type="checkbox" ' . $checked . ' name="weekdayoff[' . $key . ']" value="' . $key . '"  value="' . $key . '">' . $value . "</li>";
					endforeach;
					?>
				</ul>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo esc_html__('Specific Day Off', self::$textdomain) ?>
			</th>
			<td>
				<table id="tbl_specific_day">
					<tr id="th_rpesp_specific_day">
						<th>
							<?php echo esc_html__('Day', self::$textdomain) ?>
						</th>
						<th>
							<?php echo esc_html__('Month', self::$textdomain) ?>
						</th>
						<th>
							<?php echo esc_html__('Year', self::$textdomain) ?>
						</th>
						<th>&nbsp;</th>
					</tr>
					<?php
					$specific_day = $this->getSetting("specific_day");

					if (!empty($specific_day) && isset($specific_day["day"]) && count($specific_day["day"]) > 0):
						for ($spe = 0; $spe < count($specific_day["day"]); $spe++):
							include self::$plugin_dir . 'view/admin/global/specific-day.php';
						endfor;
					endif;
					?>
					<tr>
						<td colspan="4"><a href="javascript:void(0);" class="rpesp_adddayrow button-addmore">
								<?php echo esc_html__("Add More", self::$textdomain) ?>
							</a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo esc_html__('Specific Duration Off', self::$textdomain) ?>
			</th>
			<td>
				<table id="tbl_specific_period_day">
					<tr id="th_rpesp_specific_period_day">
						<th>
							<?php echo esc_html__('From:', self::$textdomain) ?>
						</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>
							<?php echo esc_html__('To:', self::$textdomain) ?>
						</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					<?php
					$specific_period = $this->getSetting("specific_period");
					if (!empty($specific_period) && isset($specific_period["fday"]) && count($specific_period["fday"]) > 0):
						for ($sp = 0; $sp < count($specific_period["fday"]); $sp++):
							include self::$plugin_dir . 'view/admin/global/specific-period.php';
						endfor;
					endif;
					?>
					<tr>
						<td colspan="7"><a href="javascript:void(0);" class="rpesp_addperiodrow button-addmore">
								<?php echo esc_html__("Add More", self::$textdomain) ?>
							</a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo esc_html__('Delivery End Time', self::$textdomain) ?>
			</th>
			<td>

				<?php
				$hours = $this->getSetting("hours");
				$minute = $this->getSetting("minute");
				foreach (self::$day as $key => $value):
					?>
					<table cellpadding="0">
						<tr>
							<th style="width: 75px;padding: 5px">
								<?php echo $value; ?>
							</th>
							<td style="padding: 5px">
								<select name="hours[<?php echo $key; ?>]" class="autowidth">
									<option value="">
										<?php echo __("Hours", "rp-product-deliverydate") ?>
									</option>
									<?php
									for ($i = 0; $i <= 23; $i++):
										$selected = (!empty($hours) && isset($hours[$key]) && $hours[$key] == $i) ? "selected=selected" : "";
										echo '<option ' . $selected . ' value="' . $i . '" >' . $i . '</option>';
									endfor;
									?>
								</select>
								<select name="minute[<?php echo $key; ?>]" class="autowidth">
									<option value="">
										<?php echo esc_html__("Minute", self::$textdomain) ?>
									</option>
									<?php
									for ($i = 0; $i <= 59; $i++):
										$selected = (!empty($minute) && isset($minute[$key]) && $minute[$key] == $i) ? "selected=selected" : "";
										echo '<option ' . $selected . ' value="' . $i . '" >' . $i . '</option>';
									endfor;
									?>
								</select>
							</td>
						</tr>
					</table>

					<?php
				endforeach;
				?>

			</td>

		</tr>
		<?php do_action("rpesp_dayoff_tab_additional_fields",$this->rpesp_settings); ?>
		<tr class="last-row">
			<td>&nbsp;</td>
			<td>
				<input type="submit" class="button button-primary" name="btn_submit"
					value="<?php echo esc_html__("Save Settings", self::$textdomain) ?>" />
			</td>
		</tr>
	</table>
</div>