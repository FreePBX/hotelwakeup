<div class="element-container">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <label class="control-label" for="<?php echo $key ?>"><?php echo $title ?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key ?>"></i>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="<?php echo $key ?>" id="<?php echo $key ?>" value="<?php echo $value ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <span id="<?php echo $key ?>-help" class="help-block fpbx-help-block">
            <?php echo $help ?>
            </span>
        </div>
    </div>
</div>