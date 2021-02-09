<h1><?php echo _("Hotel Style Wakeup Calls"); ?></h1>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="fpbx-container">
				<?php echo show_help( "<p>" . sprintf(_('Wake Up calls can be used to schedule a reminder or wakeup call to any valid destination. To schedule a call, dial %s or use the form below'), "<b>" . $hotelwakeup->getCode() . "</b>") . "</p>", _('What is Hotel Style Wakeup Calls?'), false, true, "info"); ?>
				<?php echo $hotelwakeup->showPage("wakeup.grid"); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="modules/hotelwakeup/assets/js/views/wakeup.js"></script>