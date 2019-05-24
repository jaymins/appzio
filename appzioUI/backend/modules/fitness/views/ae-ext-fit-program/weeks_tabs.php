<?php

/**
 * @var backend\modules\fitness\models\AeExtFitExercise $model
 * @var $movements
 * @var $movement_category
 * @var $current_selection
 * @var $weeks
 * @var $recipes_json
 * @var $exercise_json
 * @var $recipes
 * @var $exercise
 */

if (!empty($current_selection)) : ?>

    <script>
        var weeks_tabs = <?php echo $current_selection; ?>;
    </script>

<?php endif; ?>

<hr/>

<div class="row">
    <div class="col-sm-2">&nbsp;</div>
    <div class="col-md-8">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <?foreach ($weeks as $id=>$week):?>
                <li class="<?echo ($id==1)?'active':''?>"><a href="#<? echo $id?>" data-toggle="tab" aria-expanded="true"><? echo $week?></a></li>
                <? endforeach;?>
            </ul>
            <div class="tab-content">
                <?foreach ($weeks as $id=>$week):?>

                <div class="tab-pane <?echo ($id==1)?'active':''?>" id="<? echo $id?>">
                    <? if ($recipes):?>
                    <?= Yii::$app->controller->renderPartial('recipe-fields', [
                        'recipes'=>$recipes,
                        'week'=>$id,
                        'items_per_day' => $items_per_day,
                        'weeks' => $weeks,
                        'recipes_json' => $recipes_json
                    ]); ?>

                    <?php elseif($exercise):?>
                        <?= Yii::$app->controller->renderPartial('fitness-fields', [
                            'exercise'=>$exercise,
                            'week'=>$id,
                            'weeks' => $weeks,
                            'exercise_json' => $exercise_json,
                        ]); ?>
                    <? else:?>
                    <?endif?>

                </div>
                <? endforeach;?>
            </div>
        </div>
    </div>
</div>