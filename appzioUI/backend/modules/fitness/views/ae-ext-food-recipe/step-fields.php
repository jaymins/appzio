<?php

/**
 * @var backend\modules\fitness\models\AeExtFitExercise $model
 * @var $movements
 * @var $movement_category
 * @var $current_selection
 */
if (!empty($current_selection)) : ?>

    <script>
        var field_recipe_step = <?php echo $current_selection; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-answers-content required ">
    <label class="control-label col-sm-2" for="field-recipe-content">Steps</label>
    <div class="col-sm-8">

        <div class="outer-repeater" data-prefill-variable="field_recipe_step">
            <div data-repeater-list="steps-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header " style="cursor: pointer">
                            <h3 class="box-title"
                                data-field-placeholder="field-input-step"
                                data-field-prefix="Step">Step</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="form-data-group">
                                    <label>Step</label>
                                    <textarea name="field-input-step" class="form-control field-input-step" rows="3"></textarea>
                                </div>
                                <br />
                                <div class="form-data-group">
                                    <label>Time</label>
                                    <input name="field-input-time" type="text" value="" class="form-control">
                                </div>
                                <input name="field-input-id" type="hidden" value="">
                            </div>
                            <div class="form-data-group">
                                <input data-repeater-delete type="button" value="Delete"
                                       class="btn btn-danger small outer"/>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            </div>

            <input data-repeater-create type="button" value="Add" class="outer btn btn-default"/>
        </div>

    </div>
</div>