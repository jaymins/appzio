<?php

use yii\helpers\Url;

/**
 * @var backend\modules\items\models\AeExtItem $model
 * @var $categories
 * @var $categories_json
 */

if ( !empty($categories_json) ) : ?>

    <script>
        var field_item_categories = <?php echo $categories_json; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-categories-content required">
    <label class="control-label col-sm-2" for="field-categories-content">Categories</label>
    <div class="col-sm-8">

        <div id="item-categories" class="outer-repeater" data-prefill-variable="field_item_categories">
            <div data-repeater-list="categories-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title" data-field-placeholder="field-select-category" data-field-prefix="Category">Category</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="form-data-group">
                                    <select name="field-select-category" class="form-control field-select-category">

                                        <option value="">N/A</option>

                                        <?php foreach ($categories as $category) : ?>

                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>

                                        <?php endforeach; ?>

                                    </select>
                                    <input name="field-input-id" type="hidden" value="">
                                </div>
                            </div>

                            <input data-repeater-delete type="button" value="Delete" class="btn btn-danger small outer"/>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            </div>

            <input data-repeater-create type="button" value="Add" class="outer btn btn-default"/>
        </div>

    </div>
</div>