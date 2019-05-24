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
    <label class="control-label col-sm-2" for="aeextarticle-content">Movements</label>
    <div class="col-sm-8">
        <div class="outer-repeater" data-prefill-variable="field_article_data">

            <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">
                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title article-type" data-field-placeholder="field-select-relation-id"
                                data-field-prefix="Movements">Movements</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">
                            <div class="row">
                            <div class="form-data-group col-lg-12">
                                <label>Movement</label>
                                <select name="field-select-relation-id"
                                        class="form-control field-select-relation-id"
                                        id="field-select-relation-id">

                                    <option>N/A</option>

                                    <?php foreach ($movements as $category_item) : ?>

                                        <option value="<?php echo $category_item['id']; ?>">
                                            <?php echo $category_item['name']; ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>
                            </div>
                                <div class="form-data-group col-lg-2">
                                    <label>Weight</label>
                                    <input name="field-select-weight"
                                           type="text" value="" class="form-control">
                                </div>
                                <div class="form-data-group col-lg-3">
                                    <label>Unit</label>
                                    <select name="field-select-unit"
                                            class="form-control "
                                            id="field-select-unit">
                                        <option value="">
                                           Select unit
                                        </option>
                                            <option value="%">
                                               %
                                            </option>
                                        <option value="kg">
                                            kg
                                        </option>
                                        <option value="km">
                                            km
                                        </option>
                                        <option value="max">
                                            MAX
                                        </option>

                                    </select>
                                </div>
                                <div class="form-data-group col-lg-3">
                                    <label>PRs</label>
                                    <select name="field-select-pr"
                                            class="form-control "
                                            id="field-select-pr">
                                        <option>N/A</option>
                                        <?php foreach ($pr as $prs) : ?>

                                            <option value="<?php echo $prs['id']; ?>">
                                                <?php echo $prs['title']; ?>  <?php echo $prs['unit']; ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-data-group col-lg-2">
                                    <label>Reps</label>
                                    <input name="field-select-reps" type="text" value="" class="form-control">
                                </div>
                                <div class="form-data-group col-lg-2">
                                    <label>movement time</label>
                                    <input name="field-select-movement_time" type="text" value="" class="form-control">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 10px">
                                <input name="field-input-id" type="hidden" value="">
                            </div>
                            <input data-repeater-delete type="button" value="Delete"
                                   class="btn btn-danger small outer"/>
                    </div>
                </div>
            </div>
        </div>
            <input data-repeater-create type="button" value="Add" class="outer btn btn-default"/>
    </div>
</div>