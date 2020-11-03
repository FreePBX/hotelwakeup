<h1><?php echo _("Hotel Style Wakeup Calls"); ?></h1>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="fpbx-container">

				<div class="panel panel-info" id="boxMoreInfo">
					<div class="panel-heading collapsed" data-target="#moreinfo" data-toggle="collapse" class="collapsed" aria-expanded="false">
						<h3 class="panel-title">
							<span class="pull-left"><i class="fa fa-info-circle fa-fw fa-lg"></i></span>
							<?php echo _('What is Hotel Style Wakeup Calls?') ?>
							<span class="pull-right"><i class="chevron fa fa-fw fa-lg"></i></span>
						</h3>
					</div>
					<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
					<div class="panel-collapse collapse" id="moreinfo" aria-expanded="false" style="height: 0px;">
						<div class="panel-body ">
							<p><?php echo sprintf(_('Wake Up calls can be used to schedule a reminder or wakeup call to any valid destination. To schedule a call, dial %s or use the form below'), "<b>" . $hotelwakeup->getCode() . "</b>")?></p>
						</div>
					</div>
				</div>

				<?php echo $hotelwakeup->showPage("wakeup.grid"); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="modules/hotelwakeup/assets/js/views/wakeup.js"></script>