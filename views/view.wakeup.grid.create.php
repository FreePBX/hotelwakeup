<form id="callform">
	<div class="fpbx-container">
		<div class="display no-border">
			<div class="element-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="destination"><?php echo _('Destination')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="destination"></i>
								</div>
								<div class="col-md-9"><input type="text" class="form-control" name="destination" id="destination" value=""></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="destination-help" class="help-block fpbx-help-block"><?php echo _('Destination to call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="time"><?php echo _('Time')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="time"></i>
								</div>
								<div class="col-md-9"><input type="text" class="form-control" id="time" value=""></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="time-help" class="help-block fpbx-help-block"><?php echo _('Time to call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="repeat_wakeup"><?php echo _('Repeat Wakeup')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="repeat_wakeup"></i>
								</div>
								<div class="col-md-9">
									<input type="checkbox" id="repeat_wakeup" name="repeat_wakeup" value="1"> <?php echo _('Enable repeat options')?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="repeat_wakeup-help" class="help-block fpbx-help-block"><?php echo _('Enable to repeat the wakeup call on multiple days')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="single-day-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="day"><?php echo _('Day')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="day"></i>
								</div>
								<div class="col-md-9"><input type="text" class="form-control" id="day" value=""></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="day-help" class="help-block fpbx-help-block"><?php echo _('Day to call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="repeat-options-container" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="repeat_type"><?php echo _('Repeat Type')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="repeat_type"></i>
								</div>
								<div class="col-md-9">
									<select class="form-control" id="repeat_type" name="repeat_type">
										<option value="consecutive"><?php echo _('Consecutive Days')?></option>
										<option value="weekdays"><?php echo _('Specific Weekdays')?></option>
										<option value="until_date"><?php echo _('Until Date')?></option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="repeat_type-help" class="help-block fpbx-help-block"><?php echo _('Choose how you want to repeat the wakeup call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="start-date-container" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="start_date"><?php echo _('Start Date')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="start_date"></i>
								</div>
								<div class="col-md-9"><input type="text" class="form-control" id="start_date" value=""></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="start_date-help" class="help-block fpbx-help-block"><?php echo _('First day to start the wakeup calls')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="consecutive-days-container" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="consecutive_days"><?php echo _('Number of Days')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="consecutive_days"></i>
								</div>
								<div class="col-md-9">
									<input type="number" class="form-control" id="consecutive_days" min="1" max="365" value="1">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="consecutive_days-help" class="help-block fpbx-help-block"><?php echo _('Number of consecutive days to repeat the wakeup call (max 365)')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="weekdays-container" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label"><?php echo _('Weekdays')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="weekdays"></i>
								</div>
								<div class="col-md-9">
									<div class="checkbox-group">
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="1"> <?php echo _('Monday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="2"> <?php echo _('Tuesday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="3"> <?php echo _('Wednesday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="4"> <?php echo _('Thursday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="5"> <?php echo _('Friday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="6"> <?php echo _('Saturday')?>
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="weekdays[]" value="0"> <?php echo _('Sunday')?>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="weekdays-help" class="help-block fpbx-help-block"><?php echo _('Select which days of the week to repeat the wakeup call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container" id="end-date-container" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="end_date"><?php echo _('End Date')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="end_date"></i>
								</div>
								<div class="col-md-9"><input type="text" class="form-control" id="end_date" value=""></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="end_date-help" class="help-block fpbx-help-block"><?php echo _('Last day to repeat the wakeup call')?></span>
					</div>
				</div>
			</div>
			<div class="element-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="setlanguage"><?php echo _('Language')?></label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="setlanguage"></i>
								</div>
								<div class="col-md-9">
									<?php echo $hotelwakeup->languageDrawSelect('setlanguage', "",_("Use Default Language")); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="setlanguage-help" class="help-block fpbx-help-block"><?php echo _('Calling language')?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
