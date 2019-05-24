<?php

namespace backend\modules\users\controllers;

use app\models\Aetask;
use kartik\export\ExportMenu;
use Yii;

use backend\components\Helper;

use backend\modules\users\models\UsergroupsUser;
use backend\modules\users\search\UsergroupsUser as UsergroupsUserSearch;

use backend\modules\users\models\AeGamePlay;
use backend\modules\users\models\AeGamePlayVariable;
use backend\modules\users\models\AeExtMobilematching;

use yii\helpers\Url;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\filters\AccessControl;
use dmstr\bootstrap\Tabs;

/**
* This is the class for controller "UsergroupsUserController".
*/
class UsergroupsUserController extends \backend\modules\users\controllers\base\UsergroupsUserController
{

    public $enableCsrfValidation = false;

	/**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        $searchModel  = new UsergroupsUserSearch;
        $dataProvider = $searchModel->search($_GET);
        $usersModel = new UsergroupsUser();

        $app_id = Yii::$app->session['app_id'];

        if ( empty($app_id) ) {
            die( 'Missing App ID' );
        }

        $page = ( isset($_GET['page']) ? $_GET['page'] : 1 );

        $users_data = $usersModel->getUsers( $page, $app_id );
        $users = $users_data['users'];
        $total = $users_data['count'];

        $pages = new Pagination(['totalCount' => $total]);

        if ( empty($users) ) {
            
            $dataProvider->setModels( array() );

            return $this->render('index', [
                'data' => array(),
                'pages' => $pages,
                'total' => $total,
                'page' => $page,
                'variables' => Helper::getVariablesConfig(),
                'dataProvider' => $dataProvider,
            ]);
        }

        $users_full_data = array();

        foreach ($users as $i => $user) {
            
            if ( !isset($user['play_id']) OR empty($user['play_id']) ) {
                continue;
            }

            $play_id = $user['play_id'];

            $variables = $this->includeVariables( $play_id );

            $user['variables'] = $variables;

            $users_full_data[] = $user;
        }
        //$full_users_data = $usersModel->getUsers( $page, $app_id, null, true );
        /*$full_users_data = $usersModel->getUsers( $page, $app_id, null, true );
        $export_user_data = [];
        foreach ($full_users_data['users'] as $i => $user) {

            if ( !isset($user['play_id']) OR empty($user['play_id']) ) {
                continue;
            }

            $play_id = $user['play_id'];

            $variables = $this->includeVariables( $play_id );

            $user['variables'] = $variables;

            $export_user_data[] = $user;
        }*/
        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        $provider_data = array();
        foreach ($users_full_data as $data){
            foreach ($data['variables'] as $key => $var) {
                $data[$key] = $var['value'];
            }

            unset($data['variables']);
            $provider_data[] = $data;
        }
        /*$users_data = $usersModel->getUsers( $page, $app_id );
        $users = $users_data['users'];*/

