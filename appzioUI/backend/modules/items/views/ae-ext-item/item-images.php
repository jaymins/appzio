<?php

/**
 * @var backend\modules\items\models\AeExtItem $model
 * @var $questions
 * @var $current_selection
 * @var $form
 * @var $model
 * @var $images_json
 */

if (!empty($images_json)) : ?>

    <script>
        var field_item_images = <?php echo $images_json; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-images-content required">
    <label class="control-label col-sm-2" for="field-images-content">Images</label>
    <div class="col-sm-8">

        <div id="item-images" class="outer-repeater" data-prefill-variable="field_item_images">
            <div data-repeater-list="images-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title" data-field-placeholder="field-upload-image"
                                data-field-prefix="Image">Image</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="form-data-group">
                                    <?= $form->field($model, 'images', [
                                        'template' => '<div class="col-sm-12">{input}</div>'
                                    ])->fileInput([
                                        'accept' => 'image/*',
                                        'class' => 'form-control field-upload-image',
                                    ]);
                                    ?>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input name="field-image-link" type="url" disabled value="" class="form-control">
                                        </div>
                                    </div>

                                    <input name="field-image-name" type="hidden" value="">
                                    <input name="field-image-id" type="hidden" value="">
                                </div>
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