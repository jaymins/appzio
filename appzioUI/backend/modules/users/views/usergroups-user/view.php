<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;
use backend\components\Helper;

/**
* @var yii\web\View $this
* @var frontend\models\UsergroupsUser $model
*/

$this->title = Yii::t('backend', 'Application Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Application Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'View';

$home_url = Url::home();
$real_url = Url::to( $home_url . 'users/usergroups-user/update' );

?>
<div class="giiant-crud usergroups-user-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <?php
        $variables = $data['variables'];
    ?>

    <h1>
        <?php if (
            (isset($variables['name']) AND $variables['name']['value']) OR
            (isset($variables['real_name']) AND $variables['real_name']['value'])
        ) : ?>            

            <?php echo ( isset($variables['name']) ? $variables['name']['value'] : $variables['real_name']['value'] ); ?>

        <?php else : ?>

            <?= Yii::t('backend', 'Usergroups User') ?>

        <?php endif; ?>

        <div class="pull-right">
            <?= Html::a('<span class="glyphicon glyphicon-list"></span> '
            . 'Full list', ['index'], ['class'=>'btn btn-default']) ?>
        </div>
    </h1>

    <hr />

    <?php $this->beginBlock('user-variables') ?>
            
        <?php foreach ($variables as $var_name => $all_var_data) : 
            $var_value = $all_var_data['value'];
            $safe_name = ucfirst( str_replace('_', ' ', $var_name) );

            $format = false;
            $is_editable = false;

            if ( isset($vars_map[$var_name]) ) {

                $var_settings = $vars_map[$var_name];

                if ( isset($var_settings['exclude']) ) {
                    continue;
                }

                if ( isset($var_settings['name']) ) {
                    $safe_name = $var_settings['name'];
                }

                $format = ( isset($var_settings['format']) ? $var_settings['format'] : false );
                $is_editable = ( isset($var_settings['is_editable']) ? $var_settings['is_editable'] : false );
            }
        ?>

            <tr>
                <td>
                    <?php echo $safe_name; ?>
                    <!-- <small style="display: block; color: #4c4c4c; font-weight: normal;">(<?php echo $var_name; ?>)</small> -->
                </td>
                <td>
                    <?php if ( $format == 'display_device' ) : ?>

                        <?php
                            echo ( preg_match('~android~', $var_value) ? 'Android' : 'iOS' );
                        ?>

                    <?php elseif ( $format == 'switcher' ) : ?>

                        <?php 
                            echo ( $var_value ? 'ON' : 'OFF' );
                        ?>

                    <?php elseif ( $format == 'date|month' ) : ?>

                        <?php
                            if ( $var_value AND is_numeric($var_value) ) {
                                $date_obj   = DateTime::createFromFormat('!m', $var_value);
                                echo $date_obj->format('F');
                            }
                        ?>

                    <?php elseif ( $format == 'full_date' ) : ?>

	                    <?php
	                    if ( $var_value AND is_numeric($var_value) ) {
		                    echo date('F j, Y, g:i a', $var_value);
	                    }
	                    ?>

                    <?php elseif ( $format == 'blank' ) : ?>

                        <?php echo ( $var_value ? 'Yes' : '' ); ?>

                    <?php elseif ( empty($var_value) ) : ?>

                        <span class="not-set">(not set)</span>

                    <?php elseif ( $var_name == 'email' ) : ?>

                        <a href="mailto:<?php echo $var_value; ?>"><?php echo $var_value; ?></a>

                    <?php elseif ( json_decode( $var_value ) AND ( stristr( $var_value, '{' ) OR stristr( $var_value, '[' ) ) ) :
                        $arr_data = json_decode( $var_value, true );                    
                        $is_assoc = Helper::isAssoc( $arr_data );
                    ?>

                        <?php echo count( $arr_data ); ?>
                        &nbsp;

                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-<?php echo $var_name; ?>">
                            Preview Data
                        </button>

                        <div class="modal fade" id="modal-<?php echo $var_name; ?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo $safe_name; ?></h4>
                                    </div>

                                    <div class="modal-body">

                                        <div class="box">
                                            <div class="box-body">
                                                <table class="table table-bordered">
                                                    <tbody>

                                                        <?php if ( !$is_assoc ) : ?>

                                                            <?php foreach ($arr_data as $i => $arr_entry):

                                                                if ( is_array($arr_entry) ) {
                                                                    echo Yii::$app->controller->renderPartial('/partials/popup-subarray-data', array(
                                                                        'count' => $i,
                                                                        'data' => $arr_entry
                                                                    ));
                                                                } else {
                                                                    echo Yii::$app->controller->renderPartial('/partials/popup-simple-data', array(
                                                                        'count' => $i,
                                                                        'data' => $arr_entry
                                                                    ));
                                                                }
                                                                
                                                            ?>

                                                            <?php endforeach ?>

                                                        <?php else : ?>

                                                            <?php 
                                                                echo Yii::$app->controller->renderPartial('/partials/popup-assoc-data', array(
                                                                    'data' => $arr_data
                                                                ));
                                                            ?>

                                                        <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div><!-- /.box-body -->
                                        </div>

                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    <?php elseif( preg_match('~profilepic~', $var_name) ) : 
                        $image_url = Helper::getPic( $var_value );
                    ?>

                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-<?php echo $var_name; ?>">
                            <img style="max-width: 120px; height: auto;" src="<?php echo $image_url; ?>">
                        </button>

                        <div class="modal fade" id="modal-<?php echo $var_name; ?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo $safe_name; ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <img style="max-width: 100%; height: auto;" src="<?php echo $image_url; ?>">
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    <?php else : ?>

                        <?php echo $var_value; ?>

                    <?php endif; ?>
                </td>

                <td>
                    <?php if ( $is_editable ) : ?>
                        <button type="button" class="btn btn-vk btn-xs" data-toggle="modal" data-target="#modal-edit-<?php echo $var_name ?>">
                            Edit
                        </button>

                        <div class="modal fade" id="modal-edit-<?php echo $var_name; ?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Edit <?php echo $safe_name; ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <?php 
                                            // Get the edit type
                                            $type = ( isset($var_settings['edit_type']) ? $var_settings['edit_type'] : '' );
                                        ?>

                                        <div class="box box-primary">
                                            <!-- form start -->
                                            <form class="users-edit-form" method="POST" action="<?php echo $real_url; ?>" role="form">
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label>Enter <?php echo $safe_name; ?></label>

                                                        <?php if ( $type == 'textarea' ) : ?>
                                                            <textarea class="form-control" name="current_val" rows="3"><?php echo $var_value ?></textarea>
                                                        <?php else : ?>
                                                            <input class="form-control" name="current_val" value="<?php echo $var_value ?>" type="text">
                                                        <?php endif; ?>

                                                    </div>
                                                </div><!-- /.box-body -->
                                                <div class="box-footer">
                                                    <button class="btn btn-primary" type="submit">Submit</button>
                                                    <input type="hidden" name="current_var" value="<?php echo $all_var_data['id'] ?>" />
                                                    <input type="hidden" name="current_var" value="<?php echo $var_name ?>" />
                                                    <input type="hidden" name="_p" value="<?php echo ( isset($_GET['id']) ? $_GET['id'] : '' ) ?>" />
                                                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    <?php endif; ?>
                </td>

            </tr>
        
        <?php endforeach; ?>

    <?php $this->endBlock() ?>

    <div class="tab-content">
        <div class="tab-pane active" id="relation-tabs-tab0">
            
            <table class="table table-striped table-bordered detail-view" id="w0">
                <tbody>
                    <tr>
                        <td>User ID</td>
                        <td><?php echo $data['userid']; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Play ID</td>
                        <td><?php echo $data['play_id']; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Registered</td>
                        <td><?php echo $data['creation_date']; ?></td>
                        <td></td>
                    </tr>

                    <?php
                        if ( $variables ) {
                            echo $this->blocks['user-variables'];
                        }
                    ?>

                </tbody>
            </table>

            <hr>

            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . 'Delete', ['delete', 'id' => $model->id, 'delete_from_view' => true],
            [
            'class' => 'btn btn-danger',
            'data-confirm' => '' . 'Are you sure to delete this item?' . '',
            'data-method' => 'post',
            ]);
            ?>

        </div>
    </div>

</div>