        //$dataProvider->setModels( $users );
        return $this->render('index', [
            'data' => $users_full_data,
            'pages' => $pages,
            'total' => $total,
            'page' => $page,
            'variables' => Helper::getVariablesConfig(),
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionExport(){
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();
        return $this->render('export', [
            'error' => false
        ]);
    }

    public function actionDoexport(){
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();


        if(!$_POST['send_email']){
            return $this->render('export', [
                'error' => 'Please input your email'
            ]);
        }

        $parameters['email'] = $_POST['send_email'];
        $parameters['gid'] = Yii::$app->session['app_id'];
        $parameters['format'] = 'dbadmin';

        foreach ($_POST as $key=>$param){
            if(stristr($key, 'param_')){
                $parameters[$key] = $param;
            }
        }

        $task = new Aetask();
        $task->play_id = null;
        $task->context = 'async';
        $task->task = 'admin:userexport';
        $task->parameters = json_encode($parameters);
        $task->tries = 0;
        $task->launchtime = 0;
        $task->result = 'none';
        $task->timetolive = time()+1440;
        $task->insert();

        return $this->render('exportsent', []);
    }

    /**
     * Displays a single UsergroupsUser model.
     * @param string $id
     *
     * @return mixed
     * @throws HttpException
     */
    public function actionView($id)
    {

        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        $usersModel = new UsergroupsUser();

        $page = ( isset($_GET['page']) ? $_GET['page'] : 1 );

        $users_data = $usersModel->getUsers( $page, null, $id );

        if ( !isset($users_data['users'][0]) ) {
            return false;
        }

        $users = $users_data['users'];

        $variablesModel = new AeGamePlayVariable();

        $enriched_data = array();

        foreach ($users as $i => $user) {
            $play_id = $user['play_id'];
            $variables = $variablesModel->getPlayVariables( $play_id );
            $user['variables'] = $variables;
            $enriched_data[] = $user;
        }

        return $this->render('view', [
            'data' => $enriched_data[0],
            'model' => $this->findModel($id),
            'vars_map' => $this->variablesMap(),
        ]);
    }

    public function actionDelete($id, $delete_from_view = false) {
        
        if ( empty($id) ) {
            die( 'Missing Play ID' );
        }

        $play_model = new AeGamePlay();
        $user = $play_model->findOne( $id );
        $user->delete();

        if ( $delete_from_view ) {
            $this->redirect( '/appzioUI/backend/web/users/usergroups-user' );
        } else {
            $this->redirect( Url::previous() );
        }

    }

    public function actionUpdate() {

        $required_fields = array(
            '_p', 'field_to_update', 'value_to_update'
        );

        // If any of the required fields is missing, interrupt the update
        foreach ($required_fields as $field) {
            if ( !isset($_POST[$field]) ) {
                return false;
            }
        }

        $id = $_POST['_p'];

        $model = $this->findModel($id);

        // A failsafe. The findModel method would automatically handle the case
        // where the $id is not valid or doesn't exits
        if ( !is_object($model) ) {
            return false;
        }

        $field_to_update = $_POST['field_to_update'];
        $value_to_update = $_POST['value_to_update'];

        $vars_model = new AeGamePlayVariable();
        $playvar = $vars_model->findOne(array(
            'play_id' => $id,
            'variable_id' => $field_to_update,
        ));

        if ( !is_object($playvar) ) {
            return false;
        }

        $playvar->value = $value_to_update;

        if ( $playvar->update( false ) ) {
            die( json_encode(array(
                'redirect' => Url::previous()
            )) );
        }
    }

    /*
    * Return an array of variables
    * This method could also include data from _ext tables
    */
    public function includeVariables( $play_id ) {

        $vars = Helper::getVariablesConfig();

        $include_ext = false;
        $ext_vars = array();
        
        foreach ($vars as $var_key => $var_options) {
            if ( isset($var_options['location']) AND $var_options['location'] == 'mobilematching' ) {
                $include_ext = true;
                $ext_vars[$var_key] = $var_options;
            }
        }

        $var_data = array();

        $variablesModel = new AeGamePlayVariable();
        $var_data = $variablesModel->getPlayVariables( $play_id );

        if ( $include_ext ) {
            $matchingModel = new AeExtMobilematching();
            $matching_data = $matchingModel->findOne(array(
                'play_id' => $play_id
            ));

            foreach ($ext_vars as $ext_var => $ext_var_options) {
                if ( isset($matching_data->$ext_var) ) {
                    $var_data = array_merge($var_data, array(
                        $ext_var =>  array(
                            'id' => $ext_var,
                            'value' => $matching_data->$ext_var
                        ),
                    ));
                }
            }

        }

        return $var_data;
    }

    public function variablesMap() {
        return array(
            'reg_phase' => array(
                'name' => 'Registration Status',
                'is_editable' => true,
            ),
            'login_branch_id' => array(
                'exclude' => true,
            ),
            'register_branch_id' => array(
                'exclude' => true,
            ),
            'system_push_platform' => array(
                'exclude' => true,
            ),
            'onesignal_deviceid' => array(
                'exclude' => true,
            ),
            'birth_month' => array(
                'format' => 'date|month',
            ),
            'last_login' => array(
	            'format' => 'full_date',
            ),
            'dont_match_nearby' => array(
                'format' => 'switcher',
            ),
            'filter_religion' => array(
                'format' => 'switcher',
            ),
            'filter_age_end' => array(
                'name' => 'Max age',
            ),
            'filter_age_start' => array(
                'name' => 'Min age',
            ),
            'intro_repeats' => array(
                'exclude' => true,
            ),
            'last_match_update' => array(
                'exclude' => true,
            ),
            'adv_id' => array(
                'exclude' => true,
            ),
            'push_permission_checker' => array(
                'exclude' => true,
            ),
            'system_source' => array(
                'name' => 'Device Type',
                'format' => 'display_device',
            ),
            'logged_in' => array(
                'format' => 'blank',
            ),
            'notify' => array(
                'name' => 'Push Notifications',
                'format' => 'switcher',
            ),
            'real_name' => array(
                'is_editable' => true,
            ),
            'email' => array(
                'is_editable' => true,
            ),
            'phone' => array(
                'is_editable' => true,
            ),
            'name' => array(
                'is_editable' => true,
            ),
            'first_name' => array(
                'is_editable' => true,
            ),
            'surname' => array(
                'is_editable' => true,
            ),
            'country' => array(
                'is_editable' => true,
            ),
            'country_code' => array(
                'is_editable' => true,
            ),
            'additional_info' => array(
                'is_editable' => true,
                'edit_type' => 'textarea',
            ),
            'age' => array(
                'is_editable' => true,
            ),
            'gender' => array(
                'is_editable' => true,
            ),
            'user_approved' => array(
                'is_editable' => true,
            ),
            'profile_comment' => array(
                'is_editable' => true,
            ),
            'can_view_statistics' => array(
                'is_editable' => true,
            ),
        );
    }

    /**
    * Finds the UsergroupsUser model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param string $id
    * @return UsergroupsUser the loaded model
    * @throws HttpException if the model cannot be found
    */
    protected function findModel($id)
    {
        if (($model = AeGamePlay::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

}