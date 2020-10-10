<div id="toolbar-all">
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#dlgCreateCall">
        <i class="fa fa-plus"></i> <?php echo _('Add')?>
    </button>
    <span class="btn btn-time disabled">
        <b><?php echo _("Server time")?>:</b>
        <div id="servertime" data-time="<?php echo time()?>" data-zone="<?php echo date("e")?>" style="display: inline;"><span><?php echo _("Not received")?></span></div>
    </span>
</div>
<table id="callgrid" class="table table-striped"
	   data-url="ajax.php?module=hotelwakeup&amp;command=getable"
	   data-cache="false"
	   data-toolbar="#toolbar-all"
	   data-maintain-selected="true"
	   data-show-columns="false"
	   data-show-toggle="true"
	   data-toggle="table"
	   data-pagination="true"
	   data-search="true"
	   data-show-refresh="true">
    <thead>
        <tr>
            <th data-field="time"><?php echo _("Time")?></th>
            <th data-field="date"><?php echo _("Date")?></th>
            <th data-field="destination"><?php echo _("Destination")?></th>
            <th data-field="actionsjs"><?php echo _("Actions")?></th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="dlgCreateCall" tabindex="-1" role="dialog" aria-labelledby="dlgCreateCallLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="dlgCreateCallLabel"><?php echo _('Add new Wakeup Call')?></h4>
			</div>
			<div class="modal-body">
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
													<?php echo \FreePBX::View()->languageDrawSelect('setlanguage', "",_("Use Default Language")); ?>
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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo _('Cancel')?></button>
				<button type="button" class="btn btn-success" id="savecall"><?php echo _('Create Call')?></button>
			</div>
		</div>
	</div>
</div>
