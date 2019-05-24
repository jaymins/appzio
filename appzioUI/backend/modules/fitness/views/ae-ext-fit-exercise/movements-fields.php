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
                        <div class="box-body outer-body">
                            <div class="form-data-group">
                                <label>Component</label>
                                <select name="field-select-relation-id"
                                        class="form-control field-select-relation-id"
                                        id="field-select-relation-id">

                                    <option>N/A</option>

                                    <?php foreach ($component as $category_item) : ?>

                                        <option value="<?php echo $category_item['id']; ?>">
                                            <?php echo $category_item['admin_name']; ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>
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