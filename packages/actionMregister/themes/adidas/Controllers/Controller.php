<?php

namespace packages\actionMregister\themes\adidas\controllers;

use packages\actionMregister\themes\adidas\Views\Main;
use packages\actionMregister\themes\adidas\Views\View as ArticleView;
use packages\actionMregister\themes\adidas\Models\Model as ArticleModel;

use moosend;
use moosend\Models;

class Controller extends \packages\actionMregister\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {

//        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        if ($this->getMenuId() == 'add_style') {
            if ($this->model->getSubmittedVariableByName('add_style')) {
                $this->model->addToVariable(
                    'my_styles',
                    $this->model->getSubmittedVariableByName('add_style')
                );
            }
        }

        if (strstr($this->getMenuId(), 'remove_style')) {
            $this->model->removeFromVariable(
                'my_styles',
                $this->model->getSubmittedVariableByName($this->getMenuId())
            );
        }

        if ($this->model->sessionGet('reg_phase') == 2) {
            return $this->actionAddPhoto();
        }

        if ($this->model->sessionGet('reg_phase') == 3) {
            return $this->actionSelectRole();
        }

        return $this->actionPageOne();


    }

    public function actionPageOne()
    {

        if ( $this->model->getSavedVariable('fb_token') ) {
            $facebookData = \ThirdpartyServices::getCompleteFBInfo($this->model->getSavedVariable('fb_token'));
            $this->setExtraProfileData($facebookData);
        }

        $data['fieldlist'] = $this->model->getFieldlist();
        $data['mode'] = 'show';

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if ($this->getMenuId() == 'signup') {

            $this->model->validatePage1();
            $this->model->validateMyEmail();

            if (empty($this->model->validation_errors)) {

                $this->model->savePage1();

                $this->model->saveVariable('price', $this->model->getSubmittedVariableByName('price'));

                if ($this->model->getSavedVariable('role') == 'user') {
                    $this->model->closeLogin();
                    $data['mode'] = 'close';
                    return ['View', $data];
                }

                $this->model->sessionSet('reg_phase', 2);

                return $this->actionAddPhoto();
            }
        }

        return ['View', $data];
    }


    public function actionAddPhoto() {
        $data['mode'] = 'show';

        $this->collectLocation();

        if (!$this->model->getSavedVariable('profilepic')) {
            $this->model->validation_errors['profilepic'] = '{#please_add_a_profile_picture#}';
        }

        if (empty($this->model->validation_errors)) {
            $this->model->sessionSet('reg_phase', 3);
            return $this->actionSelectRole();
        }

        return ['AddPhoto', $data];


    }

    public function actionSelectRole() {
        $data['mode'] = 'show';

        // At this point we should already have the user's coordinates
        if ( $this->model->getSavedVariable('lat') ) {
            $data = \ThirdpartyServices::geoAddressTranslation(
                $this->model->getSavedVariable('lat'),
                $this->model->getSavedVariable('lon'), $this->model->appid
            );

            if ( isset($data['country']) ) {
                $this->model->saveVariable('country', $data['country']);
            }

            if ( isset($data['city']) ) {
                $this->model->saveVariable('city', $data['city']);
            }

            if ( isset($data['street']) ) {
                $this->model->saveVariable('street', $data['street']);
            }
        }

        if (strstr($this->getMenuId(), 'set_role')) {
            $role = str_replace('set_role_', '', $this->getMenuId());
            $this->model->saveVariable('role', $role);

            /* if validation succeeds, we save data to variables and move user to page 2*/
            $this->model->closeLogin();
            $data['mode'] = 'close';

        }

        return ['PickRole', $data];
    }
}