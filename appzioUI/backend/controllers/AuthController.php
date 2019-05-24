<?php
namespace backend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
// use backend\models\PasswordResetRequestForm;
// use backend\models\ResetPasswordForm;
// use backend\models\SignupForm;
// use backend\models\ContactForm;
use backend\models\UsergroupsUserBase;

/**
 * Site controller
 */
class AuthController extends Controller
{

    /**
     * Auth the user.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $required = array(
            'token', 'game_id'
        );

        // To do: handle this in a batter way - probably redirect to a default screen or something

        foreach ($required as $required_param) {
            if ( !isset($_REQUEST[$required_param]) OR empty($_REQUEST[$required_param]) ) {
                die( 'Missing required parameter: ' . $required_param );
            }
        }

        $token = $_REQUEST['token'];
        $users = new UsergroupsUserBase();

        $user = $users->findOne(array(
            'laratoken' => $token
        ));

        if ( empty($user) ) {
            die( 'Sorry, not a valid login. Try to logout and log back in.' );
        }

        if ( $user->username == 'admin' ) {
            $priv = 'admin';
        } else {
            $priv = 'client';
        }

        // To do: better security

        $session = Yii::$app->session;

        Yii::$app->session['priv'] = $priv;
        Yii::$app->session['user_id'] = $user->id;
        Yii::$app->session['firstname'] = $user->firstname;
        Yii::$app->session['lastname'] = $user->lastname;
        Yii::$app->session['username'] = $user->username;
        Yii::$app->session['email'] = $user->email;
        Yii::$app->session['app_id'] = $_GET['game_id'];
        Yii::$app->session['config_id'] = md5( $_GET['game_id'] . '-db-' . Yii::$app->db->dsn);

        $this->redirect( 'users/usergroups-user' );

        return true;
    }

}