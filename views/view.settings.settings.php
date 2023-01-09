<div class="display full-border">
    <form id="settingsform">
        <!-- Block - Operator Mode -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="operator_mode"><?php echo _('Operator Mode')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="operator_mode"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="radioset">
                                    <input id="operator_mode_yes" type="radio" value="yes" name="operator_mode">
                                    <label for="operator_mode_yes"><?php echo _('Yes')?></label>
                                    <input id="operator_mode_no" type="radio" value="no" name="operator_mode">
                                    <label for="operator_mode_no"><?php echo _('No')?></label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="operator_mode-help" class="help-block fpbx-help-block">
                        <?php echo _('When Operator Mode is enabled it will allow designated extentions to create wake up calls for any valid destination. If disabled calls can only be placed back to the caller ID of the user scheduling the wakeup call')?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Block - Operator Mode -->

        <!-- Blcok - Max Destintaion -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="extensionlength"><?php echo _('Max Destination Length')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="extensionlength"></i>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="extensionlength" id="extensionlength" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="extensionlength-help" class="help-block fpbx-help-block">
                        <?php echo _('This controls the maximum number of digits an operator can send a wakeup call to. Set to 10 or 11 to allow wake up calls to outside numbers')?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Blcok - Max Destintaion -->

        <!-- Block Operator Extensions -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="operator_extensions"><?php echo _('Operator Extensions')?></label>
                               	<i class="fa fa-question-circle fpbx-help-icon" data-for="operator_extensions"></i>
								<div class="row">
									<div class="col-md-12">
										<span id="operator_extensions-help" class="help-block fpbx-help-block">
											<?php echo _('Enter the Caller IDs of each telephone you wish to be recognized as an "Operator". Operator extensions are allowed to create wakeup calls for any valid destination. Numbers can be extension numbers or full caller ID numbers.')?>
										</span>
									</div>
								</div>
                            </div>
                            <div class="col-md-9">
								<input type="hidden" class="form-control" name="operator_extensions" id="operator_extensions" value="">
								<div class="BoxExtensionsList">
									<div class="row">
										<div class="col-md-6">
											<div class="alert alert-info" role="alert">
												<?php echo _("Available"); ?>
											</div>
										</div>
										<div class="col-md-6">
											<div class="alert alert-info" role="alert">
												<?php echo _("Selected"); ?>
											</div>								
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<ul class="ExtensionList list-group" id="available_extensions"></ul>
										</div>
										<div class="col-md-6">
											<ul class="ExtensionList ExtensionListSelected list-group" id="selected_extensions"></ul>
											<div class="input-group">
												<input type="text" class="form-control" id="operator_add_number" placeholder="<?php echo _("Number to Add"); ?>">
												<span class="input-group-btn">
													<button class="btn btn-default" id="btn_operator_add_number" type="button" title="<?php echo _("Add Manually"); ?>"><i class="fa fa-plus" aria-hidden="true"></i></button>
												</span>
											</div>
										</div>
									</div>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        <!-- Block Operator Extensions -->

        <!-- Block - Ring Time -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="waittime"><?php echo _('Ring Time')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="waittime"></i>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="waittime" id="waittime" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="waittime-help" class="help-block fpbx-help-block">
                        <?php echo _('The number of seconds for the phone to ring. Consider setting lower than the voicemail threshold or the wakeup call can end up going to voicemail')?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Block - Ring Time -->

        <!-- Blcok - Retry Time -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="retrytime"><?php echo _('Retry Time')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="retrytime"></i>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="retrytime" id="retrytime" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="retrytime-help" class="help-block fpbx-help-block">
                        <?php echo _('The number of seconds to wait between retrys.  A "retry" happens if the wakeup call is not answered')?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Blcok - Retry Time -->

        <!-- Block - Max Retries -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="maxretries"><?php echo _('Max Retries')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="maxretries"></i>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="maxretries" id="maxretries" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="maxretries-help" class="help-block fpbx-help-block">
                        <?php echo _('The maximum number of times the system should attempt to deliver the wakeup call when there is no answer. Zero retries means only one call will be placed')?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Block - Max Retries -->

        <!-- Block - Wake Up Caller ID -->
        <div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="callerid"><?php echo _('Wake Up Caller ID')?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="callerid"></i>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="callerid" id="callerid" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <span id="callerid-help" class="help-block fpbx-help-block">
                        <?php echo _('CallerID for Wake Up Calls<br><br>Format: <b>&lt;#######&gt;</b>. You can also use the format: "hidden" <b>&lt;#######&gt;</b> to hide the CallerID sent out over Digital lines if supported (E1/T1/J1/BRI/SIP/IAX)')?></span>
                </div>
            </div>
        </div>
        <!-- Block - Wake Up Caller ID -->
    </form>
</div>