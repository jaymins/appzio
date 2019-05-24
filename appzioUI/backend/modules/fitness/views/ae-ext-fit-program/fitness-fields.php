<?php

/**
 * @var backend\modules\fitness\models\AeExtFitExercise $model
 * @var $movements
 * @var $movement_category
 * @var $current_selection
 * @var $week
 * @var $weeks
 * @var $exercise
 */

if (!empty($exercise_json)) : ?>

    <script>
        <?php foreach ($weeks as $key=>$value):?>
        <?php if ( !is_array($exercise_json[$key])):?>
        var field_program_exercise_<? echo $key;?> = <?php echo $exercise_json[$key] . ';' ?>
        <?php endif; ?>
        <?php endforeach;?>
    </script>

<?php endif; ?>

<div class="form-group field-answers-content required">
    <label class="control-label col-sm-12" for="field-select-relation-id"
           style="text-align: left; margin-bottom: 10px;">Program
        exercises</label>
    <div class="col-sm-12">

        <div class="outer-repeater" data-prefill-variable="field_program_exercise_<? echo $week; ?>">
            <div data-repeater-list="field_program_exercise_<? echo $week; ?>" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header " style="cursor: pointer">
                            <input type="text" name="field-day" style="border:0px;" readonly>Day</input>

                            <h3 class="box-title" data-field-placeholder="field-select-relation-id"
                                data-field-prefix="Exercise">Associated exercises</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default row">
                                <div class="form-data-group col-lg-9">
                                    <label>Exercise</label>
                                    <select name="field-select-relation-id"
                                            class="form-control field-select-relation-id"
                                            id="field-select-relation-id">

                                        <option value="">N/A</option>

                                        <?php foreach ($exercise as $item) : ?>

                                            <option value="<?php echo $item['id']; ?>">
                                                <?php echo $item['name']; ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>
                                </div>
                                <div class="form-data-group col-lg-3">
                                    <label>Duration</label>
                                    <input name="field-input-duration" type="text" value="" class="form-control">
                                </div>
                                <input name="field-input-id" type="hidden" value="">
                            </div>
                            <input data-repeater-delete type="button" value="Delete"
                                   class="btn btn-danger small outer"/>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            </div>

            <input data-repeater-create type="button" value="Add" class="outer btn btn-default"/>
        </div>

    </div>
</div>