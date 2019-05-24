<?php

namespace packages\actionMusersettings\themes\adidas\Controllers;

use packages\actionMswipematch\themes\jammatez\Views\Main;
use packages\actionMswipematch\themes\jammatez\Models\Model as ArticleModel;

class Profile extends \packages\actionMusersettings\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
        $this->model->rewriteActionConfigField('background_color', '#ffffff');
    }

    public function actionDefault(){
        $data = array();

        if (strstr($this->getMenuId(), 'unblock_user_')) {
            $id = str_replace('unblock_user_', '', $this->getMenuId());
            $this->model->sessionSet('profile_id', $id);
            $this->model->unblockUser($id);

        } else if (strstr($this->getMenuId(), 'block_user_')) {
            $id = str_replace('block_user_', '', $this->getMenuId());
            $this->model->sessionSet('profile_id', $id);
            $this->model->blockUser($id);
        }

        if (is_numeric($this->getMenuId())) {
            $this->model->sessionSet('profile_id', $this->getMenuId());
        }

        if ($this->model->sessionGet('profile_id')) {

            $profileId = $this->model->sessionGet('profile_id');

//            $data['user_blocked'] = $this->model->isUserBlocked($profileId);
//            $data['chat_blocked'] = $this->model->isChatBlocked($profileId);

            $data['userData'] = $this->model->foreignVariablesGet($profileId);

            $data['userData']['playid'] = $profileId;
            $data['userData']['distance'] = 'NaN';
            if (isset($data['userData']['lat'])) {
                $data['userData']['distance'] = round($this->distance(
                    $data['userData']['lat'],
                    $data['userData']['lon'],
                    $this->model->getSavedVariable('lat'),
                    $this->model->getSavedVariable('lon'),
                    'k'), 2);
            }

            return ["Profile", $data];
        }

    }

    protected function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}