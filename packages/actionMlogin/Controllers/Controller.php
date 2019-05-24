<?php

namespace packages\actionMlogin\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMlogin\Models\Model as ArticleModel;

class Controller extends BootstrapController
{
    public $view;

    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        return ['Login'];
    }

    public function actionFblogin()
    {
        $data = [];
        $fbtoken = $this->model->getSavedVariable('fb_token');

        // If a Token exists, we should try to match it with an existing user first
        if ($fbtoken) {
            $token_login = false;
            $user_exists = $this->checkForFBUser();

            if ( !$user_exists ) {
                $fbinfo = \ThirdpartyServices::getUserFbInfo($fbtoken);

                if (!isset($fbinfo->id)) {
                    $data['errors'] = 'Login error';
                    return ['Login', $data];
                }

                return ['RedirectBranch', [
                    'branch' => $this->model->getConfigParam('register_branch')
                ]];
            }

            $this->finishLogin(true, true, __LINE__, $token_login);
            $data['finishLogin'] = 1;
            return ['Login', $data];
        }

        $data['errors'] = '{#couldnt_connect_with_facebook#}';
        return ['Login'];
    }

    public function checkForFBUser()
    {
        // Note - since FB no longer provides the user's ID, we could only refer to his/her email address
        $thirdpartyid = $this->model->getSavedVariable('email');
        $user_play_id = $this->model->loginWithFacebook($thirdpartyid);

        if ($user_play_id) {
            $this->playid = $user_play_id;
            return true;
        }

        return false;
    }

    public function finishLogin($skipreg = false, $fblogin = false, $line = false, $tokenlogin = false)
    {
        \AeplayBranch::activateBranch($this->model->getConfigParam('logout_branch'), $this->playid);
        \AeplayBranch::activateBranch($this->model->getActionidByPermaname('intro_branch'), $this->playid);
        \AeplayBranch::closeBranch($this->model->getConfigParam('register_branch'), $this->playid);
        \AeplayBranch::closeBranch($this->model->getConfigParam('login_branch'), $this->playid);

        $this->model->saveVariable('logged_in', 1);
        $this->model->saveVariable('reg_phase', 'complete');

        if ( $name = $this->model->getSavedVariable('name') ) {
            $this->model->saveVariable('username', $name);
        }

        // To do: Check this
        if ($fblogin OR $tokenlogin) {
            $this->model->deleteVariable('fb_universal_login');
        }

        return true;
    }

}