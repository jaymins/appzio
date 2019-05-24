<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\grid\GridView;
use yii\widgets\LinkPager;

use backend\components\Helper;

use kartik\export\ExportMenu;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var backend\models\UsergroupsUserSearch $searchModel
*/

$this->title = Yii::t('backend', 'Application Users');
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
$actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . 'New', ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>

<div class="giiant-crud usergroups-user-index">
    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('backend', 'Application Users') ?>

        <div style="display: inline-block; padding-left: 30px;">
            <?php
                $gridColumns = [
                    ['class' => 'yii\grid\SerialColumn'],
                    //'userid',
                    'play_id',
                    'creation_date',
                    //'real_name',
                    'email',
                    //'gender',
                   // 'reg_phase',
                    ['class' => 'yii\grid\ActionColumn'],
                ];


                if ( $dataProvider->totalCount > 0 ) {


                    $view_url = Url::toRoute(array( 'usergroups-user/export' ));

                    echo('<i class="text-primary glyphicon glyphicon-floppy-open" style="font-size:20px;margin-right:10px;"></i><a href="' .$view_url.'" style="font-size:14px;">CSV Export</a>');
                    // Renders a export dropdown menu
/*                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,
                        'stream' => false,
                        'batchSize' => 20,
                        'showConfirmAlert' => false,
                        'folder' => '@webroot/runtime/export', // this is default save folder on server
                        'linkPath' => '/runtime/export', // the web accessible location to the above folder
                    ]);*/
                }
            ?>
        </div>
        <!-- <div class="pull-right">
        </div> -->
    </h1>

    <hr />

    <div class="table-responsive">
        <div class="grid-view" id="w1">

            <?php if ( $total ) : 
                $per_page = 20;
                $offset = ($page) * $per_page;
            ?>

                <div class="summary">
                    Showing <b><?php echo $offset - $per_page ?>-<?php echo ( $offset > $total ? $total : $offset ); ?></b> of <b><?php echo number_format( $total ); ?></b> users.
                </div>

            <?php endif; ?>

            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="x">
                        <th class="action-column">&nbsp;</th>
                        <th>Pic</th>
                        <!-- <th>User ID</th> -->
                        <th width="70">Play ID</th>
                        <th>Registered From - To</th>

                        <?php foreach ($variables as $vn => $var_heading): ?>
                            <th><?php echo ( isset($var_heading['label']) ? $var_heading['label'] : 'N/A' ); ?></th>
                        <?php endforeach ?>
                    </tr>

                    <?php
                        $filters = array_merge(array(
                            // 'user_id' => array(
                            //     'label' => 'User ID',
                            //     'type' => 'input',
                            // ),
                            'play_id' => array(
                                'label' => 'Play ID',
                                'type' => 'input',
                            ),
                            'created_at' => array(
                                'label' => 'Created at - from',
                                'type' => 'datepicker',
                            ),
                        ), $variables);
                    ?>

                    <tr class="filters" id="w1-filters">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>

                        <?php foreach ($filters as $filter_var_key => $filter_var_value): 

                            $value = '';

                            if ( isset($_GET['query'][$filter_var_key]) ) {
                                $value = $_GET['query'][$filter_var_key];
                            }

                            $type = ( isset($filter_var_value['type']) ? $filter_var_value['type'] : '' );
                        ?>

                            <?php if ( $type == 'select' ): 
                                $select_vals = $filter_var_value['values'];
                            ?>

                                <td>
                                    <select name="query[<?php echo $filter_var_key ?>]" class="form-control select2" style="width: 100%;">
                                        <?php foreach ($select_vals as $sv): 
                                            $selected = ( $sv == $value ? 'selected="selected"' : '' );

                                            $select_label = ( $sv ? $sv : 'All' );
                                        ?>
                                            
                                            <option value="<?php echo $sv ?>" <?php echo $selected; ?>><?php echo $select_label; ?></option>
                                            
                                        <?php endforeach ?>
                                    </select>
                                </td>

                            <?php elseif ( $type == 'datepicker' ): ?>

                                <td width="250">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="datefilter" name="query[created_at]" value="<?php echo $value; ?>">
                                    </div>
                                    <!-- /.input group -->
                                </td>

                            <?php else :  ?>

                                <td><input class="form-control" name="query[<?php echo $filter_var_key ?>]" value="<?php echo $value; ?>" type="text"></td>

                            <?php endif; ?>
                            
                        <?php endforeach ?>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($data as $user): 
                        $vars = $user['variables'];

                        $play_id = $user['play_id'];

                        $view_url = Url::toRoute(array( 'usergroups-user/view', 'id' => $play_id ));
                        $delete_url = Url::toRoute(array( 'usergroups-user/delete', 'id' => $play_id ));
                        $update_url = Url::toRoute(array( 'usergroups-user/update', 'id' => $play_id ));
                    ?>
                        
                        <tr data-key="1">
                            <td nowrap="nowrap">
                                <div class="action-buttons">
                                    <a aria-label="View" data-pjax="0" href="<?php echo $view_url; ?>" title="View">
                                        <span class="glyphicon glyphicon-file"></span>
                                    </a>
                                    <!-- <a aria-label="Update" data-pjax="0" href="<?php echo $update_url; ?>" title="Update">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </a> -->
                                    <a aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0" href="<?php echo $delete_url; ?>" title="Delete">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </div>
                            </td>
                            <td class="profile-image">
                                <?php
                                    $pic = 'http://www.realcommercial.com.au/building/assets/images/avatar_placeholder.png';
                                    if ( isset($vars['profilepic']) AND !empty($vars['profilepic']['value']) ) {
                                        $pic = Helper::getPic( $vars['profilepic']['value'] );
                                    }
                                ?>
                                <a href="<?php echo $view_url; ?>">
                                    <div style="background-image: url('<?php echo $pic; ?>'); background-size: cover; width: 50px; height: 50px;">
                                    </div>
                                </a>
                            </td>
                            <!-- <td><?php echo $user['userid']; ?></td> -->
                            <td><?php echo $user['play_id']; ?></td>
                            <td><?php echo date( 'Y-m-d', strtotime($user['creation_date']) ); ?></td>

                            <?php

                                foreach ($variables as $var_key => $var_name) {

                                    if ( isset($vars[$var_key]) AND $vars[$var_key]['value'] ) {

                                        if ( stristr($vars[$var_key]['value'], '[') OR stristr($vars[$var_key]['value'], '{') ) {
                                            $current_var_value = json_decode( $vars[$var_key]['value'], true );
	                                        echo '<td>' . implode(', ', $current_var_value) . '</td>';
                                        } else {
	                                        echo '<td>' . $vars[$var_key]['value'] . '</td>';
                                        }

                                    } else {
                                        echo '<td>N/A</td>';
                                    }
                                }

                            ?>

                        </tr>

                    <?php endforeach ?>

                </tbody>
            </table>

            <?php
                // display pagination
                echo LinkPager::widget([
                    'pagination' => $pages,
                    'firstPageLabel' => 'First',
                    'lastPageLabel'  => false
                ]);
            ?>

        </div>    
    </div>

</div>

<?php
    $query = $_SERVER['REQUEST_URI'];

    // if ( isset($_GET['query']) ) {
    //     $query = preg_replace('~&page=\d~', '', $query);
    // }

    $options = [
        'filterUrl' => Url::to( $query ),
        'filterSelector' => "#w1-filters input, #w1-filters select",
    ];

    $params = Json::htmlEncode( $options );

    $this->registerJs(
        "jQuery('#w1').yiiGridView({$params});",
        4,
        'form-fixer'
    );
?>

<?php \yii\widgets\Pjax::end() ?>