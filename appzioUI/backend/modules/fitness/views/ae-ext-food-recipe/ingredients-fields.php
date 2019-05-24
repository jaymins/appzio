<?php

/**
 * @var backend\modules\fitness\models\AeExtFitExercise $model
 * @var $ingredients
 * @var $current_selection
 */

if (!empty($current_selection)) : ?>

    <script>
        var field_recipe_ingredient = <?php echo $current_selection; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-answers-content required">
    <label class="control-label col-sm-2" for="field-recipe-content">Ingredients</label>
    <div class="col-sm-8">

        <div class="outer-repeater" data-prefill-variable="field_recipe_ingredient">
            <div data-repeater-list="ingredients-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header " style="cursor: pointer">
                            <h3 class="box-title"
                                data-field-placeholder="field-select-ingredient"
                                data-field-prefix="Ingredient">Ingredient</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="row">
                                    <div class="form-data-group col-lg-9">
                                        <label>Ingredients</label>
                                        <select name="field-select-ingredient" class="form-control field-select-ingredient">

                                            <option value="">N/A</option>

                                            <?php foreach ($ingredients as $ingredient) : ?>

                                                <option value="<?php echo $ingredient['id']; ?>">
                                                    <?php echo $ingredient['name']; ?>
                                                    <?php echo ($ingredient['unit']) ? $ingredient['unit'] : ''; ?></option>

                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                    <div class="form-data-group col-lg-3">
                                        <label>Quantity</label>
                                        <input name="field-input-quantity" type="text" value="" class="form-control">
                                    </div>
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