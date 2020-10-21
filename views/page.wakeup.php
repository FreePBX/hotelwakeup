<h1><?php echo _("Hotel Style Wakeup Calls"); ?></h1>
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
						<p><?php echo sprintf(_('Wake Up calls can be used to schedule a reminder or wakeup call to any valid destination. To schedule a call, dial %s or use the form below'), $hotelwakeup->getCode())?></p>
					</div>
				</div>
				
				<?php echo $hotelwakeup->showPage("wakeup.grid"); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="modules/hotelwakeup/assets/js/views/wakeup.js"></script>