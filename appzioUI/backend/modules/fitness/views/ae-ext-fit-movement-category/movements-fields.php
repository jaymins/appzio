<?php

use yii\helpers\Url;

/**
 * @var $relations_json
 * @var $movements
 * @var $category

 */

if (!empty($relations_json)) : ?>

    <script>
        var field_article_data = <?php echo $relations_json; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-aeextarticle-content required">
    <label class="control-label col-sm-2" for="aeextarticle-content">Components</label>
    <div class="col-sm-8">
        <div class="outer-repeater" data-prefill-variable="field_article_data">

            <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">
                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title article-type" data-field-placeholder="field-select-relation-id"
                                data-field-prefix="Component">Component</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body">
                            <div class="inner-repeater-body">
                                <div data-repeater-list="inner-group" class="inner">
                                    <div data-repeater-item class="inner row" style="margin-bottom: 10px;">
                                        <div class="form-data-group col-lg-12">
                                            <label>Movement</label>
                                            <select name="field-select-sub-relation-id"
                                                    class="form-control field-select-sub-relation-id "
                                                    id="field-select-sub-relation-id">

                                                <option>N/A</option>

                                                <?php foreach ($movements as $movement_item) : ?>

                                                    <option value="<?php echo $movement_item['id']; ?>">
                                                        <?php echo $movement_item['name']; ?>
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>
                                        </div>
                                        <div class="form-data-group col-lg-2">
                                            <label>Weight</label>
                                            <input name="field-input-sub-weight" type="text" value="" class="form-control">
                                        </div>
                                        <div class="form-data-group col-lg-3">
                                            <label>Unit</label>
                                            <select name="field-input-sub-unit" class="form-control"
                                                    id="field-input-unit">
                                                <option value="%">Prs %</option>
                                                <option value="kg">Kilogram kg</option>
                                                <option value="km">Kilometers km</option>
                                            </select>
                                        </div>
                                        <div class="form-data-group col-lg-2">
                                            <label>Reps</label>
                                            <input name="field-input-sub-reps" type="text" value="" class="form-control">
                                        </div>
                                        <div class="form-data-group col-lg-2">
                                            <label>Rest</label>
                                            <input name="field-input-sub-rest" type="text" value="" class="form-control">
                                        </div>
                                        <div class="form-data-group col-lg-3">
                                            <label>Movement time</label>
                                            <input name="field-input-sub-time" type="text" value="" class="form-control">
                                        </div>
                                        <input name="field-input-sub-id" type="hidden" value="">
                                        <div class="col-sm-12" style="padding: 15px">
                                            <div style="clear: both"></div>
                                            <input data-repeater-delete type="button" value="Delete"
                                                   class="btn btn-danger inner pull-right "/>
                                        </div>
                                    </div>
                                </div>

                                <input data-repeater-create type="button" value="Add movement"
                                       class="btn btn-default inner" />
                            </div>
                        </div>
                </div>
            </div>
        </div>
            <input data-repeater-create type="button" value="Add" class="outer btn btn-default"/>
    </div>
</div>