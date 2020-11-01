<div class="element-container">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <label class="control-label" for="<?php echo $key ?>"><?php echo $label ?></label>

                        <?php if (! empty($help)) : ?>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key ?>"></i>
                        <?php endif; ?>

                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                name="<?php echo $key ?>"
                                id="<?php echo $key ?>"
                                value="<?php echo $value ?>"
                                placeholder="<?php echo $default ?>"
                            >
                            <div class="input-group-btn">

                                <?php if ($jplayer): ?>
                                <div id="jplayer-file-<?php echo $key ?>" class="jp-jplayer"></div>
                                <a class="btn btn-default btn-cmd-play hidden"><i class="fa fa-play play" aria-hidden="true"></i></a>
                                <?php endif; ?>

                                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#" class="btn-copy-default"><i class="fa fa-files-o">&nbsp;&nbsp;</i>Use the Default Value</a>
                                        <a href="#" class="btn-restart-input"><i class="fa fa-refresh">&nbsp;&nbsp;</i>Restar Value</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="#" class="btn-clean-input" style="color: #e5202e;"><i class="fa fa-trash-o">&nbsp;&nbsp;</i>Clean</a>
                                    </li>
                                </ul>                      
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php if ($key == "SayUnixTime") : ?>
    <div class="row" style="padding-top: 2px;">
        <div class="col-md-3"></div>
        <div class="col-md-9">
            <span id="SayUnixTimeExampleBox" class="label label-primary">
                <i id="SayUnixTimeExampleIcon" class="fa fa-spinner fa-spin fa-fw">&nbsp;</i>
                <span id="SayUnixTimeExample"><?php echo _("Checking...") ?></span>
            </span>
        </div>
    </div>
    <?php endif; ?>



    <?php if (! empty($help)) : ?>
    <div class="row">
        <div class="col-md-12">
            <span id="<?php echo $key ?>-help" class="help-block fpbx-help-block">
            <?php echo $help ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

</div>