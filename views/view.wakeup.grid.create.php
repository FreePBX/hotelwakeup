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