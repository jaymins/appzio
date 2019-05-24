<?php

/**
 * @var backend\modules\quizzes\models\AeExtQuizQuestion $model
 * @var $questions
 * @var $current_selection
 */

if (!empty($current_selection)) : ?>

    <script>
        var field_quiz_questions = <?php echo $current_selection; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-answers-content required">
    <label class="control-label col-sm-2" for="field-answers-content">Questions</label>
    <div class="col-sm-8">

        <div class="outer-repeater" data-prefill-variable="field_quiz_questions">
            <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title" data-field-placeholder="field-select-question"
                                data-field-prefix="Question">Associated question</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">

                            <div class="settings-group-default">
                                <div class="form-data-group">
                                    <select name="field-select-question" class="form-control field-select-question"
                                            id="field-select-question">

                                        <option value="">N/A</option>

                                        <?php foreach ($questions as $question) : ?>

                                            <option value="<?php echo $question['id']; ?>"><?php echo $question['title']; ?></option>

                                        <?php endforeach; ?>

                                    </select>
                                    <input name="field-input-id" type="hidden" value="">
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