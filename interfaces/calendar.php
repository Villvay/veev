<?php
	date_default_timezone_set('UTC');
	if (!isset($from_year))
		$from_year = date('Y') - 10;
	if (!isset($to_year))
		$to_year = date('Y') + 1;
	if (!isset($sel_year))
		$sel_year = date('Y');
?>
<div id="calendar">
	<form name="calendar_form">
		<div id="calendar_controllers">
			<input type="button" class="ctrl_button" name="calendar_PrevMonth" onclick="calendar_prev_month();" value="&lt;&lt;">
			<select name="year" onchange="Calendar_FillMonth();">
				<?php for ($Year = $to_year; $Year > $from_year; $Year--){ ?>
					<option value="<?php echo $Year; ?>"<?php if ($Year == $sel_year){ ?> selected<?php } ?>><?php echo $Year; ?></option>
				<?php } ?>
			</select>
			<select name="cal_month" onchange="Calendar_FillMonth();" style="width:92px;">
				<option value="1">January</option>
				<option value="2">February</option>
				<option value="3">March</option>
				<option value="4">April</option>
				<option value="5">May</option>
				<option value="6">June</option>
				<option value="7">July</option>
				<option value="8">August</option>
				<option value="9">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
			</select>
			<input type="button" class="ctrl_button" name="calendar_NextMonth" onclick="calendar_next_month();" value="&gt;&gt;">
			&nbsp;<input type="button" class="close_button" name="calendar_Close" onclick="CloseCalendar();" value=" X ">
			<div id="clock" style="display:none;">
				Hour: <select name="hour" style="width:60px;">
<?php for ($i = 0; $i < 24; $i++){ ?>
					<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
<?php } ?>
				</select> &nbsp; &nbsp;
				Minute: <select name="minute" style="width:60px;">
<?php for ($i = 0; $i < 12; $i++){ ?>
					<option value="<?php echo str_pad($i*5, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i*5, 2, '0', STR_PAD_LEFT); ?></option>
<?php } ?>
				</select>
			</div>
		</div>
		<div class="cal_head_row">
			<div class="cal_head">Sun</div>
			<div class="cal_head">Mon</div>
			<div class="cal_head">Tue</div>
			<div class="cal_head">Wed</div>
			<div class="cal_head">Thu</div>
			<div class="cal_head">Fri</div>
			<div class="cal_head">Sat</div>
		</div>
		<div class="cal_content" id="CalendarContent">
		</div>
	</form>
</div>
