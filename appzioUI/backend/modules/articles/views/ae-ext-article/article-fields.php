<?php

use yii\helpers\Url;

/**
 * @var backend\modules\articles\models\AeExtArticle $model
 * @var $images
 * @var $images_tags
 * @var $article_templates
 */

if (!empty($content)) : ?>

    <script>
        var field_article_data = <?php echo $content; ?>;
    </script>

<?php endif; ?>

<div class="form-group field-aeextarticle-content required">
    <label class="control-label col-sm-2" for="aeextarticle-content">Content</label>
    <div class="col-sm-8">

        <div class="outer-repeater" data-prefill-variable="field_article_data">
            <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer" style="margin-bottom: 20px;">

                    <div class="box box-default">
                        <div class="box-header with-border" style="cursor: pointer">
                            <h3 class="box-title article-type" data-field-placeholder="field-select-type"
                                data-field-prefix="Fieldtype">Block</h3>
                            <a class="show-hide-outer offset-link" href="#">show/hide</a>
                        </div>
                        <div class="box-body outer-body">
                            <div class="form-data-group">
                                <label>Select field type or template</label>
                                <select name="field-type" class="form-control select2 outer field-select-type"
                                        style="width: 100%;">
                                    <option selected="selected" value="text">Text</option>
                                    <option value="richtext">Rich Text</option>
                                    <option value="notes_box">Notes Box</option>
                                    <option value="QAsection">Q&A Section</option>
                                    <option value="blockquote">Blockquote</option>
                                    <option value="wraprow">Wrap Row</option>
                                    <option value="image">Image</option>
                                    <option value="gallery">Image gallery</option>
                                    <option value="video">Video</option>
                                    <option value="template">Predefined template</option>
                                </select>
                            </div>

                            <div class="settings-group group-text">
                                <div class="form-data-group">
                                    <label>Content</label>
                                    <textarea name="field-input" class="form-control outer" rows="3"
                                              placeholder="Text ..."></textarea>
                                </div>

                                <div class="form-data-group">
                                    <label>Styles</label>
                                    <input name="field-styles-text" type="text" class="form-control"
                                           placeholder="Define your styles">
                                </div>

                                <div class="form-data-group">
                                    <label>Link</label>
                                    <input name="field-link" type="text" class="form-control"
                                           placeholder="Set an onclick event for this field">
                                </div>
                            </div>

                            <div class="settings-group group-notes_box">
                                <div class="form-data-group">
                                    <label>Content</label>
                                    <textarea name="field-notes-content" class="form-control outer" rows="3"
                                              placeholder="Enter content ..."></textarea>
                                </div>

                                <div class="form-data-group">
                                    <label>Styles</label>
                                    <input name="field-styles" type="text" class="form-control"
                                           placeholder="Define your styles">
                                </div>
                            </div>

                            <div class="settings-group group-QAsection">
                                <div class="form-data-group">
                                    <label>Question</label>
                                    <input name="field-qa-question" type="text" class="form-control"
                                           placeholder="Enter question ...">
                                </div>

                                <div class="form-data-group">
                                    <label>Answer</label>
                                    <textarea name="field-qa-answer" class="form-control outer" rows="3"
                                              placeholder="Enter answer ..."></textarea>
                                </div>
                            </div>

                            <div class="settings-group group-blockquote">
                                <div class="form-data-group">
                                    <label>Blockquote</label>
                                    <textarea name="field-blockquote" class="form-control outer" rows="3"
                                              placeholder="Enter blockquote ..."></textarea>
                                </div>
                            </div>

                            <div class="settings-group group-repeaters">
                                <div class="inner-repeater">

                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Entry content</h3>
                                            <a class="show-hide offset-link" href="#">show/hide</a>
                                        </div>
                                        <div class="box-body">
                                            <div class="inner-repeater-body">
                                                <div data-repeater-list="inner-group" class="inner">
                                                    <div data-repeater-item class="inner" style="margin-bottom: 10px;">

                                                        <div class="form-data-group">
                                                            <label>Content</label>
                                                            <textarea name="field-input-inner"
                                                                      class="form-control outer" rows="3"
                                                                      placeholder="Text ..."></textarea>
                                                        </div>

                                                        <div class="form-data-group">
                                                            <label>Styles</label>
                                                            <input name="field-styles-inner" type="text"
                                                                   class="form-control"
                                                                   placeholder="Define your styles">
                                                        </div>

                                                        <div class="form-data-group">
                                                            <label>Link</label>
                                                            <input name="field-link-inner" type="text"
                                                                   class="form-control"
                                                                   placeholder="Set an onclick event for this field">
                                                        </div>

                                                        <div style="padding: 5px 0">
                                                            <input data-repeater-delete type="button" value="Delete"
                                                                   class="btn btn-danger inner pull-right"/>
                                                            <div style="clear: both"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input data-repeater-create type="button" value="Add inner elements"
                                                       class="btn btn-default inner"/>
                                            </div>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                            </div>

                            <div class="settings-group group-image">
                                <div class="form-data-group">
                                    <label>Attached Image</label>
                                    <!--<input name="field-image-id" type="text" class="form-control" placeholder="Set an ID of the image">-->

                                    <?php if ($images) : ?>

                                        <select class="form-control" name="field-image-id" id="field-image-id">
                                            <option value="">N/A</option>
                                            <?php
                                            foreach ($images as $image) {
                                                echo '<option value="' . $image->id . '">' . $image->photo . '</option>';
                                            }
                                            ?>
                                        </select>

                                    <?php else:
                                        $home_url = Url::home(true);
                                        ?>

                                        <p>
                                            There aren't any uploaded images yet.
                                            Please <a target="_blank"
                                                      href="<?php echo Url::to($home_url . 'articles/ae-ext-article-photo/create'); ?>">upload</a>
                                            some ;)
                                        </p>

                                    <?php endif; ?>

                                </div>
                            </div>

                            <div class="settings-group group-video">
                                <div class="form-data-group">
                                    <label>Streaming Video URL</label>
                                    <input name="field-video-link" type="text" class="form-control"
                                           placeholder="Enter a Video URL for streaming">
                                </div>
                            </div>

                            <div class="settings-group group-gallery">
                                <div class="form-data-group">
                                    <label>Gallery reference</label>

                                    <?php if ($images_tags) : ?>

                                        <select class="form-control" name="field-gallery-id" id="field-gallery-id">
                                            <option value="">N/A</option>
                                            <?php
                                            foreach ($images_tags as $tag) {
                                                echo '<option value="' . $tag . '">' . $tag . '</option>';
                                            }
                                            ?>
                                        </select>

                                    <?php else :
                                        $home_url = Url::home(true);
                                        ?>

                                        <p>
                                            There aren't any uploaded images yet.
                                            Please <a target="_blank"
                                                      href="<?php echo Url::to($home_url . 'articles/ae-ext-article-photo/create'); ?>">upload</a>
                                            some ;)
                                        </p>

                                    <?php endif; ?>

                                </div>
                            </div>

                            <div class="settings-group group-template">
                                <div class="form-data-group">
                                    <label>Select your template</label>

                                    <?php if ($article_templates) : ?>

                                        <p>
                                            <select class="form-control" name="field-template-styles"
                                                    id="field-template-styles">
                                                <?php
                                                foreach ($article_templates as $template) {
                                                    echo '<option value="' . $template['styles'] . '">' . $template['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>

                                        <p>
                                            <textarea name="field-input-template" class="form-control outer" rows="3"
                                                      placeholder="Text ..."></textarea>
                                        </p>

                                    <?php else :
                                        $home_url = Url::home(true);
                                        ?>

                                        <p>
                                            Your app doesn't have any field templates yet
                                            Please <a target="_blank"
                                                      href="<?php echo Url::to($home_url . 'articles/ae-ext-article-template/create'); ?>">create</a>
                                            some ;)
                                        </p>

                                    <?php endif; ?>

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