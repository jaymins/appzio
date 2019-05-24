<?php

/**
 * @var backend\modules\quizzes\models\AeExtQuizQuestion $model
 * @var $images
 * @var $images_tags
 * @var $article_templates
 */

if (!empty($content)) : ?>

    <script>
        var field_quiz_answers = <?php echo $content; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-answers-content required">
    <label class="control-label col-sm-2" for="field-answers-content">Answers</label>
    <div class="col-sm-8">

        <div class="outer-repeater" data-prefill-variable="field_quiz_answers">
            <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title" data-field-placeholder="field-input" data-field-prefix="Answer">
                                Answer</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="form-data-group">
                                    <textarea name="field-input" class="form-control outer field-input" rows="3"
                                              placeholder="Text ..."></textarea>
                                    <input name="field-input-id" type="hidden" value="">
                                    <div class="checkbox">
                                        <label>
                                            <input name="field-input-status" type="checkbox" value="1"> Mark answer as
                                            correct
                                        </label>
                                    </div>
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