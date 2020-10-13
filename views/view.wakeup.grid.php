<div id="toolbar-all">
	<div class="btn-group">
    	<button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#dlgCreateCall">
			<i class="fa fa-plus">&nbsp;</i><?php echo _('Add')?>
    	</button>
	</div>
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
				<?php echo $hotelwakeup->showPage("wakeup.grid.create"); ?>	
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo _('Cancel')?></button>
				<button type="button" class="btn btn-success" id="savecall"><?php echo _('Create Call')?></button>
			</div>
		</div>
	</div>
</div>
