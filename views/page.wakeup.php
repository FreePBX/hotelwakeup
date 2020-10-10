<h1><?php _("Hotel Style Wakeup Calls"); ?></h1>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="fpbx-container">

				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">
							<a href="#" data-toggle="collapse" data-target="#moreinfo" class="collapsed" aria-expanded="false"><i class="glyphicon glyphicon-info-sign"></i></a>&nbsp;&nbsp;&nbsp;<?php echo _('What is Hotel Style Wakeup Calls?')?>
						</div>
					</div>
					<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
					<div class="panel-body collapse" id="moreinfo" aria-expanded="false" style="height: 30px;">
						<p><?php echo sprintf(_('Wake Up calls can be used to schedule a reminder or wakeup call to any valid destination. To schedule a call, dial %s or use the form below'),$code)?></p>
					</div>
				</div>

				<ul class="nav nav-tabs" role="tablist">
					<li data-name="tab1" class="change-tab active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab" id="list"><?php echo _('Call List')?></a></li>
					<li data-name="tab2" class="change-tab"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab" id="settings"><?php echo _('Settings')?></a></li>
				</ul>

				<div class="tab-content display">
					<div id="tab1" class="tab-pane active">
						<?php echo load_view(__DIR__."/view.wakeup.grid.php"); ?>
					</div>
					<div id="tab2" class="tab-pane">
						<?php echo load_view(__DIR__."/view.wakeup.settings.php"); ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
